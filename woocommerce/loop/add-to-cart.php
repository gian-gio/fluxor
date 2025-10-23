<?php
defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

// Imposta valori di base
$product_id   = $product->get_id();
$product_type = $product->get_type(); // es: simple, variable, grouped...
$in_stock     = $product->is_in_stock();

$defaults = array(
    'quantity'   => 1,
    'class'      => 'button custom-add-to-cart-btn',
    'attributes' => array(
        'data-product_id'  => $product_id,
        'data-product_sku' => $product->get_sku(),
        'aria-label'       => $product->add_to_cart_description(),
        'rel'              => 'nofollow',
    ),
);

$args = isset($args) ? wp_parse_args($args, $defaults) : $defaults;
 
// Inizializza variabili per testo e icona
$button_icon = '<i class="bx bx-cart"></i>';
$button_text = esc_html__('Add to cart', 'fluxor');

// Condizione 1: Prodotto variabile
if ($product_type === 'variable') {
    $button_icon = '<i class="bx bx-slider-alt"></i>';
    $button_text = esc_html__('Choose options', 'fluxor');
}

// Condizione 2: Prodotto esaurito
if (!$in_stock) {
    $button_icon = '<i class="bx bx-block"></i>';
    $button_text = esc_html__('Not available', 'fluxor');
    $args['class'] .= ' disabled';
}

// Condizione 3: Prodotto in offerta
if ($product->is_on_sale() && $in_stock && $product_type === 'simple') {
    $button_icon = '<i class="bx bx-purchase-tag"></i>';
    $button_text = esc_html__('Buy now', 'fluxor');
}

// Costruisci link
if (!$in_stock) {
    // Prodotto non disponibile → niente link cliccabile
    echo sprintf(
        '<a href="#" class="%s" aria-disabled="true">%s %s</a>',
        esc_attr($args['class']),
        $button_text,
        $button_icon
    );
} else {
    // Prodotto disponibile → link normale
    echo sprintf(
        '<a href="%s" data-quantity="%s" class="%s" %s>%s <span>%s</span></a>',
        esc_url($product->add_to_cart_url()),
        esc_attr($args['quantity']),
        esc_attr($args['class']),
        wc_implode_html_attributes($args['attributes']),
        $button_text,
        $button_icon
    );
}
?>
