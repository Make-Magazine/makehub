<?php
/**
 * Template Name: Crew Members
 */
get_header(); ?>

<div class="container page-content page-crew">

  <div class="row">

    <div class="col-xs-12">

      <h1 class="page-title"><?php the_title() ;?></h1>

      <?php
      $description = get_field( "description" );
      echo '<div class="crew-description">' . $description . '</div>';

      if( have_rows('crew_members') ):

        $count = 0; ?>

        <div class="row crew-members-cont">

        <?php while( have_rows('crew_members') ): the_row();
			$image = get_sub_field('image');
			$name = get_sub_field('name');
			$job_title = get_sub_field('job_title');
			$aboutdescription = get_sub_field('aboutdescription');
			$email_address = get_sub_field('email_address');
			$linkedin = get_sub_field('linkedin');
			$photon = get_resized_remote_image_url($image['url'], 370, 370);
			$i = 'crew-' . $count;
        ?>

        <div class="crew-member">
          <?php if ($aboutdescription) { ?>
            <a class="modal-trigger" data-src="#modal-<?php echo $i; ?>" src="#">
          <?php } ?>
              <img class="img-responsive" src="<?php echo $photon; ?>" />
              <h3><?php echo $name; ?></h3>
              <p class="text-muted"><?php echo $job_title; ?></p>
          <?php if ($aboutdescription) { ?>
            </a>
          <?php } ?>
          <div id="<?php echo $i; ?>" style="display:none;min-width:300px;max-width:80%;height:auto;">
            <div class="crew-modal" id="modal-<?php echo $i; ?>" class="display:flex;">
            	<img class="img-responsive" src="<?php echo $photon; ?>" height="370" width="370" />
				<div class="crew-info">
					<h3><?php echo $name; ?></h3>
					<p class="crew-about"><?php echo $aboutdescription; ?></p>
					<?php if ($email_address) { ?>
						<a href="mailto:<?php echo $email_address; ?>">
							<i class="far fa-envelope"></i>
						</a>
					<?php }
					if ($linkedin) { ?>
						<a href="<?php echo $linkedin; ?>" target="_blank">
							<i class="far fa-linkedin"></i>
						</a>
					<?php } ?>
				</div>
            </div>
          </div>

        </div>

        <?php
        $count++;
        endwhile; ?>

        </div>

      <?php endif; ?>

    </div>

  </div>

</div><!-- end .page-content -->
<script>
	jQuery(".modal-trigger").on('click', function(event){
		event.preventDefault();
		jQuery(".ui-dialog-content").dialog("close");
		jQuery(jQuery(this).attr("data-src")).dialog({
			dialogClass: 'hide-heading',
			modal: true,
			show: {
				effect: "blind",
				duration: 1000
			},
			hide: {
				effect: "explode",
				duration: 1200
			},
			open: function(){
	            jQuery('.ui-widget-overlay').bind('click',function(){
	                jQuery(".ui-dialog-content").dialog("close");
	            })
	        }
		});
	});
</script>

<?php get_footer(); ?>
