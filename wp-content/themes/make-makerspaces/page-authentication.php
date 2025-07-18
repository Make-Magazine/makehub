<?php
/**
 * Template Name: Authenticated Redirect page
 */
?>
<?php get_header(); ?>
<div id="authenticated-redirect" class="container page-content">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="text-center">Please wait while we complete the login process.</h2>
      <h3 class="text-center redirect-message">You will be redirected to the page you were trying to access shortly.</h3>
      <h3 class="text-center billboard">Please wait while we get you back to the right place.</h3>
      <div class="container">
			<div class="row">
				<div class="col-md-3 hidden-sm hidden-xs"></div>
				<div class="col-md-6 col-sm-12 col-xs-12">
					<div class="progress">
						<div class="progress-bar" role="progressbar" style="width: 10%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">10%</div>
					</div>
				</div>
				<div class="col-xs-3 hidden-sm hidden-xs"></div>
			</div>
		</div>
		<iframe
         src="https://make.co/third-party-check.html"
         height="200px"
         width="100%" style="border:none;">
      </iframe>
    </div>
  </div>
</div><!-- end .page-content -->
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <?php // theloop
        if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <?php the_content(); ?>
          <?php endwhile; ?>
        <?php else: ?>
          <?php get_404_template(); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
<script type="text/javascript">
jQuery("a:not(.reload)").click(function() {
	 jQuery( "#dialog" ).dialog();
    return false;
});	
</script>
<div id="dialog" title="We’re still logging you in." style="display:none;">
  <p>Clicking around right now might cause a short circuit. We’ll redirect you as soon as this process finishes.</p>
</div>
