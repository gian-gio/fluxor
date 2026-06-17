<footer class="footer">
  <div class="grid--xl">
    <div class="col-xl-50">
      <div class="flex-start">

          <div class="footer__logo">
            <?php
                $footer_logo = get_theme_mod( 'footer_logo' );
                if ( $footer_logo ) {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                    echo '<img src="' . esc_url( $footer_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                    echo '</a>';
                }
                ?>
              </div>

          <div>
              <?php
              $footer_company = get_theme_mod( 'fluxor_footer_company', 'Company Name' );
              $footer_address = get_theme_mod( 'fluxor_footer_address', 'Address: address test, 1 ITALY' );
              $footer_email   = get_theme_mod( 'fluxor_footer_email', 'mail@mysite.com' );
              $footer_phone   = get_theme_mod( 'fluxor_footer_phone', '+39 123 45 67' );
              $footer_tel     = preg_replace( '/[^0-9+]/', '', $footer_phone );
              ?>

              <?php if ( $footer_company ) : ?>
                <h5><?php echo esc_html( $footer_company ); ?></h5>
              <?php endif; ?>

              <?php if ( $footer_address ) : ?>
                <?php echo wp_kses_post( nl2br( $footer_address ) ); ?><br>
              <?php endif; ?>

              <?php if ( $footer_email ) : ?>
                <?php esc_html_e( 'Email:', 'fluxor' ); ?>
                <a href="mailto:<?php echo esc_attr( antispambot( $footer_email ) ); ?>">
                  <?php echo esc_html( antispambot( $footer_email ) ); ?>
                </a><br>
              <?php endif; ?>

              <?php if ( $footer_phone ) : ?>
                <?php esc_html_e( 'Tel:', 'fluxor' ); ?>
                <a href="tel:<?php echo esc_attr( $footer_tel ); ?>">
                  <?php echo esc_html( $footer_phone ); ?>
                </a>
              <?php endif; ?>
          </div>

      </div>

    </div>
    <div class="col-xl-50 text-right sm-hide">
      <?php
        if ( has_nav_menu( 'footermenu' ) ) {
            echo '<nav aria-label="' . esc_attr__( 'Footer Navigation', 'fluxor' ) . '">';
            wp_nav_menu(array(
                'theme_location' => 'footermenu',
                'container' => false,
                'items_wrap' => '<ul class="footer__menu">%3$s</ul>'
            ));
            echo '</nav>';
        }
        ?>
    </div>
  </div>
  <div class="footer__copyright">
        <p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> - <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
  </div>
</footer>

<div class="back-top">
  <a href="#" aria-label="<?php esc_attr_e( 'Back to top', 'fluxor' ); ?>"></a>
</div>


<!-- Whatsapp button
--------------------------------------------------->
<?php
$whatsapp_number = get_option( 'whatsapp_phone_number', '' );

if ( ! empty( $whatsapp_number ) ) :
    $whatsapp_url = 'https://wa.me/' . rawurlencode( preg_replace( '/[^0-9]/', '', $whatsapp_number ) );
    $btn_class = 'btn-whatsapp-visible';
?>
    <a href="<?php echo esc_url( $whatsapp_url ); ?>"
       class="btn-whatsapp <?php echo esc_attr( $btn_class ); ?>"
       target="_blank"
       rel="noopener noreferrer"
       title="<?php esc_attr_e( 'Chat with us on WhatsApp', 'fluxor' ); ?>">
        <i class='bx bxl-whatsapp' aria-hidden="true"></i>
        <span class="screen-reader-text"><?php esc_html_e( 'Chat with us on WhatsApp', 'fluxor' ); ?></span>
    </a>
<?php endif; ?>



<?php wp_footer(); ?>

</body>
</html>