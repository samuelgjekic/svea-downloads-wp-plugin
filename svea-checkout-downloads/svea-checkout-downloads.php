<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit;
 } // Make sure to exit if file is directly accessed

/**
 * Plugin Name: Svea Checkout Downloads
 * Plugin URI: https://github.com/samuelgjekic/svea-downloads-wp-plugin
 * Description: Shows the total number of downloads of the Svea Checkout plugin.
 * Version: 1.0
 * Author: Samuel Gjekic
 * Text Domain: svea-checkout-downloads
 * Domain Path: /languages
 */

 /**
 * Define path to plugin dir
 */
if(! defined( 'SVEA_CHECKOUT_DOWNLOADS_DIR')) {
    define( 'SVEA_CHECKOUT_DOWNLOADS_DIR',__DIR__);
}

if ( ! class_exists( 'Svea_Checkout_Downloads\\Plugin' ) ) {

    class Plugin {

        /**
         * Name of the plugin
         */
        const PLUGIN_TITLE = 'svea-checkout-downloads';

        /**
         * Plugin version
         */
        const VERSION = '1.0';
        
        /**
         * @var string
         */
        protected $plugin_description;

        /**
         * @var string
         */
        protected $plugin_label;


        /**
         * Language class that handles the translations
         * @var I18n
         */
        public $I18n;
        

        /**
         * The widget class which contains the widget module
         *
         * @var Svea_Downloads_Widget
         */
        public $widget;
        
        /**
         * The settings class which contains the settings module for the plugin
         * @var Svea_Downloads_Settings
         */
        public $settings;

        
        /**
         * init_plugin Initializes the plugin and its dependencies
         * 
         * @return void
         */
        public function init_plugin() {

            $this->load_dependencies();
            $this->init_modules();
            $this->init_hooks();

            $this->plugin_label = esc_html__( 'Svea Checkout Download Statistics', 'svea-checkout-downloads');
            $this->plugin_description = esc_html__('Shows the total number of downloads of the Svea Checkout plugin', 'svea-checkout-downloads');
        }

        /**
         * Load plugin dependencies 
         */
        public function load_dependencies() {
            require_once SVEA_CHECKOUT_DOWNLOADS_DIR . '/vendor/autoload.php';
        }

        public function init_modules(){;

            // Load Languages
            $this->I18n = new I18n();
            $this->I18n->initialize();

            // Load settings module
            $this->settings = new Svea_Downloads_Settings();
            $this->settings->initialize();

            // Load the widget module
            $this->widget = new Svea_Downloads_Widget();

        }

        /**
         * Register hooks
         */
        public function init_hooks() {
            add_action('admin_enqueue_scripts', ['Svea_Checkout_Downloads\\Svea_Downloads_Scripts', 'enqueue_styles_admin']);
            add_action('wp_enqueue_scripts', ['Svea_Checkout_Downloads\\Svea_Downloads_Scripts', 'enqueue_styles_frontend']);
        }
                
        /**
         * Gets the instance of the Plugin class
         *
         * @return Plugin
         */
        public static function get_instance(): Plugin {

            static $instance;

            if ( $instance === null ) {
                $instance = new Plugin();
                $instance->init_plugin();
            }

            return $instance;
        }
    }

    $instance = Plugin::get_instance();
}
