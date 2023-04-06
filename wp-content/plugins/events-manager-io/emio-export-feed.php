<?php
function emio_export_endpoint(){
	add_rewrite_endpoint('events-manager-io', EP_ROOT);
}
add_action( 'init', 'emio_export_endpoint' );

function emio_export_endpoint_handler() {
	global $wp_query;
	
	// if this is not a request for json or a singular object then bail
	if ( !isset( $wp_query->query_vars['events-manager-io'] ) || !is_home() )
		return;
	
	// find the export ID
	EMIO_Loader::export();
	$export_uuid = $wp_query->query_vars['events-manager-io'];
	$EMIO_Export = EMIO_Exports::load( $export_uuid );
	if( $EMIO_Export !== false && $EMIO_Export->has_public_feed() ){
		$EMIO_Export->run();
		exit();
	}
	$wp_query->set_404();
	return;
}
add_action( 'template_redirect', 'emio_export_endpoint_handler' );
?>