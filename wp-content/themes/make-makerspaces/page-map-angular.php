<?php
/**
 * Template Name: Angular Map
 *
 * @package _makerspaces
 */

get_header(); ?>
<div class="makerspaces-map-wrp" ng-app="makerSpacesApp" ng-strict-di>
  <div class="container">
    <div class="row map-header">
      <div class="col-xs-12 col-lg-12">
        <h1>Makerspaces Directory</h1>
			<p><?php echo the_content(); ?></p>
      </div>
    </div>

    <div class="row map-app-container">
      <?php echo do_shortcode( '[makemap form="2" searchtext="Find a Makerspace"]' );?>
    </div>
  </div>
	
  <div class="container-fluid light-blue">
	  <div class="container">
			<div class="row">
				<div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
					<h4>Join our global network of makerspaces</h4>
					<a href="/register"><button class="btn blue-btn">Add your makerspace</button></a>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
					<h4>See an error or need to update your info?</h4>
               <a href="/edit-your-makerspace"><button class="btn blue-btn">Manage your listing</button></a>					
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
					<h4>Get a free PDF guide on starting a makerspace</h4>
					<a href="/playbook"><button class="btn blue-btn">Download the playbook</button></a>
				</div>
				<div class="col-md-3 col-sm-6 col-xs-12 makerspace-bottom-nav">
					<h4>Start your own makerspace today</h4>
					<a href="https://learn.make.co/courses/starting-a-school-makerspace/info"><button class="btn blue-btn">Take the course</button></a>
				</div>
			</div>
		</div>
  </div>  
	
	<div class="container makerspace-news">
		<div class="row posts-feeds-wrapper">
		  <h2>Makerspace News from <img class="logo" src="https://make.co/wp-content/themes/memberships/img/make_logo.svg" /> magazine</h2>

          <?php
          $rss = fetch_feed('https://makezine.com/tag/makerspaces/feed/');
          if (!is_wp_error($rss)) :
            $maxitems = $rss -> get_item_quantity(4); //gets latest 5 items, this can be changed to suit your requirements
            $rss_items = $rss -> get_items(0, $maxitems);
          endif;


          ?>
            <?php
            if ($maxitems == 0) echo '<li>No items.</li>';
            else foreach ( $rss_items as $item ) :
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				 <div class="post-feed">
              <a class="full-link" href="<?php echo esc_url($item -> get_permalink()); ?>" target="_blank">
					  <div class="title">
						 <img src="<?php echo get_first_image_url($item -> get_content()); ?>" alt="Makerspace post featured image">
						 <p class="p-title"><?php echo esc_html($item -> get_title()); ?></p>
						 <p><?php echo shorten(get_summary($item -> get_content()), 100); ?></p>
					  </div>
					</a>
				 </div>
            </div>
            <?php endforeach; ?>
            <a class="all-projects-title" href="http://makezine.com/tag/makerspaces/" target="_blank"><button class="btn blue-btn">See more articles</button></a>
	
  
	  </div>
	</div>
</div>
	
	      <?php /*
        <div class="posts-feeds-wrapper col-xs-12 col-sm-6">
          <?php
          $rss = fetch_feed('https://makezine.com/projects/feed/');
          if (!is_wp_error($rss)) :
            $maxitems = $rss -> get_item_quantity(5); //gets latest 5 items, this can be changed to suit your requirements
            $rss_items = $rss -> get_items(0, $maxitems);
          endif;

          //grabs our post thumbnail image
          function get_first_image_url_p($html) {
            if (preg_match('/<img.+?src="(.+?)"/', $html, $matches)) {
              return $matches[1];
            }
          }

          //shortens description
          function shorten_p($string, $length) {
            $suffix = '&hellip;';
            $short_desc = trim(str_replace(array("\r", "\n", "\t"), ' ', strip_tags($string)));
            $desc = trim(substr($short_desc, 0, $length));
            $lastchar = substr($desc, -1, 1);
            if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?')
            $suffix = '';
            $desc .= $suffix;
            return $desc;
          }
          ?>
          <h3 class="feed-title">
            <i class="fa fa-newspaper-o feed-icon"></i> Latest Projects
          </h3>
          <ul class="posts-feeds">
            <?php
            if ($maxitems == 0) echo '<li>No items.</li>';
            else foreach ( $rss_items as $item ) :
            ?>
            <li class="post-feed">
              <a class="full-link" href="<?php echo esc_url($item -> get_permalink()); ?>" target="_blank"></a>

              <div class="title">
                <img src="<?php echo get_first_image_url_p($item -> get_content()); ?>" alt="Makerspace post featured image">
                <p class="p-title"><?php echo esc_html($item -> get_title()); ?></p>
              </div>
            </li>
            <?php endforeach; ?>
            <a class="all-projects-title" href="http://makezine.com/tag/makerspaces/" target="_blank">See All Projects</a>
          </ul>
        </div> */ ?>
<?php get_footer(); ?>

