<?php
/**
 * Custom variable add to cart template with quantity buttons
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$available_variations = $product->get_available_variations();
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
?>

<form class="variations_form cart"
      action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
      method="post"
      enctype="multipart/form-data"
      data-product_id="<?php echo absint( $product->get_id() ); ?>"
      data-product_variations="<?php echo $variations_attr; ?>">

    <?php do_action( 'woocommerce_before_variations_form' ); ?>

    <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
        <p class="stock out-of-stock"><?php esc_html_e( 'Questo prodotto è attualmente non disponibile.', 'basetheme' ); ?></p>
    <?php else : ?>

        <table class="variations" cellspacing="0">
            <tbody>
                <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                    <tr>
                        <td class="label">
                            <label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
                                <?php echo wc_attribute_label( $attribute_name ); ?>
                            </label>
                        </td>
                        <td class="value">
                            <?php
                            wc_dropdown_variation_attribute_options( array(
                                'options'   => $options,
                                'attribute' => $attribute_name,
                                'product'   => $product,
                            ));
                            echo end( $attribute_keys ) === $attribute_name
                                ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Cancella', 'basetheme' ) . '</a>' ) )
                                : '';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="single_variation_wrap">
            <?php
            // Mostra solo prezzo e disponibilità
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
            do_action( 'woocommerce_single_variation' );
            ?>

            <div class="custom-add-to-cart flex">
                <div class="quantity-wrapper">
                    <?php
                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
                    ));
                    ?>
                    <div class="contain-qty">
                        <button type="button" class="qty-btn qty-plus">+</button>
                        <button type="button" class="qty-btn qty-minus">−</button>
                    </div>
                </div>

                <div>
                    <button type="submit" class="single_add_to_cart_button button alt">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                        <span class="span-cart"><i class='bx bx-cart-alt'></i></span>
                    </button>
                </div>
                <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
                <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
                <input type="hidden" name="variation_id" class="variation_id" value="0" />
            </div>

            <?php do_action( 'woocommerce_after_single_variation' ); ?>
        </div>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>
