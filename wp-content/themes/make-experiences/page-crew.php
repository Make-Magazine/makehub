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
            <a href="#<?php echo $i; ?>" class="fancybox">
          <?php } ?>
              <img class="img-responsive" src="<?php echo $photon; ?>" />
              <h3><?php echo $name; ?></h3>
              <p class="text-muted"><?php echo $job_title; ?></p>
          <?php if ($aboutdescription) { ?>
            </a>
          <?php } ?>
          <div id="<?php echo $i; ?>" style="display:none;width:300px;">
            <div class="crew-modal">
              <div class="col-sm-4">
                <img class="img-responsive" src="<?php echo $photon; ?>" />
                <?php if ($email_address) { ?>
                  <a href="mailto:<?php echo $email_address; ?>">
                    <span class="fa-stack fa-lg">
                      <i class="fa fa-square fa-stack-2x"></i>
                      <i class="fa fa-envelope fa-stack-1x fa-inverse"></i>
                    </span>
                  </a>
                <?php }
                if ($linkedin) { ?>
                  <a href="<?php echo $linkedin; ?>" target="_blank">
                    <span class="fa-stack fa-lg">
                      <i class="fa fa-square fa-stack-2x"></i>
                      <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>
                    </span>
                  </a>
                <?php } ?>
              </div>
              <div class="col-sm-8">
                <h3><?php echo $name; ?></h3>
                <p class="crew-about"><?php echo $aboutdescription; ?></p>
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
jQuery(function() {
  jQuery(".fancybox").fancybox({
    autoSize    : false,
    width       : '80%',
    height      : 'auto',
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
});
</script>

<?php get_footer(); ?>
