/**
 * This script adds notice dismissal to the Learn theme.
 *
 * @package Learn
 * @author  Maker Media
 * @license GPL-2.0+
 * @link    https://github.com/Make-Magazine/makehub
 */

jQuery(document).on( 'click', '.learn-woocommerce-notice .notice-dismiss', function() {

	jQuery.ajax({
		url: ajaxurl,
		data: {
			action: 'learn_dismiss_woocommerce_notice'
		}
	});

});