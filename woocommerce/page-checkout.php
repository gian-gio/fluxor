<?php /* 

	Template Name: Checkout Page 

*/ ?>

<?php get_header(); ?>

<div class="title-shop">
  <div class="grid--xl">
    <h1><?php esc_html_e( 'Checkout', 'fluxor' ); ?></h1>
  </div>
</div>

<div class="grid--xl">
  <div class="checkout-container section--white">
  
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn-back-to-cart">
      &larr; <?php esc_html_e( 'Back to cart', 'fluxor' ); ?>
    </a>

    <?php
    // Mostra messaggi WooCommerce, se presenti (es. errori)
    wc_print_notices();

    // Stampa il form di checkout se esiste
    if (function_exists('woocommerce_checkout')) {
        woocommerce_checkout();
    } else {
        echo do_shortcode('[woocommerce_checkout]');
    }
    ?>

  </div>
</div>

<?php get_footer(); ?>