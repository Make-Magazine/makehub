<?php if (! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed');
} ?>

    <div id="stripe-sandbox-panel" class="sandbox-panel">

        <h6 class="important-notice"><?php esc_html_e('Debug Mode is turned ON. Payments will NOT be processed', 'event_espresso'); ?></h6>

        <p class="test-credit-cards-info-pg">
            <strong><?php esc_html_e('Credit Card Numbers Used for Testing', 'event_espresso'); ?></strong><br/>
            <span class="small-text"><?php esc_html_e('Use any 3 digit CVC code, any future expiration date, and the following credit card information for testing:', 'event_espresso'); ?></span>
        </p>

        <div class="tbl-wrap">
            <table id="stripe-test-credit-cards" class="test-credit-card-data-tbl">
                <thead>
                    <tr>
                        <td><?php esc_html_e('Card Number', 'event_espresso'); ?></td>
                        <td><?php esc_html_e('Explanation', 'event_espresso');?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>4111 1111 1111 1111</td>
                        <td><?php esc_html_e('Approved, no verification required.', 'event_espresso');?></td>
                    </tr>
                    <tr>
                        <td>4000 0000 0000 0002</td>
                        <td><?php esc_html_e('Card declined.', 'event_espresso');?></td>
                    </tr>
                    <tr>
                        <td>4000 0000 0000 0101</td>
                        <td><?php esc_html_e('Declined because of invalid CVC.', 'event_espresso');?></td>
                    </tr>
                    <tr>
                        <td>4000 0025 0000 3155</td>
                        <td><?php esc_html_e('Requires 3D secure verification.', 'event_espresso'); ?></td>
                    </tr>
                    <tr>
                        <td>4000 0000 0000 3055</td>
                        <td><?php esc_html_e('3D secure verification supported, but not required..', 'event_espresso'); ?></td>
                    </tr>
                    <tr>
                        <td>4000 0082 6000 3178</td>
                        <td><?php esc_html_e('Requires 3D secure verification, but payment will thereafter be declined.', 'event_espresso'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p><a href="https://stripe.com/docs/testing#cards" target="_blank"><?php
            esc_html_e('Read Stripe\'s testing cards documentation.', 'event_espresso');
            ?></a></p>
    </div>
