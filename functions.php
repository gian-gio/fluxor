<?php
/**
 * Theme: fluxor
 *
 * Theme Functions, includes, etc.
 *
 * @package fluxor
 */


/*  Theme setup
/* ------------------------------------ */

function fluxor_setup() {

    // Enable theme supports
    add_theme_support('custom-header');
    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('align-wide');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 200,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('comments');

    // Custom image sizes
    add_image_size('image-small', 350, 270, true);
    add_image_size('image-big', 1400, 900, true);

    // Enable page excerpts
    add_post_type_support('page', 'excerpt');

    // Register menus
    register_nav_menus(array(
        'header' => esc_html__('Header', 'fluxor'),
        'quickmenu' => esc_html__('Quick Menu', 'fluxor'),
        'footermenu' => esc_html__('Footer Menu', 'fluxor'),
    ));

    // Woocommerce
    add_theme_support('wc-product-gallery-lightbox');
    //add_theme_support('wc-product-gallery-zoom');
    //add_theme_support('wc-product-gallery-slider');

  /* block pattern */
  require_once( get_template_directory() . '/functions/patterns.php' );

}

add_action('after_setup_theme', 'fluxor_setup');



// WooCommerce support
add_theme_support( 'woocommerce' );

function fluxor_woocommerce_cart_menu_item($items, $args) {
    if ($args->theme_location === 'quickmenu' && class_exists('WooCommerce')) {

        // Mostra icona account
        $account_url = esc_url(wc_get_page_permalink('myaccount'));

        if (is_user_logged_in()) {
            // Utente loggato → link diretto alla dashboard
            $items .= '<li class="account-item">
                <a href="' . $account_url . '">
                    <i class="bx bxs-user"></i>
                </a>
            </li>';
        } else {
            // Utente non loggato → link alla login (stessa pagina WooCommerce)
            $items .= '<li class="account-item">
                <a href="' . $account_url . '">
                    <i class="bx bxs-user"></i>
                </a>
            </li>';
        }

        
        // Mostra icona carrello
        if (get_theme_mod('fluxor_show_cart', true)) {
            $cart_count = WC()->cart->get_cart_contents_count();
            $cart_url = wc_get_cart_url();

            $items .= '<li class="cart-item">
                <a href="' . esc_url($cart_url) . '">
                    <i class="bx bx-cart-alt"></i> 
                    <span class="cart-count">' . esc_html($cart_count) . '</span>
                </a>
            </li>';
        }

    }

    return $items;
}
add_filter('wp_nav_menu_items', 'fluxor_woocommerce_cart_menu_item', 10, 2);



function fluxor_customize_register($wp_customize) {
  // Aggiungi una sezione WooCommerce
  $wp_customize->add_section('fluxor_woocommerce_section', array(
      'title'    => __('WooCommerce Settings', 'fluxor'),
      'priority' => 30,
  ));

  // Aggiungi una opzione per mostrare/nascondere il carrello nel menu
  $wp_customize->add_setting('fluxor_show_cart', array(
      'default'   => true,
      'transport' => 'refresh',
      'sanitize_callback' => 'fluxor_sanitize_checkbox'
  ));

  $wp_customize->add_control('fluxor_show_cart', array(
      'type'     => 'checkbox',
      'section'  => 'fluxor_woocommerce_section',
      'label'    => __('Mostra icona carrello nel menu', 'fluxor'),
  ));
}

add_action('customize_register', 'fluxor_customize_register');

// Sanification 
function fluxor_sanitize_checkbox($checked) {
  return (isset($checked) && $checked === true) ? true : false;
}

// Evita che WooCommerce cerchi di forzare l’allineamento dei template personalizzati
add_filter( 'woocommerce_defer_template_part_sync', '__return_true' );



//Imposta il numero di prodotti per pagina nello shop

add_filter( 'loop_shop_per_page', 'fluxor_products_per_page', 20 );

function fluxor_products_per_page( $cols ) {
    // Imposta i prodotti per pagina
    return 12;
}


