<?php

// [make_rss title="Makerspace", feed="https://makezine.com/tag/makerspaces/feed/", moreLink="http://makezine.com/tag/makerspaces/", number=4]
function make_rss_func( $atts ) {
	$a = shortcode_atts( array(
		'title' => '',
		'feed' => 'https://makezine.com/feed/',
                'moreLink' => "",
                'number'=> 4
	), $atts );
        
?>
<div class="wrapper makerspace-news">
   <h2><?php echo $a['title']?> News from <img class="logo" src="https://make.co/wp-content/universal-assets/v2/images/make_logo.svg" /> magazine</h2>
   <div class="posts-feeds-wrapper">
      
      <?php
      $rss = fetch_feed($a['feed']);
      if (!is_wp_error($rss)) :
         $maxitems = $rss -> get_item_quantity($a['number']); //gets latest x items, this can be changed to suit your requirements
         $rss_items = $rss -> get_items(0, $maxitems);
      endif;

      ?>
      <?php
      if ($maxitems == 0) echo '<li>No items.</li>';
      else foreach ( $rss_items as $item ) :
      ?>
      <div class="post-feed">
         <a class="full-link" href="<?php echo esc_url($item -> get_permalink()); ?>" target="_blank">
            <div class="title">
               <img src="<?php echo get_first_image_url($item -> get_content()); ?>" alt="<?php echo $a['title'];?> post featured image">
               <p class="p-title"><?php echo esc_html($item -> get_title()); ?></p>
               <p><?php echo shorten(get_summary($item -> get_content()), 100); ?></p>
            </div>
         </a>
      </div>
      <?php endforeach; ?>
      <?php if($a['moreLink']!=''){ ?>
      <div>
         <a class="btn universal-btn btn-more-articles" href="<?php echo $a['moreLink'];?>" target="_blank">See more articles</a>
      </div>
      <?php } ?>
   </div>

</div>
<?php
	
}
add_shortcode( 'make_rss', 'make_rss_func' );