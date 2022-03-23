<?php
/**
 * Theme Update Hooks.
 *
 * @package BuddyBoss_Theme
 */

// Clear transient after theme update.
if ( ! function_exists( 'buddyboss_theme_update' ) ) {

	/**
	 * Function is called when theme is updated.
	 *
	 * @since 1.7.3
	 */
	function buddyboss_theme_update() {
		$current_version = wp_get_theme()->get( 'Version' );
		$old_version     = get_option( 'buddyboss_theme_version', '1.7.2' );

		if ( $old_version !== $current_version ) {

			// Call clear learndash group users transient.
			if ( version_compare( $current_version, '1.7.2', '>' ) && function_exists( 'bb_theme_update_1_7_3' ) ) {
				bb_theme_update_1_7_3();
			}

			// Call to backup default cover images.
			if ( version_compare( $current_version, '1.8.2', '>' ) && function_exists( 'bb_theme_update_1_8_3' ) ) {
				bb_theme_update_1_8_3();
			}

			// update not to run twice.
			update_option( 'buddyboss_theme_version', $current_version );
		}
	}

	add_action( 'after_setup_theme', 'buddyboss_theme_update' );
}

/**
 * Clear the learndash course enrolled user count transient.
 *
 * @since 1.7.3
 */
function bb_theme_update_1_7_3() {
	global $wpdb;
	$sql       = 'select option_name from ' . $wpdb->options . ' where option_name like "%_transient_buddyboss_theme_ld_course_enrolled_users_count_%"';
	$all_cache = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

	if ( ! empty( $all_cache ) ) {
		foreach ( $all_cache as $cache_name ) {
			$cache_name = str_replace( '_site_transient_', '', $cache_name );
			$cache_name = str_replace( '_transient_', '', $cache_name );
			delete_transient( $cache_name );
			delete_site_transient( $cache_name );
		}
	}
}

/**
 * Backup default cover images.
 *
 * @since 1.8.4
 */
function bb_theme_update_1_8_3() {
	global $buddyboss_theme_options;

	$theme_default_member_cover = '';
	$theme_default_group_cover  = '';

	/* Check if options are set */
	if ( ! isset( $buddyboss_theme_options ) ) {
		$buddyboss_theme_options = get_option( 'buddyboss_theme_options', array() );
	}

	if ( isset( $buddyboss_theme_options['buddyboss_profile_cover_default'] ) ) {
		$theme_default_member_cover = $buddyboss_theme_options['buddyboss_profile_cover_default'];
	}

	if ( isset( $buddyboss_theme_options['buddyboss_group_cover_default'] ) ) {
		$theme_default_group_cover = $buddyboss_theme_options['buddyboss_group_cover_default'];
	}

	update_option( 'buddyboss_profile_cover_default_migration', $theme_default_member_cover );
	update_option( 'buddyboss_group_cover_default_migration', $theme_default_group_cover );

	// Delete custom css transient.
	delete_transient( 'buddyboss_theme_compressed_elementor_custom_css' );
}
