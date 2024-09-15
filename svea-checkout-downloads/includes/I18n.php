<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit;
 } // Make sure to exit if file is directly accessed

 class I18n {

    private $language_files_path;
    
    /**
     * Initialize the language class
     *
     * @return void
     */
    public function initialize() {

        $this->language_files_path = dirname(plugin_basename(__FILE__)) . '/languages';
        add_action('plugins_loaded', [ $this, 'load_language_files'], 1);
    }
    
    /**
     * Load the language files. 
     * Note: Logs error in error log if language file does not exist for current language
     *
     * @return void
     */
    public function load_language_files() {
        $this->language_files_path = dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        ;
        
        $loaded = load_plugin_textdomain('svea-checkout-downloads', false, $this->getPath());

        if (!$loaded) {
            error_log('Warning: Could not load language files! Path: ' . $this->getPath());
        }
    }
    
    /**
     * Returns the path to the language files
     *
     * @return string
     */
    public function getPath(): string{
        return $this->language_files_path;
    }
}

 