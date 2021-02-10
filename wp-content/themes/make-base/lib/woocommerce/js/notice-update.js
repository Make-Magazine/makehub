/**
 * Trigger AJAX request to save state when the WooCommerce notice is dismissed.
 *
 * @version 2.3.0
 *
 * @author Make Community
 * @license GPL-2.0-or-later
 * @package MakeBase
 */

jQuery( document ).on(
	'click', '.make-base-woocommerce-notice .notice-dismiss', function() {

		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'make_base_dismiss_woocommerce_notice'
				}
			}
		);

	}
);
