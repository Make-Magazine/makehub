<?php
/**
 * This file is called in two instances. 
 * 1 - When the plugin is activated it will be called from the activation hook in src/boot.php install().
 * 2 - When LD core (4.1.1) includes a zip copy of this plugin it will move to the mu-plugins directory. Then call this file to perform needed setup.
 */
$installed_dir = plugin_dir_path( __DIR__ );
$installed_dir = str_replace( '\\', '/', $installed_dir );
$installed_dir = strtolower( $installed_dir );
$installed_dir = trailingslashit( $installed_dir );

$wp_plugin_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'plugins';
$wp_plugin_dir = str_replace( '\\', '/', $wp_plugin_dir );
$wp_plugin_dir = strtolower( $wp_plugin_dir );
$wp_plugin_dir = trailingslashit( $wp_plugin_dir );

$plugin_slug = basename( __DIR__ );

$rename_ret = false;

if ( ( $installed_dir !== $wp_plugin_dir ) && ( is_writable( $wp_plugin_dir ) ) ) {
	if ( ( file_exists( $installed_dir . $plugin_slug ) ) && ( ! file_exists( $wp_plugin_dir . $plugin_slug ) ) ) {
		// Move the plugin from the current directory to the plugins/learndash-hub/ directory.
		$rename_ret = rename( $installed_dir . $plugin_slug, $wp_plugin_dir . $plugin_slug );
		if ( true === $rename_ret ) {
			// After the rename we need to add ourself to the WP 'active' plugins list.

			$plugin = 'learndash-hub/learndash-hub.php'; // Would be nice to determine this somewhere else.

			if ( ( isset( $network_wide ) ) && ( true === $network_wide ) ) {
				$current            = get_site_option( 'active_sitewide_plugins', array() );
				$current[ $plugin ] = time();
				update_site_option( 'active_sitewide_plugins', $current );
			} else {
				$current   = get_option( 'active_plugins', array() );
				$current[] = $plugin;
				sort( $current );
				update_option( 'active_plugins', $current );
			}
		}
	}
}
