<?php
/**
 * Provide an active panel for historical sync
 *
 * @link       https://www.activecampaign.com/
 * @since      1.7.3
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/admin/partials
 */

?>
<script>
	function runAjax(data) {
		return new Promise((resolve, reject) => {
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data
			}).done(response => {
				resolve(response.data);
				setTimeout(loadPage, 5000);
				function loadPage() {
					location.href = "<?php echo esc_url( $url ); ?>"
				}
			}).fail(response => {
				reject(response.responseJSON.data)
			});
		});
	}
</script>
<style>
	#activecampaign-for-woocommerce-processing{
		height: 100px;
		border: 1px solid #c6c6c6;
		overflow: auto;
		margin-top: 20px;
		padding: 0 15px;
	}
</style>

<section class="card">
	<p>
	<?php esc_html_e( 'Please do not stop or leave this page otherwise the process will continue to run.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
	</p>
	<p>
		<?php esc_html_e( 'You can click this button to stop the historical sync at any time.', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
	</p>

	<button id="activecampaign-cancel-historical-sync" class="button">
		<?php esc_html_e( 'Cancel Sync Process', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
	</button>

	<div id="activecampaign-for-woocommerce-interrupt" style="display:none;">
		<?php esc_html_e( 'Attempting to stop and reload page...', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?>
	</div>

	<div id="activecampaign-for-woocommerce-processing">
	<script>
		jQuery('#activecampaign-cancel-historical-sync').click(function (e) {
			console.log('Attempting to stop the sync...');
			jQuery('#activecampaign-for-woocommerce-interrupt').show();
			runAjax({
				'action': 'activecampaign_for_woocommerce_cancel_historical_sync',
				'type': 1,
				'activecampaign_for_woocommerce_settings_nonce_field': jQuery('#activecampaign_for_woocommerce_settings_nonce_field').val()
			});

		});
		window.setInterval(function() {
			var elem = document.getElementById('activecampaign-for-woocommerce-processing');
			elem.scrollTop = elem.scrollHeight;
		}, 500);
	</script>
