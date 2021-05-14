<?php
add_action( 'wp_footer', 'ldms_messages_tab_placement' );
function ldms_messages_tab_placement() {

	$position 	= ldms_get_option('ldms_indicator_location');
	$skips 		= array(
		'shortcode',
		'widget'
	);

	if( !in_array( $position, $skips ) ) ldms_messages_tab();

}

function ldms_messages_tab() {

	wp_reset_postdata();

	global $post;

	// First big gate
	if( !current_user_can('start_session') ) {

		if( !is_user_logged_in() ) {
			return;
		}

	    // Skip if the shortcode is displayed, the user is not logged in or the user can't view
	    if( isset( $post->post_content) && has_shortcode( $post->post_content, 'private_sessions' ) ) {
		    return;
	    }

	    if( !ldms_can_start_session() && !ldms_user_has_messages() ) {
		    return;
	    }

	    // Normal users who don't have messages shouldn't see the tab
	    // if( !ldms_is_group_leader() && !ldms_user_has_messages() && !current_user_can('start_session') ) return;

	}

	$location		= ldms_get_option('ldms_indicator_location');
	$class 		= 'ldms-located-' . $location;
     $label 		= '';
     $new_messages 	= ldms_get_user_unread_message_count();
	$placement	= ( $location == 'top' ? 'bottom' : 'top' );
	$tooltip		= ( $location == 'right' ? '' : 'js-ldms-tooltip' ); ?>

    <div class="ldms-message-tab <?php esc_attr_e($class); ?>">
        <?php if( ldms_can_start_session() ): ?>
            <a class="ldms-new-message-link <?php esc_attr_e($tooltip); ?>" data-toggle="tooltip" data-placement="<?php esc_attr_e($placement); ?>" title="<?php esc_html_e( 'Create a New Private Session', 'ldmessenger' ); ?>" href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>#ldms-new-session"><img src="<?php echo esc_url( LDMA_URL . 'assets/svg/write.svg' ); ?>" alt="<?php esc_attr_e( 'New Message', 'ldmessenger' ); ?>"></a>
        <?php endif; ?>
        <a href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ) ); ?>">
            <span class="ldms-message-count <?php esc_attr_e($tooltip); ?>" data-toggle="tooltip" data-placement="<?php esc_attr_e($placement); ?>" title="<?php echo esc_html( $new_messages ) . _n( ' New Message', ' New Messages', $new_messages, 'ldmessenger' ); ?>"><?php echo esc_html($new_messages); ?></span>
            <?php esc_html_e( 'My Sessions', 'ldmessenger' ); ?>
        </a>
    </div>
    <?php
}

function ldms_messages_widget( $user_can_start, $target = false ) {

	global $post;

	// Don't continue if Divi

	if( isset($_GET['et_fb']) ) {
		return;
	}

     // Skip if the shortcode is displayed, the user is not logged in or the user can't view
    if( ( has_shortcode( $post->post_content, 'display_messages' ) || !is_user_logged_in() ) || !ldms_user_can_view() && $user_can_start === 'false' ) {
	    return;
    }

	if( $user_can_start === 'false' ){
    		// Normal users who don't have messages shouldn't see the tab
    		if( !ldms_is_group_leader() && !ldms_user_has_messages() && !current_user_can('manage_options') ) return;
	}

	$target = ( $target ? 'target="' . $target . '"' : '' );

    $label = '';
    $new_messages = ldms_get_user_unread_message_count(); ?>

    <div id="ldms-message-widget">

        <?php
		if( ldms_is_group_leader() || current_user_can('manage_options') || ldms_can_start_session() || $user_can_start != 'false' ): ?>
            <p>
				<a class="ldms-btn" <?php echo $target; ?> href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>#ldms-new-session">
					<span class="ldms-btn-icon">
						<img src="<?php echo esc_url( LDMA_URL . '/assets/svg/write.svg' ); ?>" alt="<?php esc_attr_e( 'New Session', 'ldmessenger' ); ?>">
					</span>
					<span class="ldms-btn-text">
						<?php esc_html_e( 'New Session', 'ldmessenger' ); ?>
					</span>
				</a>
			</p>
        <?php endif; ?>
        <p>
			<a class="ldms-btn" href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>">
				<span class="ldms-btn-icon" title="<?php echo esc_attr( $new_messages ) . __( ' New Messages', 'ldmessenger' ); ?>">
					<span class="ldms-btn-msgs">
						<?php echo esc_html($new_messages); ?>
					</span>
				</span>
				<span class="ldms-btn-text">
	            	<?php esc_html_e( 'My Sessions', 'ldmessenger' ); ?>
				</span>
        	</a>
		</p>
    </div>

    <?php
}

