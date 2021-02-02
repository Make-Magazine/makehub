<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) { exit('No direct script access allowed'); } ?>

<div id="quickbooks-sandbox-panel" class="sandbox-panel">
	<h6 class="important-notice"><?php _e('Debug Mode is turned ON. Payments will NOT be processed !', 'event_espresso'); ?></h6>

	<p class="test-credit-card-names-info-pg">
		<strong><?php _e('Emulating credit card transaction failures', 'event_espresso'); ?></strong><br/>
		<p><?php _e('Use the test card-holder names listed in the following table to emulate various transaction failures in the sandbox environment.', 'event_espresso'); ?></p>
	</p>
		<div class="tbl-wrap">
			<table id="quickbooks-test-credit-cards" class="test-credit-card-data-tbl small-text">
				<thead>
					<tr>
						<td><strong><?php _e('Emulated Name on Card', 'event_espresso'); ?></strong></td>
						<td><strong><?php _e('Response', 'event_espresso'); ?></strong></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>emulate=10201</td>
						<td>Payment system error.</td>
					</tr>
					<tr>
						<td>emulate=10301</td>
						<td>Card number is invalid.</td>
					</tr>
					<tr>
						<td>emulate=10401</td>
						<td>General decline.</td>
					</tr>
				</tbody>
			</table>
		</div>

		<p class="test-credit-cards-info-pg">
			<strong><?php _e('Credit Card Numbers Used for Testing', 'event_espresso'); ?></strong><br/>
		</p>
		<div class="tbl-wrap">
			<table id="qb-test-credit-cards" class="test-credit-card-data-tbl small-text">
				<thead>
					<tr>
						<td><strong><?php _e('Card Type', 'event_espresso'); ?></strong></td>
						<td><strong><?php _e('Card Number', 'event_espresso'); ?></strong></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Visa</td>
						<td>4111111111111111</td>
					</tr>
					<tr>
						<td>Visa</td>
						<td>4012888888881881</td>
					</tr>
				</tbody>
			</table>
		</div>
</div>