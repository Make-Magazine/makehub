<?php
function learndash_notes_license_menu() {

	$title = ( LD_FINAL_NAME == 'LearnDash Notes' ? __( 'LearnDash Notes', 'sfwd-lms' ) : __( 'User Notes', 'sfwd-lms' ) );

	add_options_page( $title, $title, 'manage_options', 'learndash-notes-license', 'learndash_notes_license_page' );
}
add_action('admin_menu', 'learndash_notes_license_menu');

function learndash_notes_license_page() {
	$license 	= get_option( 'learndash_notes_license_key' );
	$status 	= get_option( 'learndash_notes_license_status' );

	if( isset( $_GET[ 'nt_migrate_notes' ] ) ) {
		ldnt_migrate_notes();
	}

	?>
	<div class="wrap snap-wrap">

		<div class="snap-branding">
			<a href="https://www.snaporbital.com/" target="_new">
				<img src="<?php echo esc_url(LD_NOTES_URL . '/assets/img/snaporbital-color.png'); ?>" alt="Snap Orbital">
			</a>
		</div>


		<form method="post" action="options.php">
			<?php
			/** Enqueue scripts **/
			settings_fields('learndash_notes_license');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_style('wp-color-picker');


			global $wp_roles;

			// Populate variables

			$single_note_page		= get_option('ldnt_single_note_page');
			$supported_types		= (array) get_option( 'ldnt_supported_types' );
			$links					= get_option( 'ldnt_link_new_windows', 'yes' );
			$show_admin				= get_option( 'ldnt_show_notes_in_admin', 'no' );
			$placement				= get_option( 'nt_noteicon_placement', 'top' );
			$hide_mobile			= get_option( 'nt_noteicon_hide_on_mobile', 'no' );
			$icon_style				= get_option( 'nt_noteicon_style', 'tab' );
			$colors 				= ldnt_get_the_color_settings();
			$doc_type				= get_option( 'ldnt_doc_type', 'doc' );
			$autosave				= get_option( 'ldnt_autosave', 'no' ); ?>

			<div class="postbox snap-box">
				<div class="snap-header snap-primary-header">
					<h2><?php esc_html_e( 'LearnDash Notes', 'sfwd-lms' ); ?></h2>
					<p class="snap-description"><?php esc_html_e( 'Give learners the ability to take, edit, save, and share notes on your content!', 'sfwd-lms' ); ?> <span class="snap-pipe">|</span> <a href="http://docs.snaporbital.com/article-categories/notes/" target="_new"><?php esc_html_e( 'Notes Documentation', 'sfwd-lms' ); ?></a></p>
				</div>

				<div class="snap-focus-field">
					<div class="snap-legend">
						<h3><?php esc_html_e( 'Configure note taking capabilities', 'sfwd-lms' ); ?></h3>
						<p class="snap-description"><?php esc_html_e( 'Enable what content types users can take notes on.', 'sfwd-lms' ); ?></p>
					</div>
					<div class="snap-options">
						<div class="snap-input snap-input-checkboxes">
							<label class="snap-label"><?php esc_html_e( 'Notes can be taken on', 'sfwd-lms' ); ?></label>
							<span class="snap-input-option snap-checkboxes">
								<?php
								$post_types = get_post_types( array( 'public' => true ), 'objects' );
								foreach( $post_types as $type ):
									if( $type->name == 'coursenote' ) {
										continue;
									} ?>
									<span class="snap-checkbox-option"><label for="ldnt_notes_<?php echo esc_attr( $type->name ); ?>"><input type="checkbox" name="ldnt_supported_types[]" value="<?php echo esc_attr( $type->name ); ?>" id="ldnt_notes_<?php echo esc_attr( $type->name ); ?>" <?php if( in_array( $type->name, $supported_types ) ) { echo 'checked'; } ?>> <?php echo esc_html( $type->labels->name ); ?></label></span>
								<?php endforeach; ?>
							</span>
						</div>
						<?php
						/**
						 * Conditionally show groups
						 * @var [type]
						 */
						$groups = learndash_get_groups(true);

						if( $groups && !empty($groups) ): ?>

							<div class="snap-input snap-input-checkboxes">
								<label for="note-support" class="snap-label"><?php esc_html_e( 'Only allow notes for users within specified LearnDash Groups', 'sfwd-lms' ); ?></label>
								<div class="snap-input-option snap-checkboxes">
									<?php
									$active_groups = get_option( 'ldnt_groups' );

									if( !$active_groups ) {
										$active_groups = array();
									}

									foreach( $groups as $group_id ) {

										$checked = '';

										foreach( $active_groups as $key => $status ) {
											if( $key == $group_id && $status == 'true' ) {
												$checked = 'checked';
											}
										}

										$group_object = get_post($group_id);

										echo '<span class="snap-checkbox-option"><label for="ldnt-group-' . $group_id . '"><input id="ldnt-group-' . $group_id . '" name="ldnt_groups[' . $group_id . ']" type="checkbox" value="true" ' . $checked . '>'. get_the_title($group_id) .'</label></span>';

									} ?>
								</div> <!--/.snap-input-option-->

							</div> <!--/.snap-input-->

						<?php endif; ?>

						<div class="snap-input snap-input-checkboxes">
							<label for="note-support-roles" class="snap-label"><?php esc_html_e( 'Only allow notes for users with specified WordPress Roles', 'sfwd-lms' ); ?></label>
							<div class="snap-input-option snap-checkboxes">
								<?php
								$active_roles = get_option('ldnt_roles');

								if( !$active_roles ) {
									$active_roles = array();
								}

								global $wp_roles;

								if ( ! isset( $wp_roles ) ){
									$wp_roles = new WP_Roles();
								}

								$roles = $wp_roles->get_names();

								//remove defaults
								unset($roles['administrator']);
								unset($roles['editor']);
								unset($roles['group_leader']);

								foreach( $roles as $role_value => $role_name ) {

									$checked = '';

									foreach( $active_roles as $slug => $value ){

										if( $slug == $role_value && $value == 'true'){
											$checked = 'checked';
										}

									}
									echo '<span class="snap-checkbox-option"><label for="ldnt-role-' . $role_value . '"><input id="ldnt-role-' . $role_value . '" name="ldnt_roles[' . $role_value . ']" type="checkbox" value="true" ' . $checked . '>' . $role_name . '</label></span>';
								} ?>
							</div> <!--/.snap-input-option-->
						</div> <!--/.snap-input-->
					</div>
				</div>
			</div> <!--/.snap-focus-field-->

			<div class="postbox snap-box">
				<div class="snap-header">
					<h2><?php esc_html_e( 'License', 'sfwd-lms' ); ?></h2>
				</div>
				<div class="snap-content">
					<div class="snap-options">

						<?php if(isset($_GET['lds_activate_response'])): ?>
							<div class="lds-status-message" style="max-height: 500px; overflow: scroll; border: 1px solid #ddd; padding: 15px;">
								<pre>
									<?php nt_check_activation_response(); ?>
								</pre>
							</div>
						<?php endif; ?>

						<table class="form-table snap-formtable">
							<tbody>
								<tr valign="top">
									<th scope="row" valign="top">
										<?php _e('License Key', 'sfwd-lms'); ?>
									</th>
									<td>
										<div class="snap-input-option snap-text">
											<input id="learndash_notes_license_key" name="learndash_notes_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
											<label class="description snap-after-input-description" for="learndash_notes_license_key"><?php _e('Enter your license key'); ?></label>
										</div>
									</td>
								</tr>
								<?php if( false !== $license ) { ?>
									<tr valign="top">
										<th scope="row" valign="top">
											<?php _e('Activate License', 'sfwd-lms' ); ?>
										</th>
										<td>
											<?php if( $status !== false && $status == 'valid' ) { wp_nonce_field( 'learndash_notes_nonce', 'learndash_notes_nonce' ); ?>
												<span style="color:green; padding: 5px 10px; border-radius: 8px; background: #fff;"><?php _e('active', 'sfwd-lms'); ?></span>
												<input type="submit" class="button-secondary" name="learndash_notes_license_deactivate" value="<?php _e('Deactivate License','learndash-skins'); ?>"/>
											<?php } else {
												wp_nonce_field( 'learndash_notes_nonce', 'learndash_notes_nonce' );
												if( $license && !empty($license) ): ?>
													<input type="submit" class="button-secondary" name="learndash_notes_license_activate" value="<?php _e('Activate License', 'sfwd-lms'); ?>"/>
													<a class="button" href="<?php echo admin_url(); ?>admin.php?page=learndash-notes-license&settings-updated=true&lds_activate_response=true">Check Activation Message</a>
												<?php else: ?>
													<label class="snap-after-input-description description"><?php echo esc_html_e( 'Please enter a license key and save your settings to activate', 'sfwd-lms' ); ?></label>
												<?php endif; ?>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table> <!--/.form-table-->

					</div> <!--/.snap-options-->
				</div> <!--/.snap-content-->
			</div> <!--/.snap-box-->

			<script>
				jQuery(document).ready(function($) {
					$( '.wp-color-picker' ).wpColorPicker();
				});
			</script>

			<div class="postbox snap-box">
				<div class="snap-header">
					<h2><?php esc_html_e( 'Templates', 'sfwd-lms' ); ?></h2>
				</div>
				<div class="snap-content">
					<div class="snap-options">
						<div class="snap-input snap-input-select">
							<label for="nt-all-notes-page" class="snap-label">
								<?php esc_html_e( 'All notes page', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_all_notes_page">
									<?php
									$all_notes_page = get_option( 'ldnt_all_notes_page' );
									$args 			= array(
														'post_type'			=>	'page',
														'posts_per_page'	=>	-1,
													);

									$pages 			= new WP_Query( $args ); ?>

									<option value=""></option>

									<?php if( $pages->have_posts() ): while( $pages->have_posts() ): $pages->the_post(); global $post; ?>
										<option value="<?php echo esc_attr( $post->ID ); ?>" <?php if( $all_notes_page == $post->ID ) { echo 'selected'; }?>><?php the_title(); ?></option>
									<?php endwhile; endif; ?>

								</select>
								<label for="nt-all-notes-page" class="snap-after-input-description description"><?php esc_html_e( 'Recommended you use the', 'sfwd-lms' ); ?> <span class="snap-pre">[learndash_my_notes]</span> <?php esc_html_e( 'shortcode', 'sfwd-lms' ); ?>.</label>
							</span>
						</div> <!--/.snap-input-->
						<div class="snap-input snap-input-select">
							<label for="ldnt_single_note_page" class="snap-label">
								<?php esc_html_e( 'Single notes page', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_single_note_page">
									<option value=""><?php esc_html_e( 'Theme default', 'sfwd-lms' ); ?></option>
									<?php if( $pages->have_posts() ): while( $pages->have_posts() ): $pages->the_post(); global $post; ?>
										<option value="<?php echo esc_attr( $post->ID ); ?>" <?php if( $single_note_page == $post->ID ) { echo 'selected'; }?>><?php the_title(); ?></option>
									<?php endwhile; endif; ?>
								</select>
								<label for="ldnt_single_note_page" class="snap-after-input-description description"><?php esc_html_e( 'Select a page to use a template for viewing individual notes', 'sfwd-lms' ); ?></label>
							</span>
						</div> <!--/.snap-input-select-->
						<?php
						$default_title = get_option('learndash_notes_default_title');
						$default_body  = get_option('learndash_notes_default_body'); ?>
						<div class="snap-input snap-input-text">
							<label for="learndash_notes_default_title" class="snap-label">
								<?php esc_html_e( 'Default note editor title', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-text">
								<input id="learndash_notes_default_title" name="learndash_notes_default_title" type="text" class="regular-text" value="<?php esc_attr_e( $default_title ); ?>" />
								<label for="learndash_notes_default_title" class="snap-after-input-description description"><?php esc_html_e( 'Leave empty to inherit the page title', 'sfwd-lms' ); ?></label>
							</span>
						</div>
						<div class="snap-input snap-input-text">
							<label for="learndash_notes_default_body" class="snap-label">
								<?php esc_html_e( 'Default note editor body', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-text">
								<?php wp_editor( $default_body , 'learndash_notes_default_body' , array('textarea_name'=> 'learndash_notes_default_body' ) ); ?>
								<label for="learndash_notes_default_body" class="snap-after-input-description description"><?php esc_html_e( 'Leave empty for Notes:', 'sfwd-lms' ); ?></label>
							</span>
						</div>
					</div> <!--/.snap-options-->
				</div> <!--/.snap-content-->
			</div> <!--/.snap-box-->

			<div class="postbox snap-box">
				<div class="snap-header">
					<h2><?php esc_html_e( 'Settings', 'sfwd-lms' ); ?></h2>
				</div>
				<div class="snap-content">
					<div class="snap-options">
						<div class="snap-input snap-input-select">
							<label for="ldnt_doc_type" class="snap-label">
								<?php esc_html_e( 'Save document as', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_doc_type">
									<option value="doc" <?php if( $doc_type == 'doc' ) { echo 'selected'; } ?>><?php esc_html_e( 'Word Doc Compatible', 'sfwd-lms' ); ?></option>
									<option value="rtf" <?php if( $doc_type == 'rtf' ) { echo 'selected'; } ?>><?php esc_html_e( 'Rich Text File', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div>
						<div class="snap-input snap-input-select">
							<label for="ldnt_autosave" class="snap-label">
								<?php esc_html_e( 'Enable Auto-save', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_autosave">
									<option value="yes" <?php if( $autosave == 'yes' ) { echo 'selected'; } ?>><?php esc_html_e( 'Yes', 'sfwd-lms' ); ?></option>
									<option value="no" <?php if( $autosave == 'no' ) { echo 'selected'; } ?>><?php esc_html_e( 'No', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div>
						<div class="snap-input snap-input-select">
							<label for="ldnt_show_notes_in_admin" class="snap-label">
								<?php esc_html_e( 'Show notes in the admin', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_show_notes_in_admin">
									<option value="yes" <?php if( $show_admin == 'yes' ) { echo 'selected'; } ?>><?php esc_html_e( 'Yes', 'sfwd-lms' ); ?></option>
									<option value="no" <?php if( $show_admin == 'no' ) { echo 'selected'; } ?>><?php esc_html_e( 'No', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div>
						<div class="snap-input snap-input-select">
							<label for="nt-link-new-windows" class="snap-label">
								<?php esc_html_e( 'Open links in a new window', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="ldnt_link_new_windows">
									<option value="yes" <?php if( $links == 'yes' ) { echo 'selected'; } ?>><?php esc_html_e( 'Yes', 'sfwd-lms' ); ?></option>
									<option value="no" <?php if( $links == 'no' ) { echo 'selected'; } ?>><?php esc_html_e( 'No', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div>
					</div>
				</div> <!--/.snap-content-->
			</div> <!--/.snap-box-->

			<script>
				jQuery(document).ready(function($) {

					function nt_note_side_placement_toggle() {
						if( $('#nt_noteicon_placement').val() == 'bottom' || $('#nt_noteicon_placement').val() == 'top' ) {
							$('#nt-note-placement-right').show();
						} else {
							$('#nt-note-placement-right').hide();
						}
					}

					$('#nt_noteicon_placement').change(function(e) {
						nt_note_side_placement_toggle();
					});

					nt_note_side_placement_toggle();

				});
			</script>

			<div class="postbox snap-box">
				<div class="snap-header">
					<h2><?php esc_html_e( 'Apperance', 'sfwd-lms' ); ?></h2>
				</div>
				<div class="snap-content">
					<div class="snap-options">
						<div class="snap-input snap-input-select">
							<label for="nt_noteicon_placement" class="snap-label">
								<?php esc_html_e( 'Note Icon Placement', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="nt_noteicon_placement" id="nt_noteicon_placement">
									<option value="bottom" <?php if( $placement == 'bottom' ) { echo 'selected'; } ?>><?php esc_html_e( 'Fixed to the bottom of the screen', 'sfwd-lms' ); ?></option>
									<option value="top" <?php if( $placement == 'top' ) { echo 'selected'; } ?>><?php esc_html_e( 'Fixed to the top of the screen', 'sfwd-lms' ); ?></option>
									<option value="right" <?php if( $placement == 'right' ) { echo 'selected'; } ?>><?php esc_html_e( 'Fixed to the right hand portion of the screen', 'sfwd-lms' ); ?></option>
									<option value="above-content" <?php if( $placement == 'above-content' ) { echo 'selected'; } ?>><?php esc_html_e( 'Above the content', 'sfwd-lms' ); ?></option>
									<option value="below-content" <?php if( $placement == 'below-content' ) { echo 'selected'; } ?>><?php esc_html_e( 'Below the content', 'sfwd-lms' ); ?></option>
									<option value="shortcode" <?php if( $placement == 'shortcode' ) { echo 'selected'; } ?>><?php esc_html_e( 'Using the [notepad] shortcode', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div> <!--/.snap-input-->
						<div class="snap-input snap-input-text">
							<label for="nt-note-placement-right" class="snap-label">
								<?php esc_html_e( 'Distance from Right', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-text">
								<input type="text" value="<?php echo get_option('nt_noteicon_placement_right', 30 ); ?>" name="nt_noteicon_placement_right" id="nt_noteicon_placement_right">
								<label for="nt_noteicon_placement_right" class="description snap-after-input-description"><?php esc_html_e( 'Number of pixels from the right portion of the screen the button will display', 'sfwd-lms' ); ?></label>
							</span>
						</div>
						<div class="snap-input snap-input-select">
							<label for="nt_noteicon_style" class="snap-label">
								<?php esc_html_e( 'Take notes style', 'sfwd-lms' ); ?>
							</label>
							<span class="snap-input-option snap-select">
								<select name="nt_noteicon_style" id="nt_noteicon_style">
									<option value="tab" <?php if( $icon_style == 'tab' ) { echo 'selected'; } ?>><?php esc_html_e( 'Tab', 'sfwd-lms' ); ?></option>
									<option value="circle" <?php if( $icon_style == 'circle' ) { echo 'selected'; } ?>><?php esc_html_e( 'Circle', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div> <!--/.snap-input-->
						<div class="snap-input snap-input-select">
							<label for="nt_noteicon_hide_on_mobile" class="snap-label"><?php esc_html_e( 'Hide on Mobile', 'sfwd-lms' ); ?></label>
							<span class="snap-input-option snap-select">
								<select name="nt_noteicon_hide_on_mobile">
									<option value="yes" <?php if( $hide_mobile == 'yes' ) { echo 'selected'; } ?>><?php esc_html_e( 'Yes', 'sfwd-lms' ); ?></option>
									<option value="no" <?php if( $hide_mobile == 'no' ) { echo 'selected'; } ?>><?php esc_html_e( 'No', 'sfwd-lms' ); ?></option>
								</select>
							</span>
						</div> <!--/.snap-input-->

						<?php
						$color_fields = apply_filters( 'snaporbital_notes_colors', array(
							'background_color' 		=> __( 'Notepad / Icon Background Color', 'sfwd-lms' ),
							'text_color' 			=> __( 'Notepad / Icon Text Color', 'sfwd-lms' ),
							'header_background'		=> __( 'Notepad Header Background', 'sfwd-lms' ),
							'header_text'			=> __( 'Notepad Header Text', 'sfwd-lms' ),
							'link_color'			=> __( 'Notepad Link/Icon Colors', 'sfwd-lms' ),
							'button_background'		=> __( 'Notepad Button Background', 'sfwd-lms' ),
							'button_text'			=> __( 'Notepad Button Text', 'sfwd-lms' ),
						) );

						ldnt_color_fields( $color_fields, $colors ); ?>

					</div>
				</div>
			</div>

			<div class="submit snap-submit"><?php submit_button(); ?></div>

		</form>

	</div>
	<?php
}

function ldnt_color_fields( $fields = array(), $values = array() ) {

	if( !$fields || empty($fields ) ) {
		return false;
	}

	foreach( $fields as $slug => $label ) {
		ldnt_color_field( $slug, $label, $values );
	}


}

function ldnt_color_field( $slug = false, $label = false, $values = array() ) {

	if( !$slug || empty($slug) ) {
		return false;
	} ?>

	<div class="snap-input snap-input-color">
		<label for="<?php echo esc_attr( 'nt_' . $slug ); ?>" class="snap-label"><?php echo esc_html($label); ?></label>
		<span class="snap-input-option snap-color">
			<input type="text" name="<?php echo esc_attr( 'nt_' . $slug ); ?>" class="wp-color-picker" value="<?php if( isset($values[$slug]) ) { echo esc_attr( $values[$slug] ); } ?>">
		</span>
	</div> <!--/.snap-input-->

	<?php

}

function learndash_notes_register_option() {
	// creates our settings in the options table
	register_setting('learndash_notes_license', 'learndash_notes_license_key', 'learndash_notes_sanitize_license' );

	$settings = array(
		'ldnt_notes_courses',
		'ldnt_notes_lessons',
		'ldnt_notes_topics',
		'ldnt_notes_assignments',
		'ldnt_notes_quizes',
		'ldnt_link_new_windows',
		'ldnt_show_notes_in_admin',
		'nt_noteicon_placement',
		'nt_noteicon_hide_on_mobile',
		'nt_noteicon_style',
		'nt_text_color',
		'nt_background_color',
		'nt_header_background',
		'nt_header_text',
		'nt_link_color',
		'nt_button_background',
		'nt_button_text',
		'ldnt_doc_type',
		'ldnt_all_notes_page',
		'ldnt_supported_types',
		'ldnt_single_note_page',
		'ldnt_groups',
		'ldnt_roles',
		'nt_noteicon_placement_right',
		'ldnt_autosave',
		'learndash_notes_default_body',
		'learndash_notes_default_title'
	);

	foreach( $settings as $setting ) {

		register_setting( 'learndash_notes_license', $setting );

	}

}
add_action('admin_init', 'learndash_notes_register_option');

function learndash_notes_sanitize_license( $new ) {
	$old = get_option( 'learndash_notes_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'learndash_notes_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

function learndash_notes_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST[ 'learndash_notes_license_activate' ] ) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'learndash_notes_nonce', 'learndash_notes_nonce' ) )
			return; // get out if we didn't click the Activate button
		// retrieve the license from the database
		$license = trim( $_POST[ 'learndash_notes_license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_NOTES_ITEM_NAME ), // the name of our product in EDD,
			'url'       => home_url()
		);

		$response = wp_remote_get( add_query_arg( $api_params, PSP_NOTES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;
		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"
		update_option( 'learndash_notes_license_status', $license_data->license );
	}
}
add_action('admin_init', 'learndash_notes_activate_license');

function ldnt_migrate_notes() {

	$args = array(
		'post_type'			=>	'coursenote',
		'posts_per_page'	=>	-1,
		'fields'			=>	'ids',
	);

	$notes = get_posts( $args );

	foreach( $notes as $note ) {

		$relationships = ldnt_get_post_relationships( get_post_meta( $note, 'nt-note-current-lessson-id', true ) );

		foreach( $relationships as $key => $val ) {

			update_post_meta( $note, '_' . $key, $val );

		}

	}

	echo 'Migrated';

}

add_action( 'add_meta_boxes', 'ldnt_register_metaboxes' );
function ldnt_register_metaboxes() {

	$types = ldnt_get_supported_types();
	if( in_array( get_post_type(), $types ) ) {

		add_meta_box( 'ldnt_note_options', __( 'Note Options', 'sfwd-lms' ), 'ldnt_note_options_metabox' );

	}

	if( get_post_type() == 'coursenote' ) {

		add_meta_box( 'ldnt_note_details', __( 'Note Details', 'sfwd-lms' ), 'ldnt_note_details_metabox', null, 'advanced' );

	}

}

function ldnt_note_options_metabox() {

	global $post; ?>

	<input type="hidden" name="ldnt_noncename" id="ldnt_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

	<?php

	$disabled_setting	= get_post_meta( $post->ID, '_ldnt_disable_notes', true );
	$disabled 			= ( !empty( $disabled_setting ) ? 'checked' : '' );
	$body 				= get_post_meta( $post->ID, '_ldnt_default_note_text', true );
	$title				= get_post_meta( $post->ID, '_ldnt_default_note_title', true ); ?>

	<input type="hidden" name="ldnt_origin_post" value="<?php echo esc_attr($post->ID); ?>">

	<div class="meta-options"><label for="ldnt_disable_notes"><input type="checkbox" name="ldnt_disable_notes" value="yes" <?php echo $disabled; ?>> <?php esc_html_e( 'Disable notes on this page', 'sfwd-lms' ); ?></label></div>
	<div class="meta-options"><label for="ldnt_default_note_title"><strong><?php esc_html_e( 'Default note title', 'sfwd-lms' ); ?></strong></label> <input type="text" name="ldnt_default_note_title" value="<?php echo esc_attr( $title ); ?>"></div>
	<div class="meta-options"><label for="ldnt_default_note_text"><strong><?php esc_html_e( 'Default text in the notes editor', 'sfwd-lms' ); ?></strong></label>

		<?php
		$args = array(
			'media_buttons'		=>		false,
			'textarea_name'		=>		'ldnt_default_note_text',
			'editor_height'		=>		175,
			'quicktags'			=>		false,
			'teeny'				=>		true,
			'quicktags'			=>		false,
		);

		$body = apply_filters( 'nt_user_note_body', $body );

		add_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons', 10, 2);
		wp_editor( $body, 'ldnt_default_note_text', $args );
		remove_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons' ); ?>

	</div>
	<?php
}

function ldnt_note_details_metabox() {

	global $post;
	$author = get_the_author_meta( '', $post->post_author );
	?>

	<div class="wrap">

		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Author', 'sfwd-lms'); ?></th>
				<td><a href="<?php echo esc_url( get_edit_user_link( $post->post_author ) ); ?>"><?php echo esc_html( get_userdata( $post->post_author )->display_name ); ?></a></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Recorded', 'sfwd-lms' ); ?></th>
				<td><?php echo nt_course_breadcrumbs( get_post_meta( $post->ID, '_nt-course-array', true ) ); ?></td>
			</tr>
		</table>
	</div>

	<?php

}

add_action( 'save_post', 'ldnt_note_meta_save' );
function ldnt_note_meta_save( $post_id ) {

	if( !isset( $_POST['ldnt_noncename'] ) ) return $post_id;

	if ( !wp_verify_nonce( $_POST[ 'ldnt_noncename' ], plugin_basename( __FILE__ ) ) )
		return $post_id;

	if ( !current_user_can( 'edit_post', $post_id ) )
		  return $post_id;

	if( $post_id != $_POST['ldnt_origin_post'] ) {
		return $post_id;
	}

	$types = ldnt_get_supported_types();

	if( in_array( get_post_type( $post_id ), $types ) ) {

		$disabled 		= $_POST[ 'ldnt_disable_notes' ];
		$default_notes 	= $_POST[ 'ldnt_default_note_text' ];
		$default_title 	= $_POST[ 'ldnt_default_note_title' ];

		if( $disabled == 'yes' ) {

			update_post_meta( $post_id, '_ldnt_disable_notes', 'yes' );

		} else {

			delete_post_meta( $post_id, '_ldnt_disable_notes' );

		}

		if( !empty( $default_notes ) ) {

			update_post_meta( $post_id, '_ldnt_default_note_text', $default_notes );

		} else {

			delete_post_meta( $post_id, '_ldnt_default_note_text' );

		}

		if( !empty( $default_title ) ) {

			update_post_meta( $post_id, '_ldnt_default_note_title', $default_title );

		} else {

			delete_post_meta( $post_id, '_ldnt_default_note_title' );

		}

	}

}

function nt_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['learndash_notes_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'learndash_notes_nonce', 'learndash_notes_nonce' ) )
			return; // get out if we didn't click the deactivate button

		// retrieve the license from the database
		$license = trim( get_option( 'learndash_notes_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_NOTES_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_NOTES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'learndash_notes_license_status' );

	}
}
add_action('admin_init', 'nt_deactivate_license',1);

function nt_check_activation_response() {

    // retrieve the license from the database
    $license = trim( get_option( 'lds_skins_license_key' ) );


    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( PSP_NOTES_ITEM_NAME ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, PSP_NOTES_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	var_dump($response);

}

function notes_delete_user_data( $user_id ) {

	if( !isset($user_id) || empty($user_id) || !is_int($user_id) ) {
		return false;
	}

	$notes = get_posts( array(
		'meta_key' 			=> 'nt-note-user-id',
		'meta_value' 		=> $user_id,
		'post_type' 		=> 'coursenote',
		'posts_per_page'	=> -1
	) );

	if( empty($notes) ) {
		return false;
	}

	foreach($notes as $note) {

		if( $user_id !== get_post_field( 'post_author', $note->ID ) ) {
			return false;
		}

		wp_delete_post( $note->ID , true );

	}

}
