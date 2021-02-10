/**
 * Trigger AJAX request to save state when the WooCommerce notice is dismissed.
 *
 * @version 2.3.0
 *
 * @author Make Community
 * @license GPL-2.0-or-later
 * @package MakeLearn
 */

jQuery( document ).on(
	'click', '.make-learn-woocommerce-notice .notice-dismiss', function() {

		jQuery.ajax(
			{
				url: ajaxurl,
				data: {
					action: 'make_learn_dismiss_woocommerce_notice'
				}
			}
		);

	}
);
