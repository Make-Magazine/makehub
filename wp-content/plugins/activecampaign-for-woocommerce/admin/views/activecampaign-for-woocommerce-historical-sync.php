<?php
/**
 * Provide an admin historical sync view for the plugin
 *
 * @link       https://www.activecampaign.com/
 * @since      1.5.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin/partials
 */

	$activecampaign_for_woocommerce_total_orders            = $this->get_sync_ready_order_count( 'array' );
	$activecampaign_for_woocommerce_historical_sync_running = false;
	$activecampaign_for_woocommerce_running_sync_status     = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_RUNNING_STATUS_NAME ), 'array' );
	$activecampaign_for_woocommerce_schedule_status         = get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_SCHEDULED_STATUS_NAME );
	$activecampaign_for_woocommerce_event_status            = wp_get_scheduled_event( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_RUN_SYNC_NAME );
	$activecampaign_for_woocommerce_options                 = $this->get_options();
	$activecampaign_for_woocommerce_page_url                = esc_url( admin_url( 'admin.php?page=' . ACTIVECAMPAIGN_FOR_WOOCOMMERCE_PLUGIN_NAME_SNAKE . '_historical_sync&activesync=1' ) );
	$activecampaign_for_woocommerce_page_nonce              = wp_create_nonce( 'activecampaign_for_woocommerce_historical_sync_form' );
	$activecampaign_for_woocommerce_request                 = wp_unslash( $_REQUEST );
$activecampaign_for_woocommerce_get                         = wp_unslash( $_GET );
if (
	isset( $activecampaign_for_woocommerce_request['activecampaign_for_woocommerce_nonce_field'] ) &&
	wp_verify_nonce( $activecampaign_for_woocommerce_request['activecampaign_for_woocommerce_nonce_field'], 'activecampaign_for_woocommerce_historical_sync_form' )
 ) {
	if ( isset( $activecampaign_for_woocommerce_get['activesync'] ) && ! empty( $activecampaign_for_woocommerce_get['activesync'] ) ) {
		$this->run_historical_sync_active();
	}
}
?>
<div id="activecampaign-for-woocommerce-historical-sync" class="wrap">
<h1>
	<?php
	esc_html_e( 'ActiveCampaign for WooCommerce Historical Sync', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN );
	?>
</h1>
<section>
	<div class="card">
		<div>
			<div id="sync-start-section">
				<div>
					<form method="post" action="<?php echo esc_html( $activecampaign_for_woocommerce_page_url ); ?>">
					<?php
					wp_nonce_field( 'activecampaign_for_woocommerce_historical_sync_form', 'activecampaign_for_woocommerce_nonce_field' );
					?>
					<input type="hidden" name="action" value="activecampaign_for_woocommerce_run_historical_sync_active" />

					<button type="button" id="activecampaign-run-historical-sync" class="button disabled">
						<?php esc_html_e( 'Start Background Historical Sync', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					</button>

					<button type="submit" class="submit button">
						<?php esc_html_e( 'Start Active Historical Sync', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					</button>
					<button id="activecampaign-reset-historical-sync" class="button" type="button" >
						<?php esc_html_e( 'Reset Sync Status', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					</button>
					<div>
						<div>
							<label>Sync All Contacts: </label>
							<input type="checkbox" value="1" class="checkbox" name="activecampaign-historical-sync-contacts" id="activecampaign-historical-sync-contacts" />
						</div>
						<div>
							<label>Starting Record: </label>
							<input type="text" name="activecampaign-historical-sync-starting-record" id="activecampaign-historical-sync-starting-record" size="9" placeholder="0" />
						</div>
						<div>
							<label>Batch Limit: </label>
							<select name="activecampaign-historical-sync-limit" id="activecampaign-historical-sync-limit">
								<option value="5">5</option>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option selected="selected" value="100">100</option>
								<option value="150">150</option>
								<option value="200">200</option>
							</select>
						</div>
					</div>
					</form>
				</div>
				<div id="activecampaign-historical-sync-run-shortly" style="display:none;">
					<?php esc_html_e( 'Historical sync will start shortly...', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
				</div>
				<div id="activecampaign-historical-sync-run-contacts" style="display:none;">
					<?php esc_html_e( 'Syncing all contacts...', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<span id="activecampaign-historical-sync-contacts-count"></span>
				</div>
				<div id="activecampaign-historical-sync-stop-requested" style="display:none;">
					<?php esc_html_e( 'Attempting to stop the process...', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="clear">
			<h3 id="activecampaign-last-sync-header" style="display:none;">
				<?php esc_html_e( 'Last Historic Sync Results', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</h3>
			<h3 id="activecampaign-sync-running-header" style="display:none;">
				<?php esc_html_e( 'Sync Running', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</h3>
			<hr />
			<div id="sync-run-section">
				<div id="activecampaign-run-historical-sync-running-status">
					<?php if ( $activecampaign_for_woocommerce_schedule_status ) : ?>
						<?php esc_html_e( 'Historical sync will start shortly...', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<?php endif; ?>
				</div>
				<div id="activecampaign-run-historical-sync-current-record">
					<?php esc_html_e( 'Last Record ID Processed: ', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<span id="activecampaign-run-historical-sync-current-record-num"></span>
					<br/>
					<?php esc_html_e( 'Last Status Update: ', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<span id="activecampaign-run-historical-sync-last-update"></span>
					<?php if ( $activecampaign_for_woocommerce_options['ac_debug'] ) : ?>

					<div>
						<p>Debug info: <span id="activecampaign-run-historical-sync-current-record-status"></span></p>
					</div>
					<?php endif; ?>
				</div>
				<div>
					<button id="activecampaign-cancel-historical-sync" class="button">
						<?php esc_html_e( 'Cancel Sync Process', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					</button>
					<button id="activecampaign-pause-historical-sync" class="button">
						<?php esc_html_e( 'Pause Historical Sync', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					</button>
				</div>
			</div>
			<div id="activecampaign-last-sync-status">
			</div>
		</div>
	</div>
</section>
