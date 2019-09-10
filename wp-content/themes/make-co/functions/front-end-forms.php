<?php

/**
 * Register the blog post form
 *
 */
function blog_post_form() {
    $settings = array(
        'post_type'             => 'post',
        //which post type
        'post_author'           =>  bp_loggedin_user_id(),
        //who will be the author of the submitted post
        'post_status'           => 'draft',
        //how the post should be saved, change it to 'publish' if you want to make the post published automatically
        'current_user_can_post' =>  is_user_logged_in(),
        //who can post
        'show_categories'       => true,
        //whether to show categories list or not, make sure to keep it true
        'allowed_categories'    => array( get_cat_ID('Blog Posts') )
        //array of allowed categories which should be shown, use  get_all_category_ids() if you want to allow all categories
    );
 
    $form = bp_new_simple_blog_post_form( 'blog_post', $settings );
    //create a Form Instance and register it
}
if( function_exists('bp_new_simple_blog_post_form' ) ) {
	add_action( 'bp_init', 'blog_post_form', 4 );//register a form
}

// short code for the front end forms
function front_end_form_func($atts) {
   $name = shortcode_atts(array('name' => ''), $atts);
	// Load the front end form of the set type
	$form = bp_get_simple_blog_post_form( $name['name'] );
	if ( $form ) {//if this is a valid form
		 $form->show();//it will show the form
	}
}
if( function_exists('bp_new_simple_blog_post_form' ) ) {
	add_shortcode('front_end_form', 'front_end_form_func');
}