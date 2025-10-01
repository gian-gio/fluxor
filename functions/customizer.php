<?php
  
  
/* Custom fonts
---------------------------------------- */

function fluxor_customize_fonts($wp_customize) {
    /* Typography Section */
    $wp_customize->add_section('fluxor_typography', array(
        'title'    => __('Typography', 'fluxor'),
        'priority' => 30,
    ));

    /* Font Name */
    $wp_customize->add_setting('fluxor_google_font', array(
        'default'   => 'DM Sans',
        'transport' => 'refresh',
        'sanitize_callback' => 'fluxor_sanitize_callback_function',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fluxor_google_font_control', array(
        'label'    => __('Google Font Headings (ex. Open Sans )', 'fluxor'),
        'section'  => 'fluxor_typography',
        'settings' => 'fluxor_google_font',
        'type'     => 'text',
    )));

    /* Font Weights */
    $wp_customize->add_setting('fluxor_google_font_weight', array(
        'default'   => '300,400,700',
        'transport' => 'refresh',
        'sanitize_callback' => 'fluxor_sanitize_callback_function',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fluxor_google_font_weight_control', array(
        'label'    => __('Font Weight (ex. 300,400,700 )', 'fluxor'),
        'section'  => 'fluxor_typography',
        'settings' => 'fluxor_google_font_weight',
        'type'     => 'text',
    )));

    /* Font Body Name */
    $wp_customize->add_setting('fluxor_google_font_body', array(
        'default'   => 'DM Sans',
        'transport' => 'refresh',
        'sanitize_callback' => 'fluxor_sanitize_callback_function',
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'fluxor_google_font_body_control', array(
        'label'    => __('Google Font Body (ex. Open Sans )', 'fluxor'),
        'section'  => 'fluxor_typography',
        'settings' => 'fluxor_google_font_body',
        'type'     => 'text',
    )));

    function fluxor_sanitize_callback_function($input) {
        return sanitize_text_field($input);
    }
}
add_action('customize_register', 'fluxor_customize_fonts');

  

/*Remove section "Header image"
  -------------------------------------------- */ 
  function fluxor_remove_customizer_sections($wp_customize) {
      
      $wp_customize->remove_section('header_image');
  }
  add_action('customize_register', 'fluxor_remove_customizer_sections', 20);



  /* Whatsapp Button
-----------------------------------------*/

function fluxor_customize_whatsapp_register($wp_customize) {
    // Aggiungi una sezione al Customizer
    $wp_customize->add_section('fluxor_whatsapp_section', array(
        'title'       => __('WhatsApp Settings', 'fluxor'),
        'description' => __('Configure the WhatsApp button settings.', 'fluxor'),
        'priority'    => 160,
    ));

    // Aggiungi un'impostazione per il numero di telefono
    $wp_customize->add_setting('whatsapp_phone_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'type'              => 'option',
        'capability'        => 'edit_theme_options',
    ));

    // Aggiungi un controllo per il numero di telefono
    $wp_customize->add_control('whatsapp_phone_number_control', array(
        'label'       => __('WhatsApp Phone Number', 'fluxor'),
        'section'     => 'fluxor_whatsapp_section',
        'settings'    => 'whatsapp_phone_number',
        'type'        => 'text',
        'description' => __('Enter your WhatsApp phone number (ex: 3912345678).', 'fluxor'),
    ));
}
add_action('customize_register', 'fluxor_customize_whatsapp_register');





/*  Latest Posts shortcode
/* ------------------------------------ */


function fluxor_last_post_shortcode($atts) {

    $args = array(
        'post_type' => 'post', 
        'posts_per_page' => 3, 
        'orderby' => 'date', 
        'order' => 'DESC' 
    );
  
    // Esegui la query
    $the_query = new WP_Query($args);
  
    // Inizializza una variabile per l'output
    $output = '';
  
    // Loop
    if ($the_query->have_posts()) {
        $output .= '<div class="latest-post">';
        while ($the_query->have_posts()) {
            $the_query->the_post();
            
            $output .= '<div class="col-xl-33 col-md-33 col-sm-100">';
            
            // Controlla se il post ha un'immagine in evidenza
            if (has_post_thumbnail()) {
                // Ottiene l'URL dell'immagine in evidenza
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'large', true);
                
                $output .= '<a href="' . get_the_permalink() . '">';
                $output .= '<img src="' . esc_url($thumbnail_url[0]) . '" alt="' . the_title_attribute(array('echo' => false)) . '"></a>';
            }
            
            $output .='<p>'. get_the_time('j M Y ') .'- '. get_the_category_list(', ') .'</p>';
            $output .= '<h3><a href="' . get_the_permalink() . '">' . wp_trim_words(get_the_title(), 15, '...') . '</a></h3>';
                      
            $output .= '</div>';
        }
        $output .= '</div>';
        
        // Reset original posts
        wp_reset_postdata();
    } else {
        // No find article 
        $output .= '<div class="last-post"><p>Nessun articolo trovato.</p></div>';
    }
  
    return $output;
  }
  
  add_shortcode('latest_posts', 'fluxor_last_post_shortcode');
  


  


/* Page Reserverd
-----------------------------------------*/

//Admin area block for user simple
function fluxor_admin_page_block() {
    if (is_admin() && !current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
        wp_redirect(home_url()); // Redirect to homepage
        exit;
    }
}
add_action('admin_init', 'fluxor_admin_page_block');




/* Auto install plugin 
-----------------------------------------*/
  
  require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';
  
  
  
  function fluxor_register_required_plugins() {
      $plugins = array(
          // Plugins list
          array(
              'name'      => 'Contact Form 7', 
              'slug'      => 'contact-form-7', 
              'required'  => true, 
          ),
          array(
              'name'      => 'Block Guide Lines', 
              'slug'      => 'block-guide-lines', 
              'required'  => true, 
          ),
          array(
              'name'      => 'GenerateBlocks', 
              'slug'      => 'generateblocks', 
              'required'  => true, 
          ),
          array(
              'name'      => 'Yoast Duplicate Post', 
              'slug'      => 'duplicate-post', 
              'required'  => true, 
          ),
          array(
              'name'      => 'Loco Translate', 
              'slug'      => 'loco-translate', 
              'required'  => true, 
          ),
          array(
              'name'      => 'WooCommerce', 
              'slug'      => 'woocommerce', 
              'required'  => true, 
          ),
          array(
              'name'      => 'Yith WooCommerce Product Slider', 
              'slug'      => 'yith-woocommerce-product-slider-carousel', 
              'required'  => true, 
          ),
      );
  
      $config = array(
          'id'           => 'fluxor', // Theme ID
          'default_path' => '', // Leave blank to avoid conflicts
          'menu'         => 'Install plugins', // Admin menu name
          'parent_slug'  => 'themes.php', // Parent menu slug
          'capability'   => 'edit_theme_options', // Permissions required
          'has_notices'  => true, // Show notifications in backend
          'dismissable'  => true, // Allows you to hide the warning
          'is_automatic' => false, // Does not automatically install plugins
      );
  
      tgmpa( $plugins, $config );
  }

  add_action( 'tgmpa_register', 'fluxor_register_required_plugins' );















