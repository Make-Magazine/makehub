<?php
////////////////////////////////////////////////////////////////////
// Adds SMTP Settings
////////////////////////////////////////////////////////////////////
//add_action('phpmailer_init', 'send_smtp_email');

function send_smtp_email($phpmailer) {
  // Define that we are sending with SMTP
  $phpmailer->isSMTP();

  // The hostname of the mail server
  $phpmailer->Host = "smtp.mandrillapp.com";

  // Use SMTP authentication (true|false)
  $phpmailer->SMTPAuth = true;

  // SMTP port number - likely to be 25, 465 or 587
  $phpmailer->Port = "587";

  // Username to use for SMTP authentication
  $phpmailer->Username = "webmaster@make.co";

  // Password to use for SMTP authentication
  $phpmailer->Password = "501xfT1DdCTbIUK0Cc6eDg";

  // Encryption system to use - ssl or tls
  $phpmailer->SMTPSecure = "tls";
}
