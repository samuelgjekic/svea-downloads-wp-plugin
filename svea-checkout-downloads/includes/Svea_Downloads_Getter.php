<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Make sure to exit if file is directly accessed
}

 /**
  * Getter class for fetching the download count from Svea Checkout Plugin API on Wordpress.
  */
class Svea_Downloads_Getter {

    /**
     * The time in seconds to store the value in a transient
     * @var int
     */
    protected $cache_interval = 600;
    
    /**
     * The Cache manager that handles the transient storing and fetching
     *
     * @var Cache_Manager
     */
    public $cache_manager;

    /**
     * The key to use when storing and fetching transient
     * @var string
     */
    const CACHE_KEY = 'svea_downloads_count';

    public function __construct() {
        $this->cache_manager = new Cache_Manager();
    }
        
    /**
     * Performs an HTTP request and returns number of downloads of the svea checkout plugin for woocommerce.
     * The method uses caching if one wants to avoid fetching from API every pageload if
     * $useCaching is set to true.
     * 
     * @param bool $useCaching : If set to true it will use cached value before fetching a new from the api.
     * @return int | bool depending on if the request was successful. False for failure.
     */
    public function get_download_count(bool $useCaching = false): int {
        
        /**
         * If useCaching is enabled it skips fetching from the api and check transient first.
         */

         $options = get_option('svea_checkout_downloads_options');
         $useCaching = isset($options['enable_caching']) ? (bool)$options['enable_caching'] : $useCaching;
        
        if(!$useCaching){
        $downloads = $this->fetchDownloads();
        } else {
        $cached_downloads = $this->cache_manager->exists(SELF::CACHE_KEY);
        }
        
        /**
         * Updates the value in transient if the transient value does not match the value fetched from the json
         * Will only fetch the value from the api if transient do not exist.
         */
        if ($useCaching && ($cached_downloads === false)) {
            $downloads = $this->fetchDownloads();
            $success = $this->cache_manager->set(self::CACHE_KEY, $downloads, $this->get_cache_interval());
            if (!$success) {
                error_log(__METHOD__ . ' - Failed to set transient for ' . self::CACHE_KEY . ' with value: ' . $downloads);
            }
        }
        return $downloads;
    }
    
    /**
     * Fetches the download count from the API
     *
     * @return int
     */
    private function fetchDownloads(): int {

        $response = wp_remote_get('https://api.wordpress.org/plugins/info/1.0/svea-checkout-for-woocommerce.json');
        if( is_wp_error($response)) {
            error_log(__METHOD__ . ' - API Error: ' . $response->get_error_message());
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $downloads = $data['downloaded'] ?? false;


        if ($downloads === null || !is_numeric($downloads)) {
            error_log(__METHOD__ . ' - Invalid or missing download count in JSON response.');
            return false;
        }

        return $downloads;
    }
    
    /**
     * Sets the time in seconds for how long a transient will be stored.
     * @param  int $seconds    :Time in seconds
     * @return void
     */
    public function set_cache_interval(int $seconds): void {
        $this->cache_interval = $seconds;
    }
    
    /**
     * Returns the caching interval in seconds
     * @return int 
     */
    public function get_cache_interval(): int {
        return $this->cache_interval;
    }
}