<h3>
	<?php esc_html_e( 'QuickBooks Gateway', 'event_espresso' ); ?>
</h3>
<p>
	<?php printf(
        esc_html__(
            'Settings Tips for the QuickBooks Gateway. For more information you can read %1$s QuickBooks Documentation. %2$s',
            'event_espresso'
        ),
        '<a href="https://developer.intuit.com/docs/0150_payments">',
        '</a>'
    ); ?>
</p>
<h3>
	<?php esc_html_e('QuickBooks Settings', 'event_espresso'); ?>
</h3>
<ul>
    <li>
		<strong><?php esc_html_e( 'OAuth version', 'event_espresso' ); ?></strong><br/>
		<?php esc_html_e(
            'The version of OAuth that your app integrates with.',
            'event_espresso'
        ); ?><br/>
        <?php esc_html_e(
            // @codingStandardsIgnoreStart
            'If your account has created apps before July 17, 2017, any apps created by that account, including future apps and apps under development now, will use OAuth 1.0a.',
            // @codingStandardsIgnoreEnd
            'event_espresso'
        ); ?><br/>
        <?php esc_html_e(
            // @codingStandardsIgnoreStart
            'If your account has not created any apps until after July 17, 2017, all apps created by that account will use OAuth 2.0.',
            // @codingStandardsIgnoreEnd
            'event_espresso'
        ); ?>
	</li>
    <strong><?php esc_html_e('OAuth 1.0a:', 'event_espresso'); ?></strong><br/>
	<li>
		<strong><?php esc_html_e( 'OAuth Consumer Key and OAuth Consumer Secret', 'event_espresso' ); ?></strong><br/>
		<?php printf( esc_html__( 'These are private keys generated when you create an app.
				To find and copy these keys, sign in to %1$s developer.intuit.com%2$s and click %3$s My Apps %4$s.
				Find and open the app you want and click the %5$s Keys %6$s tab.', 'event_espresso'
			),
			'<a href="https://developer.intuit.com">',
			'</a>',
			'<strong>',
			'</strong>',
			'<strong>',
			'</strong>'
		); ?>
	</li>
    <strong><?php esc_html_e( 'OAuth 2.0:', 'event_espresso' ); ?></strong><br/>
    <li>
		<strong><?php esc_html_e( 'Client ID and Client Secret', 'event_espresso' ); ?></strong><br/>
		<?php printf( esc_html__( 'These are the keys generated when you create an app.
				To find these keys, sign in to %1$s developer.intuit.com%2$s and click %3$s My Apps %4$s.
				Find and open the app you want and click the %5$s Keys %6$s tab.', 'event_espresso'
			),
			'<a href="https://developer.intuit.com">',
			'</a>',
			'<strong>',
			'</strong>',
			'<strong>',
			'</strong>'
		); ?>
	</li>
    <li>
		<?php printf(
            esc_html__('Your Redirect URI: %1$s', 'event_espresso'),
            '<strong>' . $redirect_uri . '</strong>'
        ); ?><br/>
		<?php esc_html_e(
            'This URI has to be added to the "Redirect URIs" list in your QuickBooks App settings, under "Keys" section.',
            'event_espresso'
        ); ?>
	</li><br/>
	<li>
		<strong><?php esc_html_e('Connect to QuickBooks', 'event_espresso'); ?></strong><br/>
		<?php esc_html_e(
            'The OAuth flow must be executed once for every QuickBooks Online company file to which a given App connects.',
            'event_espresso'
        ); ?>
	</li>
	<li>
		<?php printf(esc_html__( 'You can find more useful information by visiting %1$s this link.%2$s', 'event_espresso'),
			'<a href="https://developer.intuit.com/docs/0150_payments/0004_manage_a_quickbooks_app">',
			'</a>'
		); ?>
	</li>
</ul>