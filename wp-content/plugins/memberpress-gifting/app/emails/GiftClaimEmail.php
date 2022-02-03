<?php
namespace memberpress\gifting\emails;
use memberpress\gifting as base;
use memberpress\gifting\lib as lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class GiftClaimEmail extends lib\BaseOptionsUserEmail {
  /** Set the default enabled, title, subject & body */
  public function set_defaults($args=array()) {
    $this->title = __('<b>Claim Gift</b> Email','memberpress-gifting');
    $this->description = __('This email is sent to a user when payment completes for one of your memberships in her behalf.', 'memberpress-gifting');
    $this->ui_order = 1;

    $enabled = $use_template = $this->show_form = true;
    $subject = __('** {$gifter_name} has sent you a gift!', 'memberpress-gifting');
    $body = $this->body_partial();

    $this->defaults = compact( 'enabled', 'subject', 'body', 'use_template' );
    $this->variables = \MeprTransactionsHelper::get_email_vars();
  }

}

