<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */


defined('ABSPATH') || exit;

get_header('shop');
?>

<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');
?>

<div class="title-shop">
    <div class="grid--xl">
        <h1><?php woocommerce_page_title(); ?></h1>
    </div>
</div>

<div class="grid--xl">
    <div class="shop-container prod-list">

        <div class="category-list">
            <h3>Categorie</h3>
            <?php
            if (!function_exists('fluxor_display_nested_product_categories')) {
                function fluxor_display_nested_product_categories($parent_id = 0, $level = 1, $max_depth = 3)
                {
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
                        $link_attrs = $has_children && $level === 2
                            ? ' href="' . esc_url(get_term_link($category)) . '" class="toggle-link" data-toggle="true" aria-controls="' . esc_attr($submenu_id) . '" aria-expanded="false"'
                            : ' href="' . esc_url(get_term_link($category)) . '"';

                        echo '<a' . $link_attrs . '>';
                        echo '<span class="cat-name">' . esc_html($category->name) . '</span>';
                        if ($has_children && $level === 2) {
                            echo '<i class="bx bx-chevron-down"></i>';
                        }
                        echo '</a>';

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
            }

            fluxor_display_nested_product_categories(0, 1, 3);
            ?>
        </div>

        <div class="shop-products">
            <?php
            /**
             * Hook: woocommerce_shop_loop_header.
             * (gestisce il titolo e la descrizione categoria)
             */
            do_action('woocommerce_shop_loop_header');

            if (woocommerce_product_loop()) {

                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked woocommerce_output_all_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();

                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();
                        do_action('woocommerce_shop_loop');
                        wc_get_template_part('content', 'product');
                    }
                }

                woocommerce_product_loop_end();

                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');
            } else {
                do_action('woocommerce_no_products_found');
            }
            ?>
        </div>

    </div>
</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 */
do_action('woocommerce_sidebar');

get_footer('shop');
?>
