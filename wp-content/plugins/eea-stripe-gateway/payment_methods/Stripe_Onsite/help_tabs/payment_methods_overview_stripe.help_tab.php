<p><strong>
    <?php esc_html_e('Stripe Gateway', 'event_espresso'); ?>
</strong></p>
<p>
    <?php printf(esc_html__('Adjust the settings for the Stripe payment gateway. More information can be found on %sStripe.com%s.', 'event_espresso'), '<a href="http://www.stripe.com/">', '</a>'); ?>
</p>
<p><strong><?php esc_html_e('Stripe Settings', 'event_espresso'); ?></strong></p>
<ul>
    <li>
        <strong><?php esc_html_e('Integration Type', 'event_espresso'); ?></strong><br />
        <?php esc_html_e('Whether you would like to use Stripe Elements (uses inline inputs for acquiring credit card info and meets SCA requirements), or the legacy Stripe Checkout (uses a pop-up). Both meet PCI SAQ-A requirements.', 'event_espresso'); ?>
    </li>
    <li>
        <strong><?php esc_html_e('Validate the billing ZIP code', 'event_espresso'); ?></strong><br />
        <?php esc_html_e('Specify if the billing ZIP code should be validated on the checkout.', 'event_espresso'); ?>
    </li>
    <li>
        <strong><?php esc_html_e('Collect the user\'s billing address', 'event_espresso'); ?></strong><br />
        <?php esc_html_e('Specify whether Checkout should collect the user\'s billing address.', 'event_espresso'); ?>
    </li>
    <li>
        <strong><?php esc_html_e('Checkout Locale', 'event_espresso'); ?></strong><br />
        <?php printf(
            esc_html__('Select the language Stripe should use for checkout here, use auto for Stripe to determine which of its %1$ssupported languages%2$s to use or choose a specific specific language.', 'event_espresso'),
            '<a href="https://stripe.com/docs/checkout#supported-languages" target="_blank">',
            '</a>'
        );
        ?>
    </li>
    <li>
        <strong><?php esc_html_e('Logo URL', 'event_espresso'); ?></strong><br />
        <?php esc_html_e('Upload a logo that will appear at the top of the Stripe checkout, for best results use 125px by 125px.', 'event_espresso'); ?>
    </li>
</ul>