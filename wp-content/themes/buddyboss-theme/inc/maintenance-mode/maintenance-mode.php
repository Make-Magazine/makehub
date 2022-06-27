<?php

/*
 * Maintenance Page
 *
 * Displays the coming soon page for anyone who's not logged in.
 * The login page gets excluded so that you can login if necessary.
 *
 * @return void
 */

if ( !function_exists( 'bb_maintenance_mode' ) ) {

	function bb_maintenance_mode() {

		global $pagenow;

        $switch = buddyboss_theme_get_option( 'maintenance_mode' );

		if ( $switch && $pagenow !== 'wp-login.php' && !current_user_can( 'manage_options' ) && !is_admin() ) {

			if ( file_exists( dirname( __FILE__ ) . '/views/maintenance.php' ) ) {
				require_once dirname( __FILE__ ) . '/views/maintenance.php';
			}

			die();
		}
	}

	if( buddyboss_theme_get_option( 'maintenance_mode' ) ) {
		add_action( 'template_include', 'bb_maintenance_mode' );
		add_action( 'bb_maintenance_head', 'boss_generate_option_css', 99 );
	}

}