// Load translation
function fluxor_load_textdomain() {
  load_theme_textdomain('fluxor', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'fluxor_load_textdomain');


// Custom Sidebar
function fluxor_custom_sidebar() {
  register_sidebar( array(
      'name'          => 'Custom Sidebar',
      'id'            => 'custom-sidebar',
      'before_widget' => '<div class="widget">',
      'after_widget'  => '</div>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'fluxor_custom_sidebar' );


/*Custom Logo */

function fluxor_customize_logo_section($wp_customize) {
    
    $wp_customize->add_section('logo_section', array(
        'title'       => __('Logo', 'fluxor'),
        'priority'    => 30,
        'description' => __('Manage the site logo and additional logo options.', 'fluxor'),
    ));

    
    $wp_customize->get_control('custom_logo')->section = 'logo_section';
    $wp_customize->get_control('custom_logo')->priority = 1;
    $wp_customize->get_control('custom_logo')->label = __('Default Logo', 'fluxor');
    $wp_customize->get_control('custom_logo')->description = __('Upload a default logo to be used in the header site.', 'fluxor');

    
  
    $wp_customize->add_setting('footer_logo', array(
        'default'   => '', // No logo default
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'footer_logo',
        array(
            'label'       => __('Footer Logo', 'fluxor'),
            'description' => __('Upload a logo to be used in the footer.', 'fluxor'),
            'section'     => 'logo_section',
            'priority'    => 3,
        )
    ));
}
add_action('customize_register', 'fluxor_customize_logo_section');


/*  Enqueue javascript
/* ------------------------------------ */
  function fluxor_scripts() {

    wp_enqueue_script('fluxor-gsap-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', array(), null, true);
    wp_enqueue_script('fluxor-gsap-scrolltrigger-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', array(), null, true);
    wp_enqueue_script('fluxor-splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', array(), null, true);
    wp_enqueue_script('fluxor-scripts', get_template_directory_uri() . '/js/scripts.js','','', true );

  }

add_action( 'wp_enqueue_scripts', 'fluxor_scripts' );



/*  Enqueue style
/* ------------------------------------ */

function fluxor_styles() {

  // Load Custom fonts
  $font_headings = sanitize_text_field(get_theme_mod('fluxor_google_font', 'DM Sans'));
  $font_body = sanitize_text_field(get_theme_mod('fluxor_google_font_body', 'DM Sans'));
  $font_weight = sanitize_text_field(get_theme_mod('fluxor_google_font_weight', '300,400,700'));

  if ($font_headings === $font_body) {
      wp_enqueue_style('fluxor-google-font', esc_url('//fonts.googleapis.com/css?family=' . $font_headings . ':' . $font_weight));
  } else {
      wp_enqueue_style('fluxor-google-font', esc_url('//fonts.googleapis.com/css?family=' . $font_headings . ':' . $font_weight));
      wp_enqueue_style('fluxor-google-font-body', esc_url('//fonts.googleapis.com/css?family=' . $font_body . ':400,700'));
  }
  
  // Load CSS files
	wp_enqueue_style( 'boxicons-style', 'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css');
	wp_enqueue_style( 'lineawesome-style', 'https://maxst.icons8.com/vue-static/landings/line-awesome/font-awesome-line-awesome/css/all.min.css');
	wp_enqueue_style( 'splide-style', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css');
	wp_enqueue_style( 'woocommerce-style', get_template_directory_uri().'/css/woocommerce.css');
	wp_enqueue_style( 'simple-style', get_template_directory_uri().'/style.css');

}

add_action( 'wp_enqueue_scripts', 'fluxor_styles' );


/* Add custom CSS to the header */
function fluxor_customize_css() {
  $font_headings = str_replace('+', ' ', sanitize_text_field(get_theme_mod('fluxor_google_font', 'DM Sans')));
  $font_body = str_replace('+', ' ', sanitize_text_field(get_theme_mod('fluxor_google_font_body', 'DM Sans')));

  echo '<style type="text/css">';
  echo 'body, p, ul, li, ol { font-family: "' . esc_attr($font_body) . '", sans-serif; }';
  echo 'h1, h2, h3, h4, h5, h6 { font-family: "' . esc_attr($font_headings) . '", sans-serif; }';
  echo '</style>';
}
add_action('wp_head', 'fluxor_customize_css');



// Impedisce la creazione di nuovi utenti admin non autorizzati
add_action( 'user_register', 'blocca_nuovi_admin', 10, 1 );

function blocca_nuovi_admin( $user_id ) {
    $user = get_userdata( $user_id );

    if ( in_array( 'administrator', (array) $user->roles ) ) {
        // Degrada l'utente a subscriber
        $user->set_role( 'subscriber' );

        // Logga il tentativo (puoi controllare con "tail -f error_log")
        error_log( "⚠️ Tentativo bloccato: utente ID {$user_id} voleva ruolo ADMIN." );

        // Opzionale: invia notifica email all'admin
        wp_mail(
            get_option( 'admin_email' ),
            'Tentativo sospetto di creazione admin',
            "È stato registrato un utente con ID {$user_id} che tentava di ottenere ruolo ADMIN. È stato declassato a subscriber."
        );
    }

}


/* Include additional customizer functions */
if (file_exists(get_template_directory() . '/functions/customizer.php')) {
  require_once(get_template_directory() . '/functions/customizer.php');
}


?>


