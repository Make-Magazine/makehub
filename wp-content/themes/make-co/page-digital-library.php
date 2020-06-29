<?php
/**
 * Template Name: Digital-Library pages
 */

get_header(); ?>

<div id="page-content" class="mmBack bluetoad">

        <?php // theloop
        if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();  ?>
          <?php 
          /*  $results =  $vendor->addSubscriber($email, 494256);
            $results =  $vendor->addSubscriber($email, 478666);
            $results =  $vendor->addSubscriber($email, 466306);
            $results =  $vendor->addSubscriber($email, 449478);
            $results =  $vendor->addSubscriber($email, 478666);
            $results =  $vendor->addSubscriber($email, 434632);
            $results =  $vendor->addSubscriber($email, 369632);
            $results =  $vendor->addSubscriber($email, 355660);
            $results =  $vendor->addSubscriber($email, 333989);
            $results =  $vendor->addSubscriber($email, 317354);
            $results =  $vendor->addSubscriber($email, 285363);
            $results =  $vendor->addSubscriber($email, 301846);
            $results =  $vendor->addSubscriber($email, 301454);
            $results =  $vendor->addSubscriber($email, 301840);  
          
          $vendor = new PdfVendor();
          $email = 'webmaster@makermedia.com';
          $results = $vendor->showListOfPdfs($email); */
				
          ?>
			<iframe id="bluetoad-iframe" src="/wp-content/themes/make-co/blue-toad-login.php"></iframe>
          <?php the_content(); ?>
         <?php } // END WHILE ?>
        <?php } else { ?>
          <?php get_404_template(); ?>
        <?php } // END IF ?>

 </div><!-- end .page-content -->

<script type="text/javascript">
jQuery(document).ready(function(){
   var is_safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
   var is_iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
   if (is_safari || is_iOS) {
      window.location = "/wp-content/themes/make-co/blue-toad-login.php";
   }
   jQuery( ".page-template-page-digital-library footer" ).mouseleave( function() {
		jQuery( [document.documentElement, document.body] ).animate({
			scrollTop: jQuery( "#page-content.bluetoad iframe" ).offset().top
		}, 100);
	});
});
</script>

<?php
genesis_structural_wrap( 'site-inner', 'close' );
genesis_markup(
	array(
		'close'   => '</div>',
		'context' => 'site-inner',
	)
);
wp_footer();
?>