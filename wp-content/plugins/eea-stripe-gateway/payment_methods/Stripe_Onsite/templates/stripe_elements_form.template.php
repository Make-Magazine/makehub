<?php if (! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed');
}
/**
 * ------------------------------------------------------------------------
 *
 * stripe_embedded_form
 *
 * @package         Event Espresso
 * @subpackage      espresso-stripe-gateway
 *
 * ------------------------------------------------------------------------
 */
?>
<div id="ee-stripe-button-dv">
    <!-- placeholder for Elements -->
    <label><?php esc_html_e('Card Number', 'event_espresso');?></label>
    <div id="stripe-card-element"></div>
    <label><?php esc_html_e('Expiry Date (MM/YY)', 'event_espresso');?></label>
    <div id="stripe-card-expiry-element"></div>
    <label><?php printf(
        // @translators: 1: opening link tag, 2: closing link tag
        esc_html_x(
            'CVC %1$s(What\'s this?)%2$s',
            'CVC (What\'s this?)',
            'event_espresso'
        ),
        '<a href="https://www.cvvnumber.com/" target="_blank" rel="noopener noreferrer">',
        '</a>'
    );
    ?></label>
    <div id="stripe-card-cvc-element"></div>
    <input type="submit" id="stripe-card-button" value="<?php esc_html_e('Pay Now', 'event_espresso');?>">


</div>



