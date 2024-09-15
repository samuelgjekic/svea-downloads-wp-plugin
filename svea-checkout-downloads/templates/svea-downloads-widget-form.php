<?php
/**
 * This is the template file for the widget form
 */

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e('Title:', 'svea-checkout-downloads'); ?>
    </label>
    <input
        class="widefat"
        id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>"
        type="text"
        value="<?php echo esc_attr(Template_Loader::get_widget_args()['title']); ?>"
    />
</p>

<p>
    <label for="<?php echo $this->get_field_id('before_widget'); ?>">
        <?php _e('Before Widget HTML:', 'svea-checkout-downloads'); ?>
    </label>
    <input
        class="widefat"
        id="<?php echo $this->get_field_id('before_widget'); ?>"
        name="<?php echo $this->get_field_name('before_widget'); ?>"
        type="text"
        value="<?php echo esc_attr(Template_Loader::get_widget_args()['before_widget']); ?>"
    />
</p>

<p>
    <label for="<?php echo $this->get_field_id('after_widget'); ?>">
        <?php _e('After Widget HTML:', 'svea-checkout-downloads'); ?>
    </label>
    <input
        class="widefat"
        id="<?php echo $this->get_field_id('after_widget'); ?>"
        name="<?php echo $this->get_field_name('after_widget'); ?>"
        type="text"
        value="<?php echo esc_attr(Template_Loader::get_widget_args()['after_widget']); ?>"
    />
</p>
<?php