function ldms_sessions_text_link( $atts = null ) {

	global $post;

	// Skip if the shortcode is displayed, the user is not logged in or the user can't view
	if( has_shortcode( $post->post_content, 'display_messages' ) || !is_user_logged_in() || !ldms_user_can_view() ) return;

	// Normal users who don't have messages shouldn't see the tab
	if( !ldms_is_group_leader() && !ldms_user_has_messages() &&  !current_user_can('manage_options') ) return;

	$label 			= '';
	$new_messages 	= ldms_get_user_unread_message_count();

	$markup = ( !isset($atts['link']) && $atts['link'] != 'off' ?
			array(
				'<a class="ldms-text-link" href="' . esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ) . '">',
				'</a>'
			) : array(
				'<span class="ldms-text-link">',
				'</span>'
			)
		); ?>

	<?php echo wp_kses_post($markup[0]); ?>
		<span class="ldms-btn-msgs">
			<?php echo esc_html($new_messages); ?>
		</span>
		<span class="ldms-btn-text">
			<?php esc_html_e( 'My Sessions', 'ldmessenger' ); ?>
		</span>
	<?php echo wp_kses_post($markup[1]);

}

function ldms_list_messages( $user_id = NULL ){

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == NULL ? $cuser->ID : $user_id );
	$current_url		= ldms_get_current_url();
	$status			= ( isset($_GET['status']) ? $_GET['status'] : 'active' );
	$sessions 		= ldms_get_user_messages( $user_id, $_GET['ldsearch'], $_GET['status'] );
	$unread_messages 	= get_user_meta( $user_id, '_ldms_unread_message', false );

	if( $sessions && !empty($sessions) ) { ?>
		<div id="ldms-message-list">
			<div class="ldms-message-actions">
				<div class="ldms-tabs">
					<a href="<?php echo esc_url( add_query_arg( 'status', 'active', $current_url ) ); ?>" <?php if( $status == 'active' ) echo 'class="active"';  ?>><?php echo esc_html__( 'Active', 'ldmessenger' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'status', 'archived', $current_url ) ); ?>" <?php if( $status == 'archived' ) echo 'class="active"';  ?>><?php echo esc_html__( 'Archived', 'ldmessenger' ); ?></a>
				</div> <!--/.ldms-tabs-->
				<div class="lds-search-messages">
					<form method="get" action="<?php echo esc_url($current_url); ?>">
						<?php
						$value = ( isset($_GET['ldsearch']) ? $_GET['ldsearch'] : '' ); ?>
						<input type="text" placeholder="<?php echo esc_attr_e( 'Search...', 'ldmessenger' ); ?>" value="<?php echo esc_attr($value); ?>" name="ldsearch">
						<input type="submit" value="<?php esc_attr_e( 'Go', 'ldmessenger' ); ?>">
					</form> <!--/form-->
				</div> <!--/.lds-search-messages-->
			</div>
			<table>
				<thead>
					<tr>
						<th></th>
						<th><?php esc_html_e( 'Title', 'ldmessenger' ); ?></th>
						<th class="ldms-text-center"><?php esc_html_e( 'Messages', 'ldmessenger' );?></th>
						<th><?php esc_html_e( 'Started', 'ldmessenger' );?></th>
						<th><?php esc_html_e( 'Last Response', 'ldmessenger' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach( $sessions as $messages ):

					if( !$messages ) {
						continue;
					}

					while( $messages->have_posts() ): $messages->the_post();

						global $post;

						$message_id = $post->ID;

						$user 			= wp_get_current_user();
						$sent_to_id 	= get_post_meta( $message_id, 'sent_to', true);
						$session_title 	= get_post_meta( $message_id, 'session_title', true);

						$author			= get_user_by( 'id', $post->post_author );
						$sent_to 		= get_user_by( 'id', $sent_to_id );
						$username		= ( $user->ID == $sent_to_id ? $author->display_name : $sent_to->display_name );

						$result 		= '';
						$message_count 	= intval(get_comments_number( $message_id ) );
						$message_count++;
						$class 			= ( in_array( $message_id, $unread_messages ) ? 'unread-messages' : '' );

						$stared_sessions = get_user_meta( $user->ID, 'stared_sessions', false );

						if( in_array( $message_id, $stared_sessions ) ) {
							$class .= ' ldms-stared';
							$status = 'stared';
						} else {
							$status = 'unstared';
						}

						if( empty($session_title) ) {
							$session_title = __( 'Session with ' . $username );
						}

					    $args = array(
					    	'number' => '1',
					        'post_id' => $message_id
					    );
					    $comments 	= get_comments($args);

					    if( !empty($comments) ){
							$last_comment 	= end($comments);
							$link 			= get_comment_link($last_comment);
							$last_responded = get_comment_date( get_option('date_format') . ' ' . get_option('time_format'), $last_comment );
						}else{
							$link = get_permalink($message->ID);
							$last_responded = get_the_time( get_option('date_format') . ' - ' . get_option('time_format') );
						}?>

						<tr class="<?php echo esc_attr($class); ?>" data-js-url="<?php echo esc_url($link); ?>">
							<td>
								<a class="ldms-star-session" data-post_id="<?php echo esc_attr($message_id); ?>" href="#" data-status="<?php echo esc_attr($status); ?>" aria-label="<?php esc_attr_e( 'Star session', 'ldmessenger' ); ?>"></a>
							</td>
							<td data-label="<?php _e('Title', 'ldmessenger');?>">
								<strong><?php echo esc_html($session_title); ?></strong> <span class="ld-username"><?php echo esc_html($username); ?></span>
							</td>
							<td data-label="<?php _e('Messages', 'ldmessenger');?>" class="ldms-text-center"><?php echo esc_html($message_count); ?> <img class="ldms-comment-icon" src="<?php echo esc_url( LDMA_URL . '/assets/svg/comments.svg' ); ?>" alt="<?php esc_attr_e( 'Comments', 'ldmessenger' ); ?>"></td>
							<td data-label="<?php _e('Started', 'ldmessenger');?>" ><?php esc_html_e(get_the_date( get_option('date_format') ) ); ?></td>
							<td data-label="<?php _e('Last Response', 'ldmessenger');?>" ><?php esc_html_e($last_responded); ?></td>
							<td class="ldms-message-list__actions">
								<div class="ldms-action-wrapper">
									<a class="link ldms-view-message" href="<?php echo esc_url($link); ?>"><?php esc_html_e('View','ldmessenger');?></a>
									<?php if( current_user_can( 'delete_session' ) ):
										$status = ( isset($_GET['status']) && $_GET['status'] == 'archived' ? 'unarchive' : 'archive' ); ?>
										<a class="link js-ldms-tooltip ldms-archive-message" data-toggle="tooltip" data-placement="top" data-nonce="<?php echo wp_create_nonce( 'ldms-delete-nonce' );?>" data-status="<?php echo esc_attr($status); ?>" data-message="<?php echo esc_attr($message_id); ?>" href="#"><img class="ldms-archive-icon" src="<?php echo esc_url( LDMA_URL . '/assets/svg/archive.svg' ); ?>" alt="<?php esc_attr_e( 'Archive', 'ldmessenger' ); ?>" title="<?php esc_attr_e( 'Archive Session', 'ldmessenger' ); ?>"></a>
										<a class="link js-ldms-tooltip ldms-delete-message" data-toggle="tooltip" data-placement="top" data-nonce="<?php echo wp_create_nonce( 'ldms-delete-nonce' );?>" data-message="<?php echo $message_id; ?>" href="#"><img class="ldms-delete-icon" src="<?php echo esc_url( LDMA_URL . '/assets/svg/trash.svg' ); ?>" alt="<?php esc_attr_e( 'Delete', 'ldmessenger' ); ?>"></a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endwhile;
				endforeach; ?>
				</tbody>
			</table>
			<?php
			if ($messages->max_num_pages > 1) { // check if the max number of pages is greater than 1  ?>
			  <nav class="ldms-next-posts">
			    <div class="ldms-prev-posts-link">
			      <?php echo get_next_posts_link( __( 'Older Sessions', 'ldmessenger' ) , $messages->max_num_pages ); // display older posts link ?>
			    </div>
			    <div class="ldms-next-posts-link">
			      <?php echo get_previous_posts_link( __( 'Newer Sessions', 'ldmessenger' ) ); // display newer posts link ?>
			    </div>
			  </nav>
			<?php } ?>
		</div>
	<?php }else{?>
			<div class="messages"><?php esc_html_e('No Messages','ldmessenger');?></div>
	<?php }

	 wp_reset_postdata(); wp_reset_query(); //endwhile

}


