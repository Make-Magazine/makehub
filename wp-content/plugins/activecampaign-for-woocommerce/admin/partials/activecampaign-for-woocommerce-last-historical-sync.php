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
?>
<?php if ( ! empty( $activecampaign_for_woocommerce_sync_status ) ) : ?>
	<table class="form-table">
		<?php if ( ! empty( $activecampaign_for_woocommerce_sync_status['stop_type_name'] ) ) : ?>
			<tr>
				<td>Notice</td>
				<td>
					<?php esc_html_e( 'Last historic sync process was', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
					<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['stop_type_name'] ); ?>
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
				<?php esc_html_e( 'Total Records Processed:', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
			</td>
			<td>
				<?php echo esc_html( $activecampaign_for_woocommerce_sync_status['current_record'] ); ?>
			</td>
		</tr>
	</table>
<?php else : ?>
	<?php esc_html_e( 'No sync results found.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
<?php endif; ?>
