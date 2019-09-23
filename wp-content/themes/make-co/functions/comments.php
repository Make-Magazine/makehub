<?php // Functions for handling comments

// callback function for wp_list_comments
function format_comment($comment, $args, $depth) {
	/*
    * Set Cookies
    */
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
 
	/*
	 * If you do not like this loop, pass the comment depth from JavaScript code
	 */
	$comment_depth = 1;
	$comment_parent = $comment->comment_parent;
	while( $comment_parent ){
		$comment_depth++;
		$parent_comment = get_comment( $comment_parent );
		$comment_parent = $parent_comment->comment_parent;
	}
 
	
   $GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $comment_depth;
	
	$comment_author = get_user_by('email', $comment->comment_author_email);
	$comment_author_ID = $comment_author->ID;

	$comment_html = '<li ' . comment_class('', null, null, false ) . ' id="comment-' . get_comment_ID() . '">
		<article class="comment-body" id="div-comment-' . get_comment_ID() . '">
			<div class="comment-meta">
				<a href="' . get_author_posts_url($comment_author_ID) . '" class="comment-author vcard">
					' . bp_core_fetch_avatar( 
		               array(
							  'item_id' => $comment->user_id,
							  'width' => 50,
							  'height' => 50,
							  'class' => 'avatar',
							)
						 ) . '
				</a>
				<div class="comment-metadata">
				   <b class="fn">' . get_comment_author_link() . '</b>
					<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', get_comment_date(),  get_comment_time() ) . '</a>';

				$comment_html .= '</div>';
				if ( $comment->comment_approved == '0' )
					$comment_html .= '<p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>';

			$comment_html .= '</div>
			<div class="comment-content">' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '</div>
	      <div class="reply">' . get_comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) .'</div>
		</article>
	</li>';
	echo $comment_html;
}


add_action( 'wp_enqueue_scripts', 'misha_ajax_comments_scripts' );
 
function misha_ajax_comments_scripts() {
	wp_enqueue_script('jquery');
	wp_register_script( 'ajax_comment', get_stylesheet_directory_uri() . '/ajax-comment.js', array('jquery') );
 
	wp_localize_script( 'ajax_comment', 'misha_ajax_comment_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php'
	) );
 
 	wp_enqueue_script( 'ajax_comment' );
}

add_action( 'wp_ajax_ajaxcomments', 'misha_submit_ajax_comment' ); // wp_ajax_{action} for registered user
add_action( 'wp_ajax_nopriv_ajaxcomments', 'misha_submit_ajax_comment' ); // wp_ajax_nopriv_{action} for not registered users
 
function misha_submit_ajax_comment(){
	/*
	 * Wow, this cool function appeared in WordPress 4.4.0, before that my code was muuuuch mooore longer
	 *
	 * @since 4.4.0
	 */
	$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
	if ( is_wp_error( $comment ) ) {
		$error_data = intval( $comment->get_error_data() );
		if ( ! empty( $error_data ) ) {
			wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $error_data, 'back_link' => true ) );
		} else {
			wp_die( 'Unknown error' );
		}
	}
 
	/*
	 * Set Cookies
	 */
	$user = wp_get_current_user();
	do_action('set_comment_cookies', $comment, $user);
 
	/*
	 * If you do not like this loop, pass the comment depth from JavaScript code
	 */
	$comment_depth = 1;
	$comment_parent = $comment->comment_parent;
	while( $comment_parent ){
		$comment_depth++;
		$parent_comment = get_comment( $comment_parent );
		$comment_parent = $parent_comment->comment_parent;
	}
 
 	/*
 	 * Set the globals, so our comment functions below will work correctly
 	 */
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $comment_depth;
	
	$comment_author = get_user_by('email', $comment->comment_author_email);
	$comment_author_ID = $comment_author->ID;
 
	/*
	 * Here is the comment template, you can configure it for your website
	 * or you can try to find a ready function in your theme files
	 */
	$comment_html = '<li ' . comment_class('', null, null, false ) . ' id="comment-' . get_comment_ID() . '">
		<article class="comment-body" id="div-comment-' . get_comment_ID() . '">
			<div class="comment-meta">
				<a href="' . get_author_posts_url($comment_author_ID) . '" class="comment-author vcard">
					' . bp_core_fetch_avatar( 
		               array(
							  'item_id' => $comment->user_id,
							  'width' => 50,
							  'height' => 50,
							  'class' => 'avatar',
							)
						 ) . '
				</a>
				<div class="comment-metadata">
				   <b class="fn">' . get_comment_author_link() . '</b>
					<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', get_comment_date(),  get_comment_time() ) . '</a>';

				$comment_html .= '</div>';
				if ( $comment->comment_approved == '0' )
					$comment_html .= '<p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>';

			$comment_html .= '</div>
			<div class="comment-content">' . apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) . '</div>
	      <div class="reply">' . get_comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) .'</div>
		</article>
	</li>';
	echo $comment_html;
 
	die();
 
}