<?php

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Make sure to exit if file is directly accessed
}

 /**
  * The widget class which handles the Svea Downloads Widget
  */
class Svea_Downloads_Widget extends \WP_Widget {

    /**
     * The title of the widget
     * @var string
     */
    public $widget_title;

    /**
     * The description of the widget
     * @var string
     */
    public $widget_description;

    /**
     * The ID of the widget
     * @var string
     */
    public $widget_id = 'svea_downloads_widget';

    /**
     * The getter class
     * Fetches the download count from the Svea Checkout Plugin API on Wordpress.com
     *
     * @var Svea_Downloads_Getter
     */
    public $getter;

    /**
     * Constructor which checks flag isEnableOutsideAdmin, if flag is true
     * then the widget will be available both on the admin dashboard and as a widget.
     * Default is false, so the widget will only be shown in the admin dashboard.
     * @param bool $isEnabledOutsideAdmin : True will enable the widget outside the admin dashboard, will ignore parameter if WP option is available.
     * @return void
     */
    public function __construct($isEnabledOutsideAdmin = false) {

        $settings = get_option('svea_checkout_downloads_options');
        $isEnabledOutsideAdmin = isset($settings['enable_outside_admin']) ? (bool)$settings['enable_outside_admin'] : $isEnabledOutsideAdmin;
        $this->getter = new Svea_Downloads_Getter();

        $this->widget_title = esc_html__('Svea Checkout Total Downloads', 'svea-checkout-downloads');
        $this->widget_description = esc_html__('Displays total downloads of the Svea Checkout plugin.', 'svea-checkout-downloads');

        parent::__construct(
            $this->getId(),
            __($this->getTitle(), 'svea-checkout-downloads'),
            [
                'description' => __($this->getDesc(), 'svea-checkout-downloads'),
            ]
        );

        if ($isEnabledOutsideAdmin) {
            add_action('widgets_init', [$this, 'register_widget']);
            add_shortcode('svea_downloads_widget', [$this, 'shortcode_handler']);       
        }
        add_action('wp_dashboard_setup', [$this, 'register_dashboard_widget']);
    }

    /**
     * The "frontend" display of the widget.
     *
     * @param array $args  Args for 'before_widget' and 'after_widget'
     * @param array $instance The instance of the widget, needed for compatibility with WP_Widget
     */
    public function widget($args, $instance) {
        $downloads = get_transient('svea_downloads_count');

        if (false === $downloads) {
            $downloads = $this->getter->get_download_count();
            if (false === $downloads) {
                error_log('Svea Checkout: Could not retrieve download count.');
                echo '<p>' . esc_html__('Could not retrieve download count. Please try again later.', 'svea-checkout-downloads') . '</p>';
                return;
            }
        }

        echo $args['before_widget'];
        $template_file = Template_Loader::get_template_path('widget');

        if (file_exists($template_file)) {
            include_once $template_file;
        } else {
            echo '<p>' . esc_html__('Template not found!', 'svea-checkout-downloads') . '</p>';
            error_log(__METHOD__ . ' Could not include svea-downloads-widget.php');
        }

        echo $args['after_widget'];
    }

    /**
     * Displays the widget settings form in the WordPress admin for customizing the title and layout
     *
     * @param array $instance
     * @return void
     */
    public function form($instance) {
        $args = Template_Loader::get_widget_args();
        $defaults = [
            'title' => __($this->getTitle(), 'svea-checkout-downloads'),
            'before_widget' => $args['before_widget'],
            'after_widget' => $args['after_widget'],
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $template_file = Template_Loader::get_template_path('form');
        if (file_exists($template_file)) {
            include_once $template_file;
        } else {
            echo '<p>' . esc_html__('Template not found!', 'svea-checkout-downloads') . '</p>';
            error_log(__METHOD__ . ' Could not include svea-downloads-widget-form.php');
        }
    }

    /**
     * Update and sanitize the widget settings
     *
     * @param array $new_instance The new widget
     * @param array $old_instance For compatibility with WordPress WP_Widget class
     * @return array
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['before_widget'] = (!empty($new_instance['before_widget'])) ? sanitize_text_field($new_instance['before_widget']) : '<div>';
        $instance['after_widget'] = (!empty($new_instance['after_widget'])) ? sanitize_text_field($new_instance['after_widget']) : '</div>';
        return $instance;
    }

    /**
     * Register the widget to WordPress
     *
     * @return void
     */
    public static function register_widget() {
        register_widget('Svea_Checkout_Downloads\Svea_Downloads_Widget');
    }

    /**
     * Register the widget to be visible on the admin dashboard page
     *
     * @return void
     */
    public function register_dashboard_widget() {
        wp_add_dashboard_widget(
            'svea_downloads_dashboard_widget',
            __($this->getTitle(), 'svea-checkout-downloads'),
            [$this, 'render_dashboard_widget']
        );
    }

    /**
     * Render the dashboard widget
     *
     * @return void
     */
    public function render_dashboard_widget() {
        $args = Template_Loader::get_widget_args();

        $this->widget($args, []);
    }

    /**
     * Handle the shortcode output
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function shortcode_handler($atts) {
        $args = Template_Loader::get_widget_args();
        
        ob_start();
        $this->widget($args, []);
        return ob_get_clean();
    }

    /**
     * Returns the widget Title
     * @return string
     */
    public function getTitle(): string {
        return $this->widget_title;
    }

    /**
     * Returns the widget description
     * @return string
     */
    public function getDesc(): string {
        return $this->widget_description;
    }

    /**
     * Returns the widget id
     * @return string
     */
    public function getId(): string {
        return $this->widget_id;
    }

}


