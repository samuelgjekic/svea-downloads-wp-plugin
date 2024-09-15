<?php
/**
 * This is the template file for the widget
 */
namespace Svea_Checkout_Downloads;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="svea-checkout-plugin">
    <div class="card" style="max-width: 100%;">
        <div class="card-header">
            <?php echo esc_html__('Svea Checkout Downloads', 'svea-checkout-downloads'); ?>
        </div>
        <div class="card-body">
            <h5 class="card-title">
                <?php echo esc_html__('Total Downloads', 'svea-checkout-downloads'); ?>
            </h5>
            <p class="card-text">
                <?php
                echo sprintf(
                    // translators: %d is the number of times the plugin has been downloaded
                    esc_html__('The Svea Checkout plugin has been downloaded %d times.', 'svea-checkout-downloads'),
                    intval($downloads)
                );
                ?>
            </p>
        </div>
        <div class="card-footer">
            <?php echo esc_html__('Last updated on:', 'svea-checkout-downloads'); ?> <?php echo esc_html(date('F j, Y')); ?>
        </div>
    </div>
</div>
<?php
