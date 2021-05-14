<?php
add_shortcode( 'sessions_widget', 'ldms_sessions_widget' );
function ldms_sessions_widget( $atts ) {
	$atts = shortcode_atts(
		array(
			'access' 	=> 'false',
			'target'	=> false,
		), $atts, 'sessions_widget' );


	ob_start();

	ldma_custom_assets();

	ldms_messages_widget( $atts['access'], $atts['target'] );

	wp_reset_query(); wp_reset_postdata();

	return ob_get_clean();

}

add_shortcode( 'sessions_text_link', 'ldms_sessions_text_link_shortcode' );
function ldms_sessions_text_link_shortcode( $atts = null ) {

	ob_start();

	ldma_custom_assets();

	ldms_sessions_text_link( $atts );

	// For good measure, we call a lot of queries all over the place
	wp_reset_postdata(); wp_reset_query();

	return ob_get_clean();

}

add_shortcode( 'create_session', 'ldms_create_message_shortcode' );
function ldms_create_message_shortcode($atts) {
	$atts = shortcode_atts(
		array(
			'access' => 'false',
		), $atts, 'create_session' );

	ob_start();

	do_action('ldms_before_message_form');

	$user_id = get_current_user_id();

	if( !is_user_logged_in() ) {

		global $post;

		if( has_shortcode( $post->post_content, 'private_sessions' ) ) return;

		wp_login_form( array( 'redirect' => get_permalink() ));

		return ob_get_clean();

	}
	if( $atts['access'] == 'false' ){
		if( !ldms_can_start_session() && !current_user_can('start_session') ) return;
	}

	ldms_shortcode_assets();
	wp_enqueue_script( 'ldms-front' );  ?>

		<div id="ldms-new-session">

			<form id="ldmsform" action="" method="post" enctype="multipart/form-data" >

				<h2><?php esc_html_e( 'New Session', 'ldmessenger' ); ?></h2>

				<?php
				$users = get_user_ids_leader_can_message( $user_id );
				if( $users && !empty($users) ): ?>
					<p>
						<label for="ldms-send-to"><?php esc_html_e( 'Start Session With', 'ldmessenger' ); ?></label>
						<select id="ldms-send-to" name="ldms-send-to">
							<?php ldms_display_user_dropdown($user_id); ?>
						</select>
					</p>

					<p>
						<label for="ldms-session-title"><?php esc_html_e( 'Session Title', 'ldmessenger' ); ?></label>
						<input id="ldms-session-title" type="text" name="ldms-session-title" value="" />
					</p>

					<p>
						<?php
						$settings = apply_filters( 'ldms_create_message_wysiwyg', array(
							'textarea_name'		=>	'ldms-contents',
							'media_buttons'		=>	false,
						) );
						wp_editor( '', 'ldms-contents', $settings ); ?>
					</p>

					<?php wp_nonce_field( 'lmds_message_nonce', 'nonce' ); ?>

					<input id="ldms-sender" type="hidden" name="ldms-sender" value="<?php echo esc_attr($user_id); ?>" />
				    <input type="submit" class="ldms-btn" value="<?php esc_attr_e('Start Session', 'ldmessenger' ); ?>" />
				<?php else: ?>
					<div class="ldms-notice">
						<p><?php esc_html_e( 'You don\'t currently have anyone to send a message to.', 'ldmessenger' ); ?></p>
					</div>
				<?php endif; ?>

		    </form>

		</div> <!--/#ldms-new-session-->

		<script>
			jQuery(document).ready(function($) {
				$('#ldms-send-to').select2();
			});
		</script>

	<?php
	do_action('ldms_after_message_form');

	return ob_get_clean();

}

add_shortcode( 'private_sessions', 'ldms_display_messages_shortcode' );
function ldms_display_messages_shortcode($atts) {

	ob_start();

	$user_id = get_current_user_id();

	if( !is_user_logged_in() ) {

		wp_login_form( array( 'redirect' =>	get_permalink() ) );

		return ob_get_clean();

	}

	if( !ldms_user_can_view() ) {
		echo '<h3>' . __('You do not have access to any messages.','ldmessenger') . '</h3>';
		return ob_get_clean();
	}

	if( isset($_SERVER['HTTP_REFERER']) ): ?>
		<div class="ldms-back-button">
			<p><a href="<?php echo esc_url($_SERVER['HTTP_REFERER']); ?>">&laquo; <?php esc_html_e( 'Back', 'ldmessenger' ); ?></a></p>
		</div>
	<?php endif;

	ldms_list_messages($user_id);

	ldms_kill_comments();

	return ob_get_clean();

}

add_shortcode( 'message_counter', 'ldms_message_counter' );
function ldms_message_counter() {

	if( !is_user_logged_in() ) {
		return;
	}

	$new_messages = ldms_get_user_unread_message_count();

	if( !$new_messages || empty($new_messages) ) {
		$count = 0;
	} else {
		$count = count($new_messages);
	}

	return '<span class="ldms-message-count">' . $count . '</span>';

}
