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
			'name'              => 'landing_hero_block',
			'title'             => ('Landing Hero'),
			'description'       => ('6 boxes on one side, one on the other, scrolling background'),
			'render_callback'   => 'landing_hero_block_render_callback',
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
	}
}
add_action( 'acf/init', 'custom_acf_blocks' );

// whose got six boxes on one side and a wysiwyg editor on the other? this guy
function landing_hero_block_render_callback($block){
   if (get_field('activeinactive') == 'show') {
		$scrolling = get_field('background_image')['scrolling'][0];
		$return = '';
		$return .= '<div class="container-fluid landing-hero'.($scrolling === "yes" ? ' scrolling' : '').'" style="background-image: url('. get_field('background_image')['image'].');">
							<div class="row">';
		$first .=        '<div class="col-md-8 col-sm-7 col-xs-12 six-column">';
		if (have_rows('six_column_spread')) {
			$first .= '<div class="container-fluid">
								<div class="row six-column-row">';
			while (have_rows('six_column_spread')) {
				the_row();
				$color = get_sub_field('color');
				if($color === "ffffff") { $color = "fff"; }
				// background image for the link boxes are optional
				$first .= '<div class="col-md-4 col-sm-6 col-xs-12 six-column-content">
									<a href="'.get_sub_field('link').'" '.($color === "fff" ? ' class="white-bg"' : '').' style="background-color:#'.$color.';background-image:url('.get_sub_field('background_image')['url'].');">
										<h5>'.get_sub_field('title').'</h5>
										<div class="text">'.get_sub_field('text').'</div>
										<button class="btn">Sign Up</button>
									</a>
								</div>';
			}
			$first .= "   </div>
							</div>";
		}
		$first .= '      </div>';
		$second .= '     <div class="col-md-4 col-sm-5 col-xs-12 wysiwyg">'.get_field('main_content').'</div>';

		// here we get tricky and allow users to switch the position of the six column spread and the 
		if(get_field('switch_position')[0] == 'switch') {
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
		$first .=        '<div class="col-sm-6 col-xs-12 media-column" style="background-image:url('.get_field('media_side')['image']['url'].');">';
		$first .=        '</div>';
		$second .= '     <div class="col-sm-6 col-xs-12 wysiwyg"><h2>'.get_field('text_side')['title'].'</h2>'.get_field('text_side')['text'].'</div>';
		// here we get tricky and allow users to switch the position of the six column spread and the 
		if(get_field('switch_position')[0] == 'switch') {
			$return .= $second . $first;
		} else {
			$return .= $first . $second;
		}
		$return .=     '</div>
						</div>';
		echo $return;
	}
}
?>