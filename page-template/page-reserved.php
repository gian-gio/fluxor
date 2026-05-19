<?php
/*
Template Name: Page Reserved
*/

if (!is_user_logged_in()) {
    wp_redirect(site_url('/login/?redirect_to=' . urlencode(get_permalink()))); 
}


$current_user = wp_get_current_user(); 

$nome = get_user_meta($current_user->ID, 'first_name', true); 
$cognome = get_user_meta($current_user->ID, 'last_name', true); 

if (!$nome) $nome = $current_user->user_login; 

get_header();
?>

    <div class="reserved-page" id="primary-content">
        <div class="reserved-page__header">
            <h4>
                <?php 
                printf(
                    esc_html__( 'Hi, %s', 'fluxor' ),
                    '<strong>' . esc_html( trim( $nome . ' ' . $cognome ) ) . '</strong>'
                ); 
                ?>
            </h4>
            <a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a>
        </div>

        <div class="grid--xl">
            <?php the_content(); ?>
        </div>

    </div>




<?php get_footer(); ?>
