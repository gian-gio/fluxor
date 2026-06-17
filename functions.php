<?php
/**
 * Theme: fluxor
 *
 * Theme Functions, includes, etc.
 *
 * @package fluxor
 */


/* Theme setup
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
    add_filter( 'big_image_size_threshold', function() { return 2560; } );
        add_filter('intermediate_image_sizes_advanced', function($sizes) {
        unset($sizes['medium_large']); 
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        return $sizes;
    });

    // Prevents lazy loading on the very first image on the page (usually the most important one)
    add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
        static $count = 0;
        if ( ! is_admin() && $count === 0 ) {
            $attr['loading'] = 'eager'; // Upload image
            $attr['fetchpriority'] = 'high'; // Top priority
        }
        $count++;
        return $attr;
    }, 10, 3 );

    // Decode images asynchronously
    add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
    if ( ! is_admin() ) {
        $attr['decoding'] = 'async';
    }
    return $attr;
    }, 10 );

    // Reduce compression quality to save space
    add_filter( 'jpeg_quality', function($quality) {
        return 75;
    });
    // Optionally resize oversized originals. Disabled by default to preserve source uploads.
    add_filter( 'wp_handle_upload', function( $upload ) {
        if ( empty( $upload['type'] ) || ! in_array( $upload['type'], array( 'image/jpeg', 'image/png' ), true ) ) {
            return $upload;
        }

        if ( ! apply_filters( 'fluxor_resize_original_upload', false ) ) {
            return $upload;
        }

        $image = wp_get_image_editor( $upload['file'] );
        if ( ! is_wp_error( $image ) ) {
            $size = $image->get_size();
            if ( $size['width'] > 2000 || $size['height'] > 2000 ) {
                $image->resize( 2000, 2000, false );
                $image->save( $upload['file'] );
            }
        }
        return $upload;
    });

    // Diciamo a WordPress di usare la funzione, ma la funzione la dichiariamo FUORI da qui.
    add_filter( 'wp_handle_upload_prefilter', 'fluxor_limit_upload_size' );

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

    // block pattern
    require_once( get_template_directory() . '/functions/patterns.php' );

}
add_action('after_setup_theme', 'fluxor_setup');


function fluxor_limit_upload_size( $file ) {
    $size = $file['size']; // Size in bytes
    $limit = 2 * 1024 * 1024; // 2 Megabyte
    $limit_text = '2MB';

    if ( $size > $limit ) {
        $file['error'] = sprintf( __( 'The file is too large! The maximum allowed size is %s. Optimize it before uploading.', 'fluxor' ), $limit_text );
    }

    return $file;
}



/* Add JSON-LD Structured Data to the home page.
---------------------------------------------------------------------------------------- */
function schema_json_ld_fluxor() {
    if ( ! is_front_page() ) {
        return;
    }

    $site_url    = home_url( '/' );
    $description = get_bloginfo( 'description' );
    $logo_id     = get_theme_mod( 'custom_logo' );
    $logo_url    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
    $phone       = get_option( 'whatsapp_phone_number', '' );

    $payload = array_filter(
        array(
            '@context'    => 'https://schema.org',
            '@type'       => 'ProfessionalService',
            'name'        => get_bloginfo( 'name' ),
            'image'       => $logo_url,
            '@id'         => $site_url,
            'url'         => $site_url,
            'telephone'   => $phone,
            'priceRange'  => 'EUR',
            'description' => $description,
        )
    );

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'schema_json_ld_fluxor' );



/* WooCommerce support
------------------------------------------------------------------------ */
add_theme_support( 'woocommerce' );

function fluxor_woocommerce_cart_menu_item( $items, $args ) {
    if ( empty( $args->theme_location ) || 'quickmenu' !== $args->theme_location || ! class_exists( 'WooCommerce' ) ) {
        return $items;
    }

    $account_url = wc_get_page_permalink( 'myaccount' );
    $items      .= '<li class="account-item"><a href="' . esc_url( $account_url ) . '" aria-label="' . esc_attr__( 'My account', 'fluxor' ) . '"><i class="bx bxs-user" aria-hidden="true"></i></a></li>';

    if ( get_theme_mod( 'fluxor_show_cart', true ) && WC()->cart ) {
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_url   = wc_get_cart_url();

        $items .= '<li class="cart-item"><a href="' . esc_url( $cart_url ) . '" aria-label="' . esc_attr__( 'Cart', 'fluxor' ) . '"><i class="bx bx-cart-alt" aria-hidden="true"></i><span class="cart-count">' . esc_html( $cart_count ) . '</span></a></li>';
    }

    return $items;
}
add_filter( 'wp_nav_menu_items', 'fluxor_woocommerce_cart_menu_item', 10, 2 );



