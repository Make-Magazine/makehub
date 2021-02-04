<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit('No direct script access allowed'); }

/**
 * QuickBooks oAuth section.
 *
 *	@package 		Event Espresso
 *	@subpackage 	eea-quickbooks-gateway
 *	@author 		Event Espresso
 *	
 */
?>

<!-- OAuth'enticated -->
<div id="eea_quickbooks_oauth_ok_dv" <?php echo ($qb_connected) ? '' : 'hidden'; ?> >
	<span><img src="<?php echo $connected_png; ?>" /></span>
	<strong class="eea-qb-connected-txt"><?php _e('CONNECTED ', 'event_espresso'); ?></strong>
	<p><strong><?php echo sprintf( __('Expires on: %1$s ', 'event_espresso'), $expires_on ); ?></strong></p>
	<p>
		<span class="eea-qb-connected-controls">
			<?php if ( $expires_in_30 ) { ?>
				<img class="eea-qb-reconnect" id="eea_qb_oauth_reconnect" src="<?php echo $qb_reconnect_ico; ?>" />
			<?php } ?>
			<img class="eea-qb-disconnect" id="eea_qb_oauth_disconnect" src="<?php echo $qb_disconnect_ico; ?>" />
		</span>
	</p>
</div>

<!-- Not connected to Intuit -->
<div id="eea_quickbooks_oauth_x_dv" <?php echo ($qb_connected) ? 'hidden' : ''; ?> >
	<span><img src="<?php echo $not_connected_png; ?>" /></span>
	<strong class="important-notice"><?php _e('NOT CONNECTED TO QUICKBOOKS ! ', 'event_espresso'); ?></strong>
	<i><?php _e(' This payment method requires an Authentication.', 'event_espresso'); ?></i><br/><br/>
	<img src="<?php echo $qb_connected_no; ?>" id="eea_quickbooks_oauth_x" class="eea-qb-oauth-btn" />
</div>