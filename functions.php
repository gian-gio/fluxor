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
    add_filter('big_image_size_threshold', function() { return 2560; });
    add_filter('intermediate_image_sizes_advanced', function($sizes) {
        unset($sizes['medium_large']); 
        unset($sizes['1536x1536']);
        unset($sizes['2048x2048']);
        return $sizes;
    });

    // Impedisce il lazy load sulla primissima immagine della pagina (solitamente la più importante)
    add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
        static $count = 0;
        if ( ! is_admin() && $count === 0 ) {
            $attr['loading'] = 'eager'; // Carica immediatamente
            $attr['fetchpriority'] = 'high'; // Priorità massima
        }
        $count++;
        return $attr;
    }, 10, 3 );

    //Decodifica immagini in modo asincrono
    add_filter( 'wp_get_attachment_image_attributes', function( $attr ) {
    if ( ! is_admin() ) {
        $attr['decoding'] = 'async';
    }
    return $attr;
    }, 10 );

    /**
     * Limita il peso massimo dei file caricati (es. 2MB)
     */
    add_filter( 'wp_handle_upload_prefilter', 'fluxor_limit_upload_size' );
    function fluxor_limit_upload_size( $file ) {
        $size = $file['size']; // Dimensione in byte
        $limit = 2 * 1024 * 1024; // 2 Megabyte
        $limit_text = '2MB';

        if ( $size > $limit ) {
            $file['error'] = "The file is too large! The maximum allowed size is $limit_text. Optimize it before uploading.";
        }

        return $file;
    }

    // Forza la soglia massima a 2560px (standard 4K)
    add_filter( 'big_image_size_threshold', function() {
        return 2560; 
    });

    /**
     * Riduce la qualità di compressione per risparmiare spazio
     */
    add_filter( 'jpeg_quality', function($quality) {
        return 75;
    });

    /**
     * Forza il ridimensionamento fisico dell'originale a 2000px.
     * Questo cancella il file gigante e tiene solo una versione ragionevole.
     */
    add_filter( 'wp_handle_upload', function( $upload ) {
        if ( $upload['type'] != 'image/jpeg' && $upload['type'] != 'image/png' ) return $upload;

        $image = wp_get_image_editor( $upload['file'] );
        if ( ! is_wp_error( $image ) ) {
            $size = $image->get_size();
            if ( $size['width'] > 2000 || $size['height'] > 2000 ) {
                $image->resize( 2000, 2000, false );
                $image->save( $upload['file'] ); // Sovrascrive l'originale!
            }
        }
        return $upload;
    });

    /**
     * Aggiunge lazy load ai banner di WP Bannerize Pro
     */
    add_filter( 'wp_bannerize_pro_banner_html', function( $html ) {
        // Se il banner non è il primissimo in alto, aggiungiamo lazy load
        if ( strpos( $html, 'loading=' ) === false ) {
            $html = str_replace( '<img ', '<img loading="lazy" ', $html );
        }
        return $html;
    });


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



/**
 * Aggiunge i Dati Strutturati JSON-LD alla Home Page per migliorare la SEO Locale
 */
function schema_json_ld_fluxor() {
    if ( is_front_page() ) {
        $payload = array(
            "@context" => "https://schema.org",
            "@type" => "ProfessionalService",
            "name" => "Gianluca Giuliano - Web Design & Development",
            "image" => "https://gianlucagiuliano.it/wp-content/uploads/2025/02/avatar.webp",
            "@id" => "https://gianlucagiuliano.it",
            "url" => "https://gianlucagiuliano.it",
            "telephone" => "",
            "priceRange" => "€€",
            "address" => array(
                "@type" => "PostalAddress",
                "streetAddress" => " ", 
                "addressLocality" => "Marano di Napoli",
                "addressRegion" => "NA",
                "postalCode" => "80016",
                "addressCountry" => "IT"
            ),
            "geo" => array(
                "@type" => "GeoCoordinates",
                "latitude" => 40.9022,
                "longitude" => 14.1925
            ),
            "openingHoursSpecification" => array(
                "@type" => "OpeningHoursSpecification",
                "dayOfWeek" => array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday"),
                "opens" => "09:00",
                "closes" => "19:00"
            ),
            "sameAs" => array(
                "https://www.linkedin.com/in/gianluca-giuliano", 
                "https://www.instagram.com/gianlucagiuliano_/",
				"https://codepen.io/gianluca-giuliano"
            ),
            "description" => "Esperto Web Designer e Sviluppatore WordPress Custom a Napoli. Oltre 15 anni di esperienza e 200+ progetti realizzati con focus su performance e UX."
        );

        echo "\n\n";
        echo '<script type="application/ld+json">' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
}
add_action('wp_head', 'schema_json_ld_fluxor');



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
      'before_title'  => '<h3 class="widget-title">',
      'after_title'   => '</h3>',
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


/**
 * Personalizzazioni pagina login (logo dinamico + styling)
 */
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
    add_filter( 'login_headertitle', 'fluxor_login_logo_title' );
}




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
	wp_enqueue_style( 'lineawesome-style', 'https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css');
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


/**
 * OTTIMIZZAZIONE PERFORMANCE EXTRA
 * Rimuove script inutili (Emoji) e ottimizza il caricamento dei font
 */
function fluxor_extra_optimization() {
    // Rimuove le emoji che caricano JS e CSS su ogni pagina
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    // Rimuove il link agli Shortlink (inutile per la SEO e rallenta l'head)
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    
    // Rimuove la versione di WordPress per sicurezza e pulizia
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'fluxor_extra_optimization');

/**
 * Caricamento dei Font con "Swap" per evitare testi invisibili durante il caricamento
 */
add_filter('style_loader_tag', 'fluxor_add_font_display_swap', 10, 2);
function fluxor_add_font_display_swap($tag, $handle) {
    if ('fluxor-google-font' === $handle || 'fluxor-google-font-body' === $handle) {
        return str_replace('href', 'display=swap&href', $tag);
    }
    return $tag;
}



/* Include additional customizer functions */
if (file_exists(get_template_directory() . '/functions/customizer.php')) {
  require_once(get_template_directory() . '/functions/customizer.php');
}


