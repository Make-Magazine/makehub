<?php
function ldms_before_page_load(){

    global $post;

	if( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'create_message' ) || has_shortcode( $post->post_content, 'display_messages' ) ) ) {
	    ldms_kill_comments();
	    add_filter( "comments_template", "ldms_display_empty_comments_template" );
	}

	if( is_singular( 'ldms_message' ) ){
		// stop pagination
		add_filter( 'navigation_markup_template', '__return_empty_string' );

		//hijack comments_template and display none
		add_filter( "comments_template", "ldms_display_empty_comments_template" );

		// check permission and display our own content as well as the comments template
		add_filter( 'the_content', 'ldms_content_filter');
	}


}
add_action('template_redirect', 'ldms_before_page_load');


//remove comments from recent comments widget
function ra_recent_comments_less_author( $array ) {

	$messages = get_posts(array('post_type' => 'ldms_message', 'fields' => 'ids'));
	$array['post__not_in'] = $messages;
	return $array;
}
add_action( 'widget_comments_args', 'ra_recent_comments_less_author' );


//KILL COMMENTS
function ldms_kill_comments(){
	 	//remove comment form
	    add_filter( 'comments_open', '__return_false' );
	    // Remove the list of comments
	    add_filter( 'comments_array', '__return_empty_array' );

	   // add_filter('gettext', 'ldms_remove_comments_are_closed', 20, 3);

}

add_filter( 'comments_open', 'ldms_force_comments_open', 10, 2 );
function ldms_force_comments_open( $open, $post_id ) {

    if( get_post_type($post_id) == 'ldms_message' ) {
        return true;
    }

    return $open;

}

add_action( 'pre_ldms_comment_on_post', 'ldms_override_comment_closed', 1000 );
function ldms_override_comment_closed( $post_id ) {

    if( get_post_type($post_id) == 'ldms_message' ) {

        $post = get_post($post_id);
        if( $post->comment_status != 'open' ) {
            $post->comment_status = 'open';
            wp_update_post($post);
        }

    }

}


function ldms_display_empty_comments_template( $comment_template ) {
     global $post;
     if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
        return;
     }
     if($post->post_type == 'ldms_message'){
        return LDMA_PATH . '/templates/empty_comments.php';
     }
}

//approve all comments for our CPT by users that can post a message
add_filter( 'pre_comment_approved', 'ldms_approve_comments_for_users_in_message', 999, 2 );
function ldms_approve_comments_for_users_in_message( $approved, $commentdata ) {

    if( get_post_type($commentdata['comment_post_ID']) == 'ldms_message' ) return 1;

    return $approved;

}

function ldms_comments() {

	ob_start();

	$comment_args = apply_filters( 'ldms_comment_args', array(
		'post_id' => get_the_id(),
		'status' 	=> 'all',
		'order' 	=> 'ASC'
	) );

	$comments = get_comments( $comment_args ); ?>

	<div id="ldms-comments" class="ldms-comments-area">

		<?php
		// You can start editing here -- including this comment!
		if ( $comments ){
			foreach( $comments as $comment ) include( LDMA_PATH . '/templates/partials/comment.php' );
		} ?>

          <?php
          if( !has_term('archived', 'ldms_status', get_the_ID()) ): ?>
     		<div id="ldms-comment-form">
     			<?php
     			$args = apply_filters( 'ldms_comment_form_args', array(
     				'title_reply'	=>	__( 'Leave a Response', 'ldmessenger' ),
     				'label_submit'	=>	__( 'Respond', 'ldmessenger' ),
     			) );

     			do_action( 'pre_ldms_comment_on_post', get_the_ID() );

     			comment_form($args); ?>
     		</div>
          <?php else: ?>
               <div class="ldms-notice">
                    <p><?php echo esc_html_e( 'This session has been archived.', 'ldmessenger' ); ?></p>
               </div>
          <?php endif; ?>

	</div><!-- #comments -->

	<?php
	return ob_get_clean();
}

function ldms_remove_comments_are_closed($translated_text, $untranslated_text, $domain) {
	if ( $untranslated_text == 'Comments are closed.' ) {
		return '';
	}
	return $translated_text;
}
