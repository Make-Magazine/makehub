<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
use memberpress\gifting\models as models;
use memberpress\gifting\helpers as helpers;
?>

<div class="mp_wrapper">

<table id="mepr-account-subscriptions-table" class="mepr-account-table">
      <thead>
        <tr>
          <th><?php _ex('Date', 'ui', 'memberpress-gifting'); ?></th>
          <th><?php _ex('Total', 'ui', 'memberpress-gifting'); ?></th>
          <th><?php _ex('Membership', 'ui', 'memberpress-gifting'); ?></th>
          <th><?php _ex('Method', 'ui', 'memberpress-gifting'); ?></th>
          <th><?php _ex('Status', 'ui', 'memberpress-gifting'); ?></th>
          <th><?php _ex('Invoice', 'ui', 'memberpress-gifting'); ?></th>
          <?php if (class_exists('MePdfInvoicesCtrl')) {  ?>
          <th><?php _ex('Download', 'ui', 'memberpress-gifting'); ?></th>
          <?php } ?>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach($my_gifts as $gift):
          $txn = $gift->transaction();
          $prd = $txn->product();
          $pm  = $txn->payment_method();
        ?>
          <tr id="mepr-gifting-row-<?php echo $txn->id; ?>" class="mepr-gifting-row <?php echo (isset($alt) && !$alt)?'mepr-alt-row':''; ?>">
            <td data-label="<?php _ex('Date', 'ui', 'memberpress-gifting'); ?>">
              <?php echo MeprAppHelper::format_date($txn->created_at); ?>
            </td>
            <td data-label="<?php _ex('Total', 'ui', 'memberpress-gifting'); ?>"><?php echo MeprAppHelper::format_currency( $txn->total <= 0.00 ? $txn->amount : $txn->total ); ?></td>
            </td>
            <!-- MEMBERSHIP ACCESS URL -->
            <?php if(isset($prd->access_url) && !empty($prd->access_url)): ?>
              <td data-label="<?php _ex('Membership', 'ui', 'memberpress-gifting'); ?>"><a href="<?php echo stripslashes($prd->access_url); ?>"><?php echo MeprHooks::apply_filters('mepr-account-payment-product-name', $prd->post_title, $txn); ?></a></td>
            <?php else: ?>
              <td data-label="<?php _ex('Membership', 'ui', 'memberpress-gifting'); ?>"><?php echo MeprHooks::apply_filters('mepr-account-payment-product-name', $prd->post_title, $txn); ?></td>
            <?php endif; ?>
            <td data-label="<?php _ex('Method', 'ui', 'memberpress-gifting'); ?>"><?php echo (is_object($pm)?$pm->label:_x('Unknown', 'ui', 'memberpress-gifting')); ?></td>
            <td data-label="<?php _ex('Status', 'ui', 'memberpress-gifting'); ?>">
              <?php echo helpers\AppHelper::human_readable_status($gift->status, $txn); ?>
              <?php if($txn->status != \MeprTransaction::$refunded_str && models\Gift::$unclaimed_str == $gift->status): ?>
                <br/><a href="#0" class="mp-clipboardjs" data-clipboard-text="<?php echo $gift->claim_url($txn); ?>"><?php _ex('Copy Gift URL', 'ui', 'memberpress-gifting'); ?></a>
                <br/><a class="mpgft-open-send-gift" href="#mpgft-send-gift-<?php echo $txn->id; ?>"><?php _ex('Send Gift Email', 'ui', 'memberpress-gifting'); ?></a>

                <div id="mpgft-send-gift-<?php echo $txn->id; ?>" class="mpgft-white-popup mfp-hide mp_wrapper">
                  <form class="mpgft-send-gift-form">
                    <h3><?php _ex('Send Gift Email', 'ui', 'memberpress-gifting'); ?><img src="<?php echo MEPR_IMAGES_URL . '/square-loader.gif'; ?>" alt="<?php _e('Loading...', 'memberpress-gifting'); ?>" class="mpgft-loader mepr-hidden" /></h3>

                    <input type="hidden" name="mpgft_transaction_id" value="<?php echo $txn->id; ?>" />


                    <div class="mp-form-row mpgft_gifter_name">
                      <div class="mp-form-label">
                        <label for="mpgft_gifter_name"><?php _ex('From (Name):', 'ui', 'memberpress-gifting'); ?></label>
                        <span class="cc-error"><?php _ex('From (Name) Required', 'ui', 'memberpress-gifting'); ?></span>
                      </div>
                      <input type="text" name="mpgft_gifter_name" id="mpgft-gifter-name" class="mepr-form-input" value="" />
                    </div>

                    <div class="mp-form-row mpgft_giftee_name">
                      <div class="mp-form-label">
                        <label for="mpgft_giftee_name"><?php _ex('To (Name):', 'ui', 'memberpress-gifting'); ?></label>
                        <span class="cc-error"><?php _ex('To (Name) Required', 'ui', 'memberpress-gifting'); ?></span>
                      </div>
                      <input type="text" name="mpgft_giftee_name" id="mpgft-giftee-name" class="mepr-form-input" value="" />
                    </div>

                    <div class="mp-form-row mpgft_giftee_email">
                      <div class="mp-form-label">
                        <label for="mpgft_giftee_email"><?php _ex('To (Email):', 'ui', 'memberpress-gifting'); ?></label>
                        <span class="cc-error"><?php _ex('Empty or Invalid Email', 'ui', 'memberpress-gifting'); ?></span>
                      </div>
                      <input type="text" name="mpgft_giftee_email" id="mpgft-giftee-email" class="mepr-form-input" value="" placeholder="mail@example.com" />
                    </div>

                    <div class="mp-form-row mpgft_gift_note">
                      <div class="mp-form-label">
                        <label for="note"><?php _ex('Note:', 'ui', 'memberpress-gifting'); ?></label>
                      </div>
                      <textarea name="mpgft_gift_note" id="mpgft-gift-note" cols="30" rows="5"></textarea>
                    </div>
                    <?php wp_nonce_field( 'mpgft_send_gift_email' ); ?>
                    <div class="mp-form-row mepr_first_name">
                      <input class="mpgft-send-gift-submit" type="submit" value="<?php _ex('Send', 'ui', 'memberpress-gifting'); ?>">
                    </div>
                  </form>

                </div>

              <?php endif; ?>
            </td>
            <td data-label="<?php _ex('Invoice', 'ui', 'memberpress-gifting'); ?>"><?php echo $txn->trans_num; ?></td>
            <?php
            if (class_exists('MePdfInvoicesCtrl')) {  ?>
              <td><a href="
                <?php
                echo \MeprUtils::admin_url(
                  'admin-ajax.php',
                  array( 'download_invoice', 'mepr_invoices_nonce' ),
                  array(
                    'action' => 'mepr_download_invoice',
                    'txn'    => $txn->id,
                  )
                );
                ?>
              " target="_blank"><?php echo esc_html_x( 'PDF', 'ui', 'memberpress-gifting', 'memberpress' ); ?></a></td>
            <?php }
            ?>
          </tr>
        <?php endforeach; ?>
        <?php // MeprHooks::do_action('mepr-account-subscriptions-table', $current_user, $subscriptions); ?>
      </tbody>
    </table>
    <p></p>