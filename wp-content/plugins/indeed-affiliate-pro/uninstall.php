<?php

if (!defined('WP_UNINSTALL_PLUGIN')){
	exit();
}
if ( get_option('uap_keep_data_after_delete') == 1 ){
		return;
}

include plugin_dir_path(__FILE__) . 'classes/Uap_Db.class.php';
$uap_uninstall_object = new Uap_Db();
$uap_uninstall_object->unistall();
