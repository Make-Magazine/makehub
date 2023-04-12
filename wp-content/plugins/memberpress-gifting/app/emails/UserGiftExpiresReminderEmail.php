<?php
namespace memberpress\gifting\emails;
use memberpress\gifting as base;
use memberpress\gifting\lib as lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// class GiftClaimEmail extends lib\BaseEmail {
class UserGiftExpiresReminderEmail extends lib\BaseReminderEmail {
  /** Set the default enabled, title, subject & body */
  public function set_defaults($args=array()) {
    $this->title = __('Gifted Membership Reminder Email to User','memberpress-gifting');
    $this->description = __('This email is sent to the user when triggered.', 'memberpress-gifting');
    $this->ui_order = 0;

    $enabled = $use_template = $this->show_form = true;
    $subject = sprintf( __('** Your %1$s', 'memberpress-gifting'), '{$reminder_description}' );
    $body = $this->body_partial();

    $this->defaults = compact( 'enabled', 'subject', 'body', 'use_template' );
    $this->variables = array_unique(
                         array_merge( \MeprRemindersHelper::get_email_vars(),
                                      \MeprSubscriptionsHelper::get_email_vars(),
                                      \MeprTransactionsHelper::get_email_vars() )
                       );

    $this->test_vars = array(
       'reminder_id'               => 28000,
       'reminder_trigger_length'   => 2,
       'reminder_trigger_interval' => 'days',
       'reminder_trigger_timing'   => 'after',
       'reminder_trigger_event'    => 'gift-expires',
       'reminder_name'             => __('Gift Membership Expired', 'memberpress-gifting'),
       'reminder_description'      => __('Your Gift Membership expired 1 Day ago', 'memberpress-gifting')
    );
  }
}

