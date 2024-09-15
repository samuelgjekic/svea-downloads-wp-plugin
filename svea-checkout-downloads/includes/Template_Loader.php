<?php

namespace Svea_Checkout_Downloads;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Make sure to exit if file is directly accessed
}

 /**
  * Loader class to load all the template files for the plugin.
  */
class Template_Loader {
    
    /**
     * Array that contains the template filenames with a set key.
     * @var array
     */
    private static $templates = [
        'widget' => 'svea-downloads-widget.php',
        'form'   => 'svea-downloads-widget-form.php',
        'widget_settings' => 'svea-downloads-widget-settings.php',
    ];

    /**
     * A string containing the path to the templates to use as views.
     * @var string default: templates/ 
     */
    private static $template_path;

    /**
     * The array containing the arguments to use in the widget inner and outer HTML.
     * @var array 
     */
    private static $widget_args;
    
    /**
     * Loads the template loader.
     *
     * @return void
     */
    public static function init() {
        if ( ! defined( 'SVEA_TEMPLATE_PATH' ) ) {
            define( 'SVEA_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . '../templates/' );
        }

        self::$template_path = SVEA_TEMPLATE_PATH;

        self::$widget_args = [
            'before_widget' => '<div class="wrap">',
            'after_widget' => '</div>',
            'title' => 'Svea Checkout Downloads'
        ];
    }
    
    /**
     * Returns the path to the template files with filename based on parameter.
     *
     * @param  mixed $template_name The key of the array containing the filenames. Example: 'widget_settings'
     * @return string
     */
    public static function get_template_path( string $template_name ): string {
        if ( isset( self::$templates[ $template_name ] ) ) {
            return self::$template_path . self::$templates[ $template_name ];
        }
        return '';
    }
    
    /**
     * Sets the widget arguments for the BEFORE_WIDGET and AFTER_WIDGET.
     *
     * @param  mixed $args The array with the before_widget and after_widget keys.
     * @return void
     */
    public static function set_widget_args(array $args): void{
        self::$widget_args = $args;
    }
    
    /**
     * Returns the set widget arguments for BEFORE_WIDGET and AFTER_WIDGET.
     *
     * @return array Returns before_widget and after_widget values.
     */
    public static function get_widget_args(): array{
        return self::$widget_args;
    }
}

Template_Loader::init();
