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
    <input type="submit" id="ee-stripe-button-btn" value="<?php _e('Pay Now', 'event_espresso');?>">
    <p id="ee-stripe-response-pg" class="clear" style="display: none;"></p>
    <div class="clear"></div>
</div>
