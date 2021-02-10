/**
 * Trigger AJAX request to save state when the WooCommerce notice is dismissed.
 *
 * @version 2.3.0
 *
 * @author Make Community
 * @license GPL-2.0-or-later
 * @package makeCo
 */

jQuery( document ).on(
	'click', '.make-co-woocommerce-notice .notice-dismiss', function() {

		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'make_co_dismiss_woocommerce_notice'
				}
			}
		);

	}
);
