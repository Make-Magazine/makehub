<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<h1 style="margin-top: 0; color: #333333; font-size: 22px; font-weight: bold;">Hi {$first_name}!</h1>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">Your application to the {$site_name} Affiliate Program was approved!</p>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">To complete your signup, use the button below:</p>
<table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; -premailer-width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; text-align: center; margin: 30px auto; padding: 0;">
  <tr>
    <td align="center" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
        <tr>
          <td align="center" style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
            <a href="{$signup_url}" class="f-fallback button" target="_blank" style="color: #FFF; border-color: #3869d4; border-style: solid; border-width: 10px 18px; background-color: #3869D4; display: inline-block; text-decoration: none; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); -webkit-text-size-adjust: none; box-sizing: border-box;">Complete Signup</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 0;">Cheers!</p>
<p style="font-size: 16px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">The {$site_name} Team</p>
<table class="body-sub" role="presentation" style="margin-top: 25px; padding-top: 25px; border-top-width: 1px; border-top-color: #EAEAEC; border-top-style: solid;">
  <tr>
    <td style="word-break: break-word; font-family: &quot;Nunito Sans&quot;, Helvetica, Arial, sans-serif; font-size: 16px;">
      <p class="f-fallback sub" style="font-size: 13px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">If you're having trouble with the button above, copy and paste the URL below into your web browser.</p>
      <p class="f-fallback sub" style="font-size: 13px; line-height: 1.625; color: #51545E; margin: .4em 0 1.1875em;">{$signup_url}</p>
    </td>
  </tr>
</table>
