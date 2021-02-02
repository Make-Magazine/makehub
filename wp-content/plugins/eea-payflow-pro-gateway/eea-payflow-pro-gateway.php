<?php
/*
  Plugin Name: Event Espresso - PayPal Payflow Pro (EE 4.6.0+)
  Plugin URI: https://eventespresso.com
  Description: PayPal Payflow Pro is an on-site payment method for accepting credit and debit cards and is available to event organizers in the United States, United Kingdom, Canada, and New Zealand. An account with PayPal Payflow Pro is required to accept payments.

  Version: 1.0.4.p

  Author: Event Espresso
  Author URI: https://eventespresso.com
  Copyright 2014 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
 *
 * ------------------------------------------------------------------------
 *
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package		Event Espresso
 * @ author			Event Espresso
 * @ copyright	(c) 2008-2015 Event Espresso  All Rights Reserved.
 * @ license		https://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link			  https://eventespresso.com
 * @ version	 	EE4
 *
 * ------------------------------------------------------------------------
 */

define( 'EEA_PAYFLOW_PRO_VERSION', '1.0.4.p' );
define( 'EEA_PAYFLOW_PRO_PLUGIN_FILE', __FILE__ );
define( 'EEA_PAYFLOW_PRO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

function load_espresso_payflow_pro() {
	if ( class_exists('EE_Addon') ) {
		require_once( EEA_PAYFLOW_PRO_PLUGIN_PATH . 'EE_Payflow_Pro_Gateway.class.php' );
		EE_PayFlow_Pro_Gateway::register_addon();
	}
}
add_action( 'AHEE__EE_System__load_espresso_addons', 'load_espresso_payflow_pro' );