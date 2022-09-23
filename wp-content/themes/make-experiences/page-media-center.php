<?php
/*
Template name: Content Tabs
*/
get_header(); ?>

<div id="media-center-page" class="page-content">

  <div class="container">

    <h1 class="page-title"><?php echo get_the_title(); ?></h1>

  </div>

  <div class="flag-banner"></div>

  <div class="mcp-body">

    <div class="container">

      <div class="row">

        <div class="content col-xs-12">

          <div class="tabbable" id="tabs">

            <ul class="nav col-xs-12 col-sm-3">

              <?php if( have_rows('tabs') ):
                while( have_rows('tabs') ): the_row();
                  $tab_title = get_sub_field('tab_title');
                  $replace_these = array("/", "&");
                  $tab_title2 = (str_replace($replace_these, '', $tab_title));
                  $tab_url = (str_replace(' ', '-', strtolower($tab_title2)));
				  ?>

                    <li>
                      <a href="#<?php echo $tab_url ?>"><span><?php echo $tab_title; ?></span></a>
                    </li>

                  <?php
                endwhile;
              endif; ?>

            </ul>

              <?php if( have_rows('tabs') ):
                while( have_rows('tabs') ): the_row();She
                  $tab_content = get_sub_field('tab_content');
                  $tab_title = get_sub_field('tab_title');
                  $replace_these = array("/", "&");
                  $tab_title2 = (str_replace($replace_these, '', $tab_title));
                  $tab_url = (str_replace(' ', '-', strtolower($tab_title2)));
				  ?>

                    <div id="<?php echo $tab_url ?>">
                      <?php echo $tab_content; ?>
                    </div>

                  <?php
                endwhile;
              endif; ?>

          </div>

        </div><!--Content-->

      </div>

    </div>

  </div><!--Container-->

</div><!--#media-center-page-->

<script>
	jQuery(document).ready(function () {
		jQuery( "#tabs" ).tabs();
	} );
</script>
<?php get_footer(); ?>
