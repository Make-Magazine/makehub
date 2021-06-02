<?php

define('BP_BUDDYBLOG_SLUG', 'blog');
/*
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
                'name' 								=> __( 'Blog Posts' ),
                'singular_name' 					=> __( 'Blog Post' ),
					 'bp_activity_admin_filter'   => __( 'Published a new Blog Post' ),
        			 'bp_activity_front_filter'   => __( 'Blog Posts'),
					 'bp_activity_new_post'       => __( '%1$s posted a new <a href="%2$s">Blog Post</a>' ),
        			 'bp_activity_new_post_ms'    => __( '%1$s posted a new <a href="%2$s">Blog Post</a>' ),
					 'bp_activity_new_comment'    => __( '%1$s commented on a <a href="%2$s">Blog Post</a>' ),
    				 'bp_activity_new_comment_ms' => __( '%1$s commented on a <a href="%2$s">Blog Post</a>' )
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
add_action( 'init', 'create_posttypes' );*/


/**
 *   Add the title and featured image of a blog post to the activity feed. 
 */ 
function record_cpt_activity_content( $cpt ) {
	if ( 'new_blog_posts' === $cpt['type'] ) {
		global $wpdb, $post, $bp;
		$cpt['content'] = '<a href="'.$cpt['primary_link'].'">'
			. get_the_post_thumbnail($cpt['secondary_item_id']) . '</a>';
		$cpt['content'] .= '<a href="'.$cpt['primary_link'].'">test ' . get_the_title($cpt['secondary_item_id']) . '</a>';
		$cpt['content'] .= '<a href="'.$cpt['primary_link'].'" class="btn universal-btn">Read More</a>';
	}

	return $cpt;
}
add_filter('bp_after_activity_add_parse_args', 'record_cpt_activity_content');


/**
 *   Delete activity if it's secondary item id matches the postid of the deleted post
 */ 
add_action( 'before_delete_post', 'delete_post_activity' );
function delete_post_activity( $postid ){;
	bp_activity_delete( array( 'secondary_item_id' => $postid ) );
}

function use_profile_as_comment_author_url( $url, $id, $comment ) {
    if ( $comment->user_id ) {
        return get_author_posts_url( $comment->user_id );
    }
    return $url;
}
add_filter( 'get_comment_author_url', 'use_profile_as_comment_author_url', 10, 3 );

/**
 * Register the blog post form Might NOT NEED ALL THIS BECAUSE OF BUDDYBLOG
 *
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
*/
	
/**
 * Filter the except length to 20 words.
 */
function custom_excerpt_length( $length ) {
    return 15;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
 * Filter the excerpt "read more" string.
 */
function custom_excerpt_more( $more ) {
    if ( ! is_single() ) {
        $more = '... <a class="yz-read-more" href="'.get_permalink( get_the_ID()).'"><span class="yz-rm-icon"><i class="fas fa-angle-double-right"></i></span>Read More</a>';
    }
    return $more;
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );
