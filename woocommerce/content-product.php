<?php
defined('ABSPATH') || exit;

global $product;

// Assicurati che il prodotto sia visibile
if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<div <?php wc_product_class('custom-product-item', $product); ?>>
	<div class="container-img-product">
		<a href="<?php the_permalink(); ?>">
			<?php echo woocommerce_get_product_thumbnail('full'); ?>

			<?php if ($product->is_on_sale()) : ?>
				<span class="badge sale-badge"><?php esc_html_e('On sale', 'fluxor'); ?></span>
			<?php endif; ?>

			<?php if (!$product->is_in_stock()) : ?>
				<span class="badge outofstock-badge"><?php esc_html_e('Not available', 'fluxor'); ?></span>
			<?php endif; ?>
		</a>
		<div class="product-add-to-cart">
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>

    <div class="product-info">
        <h2 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

        <div class="product-price">
            <?php woocommerce_template_loop_price(); ?>
        </div>

    </div>
</div>
