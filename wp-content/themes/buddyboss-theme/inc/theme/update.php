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

			// Call to backup default cover images.
			if ( version_compare( $current_version, '1.8.6', '>' ) && function_exists( 'bb_theme_update_1_8_7' ) ) {
				bb_theme_update_1_8_7();
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

/**
 * Backup cover width and height for profile and group.
 *
 * @since 1.8.7
 */
function bb_theme_update_1_8_7() {
	global $buddyboss_theme_options;

	$is_platform_upto_date = function_exists( 'buddypress' ) && defined( 'BP_PLATFORM_VERSION' ) && version_compare( BP_PLATFORM_VERSION, '1.9.1', '>=' );

	/* Check if options are empty */
	if ( ! isset( $buddyboss_theme_options ) ) {
		$buddyboss_theme_options = get_option( 'buddyboss_theme_options', array() );
	}

	if ( ! empty( $buddyboss_theme_options ) ) {
		update_option( 'old_buddyboss_theme_options_1_8_7', $buddyboss_theme_options );
	}

	if ( isset( $buddyboss_theme_options['buddyboss_profile_cover_width'] ) ) {
		$profile_cover_width = buddyboss_theme_get_option( 'buddyboss_profile_cover_width' );

		// If platform is not updated then option will migrate.
		if ( ! $is_platform_upto_date ) {
			delete_option( 'bb-pro-cover-profile-width' );
			add_option( 'bb-pro-cover-profile-width', $profile_cover_width );
		}
		unset( $buddyboss_theme_options['buddyboss_profile_cover_width'] );
	}

	if ( isset( $buddyboss_theme_options['buddyboss_profile_cover_height'] ) ) {
		$profile_cover_height = buddyboss_theme_get_option( 'buddyboss_profile_cover_height' );

		// If platform is not updated then option will migrate.
		if ( ! $is_platform_upto_date ) {
			delete_option( 'bb-pro-cover-profile-height' );
			add_option( 'bb-pro-cover-profile-height', $profile_cover_height );
		}
		unset( $buddyboss_theme_options['buddyboss_profile_cover_height'] );
	}

	if ( isset( $buddyboss_theme_options['buddyboss_group_cover_width'] ) ) {
		$group_cover_width = buddyboss_theme_get_option( 'buddyboss_group_cover_width' );

		// If platform is not updated then option will migrate.
		if ( ! $is_platform_upto_date ) {
			delete_option( 'bb-pro-cover-group-width' );
			add_option( 'bb-pro-cover-group-width', $group_cover_width );
		}
		unset( $buddyboss_theme_options['buddyboss_group_cover_width'] );
	}

	if ( isset( $buddyboss_theme_options['buddyboss_group_cover_height'] ) ) {
		$group_cover_height = buddyboss_theme_get_option( 'buddyboss_group_cover_height' );

		// If platform is not updated then option will migrate.
		if ( ! $is_platform_upto_date ) {
			delete_option( 'bb-pro-cover-group-height' );
			add_option( 'bb-pro-cover-group-height', $group_cover_height );
		}
		unset( $buddyboss_theme_options['buddyboss_group_cover_height'] );
	}

	if ( ! empty( $buddyboss_theme_options ) ) {
		update_option( 'buddyboss_theme_options', $buddyboss_theme_options );
	}
}

// Hook on to admin_init.
add_action( 'admin_init', 'bb_theme_setup_updater' );

/**
 * Set up the BuddyBoss theme updater.
 *
 * @return void
 *
 * @since 1.8.7
 */
function bb_theme_setup_updater() {
	// Are we running an outdated version of BuddyBoss Theme?
	if ( wp_doing_ajax() || ! bb_theme_is_update() ) {
		return;
	}

	bb_theme_version_updater();
}

/**
 * Is this a BuddyBoss theme update?
 *
 * @return bool True if update, otherwise false.
 * @since 1.8.7
 */
function bb_theme_is_update() {

	// Current DB version of this site (per site in a multisite network).
	$current_db   = bb_theme_get_db_version();
	$current_live = bb_theme_get_db_version_raw();

	// Compare versions (cast as int and bool to be safe).
	$is_update = (bool) ( (int) $current_db < (int) $current_live );

	// Return the product of version comparison.
	return $is_update;
}

/**
 * Initialize an update or installation of BuddyBoss Theme.
 *
 * BuddyBoss Theme's version updater looks at what the current database version is,
 * and runs whatever other code is needed - either the "update" or "install"
 * code.
 *
 * @since 1.8.7
 */
function bb_theme_version_updater() {
	// Get the raw database version.
	$raw_db_version = (int) bb_theme_get_db_version();

	/* All done! *************************************************************/

	// Add the conditional logic for each version migration code.
	if ( $raw_db_version < 100 ) {
		// Callback function for version migration code.
	}

	// Bump the version.
	bb_theme_version_bump();
}

/**
 * Update the BuddyBoss Theme version stored in the database to the current version.
 *
 * @since 1.8.7
 */
function bb_theme_version_bump() {
	update_option( '_bb_theme_db_version', bb_theme_get_db_version_raw() );
}

/**
 * Output the BuddyBoss Theme database version.
 *
 * @since 1.8.7
 */
function bb_theme_db_version() {
	echo bb_theme_get_db_version();
}
/**
 * Return the BuddyBoss Theme database version.
 *
 * @since 1.8.7
 *
 * @return string The BuddyBoss Theme database version.
 */
function bb_theme_get_db_version() {
	return get_option( '_bb_theme_db_version', 0 );
}

/**
 * Output the BuddyBoss Theme database version.
 *
 * @since 1.8.7
 */
function bb_theme_db_version_raw() {
	echo bb_theme_get_db_version_raw();
}

/**
 * Return the BuddyBoss Theme database version.
 *
 * @since 1.8.7
 *
 * @return string The BuddyBoss Theme version direct from the database.
 */
function bb_theme_get_db_version_raw() {
	return ! empty( buddyboss_theme()->bb_theme_db_version ) ? buddyboss_theme()->bb_theme_db_version : 0;
}
