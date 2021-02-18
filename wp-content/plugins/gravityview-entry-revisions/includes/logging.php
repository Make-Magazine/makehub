<?php

add_filter( 'gform_logging_supported', 'gv_revisions_add_logging_support' );

/**
 * @param array $plugins
 *
 * @return array
 */
function gv_revisions_add_logging_support( $plugins = array() ) {

	$plugins['gravityview-entry-revisions'] = 'GravityView Entry Revisions';

	return $plugins;
}

/**
 * @param $message
 * @param string $level
 */
function gv_revisions_log( $message, $level = 'debug' ) {

	if ( ! class_exists( 'GFLogging' ) ) {
		return;
	}

	GFLogging::include_logger();

	if ( ! class_exists( 'KLogger' ) ) {
		return;
	}

	switch ( $level ) {
		case 'fatal':
		case 5:
			$level = KLogger::FATAL;
			break;
		case 'error':
		case 4:
			$level = KLogger::ERROR;
			break;
		case 'warning':
		case 3:
			$level = KLogger::WARN;
			break;
		case 'info':
		case 2:
			$level = KLogger::INFO;
			break;
		case 'debug':
		case 1:
		default:
			$level = KLogger::DEBUG;
			break;
	}

	GFLogging::log_message( 'gravityview-entry-revisions', $message, KLogger::DEBUG );
}

function gv_revisions_log_debug( $message ) {
	gv_revisions_log( $message, 'debug' );
}

function gv_revisions_log_info( $message ) {
	gv_revisions_log( $message, 'info' );
}

function gv_revisions_log_warning( $message ) {
	gv_revisions_log( $message, 'warning' );
}

function gv_revisions_log_error( $message ) {
	gv_revisions_log( $message, 'error' );
}

function gv_revisions_log_fatal( $message ) {
	gv_revisions_log( $message, 'fatal' );
}