<?php
/**
 * This is the template file for the widget settings
 */

namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Make sure to exit if file is directly accessed
}
?>
<div class="wrap">
            <h1><?php esc_html_e('Svea Checkout Downloads Settings', 'svea-checkout-downloads'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('svea_checkout_downloads_options_group');
                do_settings_sections('svea-checkout-downloads-settings');
                submit_button();
                ?>
            </form>
        </div>

<?php
