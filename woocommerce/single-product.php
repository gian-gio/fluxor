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
    <div class="grid">
        <?php woocommerce_breadcrumb(); ?>
    </div>
</div>

<div class="grid">
        
    <!--Single Procuct-->
    <?php while (have_posts()) : the_post(); ?>
    
        <div class="product-content">
            <div class="product-image">
                <?php woocommerce_show_product_images(); ?>
            </div>
            <div class="product-details">
                <h1><?php the_title(); ?></h1>
                <?php woocommerce_template_single_price(); ?>

                <div class="label-product">
                <?php
                    global $product;
                    
                    if ( $product ) {
                        // SKU
                        if ( $product->get_sku() ) {
                            echo '<p><strong>SKU:</strong> ' . esc_html( $product->get_sku() ) . '</p>';
                        }

                        // Categorie
                        $categories = wc_get_product_category_list(
                            $product->get_id(),
                            ', ', // separatore
                            '<p><strong>' . __( 'Categorie:', 'basetheme' ) . '</strong> ',
                            '</p>'
                        );
                        echo $categories;
                    }
                ?>
                </div>

                <div class="description-short"><?php woocommerce_template_single_excerpt(); ?></div>
                <?php woocommerce_template_single_add_to_cart(); ?>
                <?php
                    global $product;
                    if ( $product ) {
                        $long_description = $product->get_description();
                        if ( $long_description ) {
                            echo '<div class="product-description">';
                            echo '<h3 class="accordion-header">' . esc_html__( 'Product Description', 'fluxor' ) . '</h3>';
                            echo '<div class="accordion-content">' . wp_kses_post( wpautop( $long_description ) ) . '</div>';
                            echo '</div>';
                        }
                    }
                ?>    
            </div>
        </div>


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
