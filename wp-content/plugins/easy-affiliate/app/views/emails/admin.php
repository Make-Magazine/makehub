<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h1 style="margin-top: 0; color: #333333; font-size: 22px; font-weight: bold;">New affiliate sale</h1>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">A new sale has been made with an affiliate link!</p>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">You may find the sale details below:</p>
<table class="attributes" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 0 21px;">
  <tr>
    <td class="attributes_content" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; background-color: #F4F4F7; padding: 16px;" bgcolor="#F4F4F7">
      <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Affiliate:</strong> {$affiliate_login} ({$affiliate_email})
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Transaction Type:</strong> {$transaction_type}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Customer Name:</strong> {$customer_name}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Customer Email:</strong> {$customer_email}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Product:</strong> {$item_name}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Transaction #:</strong> {$trans_num}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Remote IP Address:</strong> {$remote_ip_addr}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Total:</strong> {$payment_amount}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Commission Total:</strong> {$commission_total}
            </span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
