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
			   'supports' => array('comments', 'thumbnail', 'excerpt', 'buddypress-activity'),
				'bp_activity' => array(
					'component_id' => buddypress()->activity->id,
					'action_id'    => 'new_projects',
					'contexts'     => array( 'activity', 'member' ),
					'position'     => 40,
			  ),
        )
    );
	 register_post_type(
	 	  'blog_posts',
        array(
            'labels' => array(
                'name' => __( 'Blog Posts' ),
                'singular_name' => __( 'Blog Post' ),
					 'bp_activity_admin_filter' => __( 'Published a new Blog Post' ),
        			 'bp_activity_front_filter' => __( 'Blog Posts'),
					 'bp_activity_new_post'     => __( '%1$s posted a new <a href="%2$s">Blog Post</a>' ),
        			 'bp_activity_new_post_ms'  => __( '%1$s posted a new <a href="%2$s">Blog Post</a>' ),
					 'bp_activity_new_comment'           => __( '%1$s commented on a <a href="%2$s">Blog Post</a>', 'custom-textdomain' ),
    				 'bp_activity_new_comment_ms'        => __( '%1$s commented on a <a href="%2$s">Blog Post</a>', 'custom-textdomain' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'blog'),
			   'supports' => array('comments', 'editor', 'author', 'thumbnail', 'excerpt', 'buddypress-activity'),
				'bp_activity' => array(
					'component_id' => buddypress()->activity->id,
					'action_id'    => 'new_blog_posts',
					'contexts'     => array( 'activity', 'member' ),
					'position'     => 40,
			  ),
        )
	 );
}
add_action( 'init', 'create_posttypes' );

// Add the excerpt of a blog post to the activity feed. formatting is stripped out of cpt[content], unfortunately
function record_cpt_activity_content( $cpt ) {

	if ( 'new_blog_posts' === $cpt['type'] ) {
		global $wpdb, $post, $bp;
		//$cpt['action'] .= " - " . get_the_title($cpt['secondary_item_id']);
		$cpt['content'] = get_the_post_thumbnail( $cpt['secondary_item_id'] );
		$cpt['content'] .= get_the_excerpt( $cpt['secondary_item_id'] );
		//$theimg = wp_get_attachment_image_src( get_post_thumbnail_id( $cpt['secondary_item_id'] ) );
		//$cpt['content'] .= $theimg;
		//error_log(print_r($theimg, TRUE));
	}
   //error_log(print_r($cpt, TRUE));
	return $cpt;
}
add_filter('bp_after_activity_add_parse_args', 'record_cpt_activity_content');

// and delete the activity associated with a post when the post is deleted, matching it by the content of the post... which is a lot easier than just matching the postid to the activity id for some reason
add_action( 'before_delete_post', 'delete_post_activity' );
function delete_post_activity( $postid ){;
	bp_activity_delete(
		array(
			//'user_id' => bp_displayed_user_id(),
			'content' => get_the_excerpt($postid)
		)
	);
}

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
    return 15;
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
        $more = '<a class="yz-read-more" href="'.get_permalink( get_the_ID()).'"><span class="yz-rm-icon"><i class="fas fa-angle-double-right"></i></span>Read More</a>';
    }
    return $more;
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );
