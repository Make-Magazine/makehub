<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
$acct_ctrl = new MeprAccountCtrl();

echo("<h3>Subscriptions</h3>");
echo $acct_ctrl->subscriptions();
echo("<h3>Payments</h3>");
echo $acct_ctrl->payments();
