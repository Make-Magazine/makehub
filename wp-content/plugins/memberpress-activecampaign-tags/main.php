<?php
/*
Plugin Name: MemberPress ActiveCampaign - Tags Version
Plugin URI: http://www.memberpress.com/
Description: ActiveCampaign Autoresponder tag-based integration for MemberPress.
Version: 1.0.11
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-activecampaigntags
Copyright: 2004-2015, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(is_plugin_active('memberpress/memberpress.php')) {
  define('MPACTIVECAMPAIGNTAGS_PLUGIN_SLUG','memberpress-activecampaign-tags/main.php');
  define('MPACTIVECAMPAIGNTAGS_PLUGIN_NAME','memberpress-activecampaign-tags');
  define('MPACTIVECAMPAIGNTAGS_EDITION',MPACTIVECAMPAIGNTAGS_PLUGIN_NAME);
  define('MPACTIVECAMPAIGNTAGS_PATH',WP_PLUGIN_DIR.'/'.MPACTIVECAMPAIGNTAGS_PLUGIN_NAME);
  $mpactivecampaign_url_protocol = (is_ssl())?'https':'http'; // Make all of our URLS protocol agnostic
  define('MPACTIVECAMPAIGNTAGS_URL',preg_replace('/^https?:/', "{$mpactivecampaign_url_protocol}:", plugins_url('/'.MPACTIVECAMPAIGNTAGS_PLUGIN_NAME)));

  // Load Addon
  require_once(MPACTIVECAMPAIGNTAGS_PATH . '/MpActiveCampaignTags.php');
  new MpActiveCampaignTags;

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPACTIVECAMPAIGNTAGS_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPACTIVECAMPAIGNTAGS_EDITION,
    MPACTIVECAMPAIGNTAGS_PLUGIN_SLUG,
    'mpactivecampaigntags_license_key',
    __('MemberPress ActiveCampaign - Tags Version', 'memberpress-activecampaigntags'),
    __('ActiveCampaign Autoresponder Tags-based Integration for MemberPress.', 'memberpress-activecampaigntags')
  );
}