// Check if user can view the messages
function ldms_content_filter($content) {

    if( !is_singular( 'ldms_message') ) return $content;

    if( !is_user_logged_in() ) {

        ldms_kill_comments();

        return wp_login_form( array( 'redirect' => get_the_permalink(), 'echo' => false ) );

    }

    if( !ldms_user_can_view_thread() ) {
        ldms_kill_comments();
        return '<h3>'.__('You do not have access to view this message','ldmessenger').'</h3>';
    }

    /**
     * No longer mark this message as being unviewed
     *
     */
    $cuser = wp_get_current_user();
    delete_user_meta( $cuser->ID, '_ldms_unread_message', get_the_ID() );

    // Include the template;
	// include(LDMA_PATH . '/templates/ldms_comments.php');

	ob_start(); ?>

	<div id="ldms-return-to-message">
		<p><a href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>">&laquo; <?php esc_html_e( 'Back to Private Sessions', 'ldmessenger' ); ?></a></p>
	</div>

	<?php

	if( isset($_GET['success']) ): ?>
		<div class="ldms-success">
			<p><?php esc_html_e( 'Private Session Started', 'ldmessenger' ); ?></p>
		</div>
	<?php endif;

	$tabs = apply_filters( 'ldms_session_tabs', array(
		'discussions' => array(
			'title'	=>	__( 'Discussions', 'ldmessenger' ),
			'content'	=>	ldms_discussion_content(),
		),
		'attachments' => array(
			'title'	=>	__( 'Files', 'ldmessenger' ),
			'content'	=>	ldms_comment_files(),
		)
	) );

	if( !empty($tabs) ): ?>
		<div class="ldms-session">

			<div class="ldms-session__tabs">
				<?php $i = 0; foreach( $tabs as $tab ): $class = 'ldms-session__tabs-tab' . ( $i == 0 ? ' is-active' : '' );
					?>
					<div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr( 'tab-parent-' . sanitize_title($tab['title']) ); ?>">
						<button data-target="<?php echo esc_attr( 'tab-' . sanitize_title($tab['title']) ); ?>">
							<?php echo esc_html($tab['title']); ?>
						</button>
					</div>
				<?php $i++; endforeach; ?>
			</div>

			<div class="ldms-session__content">
				<?php $i = 0; foreach( $tabs as $tab ): $class = 'ldms-session__content-tab' . ( $i == 0 ? ' is-active' : '' ); ?>
					<div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr( 'tab-' . sanitize_title($tab['title']) ); ?>">
						<?php echo $tab['content']; ?>
					</div>
				<?php $i++; endforeach; ?>
			</div>

		</div> <!--/.ldms-session-->
	<?php
	endif;

	return ob_get_clean();

}

