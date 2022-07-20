<?php
/**
Plugin Name: Gravity Forms to CPT Add-On
Plugin URI: https://make.co
Description: Make: custom GF plugin. Allows you to create new CPT posts from GravityForm submission.
Version: 1.0
Author: Make Community, LLC
Text Domain: gravityformsgftocpt
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2009 - 2022 Rocketgenius, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 **/

defined( 'ABSPATH' ) || die();

define( 'GF_GFTOCPT_VERSION', '1.2' );

// If Gravity Forms is loaded, bootstrap the GF to CPT Add-On.
add_action( 'gform_loaded', array( 'GF_GfToCPT_Bootstrap', 'load' ), 5 );

//include gravity view functions
require_once __DIR__ . '/includes/gv_funcs.php';


/**
 * Class GF_GfToCPT_Bootstrap
 *
 * Handles the loading of the GF to CPT Add-On and registers with the Add-On framework.
 */
class GF_GfToCPT_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, GF to CPT Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-gftocpt.php' );

		GFAddOn::register( 'GF_GF_To_CPT' );

	}

}

/**
 * Returns an instance of the GF_GF_To_CPT class
 *
 * @see    GF_GF_To_CPT::get_instance()
 *
 * @return GF_GF_To_CPT
 */
function gf_gftocpt() {
	return GF_GF_To_CPT::get_instance();
}
