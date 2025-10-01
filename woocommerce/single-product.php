<?php get_header(); ?>

<div class="grid--xl">
    <div class="col-xl-100">
        <div class="mt-30">
            <?php wc_print_notices(); ?>
        </div>
    </div>
</div>

<div class="back-button-mobile">
    <div class="grid--xl">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
            <i class='bx bxs-chevron-left'></i> <span>TORNA ALLO SHOP</span>
        </a>
    </div>
</div>

<div class="product-breadcrumbs">
    <div class="grid--xl">
        <?php woocommerce_breadcrumb(); ?>
    </div>
</div>

<div class="grid--xl">
        
    <!--Single Procuct-->
    <?php while (have_posts()) : the_post(); ?>
    
        <div class="product-content">
            <div class="product-image">
                <?php woocommerce_show_product_images(); ?>
            </div>
            <div class="product-details">
                <h1><?php the_title(); ?></h1>
                <?php woocommerce_template_single_price(); ?>
                <div class="description-short"><?php woocommerce_template_single_excerpt(); ?></div>
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>
        </div>

        <?php
            global $product;
            if ( $product ) {
                $long_description = $product->get_description();
                if ( $long_description ) {
                    echo '<div class="product-description">';
                    echo '<h3>' . esc_html__( 'Description', 'fluxor' ) . '</h3>';
                    echo wp_kses_post( wpautop( $long_description ) );
                    echo '</div>';
                }
            }
        ?>    

    <?php endwhile; ?>

    <!--Related product-->
    <div class="related-products">
        <?php
            woocommerce_related_products(array(
                'posts_per_page' => 4, // Numero di prodotti
                'columns'        => 4, // Colonne per riga
            ));
        ?>
    </div>


</div>

<?php get_footer(); ?>
