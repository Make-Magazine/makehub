<?php

define('BP_BUDDYBLOG_SLUG', 'blog');

function create_posttypes() {
    register_post_type( 
		 'projects',
        array(
            'labels' => array(
                'name' => __( 'Projects' ),
                'singular_name' => __( 'Project' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'projects'),
			   'supports' => array('comments', 'thumbnail', 'excerpt'),
        )
    );
	 register_post_type(
	 	  'blog_posts',
        array(
            'labels' => array(
                'name' => __( 'Blog Posts' ),
                'singular_name' => __( 'Blog Post' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'blog'),
			   'supports' => array('comments', 'thumbnail', 'excerpt'),
        )
	 );
}
add_action( 'init', 'create_posttypes' );

/**
 * Register the blog post form
 *
 */
function blog_post_form() {
    $settings = array(
        'post_type'             => 'blog_posts',
        //which post type
        'post_author'           =>  bp_loggedin_user_id(),
        //who will be the author of the submitted post
        'post_status'           => 'publish',
        //how the post should be saved, change it to 'publish' if you want to make the post published automatically
        'current_user_can_post' =>  is_user_logged_in(),
        //who can post
		  'allow_upload'=> true,
        //whether to show categories list or not, make sure to keep it true
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
	
/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function custom_excerpt_more( $more ) {
    if ( ! is_single() ) {
        $more = sprintf( '<p><a class="read-more" href="%1$s">%2$s</a></p>',
            get_permalink( get_the_ID() ),
            __( 'Read More &rarr;', 'textdomain' )
        );
    }
    return $more;
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );
