<?php 

// Create new categories for our blocks

function acf_blocks( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'acf-blocks',
				'title' => __( 'ACF Blocks', 'acf-blocks' ),
			),
		)
	);
}
add_filter( 'block_categories', 'acf_blocks', 10, 2);

// register all our acf blocks here
function custom_acf_blocks() {
   if( function_exists('acf_register_block') ) {
		acf_register_block(array(
			'name'              => 'grid_block_split',
			'title'             => ('Grid Block Split'),
			'description'       => ('4/6 boxes on one side, one on the other, scrolling background'),
			'render_callback'   => 'grid_block_split_block_render_callback',
			'category'          => 'acf-blocks',
			'icon'              => 'feedback',
			'keywords'          => array( 'hero', 'landing-page' ),
		));
		acf_register_block(array(
			'name'              => 'two_column_media',
			'title'             => ('2 Column Media'),
			'description'       => ('2 Columns, Media and text'),
			'render_callback'   => 'two_column_media_block_render_callback',
			'category'          => 'acf-blocks',
			'icon'              => 'analytics',
			'keywords'          => array( 'media', 'two column' ),
		));
		acf_register_block(array(
			'name'              => 'ribbon_banner',
			'title'             => ('Ribbon Banner'),
			'description'       => ('Header, text and link on colored background'),
			'render_callback'   => 'ribbon_banner_block_render_callback',
			'category'          => 'acf-blocks',
			'icon'              => 'minus',
			'keywords'          => array( 'banner', 'one column' ),
		));
	}
}
add_action( 'acf/init', 'custom_acf_blocks' );

// whose got 4/6 boxes on one side and a wysiwyg editor on the other? this guy
function grid_block_split_block_render_callback($block){
   if (get_field('activeinactive') == 'show') {
		$scrolling = get_field('background_image')['scrolling'][0];
		$return = '';
		$return .= '<div class="container-fluid grid-column-split'.($scrolling === "yes" ? ' scrolling' : '').'" style="background-image: url('. get_field('background_image')['image'].');">
							<div class="row">';
		if(get_field('header')){
			$return .=     '<div class="col-xs-12"><h1>'.get_field('header').'</h1></div>';
		}
		$first =         '<div class="col-md-8 col-sm-7 col-xs-12 grid-column">';
		if (have_rows('grid_column_spread')) {
			$first .= '<div class="container-fluid">
								<div class="row grid-column-row">';
			// only repeat for the number of boxes we've set to display
			$curRow = 1;
			$gridRows = intval(get_field('how_many_of_the_boxes_to_display'));
			if($gridRows === 6) { $gridRows = 4; }
			while (have_rows('grid_column_spread') && ($curRow <= $gridRows)) {
				the_row();
				$curRow += 1;
				$color = get_sub_field('color');
				if($color === "ffffff") { $color = "fff"; }
				$link = ''; // different link if logged in
		      if(get_sub_field('link')){ $link = get_sub_field('link'); }
		      if(is_user_logged_in() && get_sub_field('logged_in_link')){ $link = get_sub_field('logged_in_link'); }
				// background image for the link boxes are optional
				$first .= '<div class="col-md-'.get_field('how_many_of_the_boxes_to_display').' col-sm-6 col-xs-12 grid-column-content">';
				           if($link != ''){ $first .= '<a href="'.$link.'">'; }
				$first .= '		<div class="grid-column-inner rows-'.$gridRows.($color === "fff" ? ' white-bg' : '').'" style="background-color:#'.$color.';background-image:url('.get_sub_field('background_image')['url'].');">';
				if(get_sub_field('title')){ $first .= '<h5>'.get_sub_field('title').'</h5>'; }
				$first .= '			<div class="text">'.get_sub_field('text').'</div>';
				// only show the button if there's a link and button text
				if($link != '' && get_sub_field('button_text') != ''){ $first .= '<button class="btn">'.get_sub_field('button_text').'</button>'; }
				$first .= '		</div>';
				            if($link != ''){ $first .= '</a>'; }
				$first .= '</div>';
			} // end while loop
			$first .= "   </div>
							</div>";
		}
		$first .= '      </div>';
		if ( is_user_logged_in() && is_front_page() ) { // if we're on the front page, we can assume the login shortcode was in the wysiwyg and it should have a different display when logged in
			error_log("conditions are being met");
			$second = '	  <div class="col-md-4 col-sm-5 col-xs-12 wysiwyg" style="padding: 35px 30px 0px;">
								  <h2 style="color:#fff;margin-top:0px;font-weight: 300;">Welcome, '.bp_core_get_user_displayname( bp_loggedin_user_id() ).'</h2>
								  <p><a href="'.bp_loggedin_user_domain().'" class="btn universal-btn-reversed" style="width:100%;">My Profile</a></p>
								  <p><a href="/groups" class="btn universal-btn-reversed" style="width:100%;">Browse Groups</a></p>
								  <p><a href="/members" class="btn universal-btn-reversed" style="width:100%;">Member Directory</a></p>
								  <p><a href="/activity" class="btn universal-btn-reversed" style="width:100%;">Newsfeed</a></p>
								  <p><a href="/digital-library" class="btn universal-btn-reversed" style="width:100%;">View Digital Magazine</a></p>
							  </div>';
		} else {
			$second = '   <div class="col-md-4 col-sm-5 col-xs-12 wysiwyg">'.get_field('main_content').'</div>';
		}

		// here we get tricky and allow users to switch the position of the six column spread and the 
		if(get_field('switch_position')) {
			$return .= $second . $first;
		} else {
			$return .= $first . $second;
		}
		$return .=     '</div>
						</div>';
		echo $return;
	}
}



// Two column layout with media on one side and a wysiwyg editor on the other
function two_column_media_block_render_callback($block){
   $color = get_field('background_color');
	if (get_field('activeinactive') == 'show') {
		$return = '';
		$return .= '<div class="container-fluid two-column-media'.($color === "fff" ? ' white-bg' : '').'" style="background-color:#'.$color.';">
							<div class="row">';
		$first =         '<div class="col-sm-6 hidden-xs media-column" style="background-image:url('.get_field('media_side')['image']['url'].');">';
		$first .=        '</div>';
		$second = '       <div class="col-sm-6 col-xs-12 wysiwyg"><h2>'.get_field('text_side')['title'].'</h2>'.get_field('text_side')['text'].'</div>';
		// here we get tricky and allow users to switch the position of the six column spread and the 
		if(get_field('switch_position')) {
			$return .= $second . $first;
		} else {
			$return .= $first . $second;
		}
		$return .=     '</div>
						</div>';
		echo $return;
	}
}

// One column ribbon with background
function ribbon_banner_block_render_callback($block){
   $color = get_field('background_color');
	if (get_field('activeinactive') == 'show') {
		$return = '';
		$return .= '<div class="container-fluid ribbon-banner'.($color === "fff" ? ' white-bg' : '').'" style="background-color:#'.$color.';"><div class="container">';
		if(get_field('link')) {
			$return .= '<a href="'.get_field('link').'">';
		}
		$return .= '<h2>'.get_field('header').'</h2>';
		if(get_field('text')) {
			$return .= '<p>'.get_field('text').'</p>';
		}
		if(get_field('link')) {
			$return .= '</a>';
		}				
		$return .=     '</div>
						</div>';
		echo $return;
	}
}
?>