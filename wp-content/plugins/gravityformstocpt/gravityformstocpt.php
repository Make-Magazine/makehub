<?php
/**
Plugin Name: Gravity Forms - Form to CPT
Description: Allows you to create new posts through Gravity Forms.
Version: 1.0
Author: Make
Text Domain: gravityformstocpt
Domain Path: /languages
 **/

defined( 'ABSPATH' ) || die();

define( 'GF_FORMSTOCPT_VERSION', '1.0-beta-7.1' );

// If Gravity Forms is loaded, bootstrap the Gravity Forms - Form to CPT Add-On.
add_action( 'gform_loaded', array( 'GF_FormsToCPT_Bootstrap', 'load' ), 5 );

/**
 * Class GF_FormsToCPT_Bootstrap
 *
 * Handles the loading of the Gravity Forms - Form to CPT Add-On and registers with the Add-On framework.
 */
class GF_FormsToCPT_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Gravity Forms - Form to CPT Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-formstocpt.php' );

		GFAddOn::register( 'GF_Forms_To_CPT' );

	}

}

/**
 * Returns an instance of the GF_Forms_To_CPT class
 *
 * @see    GF_Forms_To_CPT::get_instance()
 *
 * @return GF_Forms_To_CPT
 */
function gf_formstocpt) {
	return GF_Forms_To_CPT::get_instance();
}
