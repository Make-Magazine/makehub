<?php
// create shortcode to list all notes
function nt_mass_listing_shortcode( $atts ) {

	/**
	 * Make sure users are logged in
	 */
	if( !is_user_logged_in() )
		return;

	$default_ppp 	= ( isset($atts['posts_per_page']) ? $atts['posts_per_page'] : '25' );

	$display_mine 	= ( isset($atts['display']) && $atts['display'] == 'user' ? 'yes' : 'no' );
	$current_user 	= get_current_user_id();
	$course_array 	= array();
	$posts_per_page = isset( $_GET['posts_per_page'] ) ? intval($_GET['posts_per_page']) : $default_ppp;
	$paged			= ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$caps 			= apply_filters( 'learndash_note_capabilities', 'read_others_nt_notes' );

	nt_frontend_note_assets();

	ob_start();

	//Admin and editor users can view all notes
	if( current_user_can($caps) && $display_mine != 'yes' && !learndash_is_group_leader_user() ) {
		$args = array(
				'post_type' 		=> 'coursenote',
				'posts_per_page' 	=> $posts_per_page,
				'paged'				=> $paged,
				'post_status' 		=> array('draft', 'publish'),
		);
	}
	//Viewer can only see their notes
	else {

		$args = array(
				'post_type' 		=> 'coursenote',
				'posts_per_page' 	=> isset( $_GET['posts_per_page'] ) ? $_GET['posts_per_page'] : '25',
				'post_status' 		=> array('draft', 'publish'),
				'author__in' 		=> $current_user,
				'posts_per_page' 	=> $posts_per_page,
				'paged'				=> $paged,
		);
	}

	if( isset( $atts['order'] ) ) $args = array_merge( $args, array( 'order' => $atts['order'] ) );

	if( isset($_GET['search']) ) $args['s'] = $_GET['search'];

	$new_window 	= ( get_option( 'ldnt_link_new_windows', 'no' ) == 'no' ? '' : ' target="new" ' );
	$download_sep 	= ( get_option( 'permalink_structure' ) ? '?' : '&' );

    $query = new WP_Query($args);

	include( ldnt_get_template( 'archive-note-listing' ) );

    return ob_get_clean();

}
add_shortcode( 'learndash_my_notes', 'nt_mass_listing_shortcode' );
add_shortcode( 'my_notes', 'nt_mass_listing_shortcode' );

add_shortcode( 'learndash_course_notes', 'ldnt_course_note_shortcode' );
function ldnt_course_note_shortcode( $args ) {

	if( !is_user_logged_in() )
		return;

	include( 'templates/shortcodes/course-listing.php' );

}

add_shortcode( 'notepad', 'nt_notepad_shortcode' );
function nt_notepad_shortcode( $args ) {

	// Compatability fix with Elementor...
	if( isset($_GET['action']) && $_GET['action'] == 'elementor' ) {
		return;
	}

	if( get_post_type() == 'coursenote' ) {
		return false;
	}

	if( !is_user_logged_in() ) {
		return false;
	}

	if( !isset($args['force_display']) ) {
		if( !ldnt_user_can_take_notes() ) {
			return false;
		}
	}

	global $post;

	$id = ( isset($args['id']) ? $args['id'] : false );

	if( get_option('ldnt_single_note_page') == $post->ID ) {
		return false;
	}

	$style = ( isset($args['style']) ? $args['style'] : null );

	ob_start(); ?>

	<div class="ldnt-content-notes">
		<?php nt_course_note_entry_field( $style, $id ); ?>
	</div>

	<?php
	return ob_get_clean();

}

add_shortcode( 'note_editor', 'nt_note_editor_shortcode' );
function nt_note_editor_shortcode( $args ) {

	global $post;

	if( get_option('ldnt_single_note_page') == $post->ID ) {
		return false;
	}


	if( !is_user_logged_in() ) {
		return false;
	}

	if( !isset($args['force_display']) ) {

		if( !ldnt_user_can_take_notes() ) {
			return false;
		}
	}

	$id = ( isset($args['id']) ? $args['id'] : false );

	ob_start(); ?>

	<style type="text/css">
		.ldnt-content-notes .nt-note-tab {
			display: none;
		}
		#nt_note_cont .ui-resizable, .nt-note-wrapper.ui-resizable,
		#nt_note_cont .nt-note-wrapper {
			position: static !important;
			display: block;
		}
	</style>

	<div class="ldnt-content-notes ldnt-in-content-notes">
		<?php nt_course_note_entry_field( null, $id ); ?>
	</div>

	<?php
	return ob_get_clean();
}

add_shortcode( 'learndash_group_notes', 'nt_my_groups_notes_shortcode' );
function nt_my_groups_notes_shortcode() {

	if( !function_exists('learndash_get_users_group_ids') ) {
		return;
	}

	nt_frontend_note_assets();

	ob_start();

	if( isset($_GET['user']) ) {
		include( ldnt_get_template('user-notes-by-course') );
		return ob_get_clean();
	}

	/**
	 * Look up the current user and see what groups they are administrators of
	 * @var [type]
	 */
	$cuser 	= wp_get_current_user();
	$groups = learndash_get_administrators_group_ids($cuser->ID);

	if( !$groups ) {
		echo '<p class="ld-notes-notice ld-notes-error">' . __( 'You are not assigned to any groups', 'sfwd-lms' ) . '</p>';
		return;
	}

	foreach( $groups as $group_id ) {

		$users = learndash_get_groups_users($group_id);

		if( !$users ) continue;

		include( ldnt_get_template('group-users') );

	 }

	return ob_get_clean();

}
