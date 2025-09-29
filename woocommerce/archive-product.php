<?php get_header(); ?>


<div class="title-shop">
    <div class="grid--xl">
        <h1><?php woocommerce_page_title(); ?></h1>
    </div>
</div>


<div class="grid--xl">


    <div class="shop-container">
        
        <div class="shop-products">

            <div class="category-list">
                <h3>Categorie</h3>
                <?php
                function fluxor_display_nested_product_categories($parent_id = 0, $level = 1, $max_depth = 3) {
                    $args = array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                        'parent'     => $parent_id,
                    );

                    $categories = get_terms($args);
                    if (empty($categories) || is_wp_error($categories)) {
                        return;
                    }

                    $ul_class = 'product-categories level-' . intval($level);
                    if ($level > 1) {
                        $ul_class .= ' subcategories';
                    }

                    echo '<ul class="' . esc_attr($ul_class) . '">';

                    foreach ($categories as $category) {
                        // Verifica figli
                        $has_children = false;
                        if ($level < $max_depth) {
                            $children = get_terms(array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => true,
                                'parent'     => $category->term_id,
                                'fields'     => 'ids',
                                'number'     => 1,
                            ));
                            $has_children = !empty($children);
                        }

                        echo '<li class="' . ($has_children ? 'has-children' : '') . '">';

                        $submenu_id = 'pcat-sub-' . intval($category->term_id);

                        // Link: se ha figli aggiungiamo attributi per il toggle
                        $link_attrs = $has_children && $level === 2
                            ? ' href="' . esc_url(get_term_link($category)) . '" class="toggle-link" data-toggle="true" aria-controls="' . esc_attr($submenu_id) . '" aria-expanded="false"'
                            : ' href="' . esc_url(get_term_link($category)) . '"';

                        echo '<a' . $link_attrs . '>';

                        // Testo categoria dentro span
                        echo '<span class="cat-name">' . esc_html($category->name) . '</span>';

                        // Freccia a destra solo per level 2 con figli
                        if ($has_children && $level === 2) {
                            echo '<i class="bx bx-chevron-down"></i>';
                        }

                        echo '</a>';

                        // Figli
                        if ($has_children) {
                            if ($level === 2) {
                                ob_start();
                                fluxor_display_nested_product_categories($category->term_id, $level + 1, $max_depth);
                                $nested = ob_get_clean();
                                echo '<div id="' . esc_attr($submenu_id) . '" class="submenu-container" style="display:none;">' . $nested . '</div>';
                            } else {
                                fluxor_display_nested_product_categories($category->term_id, $level + 1, $max_depth);
                            }
                        }

                        echo '</li>';
                    }

                    echo '</ul>';
                }

                fluxor_display_nested_product_categories(0, 1, 3);
                ?>
            </div>

           

            <?php if (woocommerce_product_loop()) : ?>
                <?php woocommerce_product_loop_start(); ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php wc_get_template_part('content', 'product'); ?>
                    <?php endwhile; ?>
                <?php woocommerce_product_loop_end(); ?>
            <?php else : ?>
                <p><?php esc_html_e('Nessun prodotto trovato', 'fluxor'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php get_footer(); ?>
