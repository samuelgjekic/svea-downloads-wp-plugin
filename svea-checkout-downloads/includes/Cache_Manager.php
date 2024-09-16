<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Make sure to exit if file is directly accessed
}

class Cache_Manager {

     /**
     * The key to use when storing and fetching transient
     * @var string
     */
    const CACHE_KEY = 'svea_downloads_count';
    
    /**
     * Get a cached value using a transient.
     *
     * @param string $key The key for the cached value.
     * @return mixed The cached value or false if not found.
     */
    public function get($key) {
        $cached_value = get_transient($key);

        if ($cached_value === false) {
            return false;
        }

        return $cached_value;
    }

    /**
     * Set a value to be cached with a transient.
     *
     * @param string $key The key for the cached value.
     * @param mixed $value The value.
     * @param int $expiration Time in seconds for the cache to expire. Default is 1 hour.
     * @return bool True if set successfully.
     */
    public function set($key, $value, $expiration = HOUR_IN_SECONDS) {
        if (empty($key)) {
            error_log("Svea Checkout Cache Manager: Invalid cache key provided.");
            return false;
        }

        $is_set = set_transient($key, $value, $expiration);

        if (!$is_set) {
            error_log("Svea Checkout Cache Manager: Failed to set cache for key '{$key}'.");
        }

        return $is_set;
    }

    /**
     * Check if a cached value exists.
     *
     * @param string $key The key for the value.
     * @return bool True if the cached value exists.
     */
    public function exists($key) {
        $exists = get_transient($key) !== false;

        return $exists;
    }

    /**
     * Delete a cached value using a transient.
     *
     * @param string $key The key for value.
     * @return bool True if deleted.
     */
    public function delete($key) {
        $is_deleted = delete_transient($key);

        if (!$is_deleted) {
            error_log("Svea Checkout Cache Manager: Failed to delete cache for key '{$key}'.");
        }

        return $is_deleted;
    }
}
