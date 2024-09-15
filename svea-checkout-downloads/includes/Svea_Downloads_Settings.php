<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Make sure to exit if file is directly accessed
}

 /**
  * Settings class that handles the Svea Downloads widget custom settings
  */
class Svea_Downloads_Settings {

    /**
     * Register the settings and add settings page.
     */
    public function initialize(): void{
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Add the settings page to the admin menu.
     */
    public function add_settings_page() {
        add_options_page(
            'Svea Checkout Downloads Settings',
            'Svea Checkout Downloads',
            'manage_options',
            'svea-checkout-downloads-settings',
            [$this, 'settings_page']
        );
    }

    /**
     * Render the settings page content.
     */
    public function settings_page() {

        $template_file = Template_Loader::get_template_path('widget_settings');
        include_once($template_file);
    }

    /**
     * Register settings and fields.
     */
    public function register_settings() {

        register_setting(
            'svea_checkout_downloads_options_group',
            'svea_checkout_downloads_options',
            [$this, 'sanitize_options']
        );

        add_settings_section(
            'svea_checkout_downloads_main_section',
            '',
            null,
            'svea-checkout-downloads-settings'
        );

        add_settings_field(
            'enable_caching',
            __('Enable Caching', 'svea-checkout-downloads'),
            [$this, 'enable_caching_callback'],
            'svea-checkout-downloads-settings',
            'svea_checkout_downloads_main_section'
        );

        add_settings_field(
            'enable_outside_admin',
            __('Enable Widget Outside Admin Dashboard', 'svea-checkout-downloads'),
            [$this, 'enable_outside_admin_callback'],
            'svea-checkout-downloads-settings',
            'svea_checkout_downloads_main_section'
        );
    }

    /**
     * Sanitize and validate the options.
     *
     * @param array $input The input data.
     * @return array The sanitized data.
     */
    public function sanitize_options($input) {

        $sanitized = [];
        $sanitized['enable_caching'] = isset($input['enable_caching']) ? (bool)$input['enable_caching'] : false;
        $sanitized['enable_outside_admin'] = isset($input['enable_outside_admin']) ? (bool)$input['enable_outside_admin'] : false;
        return $sanitized;
    }

    /**
     * Callback for the Enable Caching checkbox.
     */
    public function enable_caching_callback() {
        $options = get_option('svea_checkout_downloads_options');
        $checked = isset($options['enable_caching']) ? (bool)$options['enable_caching'] : false;
        echo '<input type="checkbox" name="svea_checkout_downloads_options[enable_caching]" value="1" ' . checked(1, $checked, false) . '/>';
    }

    /**
     * Callback for the Enable Widget Outside Admin Dashboard checkbox.
     */
    public function enable_outside_admin_callback() {
        $options = get_option('svea_checkout_downloads_options');
        $checked = isset($options['enable_outside_admin']) ? (bool)$options['enable_outside_admin'] : false;
        echo '<input type="checkbox" name="svea_checkout_downloads_options[enable_outside_admin]" value="1" ' . checked(1, $checked, false) . '/>';
    }

}
