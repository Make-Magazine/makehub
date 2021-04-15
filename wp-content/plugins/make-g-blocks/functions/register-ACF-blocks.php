<?php

// Create new categories for our blocks

function make_panels($categories, $post) {
    return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'make-panels',
                    'title' => __('Make: Panels', 'make-panels'),
                ),
            )
    );
}
add_filter('block_categories', 'make_panels', 10, 2);

// I'd like to get the makermedia page builder panels added as well
function maker_media_panels($categories, $post) {
    return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'maker-media-panels',
                    'title' => __('MM Legacy Panels', 'maker-media-panels'),
                ),
            )
    );
}
add_filter('block_categories', 'maker_media_panels', 10, 2);

add_action('acf/init', 'make_add_acf_blocks');

function make_add_acf_blocks() {

    // check function exists
    if (function_exists('acf_register_block')) {

        //Get tickets
        acf_register_block(array(
            'name' => 'buy_tickets_float',
            'title' => __('Get Tickets Floating Banner'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('buy', 'tickets', 'panel'),
            'example' => [
			'attributes' => [
			'mode' => 'preview',
			'data' => ['is_example' => true],
		]
            ]
        ));
		acf_register_block(array(
            'name' => 'panel_rollover_items',
            'title' => __('Rollover Items'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'makers', 'square', 'panel'),
        ));
		acf_register_block(array(
            'name' => 'ribbon_separator_panel',
            'title' => __('Ribbon Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('spacer','separator', 'banner', 'panel'),
        ));

        acf_register_block(array(
            'name' => 'post_feed',
            'title' => __('News / Post Feed'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('featured', 'events', 'dynamic', 'panel'),
        ));

        acf_register_block(array(
            'name' => 'call_to_action_panel',
            'title' => __('Call to Action'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('call', 'action', 'panel'),
        ));
		 
		acf_register_block(array(
            'name' => 'one_column',
            'title' => __('Hero Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('hero', 'image', 'panel'),
        ));
		acf_register_block(array(
            'name' => 'one_column_wysiwyg',
            'title' => __('1 column WYSIWYG'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('hero', 'image', 'panel'),
        ));
		  acf_register_block(array(
            'name' => 'three_column',
            'title' => __('3 column'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'dynamic', 'panel'),
        ));
		  acf_register_block(array(
            'name' => 'six_column',
            'title' => __('6 column navigation panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('columns', 'dynamic', 'panel'),
        ));
		acf_register_block(array(
            'name' => 'home_page_image_carousel',
            'title' => __('Home Page Image Carousel'),
            'render_callback' => 'home_page_image_carousel',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel'),
        ));    
        acf_register_block(array(
            'name' => 'static_or_carousel',
            'title' => __('Image Carousel (Rectangle)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel', 'panel'),
        ));
        acf_register_block(array(
            'name' => 'square_image_carousel',
            'title' => __('Image Carousel (Square)'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'carousel', 'panel', 'square'),
        ));

        acf_register_block(array(
            'name' => 'social_media',
            'title' => __('Social Media'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('social', 'media', 'panel'),
        ));
 
        //2 column video and text panel
        acf_register_block(array(
            'name' => 'two_column_video',
            'title' => __('2 Column Video Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('video', 'panel'),
        ));
        // Image Panels in the same style as the Video Panels
        acf_register_block(array(
            'name' => 'two_column_image',
            'title' => __('2 column Image and text Panel'),
            'render_callback' => 'call_ACF_block_panels',            
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));      
		  // Dynamic carousel with multiple columns
        acf_register_block(array(
            'name' => 'image_slider',
            'title' => __('Image Slider'),
            'render_callback' => 'call_ACF_block_panels',            
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'panel'),
        ));    
		// News Block Panel
        acf_register_block(array(
            'name' => 'news_block_panel',
            'title' => __('News Block Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('makey', 'banner','panel'),
        ));            

		//Flag Banner Separator Panel
        acf_register_block(array(
            'name' => 'flag_banner_panel',
            'title' => __('Flag Banner Separator Panel'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('flag', 'banner', 'separator','panel'),
        ));

        acf_register_block(array(
            'name' => 'sponsors_panel',
            'title' => __('Sponsors'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('sponsors', 'panel'),
        ));        
        acf_register_block(array(
            'name' => 'what_is_maker_faire',
            'title' => __('What is Maker Faire'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('maker', 'faire', 'panel'),
        ));
		acf_register_block(array(
            'name' => 'image_button',
            'title' => __('Image Button'),
            'render_callback' => 'call_ACF_block_panels',
            'category' => 'make-panels',
            'icon' => 'admin-comments',
            'mode' => 'auto',
            'keywords' => array('image', 'button', 'panel'),
        ));

    }
}

function call_ACF_block_panels($block) {
    GLOBAL $acf_blocks;
    $acf_blocks = TRUE;
    $name = str_replace("acf/", "", $block['name']);
    $name = str_replace("-", "_", $name);
	if (get_field('is_example')){
	/* Render screenshot for example */
        echo 'this is it'.plugin_dir_path().'/examples/buy_tickets.png';
        //echo '<img src="'.plugin_dir_path().'/examples/buy_tickets.png" />';
    }
    echo ($name != '' ? dispLayout($name) : '');
}
