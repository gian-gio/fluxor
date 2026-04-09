  <?php 
  
      /*
    Template Name: No Head Page
    */

  
  get_header(); // insert header.php inclusion  ?>



  <?php if (have_posts()) :?><?php while(have_posts()) : the_post(); // start of the loop ?>

    <!-- loop content -->

  <div class="main" id="primary-content">

    <?php 
    /* Image Url */
    $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
    $image_url = $image_attributes ? $image_attributes[0] : ''; // fallback a stringa vuota
    //?>




  <div class="grid--xl">
    <div class="col-xl-100">

      <?php the_content(); ?>

    </div>
  </div>

      



  <?php endwhile; else : // if no result dispaly message ?>

    <div class="grid--xl">
      <div class="col-100">

          <p><?php esc_html_e('Sorry, no posts matched your criteria.', 'fluxor'); // dispaly no result message ?></p>

      </div>
    </div>
    
  <?php endif; ?>


</div>

<?php get_footer(); // insert footer.php inclusion  ?>
