<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) { exit('NO direct script access allowed'); } ?>

<div id="payflow-pro-sandbox-panel" class="sandbox-panel">
	<h6 class="important-notice"><?php _e( 'Debug Mode is turned ON. Payments will NOT be processed !', 'event_espresso' ); ?></h6>

	<p class="test-credit-card-info-pg">
		<strong><?php _e( 'Credit Card Numbers that can be used for testing:', 'event_espresso' ); ?></strong>
	</p>
	<div class="tbl-wrap">
		<table id="payflow-pro-test-credit-cards" class="test-credit-card-data-tbl small-text">
			<head>
				<tr>
					<td><strong>Card Number</strong></td>
					<td><strong>Description</strong></td>
				</tr>
			</head>
			<tbody>
				<tr>
					<td>4111111111111111</td>
					<td>Visa</td>
				</tr>
				<tr>
					<td>378282246310005</td>
					<td>American Express</td>
				</tr>
				<tr>
					<td>378734493671000</td>
					<td>American Express Corporate</td>
				</tr>
				<tr>
					<td>5555555555554444</td>
					<td>MasterCard</td>
				</tr>
				<tr>
					<td>3530111333300000</td>
					<td>JCB</td>
				</tr>
				<tr>
					<td>6011111111111117</td>
					<td>Discover</td>
				</tr>
				<tr>
					<td>30569309025904</td>
					<td>Diners Club</td>
				</tr>
			</tbody>
		</table>
	</div>

	<p class="test-credit-card-info-pg">
		<strong><?php _e( 'Testing Card Security Code:', 'event_espresso' ); ?></strong>
	</p>
	<div class="tbl-wrap">
		<table id="payflow-pro-test-credit-cards" class="test-credit-card-data-tbl small-text">
			<head>
				<tr>
					<td><strong>CVV2 Value</strong></td>
					<td><strong>Description</strong></td>
				</tr>
			</head>
			<tbody>
				<tr>
					<td>000 - 300</td>
					<td>Accepted</td>
				</tr>
				<tr>
					<td>301 - 600</td>
					<td>Declined</td>
				</tr>
				<tr>
					<td>601 +</td>
					<td>Not Supported</td>
				</tr>
			</tbody>
		</table>
	</div>

	<p class="test-credit-card-info-pg">
		<strong><?php _e( 'The following table tests AVS_ZIP:', 'event_espresso' ); ?></strong>
	</p>
	<div class="tbl-wrap">
		<table id="payflow-pro-test-credit-cards" class="test-credit-card-data-tbl small-text">
			<head>
				<tr>
					<td><strong>Submitted Value for BILL_TO_ZIP</strong></td>
					<td><strong>AVSZIP Result</strong></td>
				</tr>
			</head>
			<tbody>
				<tr>
					<td>00000 - 50000</td>
					<td>Accepted</td>
				</tr>
				<tr>
					<td>50001 - 99999</td>
					<td>No match</td>
				</tr>
			</tbody>
		</table>
	</div>

	<p class="test-credit-card-info-pg">
		<strong><?php _e( 'Result Values Based On Amount:', 'event_espresso' ); ?></strong>
	</p>
	<div class="tbl-wrap">
		<table id="payflow-pro-test-credit-cards" class="test-credit-card-data-tbl small-text">
			<head>
				<tr>
					<td><strong>Definition</strong></td>
					<td><strong>How to test</strong></td>
				</tr>
			</head>
			<tbody>
				<tr>
					<td>Approved</td>
					<td>Use an AMOUNT of $1000 or less</td>
				</tr>
				<tr>
					<td>Referral</td>
					<td>Use the AMOUNT $1013</td>
				</tr>
				<tr>
					<td>Invalid account number</td>
					<td>Submit an invalid account number, for example: 000000000000000</td>
				</tr>
				<tr>
					<td>Generic Host (Processor) Error</td>
					<td>Use the AMOUNT $2000</td>
				</tr>
				<tr>
					<td>Timeout waiting for processor response</td>
					<td>Use the AMOUNT $1104</td>
				</tr>
				<tr>
					<td>Declined</td>
					<td>Use the AMOUNT $1012 or an AMOUNT of $2001 or more</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>