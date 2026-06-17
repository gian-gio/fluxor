<!DOCTYPE html>
<html <?php language_attributes(); // display the html language tag ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>"> 
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php wp_head(); // insert all the script and styles of WordPress ?>

</head>
<body <?php body_class(); // add automic css classes based on the page ?> >

  <?php wp_body_open(); // insert script right after the body if needed ?>

<a class="skip-link screen-reader-text" href="#primary-content">
    <?php esc_html_e( 'Skip to content', 'fluxor' ); ?>
</a>
  

<div class="search-panel" aria-hidden="true">

  <div class="container-search">

    <div class="search-form">
      <h3><?php esc_attr_e('Search in the site', 'fluxor'); ?></h3>
      <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="form-search">
        <input type="text" placeholder="<?php esc_attr_e('search...', 'fluxor'); ?>" name="s" id="search-input">
        <button type="submit" aria-label="<?php esc_attr_e('Run search', 'fluxor'); ?>">
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/search-outline.svg" alt=""> 
        </button>
      </form>
    </div>
    
    <a href="#" class="btn-close-search" role="button" aria-label="<?php esc_attr_e( 'Close search', 'fluxor' ); ?>"></a>
    
  </div>

</div>


  <!--Top Bar-->
  <?php 
  $topbar_text = get_theme_mod( 'fluxor_topbar_text', '' ); 
  $topbar_bg   = get_theme_mod( 'fluxor_topbar_bg_color', '#000000' );
  $topbar_text_color   = get_theme_mod( 'fluxor_topbar_text_color', '#000000' );

  if ( ! empty( $topbar_text ) ) : ?> 
      <div class="top-bar" style="background-color: <?php echo esc_attr( $topbar_bg );?>; color: <?php echo esc_attr( $topbar_text_color ); ?>">
          <div class="container">
              <?php echo wp_kses_post( $topbar_text ); ?>
          </div>
      </div>
  <?php endif; ?>

  <header class="container-header">

        <div class="header">

        <?php
          $custom_logo_id = get_theme_mod('custom_logo'); // Ottiene l'ID del logo

          if (function_exists('the_custom_logo') && has_custom_logo()) {

          $logo_url = wp_get_attachment_image_src($custom_logo_id, 'full')[0]; 

              echo '<a class="header__logo" href="' . esc_url(home_url('/')) . '">';
              echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
              echo '</a>';
          } else {

          echo '<a class="header__logo" href="' . esc_url(home_url('/')) . '">';
              echo '<span>' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
              echo '</a>';
          }
        ?>



        <?php // insert custom menu header
          wp_nav_menu(array(
            'theme_location' => 'header',
            'container' => false, 
            'items_wrap' => '<ul class="header__menu">%3$s</ul>'
          ));
        ?>


        <?php
        if (has_nav_menu('quickmenu')) {
            wp_nav_menu(array(
              'theme_location' => 'quickmenu',
              'container' => false, 
              'items_wrap' => '<ul class="header__quick">%3$s</ul>'
              ));
        }
        ?>


            <button type="button" class="header__hamburger" aria-label="<?php esc_attr_e( 'Open menu', 'fluxor' ); ?>" aria-expanded="false"><span></span><span></span></button>
        </div>

        
    </header>

