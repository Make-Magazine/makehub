<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
if ( get_option('ihc_keep_data_after_delete') == 1 ) return;

include plugin_dir_path(__FILE__) . 'utilities.php';
require_once plugin_dir_path(__FILE__) . 'classes/Ihc_Db.class.php';
Ihc_Db::do_uninstall();
