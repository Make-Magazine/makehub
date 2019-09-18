<?php
// add acf titles to admin for ease of use
function acf_flexible_content_layout_title( $title, $field, $layout, $i ) {
	
   $newTitle = '';
	
   if( $panelTitle = get_sub_field('panel_title') ) {
      $newTitle = $panelTitle . ' ';
   }
	if( $bannerh2 = get_sub_field('banner_h2') ) {
      $newTitle = $bannerh2 . ' ';
   }
	if( $bannerh1 = get_sub_field('banner_h1') ) {
      $newTitle = $bannerh1 . ' ';
   }
   $newTitle .= '<div style="font-size: 12px; margin-right: 2em;">' . $title . '</div>';
	
	return $newTitle;
}

add_filter('acf/fields/flexible_content/layout_title', 'acf_flexible_content_layout_title', 10, 4);


// Collapse flexible content panels, it's just a little much
function acf_flexible_content_collapse() {
  if( get_field('content_panels') ) { // Maker toolkit
	  ?>
	  <style id="acf-flexible-content-collapse">.acf-flexible-content .acf-fields { display: none; }</style>
	  <script type="text/javascript"> 
			jQuery(function($) {
				 $('.acf-flexible-content .layout').addClass('-collapsed');
				 $('#acf-flexible-content-collapse').detach();
			});
	  </script>
	  <?php
  }
}

add_action('acf/input/admin_head', 'acf_flexible_content_collapse');
