<?php
/**
 * The output for a historical sync results.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.6.5
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin/partials
 */

$activecampaign_for_woocommerce_sync_status = json_decode( get_option( ACTIVECAMPAIGN_FOR_WOOCOMMERCE_SYNC_LAST_STATUS_NAME ), 'array' );
if ( function_exists( 'wc_admin_url' ) ) {
	$activecampaign_for_woocommerce_status_link =
		wc_admin_url(
			'status',
			array(
				'page' => 'wc-status',
				'tab'  => 'logs',
			)
		);
} else {
	$activecampaign_for_woocommerce_status_link = admin_url( 'admin.php?page=wc-status&tab=logs' );
}

?>
<?php if ( ! empty( $activecampaign_for_woocommerce_sync_status ) ) : ?>
	<table class="form-table">
		<?php if ( ! empty( $activecampaign_for_woocommerce_sync_status['stop_type_name'] ) ) : ?>
			<tr>
				<td>Notice</td>
				<td>
					<?php esc_html_e( 'Last historic sync process was', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['stop_type_name'] ); ?>
					<?php if ( isset( $activecampaign_for_woocommerce_sync_status['stop_type_code'] ) ) : ?>
						<div>
							<hr/>
							<p><?php esc_html_e( 'ActiveCampaign returned a fatal error and cannot process your request.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?></p>
							<p><?php esc_html_e( 'Hosted returned error: ', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?><?php echo esc_html( $activecampaign_for_woocommerce_sync_status['stop_type_code'] ); ?></p>
							<p><?php esc_html_e( 'Please wait and try again later.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?></p>
							<p><?php esc_html_e( 'If you continue to receive this message please contact support.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?></p>
							<p>
								<a href="<?php echo esc_url( $activecampaign_for_woocommerce_status_link ); ?>"><?php esc_html_e( 'Check your (ActiveCampaign for WooCommerce) logs for more details.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?></a>
							</p>
						</div>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr class="alternate">
			<td>
				<?php esc_html_e( 'Start Time:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['start_time'] ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Finished Time:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php if ( isset( $activecampaign_for_woocommerce_sync_status['end_time'] ) ) : ?>
					<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['end_time'] ); ?>
				<?php else : ?>
					<?php esc_html_e( 'The last sync was paused or canceled.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<?php esc_html_e( 'Success Count:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['success_count'] ); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Failed Count:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php echo esc_html( count( $activecampaign_for_woocommerce_sync_status['failed_order_id_array'] ) ); ?>
			</td>
		</tr>
		<tr class="alternate">
			<td>
				<?php esc_html_e( 'Failed Order IDs:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php if ( empty( $activecampaign_for_woocommerce_sync_status['failed_order_id_array'] ) ) : ?>
					<?php esc_html_e( 'No failed orders.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
				<?php else : ?>
					<?php echo esc_html( wp_json_encode( $activecampaign_for_woocommerce_sync_status['failed_order_id_array'] ) ); ?>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php esc_html_e( 'Last Record Processed:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['last_processed_id'] ); ?>
			</td>
		</tr>
	</table>
<?php else : ?>
	<?php esc_html_e( 'No sync results found.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
<?php endif; ?>
