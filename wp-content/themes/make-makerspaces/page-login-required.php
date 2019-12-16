<?php
/**
 * Template Name: Login Required
 */

if (!is_user_logged_in())
    auth_redirect();

get_header(); ?>
  <!-- probably not necessary now?
  <div id="loadingOverlay" class="loadingOverlay">
	 <div class="text-center">
		<h2>Please wait while we log you in</h2>
		<br />
		<div class="universal-loader"></div>
	 </div>
  </div>
  -->

  <div class="container">

    <div class="row">

      <div class="col-xs-12">

      	<?php 
			if (have_posts()) {
				 while (have_posts()) {
					  the_post();
					  ?>
			  <div class="container">
					<?php the_content(); ?>
			  </div> 
				 <?php 
				  } // end while
			} // end if
			?>

      </div>

    </div>

  </div>

<?php get_footer(); ?>
