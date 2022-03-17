<?php

	/**
	 * Header Include
	 *
	 * @link       https://www.activecampaign.com/
	 * @since      1.?
	 *
	 * @package    Activecampaign_For_Woocommerce
	 * @subpackage Activecampaign_For_Woocommerce/admin/partials
	 */

	$activecampaign_for_woocommerce_health_check = $this->connection_health_check();
?>

<header id="activecampaign-for-woocommerce-app-header">
	<div class="logo">
		<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" xmlns:v="https://vecta.io/nano" class="activecampaign-for-woocommerce-info-icon"><circle fill="#004cff" cx="15" cy="15" r="15"/><path d="M20.089 14.966l-8.777 5.829c-.407.271-.61.712-.61 1.152v1.457l10.641-6.981c.474-.339.779-.881.779-1.457a1.73 1.73 0 0 0-.779-1.457L10.702 6.596v1.355c0 .475.237.915.61 1.152l8.777 5.863zm-5.287.475c.474.305 1.085.305 1.559 0l.745-.508-5.558-3.762c-.339-.237-.847 0-.847.44v1.118l2.881 1.932 1.22.779z" fill="#fff"/></svg>
		<h1>
			<?php
			esc_html_e( 'ActiveCampaign for WooCommerce', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN );
			?>
		</h1>
	</div>
	<a href="https://help.activecampaign.com/hc/en-us/articles/115000652490-WooCommerce-Deep-Data-integration-overview" target="_blank" rel="noopener noreferrer"><span><?php esc_html_e( 'Learn more about ActiveCampaign for WooCommerce', ACTIVECAMPAIGN_FOR_WOOCOMMERCE_LOCALIZATION_DOMAIN ); ?></span><svg class="is-styled css-ws9hmn" height="16px" width="16px" role="img" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M5 0H0V16H16V11H14V14H2V2H5V0ZM8.99995 2H12.5857L6.29285 8.29289L7.70706 9.70711L14 3.41421V7H16V0H8.99995V2Z" fill-rule="evenodd"></path></svg></a>
</header>
<?php if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'activecampaign-subscription-forms/activecampaign.php' ) ) { ?>
<div class="notice notice-success"><p>Be sure to also install the <a href="/wp-admin/plugin-install.php?s=ActiveCampaign&tab=search&type=term" aria-label="More information about ActiveCampaign" data-title="ActiveCampaign" target="_blank">ActiveCampaign Forms, Site Tracking, & Live Chat plugin</a> to capitalize on all of the awesome capabilities of ActiveCampaign. You can:</p><ul><li>&mdash; Easily embed forms to your website.</li><li>&mdash; Track visitors to your site to target shoppers to boost conversion.</li><li>&mdash; Enable live chat for accounts using Conversations (our site messaging platform)!</li></ul><a href="/wp-admin/plugin-install.php?s=ActiveCampaign&tab=search&type=term" aria-label="More information about ActiveCampaign" data-title="ActiveCampaign" target="_blank">Install now</a></p></div>
<?php } ?>
<div class="activecampaign-for-woocommerce-health-check">
	<?php if ( count( $activecampaign_for_woocommerce_health_check ) > 0 ) : ?>
		<div class="notice notice-error inline">
			Connection Status: Issues Found
				<ul>
				<?php foreach ( $activecampaign_for_woocommerce_health_check as $activecampaign_for_woocommerce_issue ) : ?>
					<li>
						<?php echo esc_html( $activecampaign_for_woocommerce_issue ); ?>
					</li>
				<?php endforeach; ?>
				</ul>
		</div>
	<?php else : ?>
		<div class="notice notice-success inline">
			Connection Status: Connected
		</div>
	<?php endif; ?>
</div>
