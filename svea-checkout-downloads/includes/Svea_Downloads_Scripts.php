<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit;
 } // Make sure to exit if file is directly accessed

 /**
  * Scripts class that handles loading scripts in Wordpress
  */
 class Svea_Downloads_Scripts {
     
    /**
     * Enqueue bootstrap css for admin dashboard only
     *
     * @return void
     */
    public static function enqueue_styles_admin() {
        wp_enqueue_style(
            'svea-checkout-backend-css',
            plugin_dir_url(__FILE__) . '../assets/css/style_widget.css',
              array(), '1.1.9'
            );
    }

     /**
     * Enqueue bootstrap css for frontend
     *
     * @return void
     */
    public static function enqueue_styles_frontend() {
        wp_enqueue_style('svea-checkout-frontend-css',
        plugin_dir_url(__FILE__) . '../assets/css/style_widget.css',
          array(), '1.1.9'
        );
    }
 }