function fluxor_customize_register($wp_customize) {
  // Add a WooCommerce section
  $wp_customize->add_section('fluxor_woocommerce_section', array(
      'title'    => __('WooCommerce Settings', 'fluxor'),
      'priority' => 30,
  ));

  // Add an option to show/hide the cart in the menu
  $wp_customize->add_setting('fluxor_show_cart', array(
      'default'   => true,
      'transport' => 'refresh',
      'sanitize_callback' => 'fluxor_sanitize_checkbox'
  ));

  $wp_customize->add_control('fluxor_show_cart', array(
      'type'     => 'checkbox',
      'section'  => 'fluxor_woocommerce_section',
      'label'    => __('Show cart icon in menu', 'fluxor'),
  ));
}

add_action('customize_register', 'fluxor_customize_register');

// Sanification 
function fluxor_sanitize_checkbox($checked) {
  return (bool) $checked;
}

// Prevent WooCommerce from trying to force custom template alignment
add_filter( 'woocommerce_defer_template_part_sync', '__return_true' );



// Set the number of products per page in the shop

add_filter( 'loop_shop_per_page', 'fluxor_products_per_page', 20 );

function fluxor_products_per_page( $cols ) {
    // Set products per page
    return 12;
}


/* Load translation
---------------------------------------------------------------------------------- */
function fluxor_load_textdomain() {
  load_theme_textdomain('fluxor', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'fluxor_load_textdomain');


/* Custom Sidebar
----------------------------------------------------------------------------------- */

function fluxor_custom_sidebar() {
  register_sidebar( array(
      'name'          => 'Custom Sidebar',
      'id'            => 'custom-sidebar',
      'before_widget' => '<div class="widget">',
      'after_widget'  => '</div>',
      'before_title'  => '<h3 class="widget-title">',
      'after_title'   => '</h3>',
  ) );
}
add_action( 'widgets_init', 'fluxor_custom_sidebar' );


/* Custom Logo 
--------------------------------------------------------------------------------- */

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
        'default'           => '', // No logo default
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
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



 /* Login page customizations (dynamic logo + styling)
 ----------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'fluxor_custom_login_style' ) ) {
    function fluxor_custom_login_style() {
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_url = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : '';

        $color_primary = '#0073aa';
        $color_default = '#1a1a1a';
        $color_background = '#EEEEEE';
        ?>
        <style type="text/css">
            body.login {
                background: <?php echo esc_html($color_background); ?>;
                font-family: 'Inter', 'Roboto', sans-serif;
            }

            body.login div#login h1 a {
                <?php if ( $logo_url ) : ?>
                background-image: url('<?php echo esc_url( $logo_url ); ?>');
                <?php endif; ?>
                background-size: contain;
                background-repeat: no-repeat;
                width: 220px;
                height: 110px;
                padding-bottom: 10px;
            }

            body.login form {
                background: #fff;
                border: 1px solid #DDD;
                box-shadow: none;
            }

            body.login form input[type=text],body.login form input[type=password]{
                border:1px solid #DDD;
                border-radius:0px;
                height:40px;
            }

            body.login form select{
                border:1px solid #DDD;
                border-radius:0px;
                height:40px;
            }

            body.login .language-switcher input[type=submit]{
                border:0px;
                border-radius:0px;
                height:40px;
            }

            body.login form input[type=submit]{
                border:0px;
                border-radius:0px;
                height:30px;
                padding:0 18px!important;
            }
        </style>
        <?php
    }
    add_action( 'login_enqueue_scripts', 'fluxor_custom_login_style' );
}

if ( ! function_exists( 'fluxor_login_logo_url' ) ) {
    function fluxor_login_logo_url() {
        return home_url('/');
    }
    add_filter( 'login_headerurl', 'fluxor_login_logo_url' );
}

if ( ! function_exists( 'fluxor_login_logo_title' ) ) {
    function fluxor_login_logo_title() {
        return get_bloginfo('name');
    }
    // Replaces deprecated login_headertitle with login_headertext.
    add_filter( 'login_headertext', 'fluxor_login_logo_title' );
}




/* Enqueue javascript
/* ------------------------------------------------------------------------------ */

  function fluxor_scripts() {
    $theme_version = wp_get_theme()->get( 'Version' );

    wp_enqueue_script( 'fluxor-gsap-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', array(), '3.12.5', true );
    wp_enqueue_script( 'fluxor-gsap-scrolltrigger-js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', array( 'fluxor-gsap-js' ), '3.12.5', true );
    wp_enqueue_script( 'fluxor-splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', array(), '4.1.4', true );
    wp_enqueue_script( 'fluxor-scripts', get_template_directory_uri() . '/js/scripts.js', array( 'fluxor-gsap-js', 'fluxor-gsap-scrolltrigger-js', 'fluxor-splide-js' ), $theme_version, true );
}
add_action( 'wp_enqueue_scripts', 'fluxor_scripts' );



/* Enqueue style
/* ------------------------------------------------------------------------------ */

function fluxor_styles() {

  // Load Custom fonts
  $font_headings = sanitize_text_field( get_theme_mod( 'fluxor_google_font', 'DM Sans' ) );
  $font_body     = sanitize_text_field( get_theme_mod( 'fluxor_google_font_body', 'DM Sans' ) );
  $font_weight   = sanitize_text_field( get_theme_mod( 'fluxor_google_font_weight', '300,400,700' ) );

  $font_headings_query = str_replace( ' ', '+', $font_headings );
  $font_body_query     = str_replace( ' ', '+', $font_body );

  wp_enqueue_style(
      'fluxor-google-font',
      esc_url( add_query_arg( 'family', $font_headings_query . ':' . $font_weight, 'https://fonts.googleapis.com/css' ) ),
      array(),
      null
  );

  if ( $font_headings !== $font_body ) {
      wp_enqueue_style(
          'fluxor-google-font-body',
          esc_url( add_query_arg( 'family', $font_body_query . ':400,700', 'https://fonts.googleapis.com/css' ) ),
          array(),
          null
      );
  }

  // Load CSS files
    wp_enqueue_style( 'boxicons-style', 'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css', array(), '2.1.4' );
    wp_enqueue_style( 'lineawesome-style', 'https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css', array(), '1.3.0' );
    wp_enqueue_style( 'fluxor-splide-style', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css', array(), '4.1.4' );
    wp_enqueue_style( 'woocommerce-style', get_template_directory_uri() . '/css/woocommerce.css', array(), wp_get_theme()->get( 'Version' ) );
    wp_enqueue_style( 'simple-style', get_template_directory_uri() . '/style.css', array(), wp_get_theme()->get( 'Version' ) );
    wp_enqueue_style( 'custom-style', get_template_directory_uri() . '/css/custom_style.css', array( 'simple-style' ), wp_get_theme()->get( 'Version' ) );

}

add_action( 'wp_enqueue_scripts', 'fluxor_styles' );


/* Add custom CSS to the header 
------------------------------------------------------------------------------------------------------ */

function fluxor_customize_css() {
  $font_headings = str_replace('+', ' ', sanitize_text_field(get_theme_mod('fluxor_google_font', 'DM Sans')));
  $font_body = str_replace('+', ' ', sanitize_text_field(get_theme_mod('fluxor_google_font_body', 'DM Sans')));

  echo '<style type="text/css">';
  echo 'body, p, ul, li, ol { font-family: "' . esc_attr($font_body) . '", sans-serif; }';
  echo 'h1, h2, h3, h4, h5, h6 { font-family: "' . esc_attr($font_headings) . '", sans-serif; }';
  echo '</style>';
}
add_action('wp_head', 'fluxor_customize_css');



/* Prevents the creation of new unauthorized admin users
-------------------------------------------------------------------------------------------------------- */

add_action( 'user_register', 'fluxor_block_new_admin_users', 10, 1 );

function fluxor_block_new_admin_users( $user_id ) {
    $user = get_userdata( $user_id );

    if ( in_array( 'administrator', (array) $user->roles ) ) {
        // Demote the user to subscriber
        $user->set_role( 'subscriber' );

        // Log the attempt (you can check with "tail -f error_log")
        error_log( "Tentativo bloccato: utente ID {$user_id} voleva ruolo ADMIN." );

        // Optional: Send email notification to admin
        wp_mail(
            get_option( 'admin_email' ),
            'Tentativo sospetto di creazione admin',
            "E stato registrato un utente con ID {$user_id} che tentava di ottenere ruolo ADMIN. E stato declassato a subscriber."
        );
    }

}



 /* EXTRA PERFORMANCE OPTIMIZATION
    Remove unnecessary scripts (Emoji) and optimize font loading
 --------------------------------------------------------------------------------------------------------- */

function fluxor_extra_optimization() {
    // Removes emojis that load JS and CSS on every page
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    // Removes the Shortlink link (useless for SEO and slows down the head)
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    
    // Remove WordPress version for security and cleanup
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'fluxor_extra_optimization');

/* Loading Fonts with "Swap" to avoid invisible text during loading
 -------------------------------------------------------------------------------------------------------- */

add_filter( 'style_loader_tag', 'fluxor_add_font_display_swap', 10, 2 );
function fluxor_add_font_display_swap( $tag, $handle ) {
    if ( 'fluxor-google-font' !== $handle && 'fluxor-google-font-body' !== $handle ) {
        return $tag;
    }

    return preg_replace_callback(
        "/href=['\"]([^'\"]+)['\"]/",
        function( $matches ) {
            $url = add_query_arg( 'display', 'swap', html_entity_decode( $matches[1] ) );
            return 'href="' . esc_url( $url ) . '"';
        },
        $tag
    );
}



/* Includes additional customizer functions
---------------------------------------------------------------------------------------------------------- */

if (file_exists(get_template_directory() . '/functions/customizer.php')) {
  require_once(get_template_directory() . '/functions/customizer.php');
}