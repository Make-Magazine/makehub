<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h1 style="margin-top: 0; color: #333333; font-size: 22px; font-weight: bold;">New affiliate application</h1>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">Someone just applied to join your Affiliate Program:</p>
<table class="attributes" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 0 21px;">
  <tr>
    <td class="attributes_content" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; background-color: #F4F4F7; padding: 16px;" bgcolor="#F4F4F7">
      <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>First Name:</strong> {$first_name}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Last Name:</strong> {$last_name}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Website(s):</strong> {$websites}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Strategy:</strong> {$strategy}
            </span>
          </td>
        </tr>
        <tr>
          <td class="attributes_item" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px; padding: 0;">
            <span class="f-fallback">
              <strong>Social Media:</strong> {$social}
            </span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<p>To approve or reject the application, use the button below:</p>
<table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; -premailer-width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; text-align: center; margin: 30px auto; padding: 0;">
  <tr>
    <td align="center" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
        <tr>
          <td align="center" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
            <a href="{$edit_url}" class="f-fallback button" target="_blank" style="color: #FFF; border-color: #3869d4; border-style: solid; border-width: 10px 18px; background-color: #3869D4; display: inline-block; text-decoration: none; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); -webkit-text-size-adjust: none; box-sizing: border-box;">Manage Application</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table class="body-sub" role="presentation" style="margin-top: 25px; padding-top: 25px; border-top-width: 1px; border-top-color: #EAEAEC; border-top-style: solid;">
  <tr>
    <td style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
      <p class="f-fallback sub" style="font-size: 13px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">If you're having trouble with the button above, copy and paste the URL below into your web browser.</p>
      <p class="f-fallback sub" style="font-size: 13px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">{$edit_url}</p>
    </td>
  </tr>
</table>
