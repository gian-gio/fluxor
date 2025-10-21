<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to fluxor/woocommerce/single-product/add-to-cart/simple.php.
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product->is_purchasable()) {
    return;
}

echo wc_get_stock_html($product); // Availability

if ($product->is_in_stock()) : ?>

    <?php do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
        <?php do_action('woocommerce_before_add_to_cart_button'); ?>

        <?php
        do_action('woocommerce_before_add_to_cart_quantity');
        ?>

        <div class="quantity-wrapper">
            <?php
            woocommerce_quantity_input(array(
                'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                'input_value' => isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : $product->get_min_purchase_quantity(),
            ));
            ?>
            <div class="contain-qty">
                <button type="button" class="qty-btn qty-plus">+</button>
                <button type="button" class="qty-btn qty-minus">−</button>
            </div>
        </div>

        <?php
        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt">
            <?php echo esc_html($product->single_add_to_cart_text()); ?>
            <span class="span-cart"><i class='bx bx-cart-alt'></i></span>
        </button>

        <?php do_action('woocommerce_after_add_to_cart_button'); ?>
    </form>

    <?php do_action('woocommerce_after_add_to_cart_form'); ?>

<?php endif; ?>
