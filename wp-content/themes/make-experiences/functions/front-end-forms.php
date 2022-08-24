<?php

define('BP_BUDDYBLOG_SLUG', 'blog');


function create_posttypes() {
	if(function_exists('buddypress')) {
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
}
add_action( 'init', 'create_posttypes' );


/**
 *   Add the title and featured image of a blog post to the activity feed.
 */
function record_cpt_activity_content( $cpt ) {
	if ( 'new_blog_posts' === $cpt['type'] ) {
		global $wpdb, $post, $bp;
		$cpt['content'] = '<a href="'.$cpt['primary_link'].'">'
			. get_the_post_thumbnail($cpt['secondary_item_id']) . '</a>';
		$cpt['content'] .= '<a href="'.$cpt['primary_link'].'">' . get_the_title($cpt['secondary_item_id']) . '</a>';
		$cpt['content'] .= '<a href="'.$cpt['primary_link'].'" class="universal-btn">Read More!</a>';
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
        $more = '... <a class="read-more-btn" href="'.get_permalink( get_the_ID()).'"><i class="fas fa-angle-double-right"></i></span>Read More</a>';
    }
    return $more;
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );
