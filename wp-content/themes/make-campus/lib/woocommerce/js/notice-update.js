/**
 * Trigger AJAX request to save state when the WooCommerce notice is dismissed.
 *
 * @version 2.3.0
 *
 * @author Make - COmmunity
 * @license GPL-2.0-or-later
 * @package makeCo
 */

jQuery( document ).on(
	'click', '.make-campus-woocommerce-notice .notice-dismiss', function() {

		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'make_campus_dismiss_woocommerce_notice'
				}
			}
		);

	}
);