add_action( 'wp_head', 'ldms_noindex_nofollow');
function ldms_noindex_nofollow() {

	if( get_post_type() != 'ldms_message' ) return;

	echo '<meta name="robots" content="noindex,nofollow">';

}

function ldms_discussion_content() {

	ob_start();

	include( LDMA_PATH . '/templates/partials/original-comment.php' );

	echo ldms_comments();

	return ob_get_clean();

}

function ldms_comment_files() {

	ob_start();

	$comment_args = apply_filters( 'ldms_comment_args', array(
		'post_id' => get_the_id(),
		'status' 	=> 'all',
		'order' 	=> 'ASC'
	) );

	$comments = get_comments( $comment_args );

	if( empty($comments) || !class_exists('wpCommentAttachment') ): ?>

		<div class="ldms-notice"><?php esc_html_e( 'No files shared on this session', 'ldmessenger' ); ?></div>

	<?php else: ?>

		<div class="ldms-file-list">
			<?php
			$commentAttachment = new wpCommentAttachment();

			foreach( $comments as $comment ):

				$attachmentId = get_comment_meta( $comment->comment_ID, 'attachmentId', TRUE );

				if( is_numeric($attachmentId) && !empty($attachmentId) ):

				    // atachement info
				    $attachmentLink = wp_get_attachment_url($attachmentId);
				    $attachmentMeta = wp_get_attachment_metadata($attachmentId);
				    $attachmentName = basename(get_attached_file($attachmentId));
				    $attachmentType = get_post_mime_type($attachmentId);

				    $typeSplit = explode( '/', $attachmentType );

				    $attachmentRel  = '';

				    $author = get_user_by( 'slug', $comment->comment_author ); ?>

				    <div class="ldms-file__file <?php echo esc_attr( sanitize_title('file-type-' . $typeSplit[0] ) ); ?>">
					    <div class="ldms-file__body">
						    <?php
						    if( in_array( $attachmentType, $commentAttachment->getImageMimeTypes() ) ):
						        $thumbnail = wp_get_attachment_image($attachmentId, ATT_TSIZE); ?>
							   <a href="<?php echo esc_url($attachmentLink); ?>" target="_new" rel="lightbox" class="has-thumbnail">
								   <span class="ldms-file__file-thumb">
									   <?php echo $thumbnail; ?>
								   </span>
								   <span class="ldms-file__file-name">
								   	<?php echo esc_html($attachmentName); ?>
							   	   </span>
							   </a>
						    <?php
						    elseif( in_array( $attachmentType, $commentAttachment->getAudioMimeTypes() ) ):
						    	   echo do_shortcode('[audio src="'. $attachmentLink .'"]');
						    elseif( in_array( $attachmentType, $commentAttachment->getVideoMimeTypes() ) ):
						        if( shortcode_exists('video') ):
						    	    	echo do_shortcode('[video src="'. $attachmentLink .'"]');
							   endif;
						   else: ?>
							   <a href="<?php echo esc_url($attachmentLink); ?>" target="_new">
								   <span class="ldms-file__file-name">
						    			<?php echo esc_html($attachmentName); ?>
   								</span>
	    						   </a>
						   <?php endif; ?>
				   	    </div>
					    <div data-target="<?php echo esc_attr( '#comment-' . $comment->comment_ID ); ?>" class="ldms-file__meta">
						    <span class="ldms-file__meta-date">
						    		<?php echo _e( 'Posted on ', 'ldmessenger' ) . date_i18n( get_option('date_format'), strtotime($comment->comment_date) ); ?>
					    	    </span>
						    <span class="ldms-file__meta-author">
 						    		<?php echo _e( 'by', 'ldmessenger') . ' '  . $author->data->display_name; ?>
						    </span>
					    </div>
				    </div>

			    <?php
			    endif;
		    endforeach; ?>
	    </div>

	<?php
	endif;

	return ob_get_clean();

}
