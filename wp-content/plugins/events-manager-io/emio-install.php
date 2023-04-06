<?php
global $wpdb;
require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

$sql = "CREATE TABLE " . EMIO_TABLE . " (
		ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		uuid CHAR(36) NULL DEFAULT NULL,
		type CHAR( 6 ) NULL DEFAULT NULL,
		format VARCHAR( 25 ) NULL DEFAULT NULL,
		status int(1) NOT NULL DEFAULT 0,
		user_id bigint(20) unsigned NOT NULL,
		name text NULL DEFAULT NULL,
		scope VARCHAR( 20 ) NULL DEFAULT NULL,
		source VARCHAR( 200 ) NULL DEFAULT NULL,
		filter VARCHAR( 200 ) NULL DEFAULT NULL,
		filter_scope VARCHAR( 21 ) NULL DEFAULT NULL,
		filter_limit int( 6 ) NULL DEFAULT NULL,
		frequency VARCHAR( 25 ) NULL DEFAULT NULL,
  		frequency_start DATE NULL DEFAULT NULL,
  		frequency_end DATE NULL DEFAULT NULL,
  		date_created datetime NULL DEFAULT NULL,
  		date_modified datetime NULL DEFAULT NULL,
  		last_update datetime NULL DEFAULT NULL,
		meta LONGTEXT NULL,
		PRIMARY KEY  (ID)
		) DEFAULT CHARSET=utf8 ;";
dbDelta ( $sql );

em_sort_out_table_nu_keys ( EMIO_TABLE, array (
	'uuid',
	'user_id',
	'type',
	'date_modified',
	'frequency' 
) );

$sql = "CREATE TABLE " . EMIO_TABLE_SYNC . " (
		sync_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		io_id bigint(20) unsigned NOT NULL,
		post_id bigint(20) unsigned NOT NULL,
		uid VARCHAR(255) NULL DEFAULT NULL,
		type VARCHAR(8) NOT NULL,
		date_created datetime NOT NULL,
  		date_modified datetime NULL DEFAULT NULL,
		uid_md5 BINARY(16) NOT NULL,
		checksum BINARY(16) NOT NULL,
		PRIMARY KEY  (sync_id)
		) DEFAULT CHARSET=utf8 ;";
dbDelta ( $sql );
em_sort_out_table_nu_keys ( EMIO_TABLE_SYNC, array('post_id', 'io_id', 'uid_md5') );

if( version_compare( '0.5.0.14', get_option('emio_version') ) == 1 ){
	$wpdb->query('INSERT INTO '. EMIO_TABLE_SYNC .' SELECT import_id, post_id, uid_raw, type, date_created, date_modified, UNHEX(uid), UNHEX(checksum) FROM '. $wpdb->prefix . 'em_io_imports');
	//we won't delete the table wp_em_io_imports to prevent problems updating and not being able to revert, just delete the folder when you're happy
}

$sql = "CREATE TABLE " . EMIO_TABLE_LOG . " (
		log_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		io_id bigint(20) unsigned NOT NULL,
		post_id bigint(20) unsigned NOT NULL,
		uid VARCHAR(255),
		action VARCHAR(6) NOT NULL,
		type VARCHAR(8) NOT NULL,
		log_desc TINYTEXT NOT NULL,
		log_date datetime NOT NULL,
		url VARCHAR(2083) NULL DEFAULT NULL,
		uuid BINARY(16) NOT NULL,
		uid_md5 BINARY(16) NOT NULL,
		PRIMARY KEY  (log_id)
		) DEFAULT CHARSET=utf8 ;";
dbDelta ( $sql );
em_sort_out_table_nu_keys ( EMIO_TABLE_LOG, array('io_id','uuid', 'uid_md5') );

add_option('dbem_imports', true);
add_option('dbem_exports', true);
add_option('dbem_io_ignore_timezone', false);

update_option('emio_version', EMIO_VERSION);
EM_IO\License::get_license(true);