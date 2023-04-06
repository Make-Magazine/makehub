<?php
class EMIO_Admin_Export_Actions extends EMIO_Admin_Actions {
	
	public static function init(){
		parent::init();
		if( !empty($_REQUEST['emio_export_nonce']) && !empty($_REQUEST['action']) ){
			global $EM_Notices; /* @var EM_Notices $EM_Notices */
			//Saving an Export
			if( $_REQUEST['action'] == 'save' && wp_verify_nonce($_REQUEST['emio_export_nonce'], 'emio-export-edit') ){
				global $EMIO_Export;
				if( !empty($_REQUEST['item_id']) ){
					$EMIO_Export = EMIO_Exports::load($_REQUEST['item_id']);
				}else{
					if( !empty($_REQUEST['format']) ){
						$EMIO_Export = EMIO_Exports::get_format($_REQUEST['format']);
						$EMIO_Export = new $EMIO_Export();
					}
				}
				EMIO_Export_Admin::get_post( $EMIO_Export );
				if( EMIO_Export_Admin::validate( $EMIO_Export ) ){
					if( EMIO_Export_Admin::save( $EMIO_Export ) ){
						$EM_Notices->add_confirm(sprintf(__('Export %1$s.', 'events-manager-io'), __('Saved', 'events-manager-io')), true);
						if( !defined('DOING_AJAX') ) {
							$args = array('item_id'=>$EMIO_Export->ID);
							wp_redirect(add_query_arg( $args, em_wp_get_referer() ));
							exit();
						}
					}else{
						$EM_Notices->add_error( $EMIO_Export->errors, true);
					}
				}else{
					$EM_Notices->add_error( $EMIO_Export->errors, true);
				}
			}elseif( $_REQUEST['action'] == 'download' && wp_verify_nonce($_REQUEST['emio_export_nonce'], 'emio-export-download') && !empty($_REQUEST['item_id']) ){
				$EMIO_Export = EMIO_Exports::load($_REQUEST['item_id']);
				if( $EMIO_Export::$method == 'pull' ){
					$EMIO_Export->source = 'public-dl';
					$EMIO_Export->run();
					exit();
				}
			}elseif( $_REQUEST['action'] == 'run' && wp_verify_nonce($_REQUEST['emio_export_nonce'], 'emio-export-run') && !empty($_REQUEST['item_id']) ){
				$EMIO_Export = EMIO_Exports::load($_REQUEST['item_id']);
				if( $EMIO_Export::$method == 'push' ){
					$export_result = $EMIO_Export->run();
					if( is_wp_error($export_result) ){
						$error_msg = esc_html__('%d item(s) have been successfully exported, but %s item(s) could not be exported due to errors.', 'events-manager-io');
						$EM_Notices->add_confirm( sprintf($error_msg, $EMIO_Export->export_output_count, count($export_result->errors)), true );
						$EM_Notices->add_error( $export_result->get_error_messages(), true );
					}else{
						$EM_Notices->add_confirm( sprintf(__('%d item(s) have been successfully exported.', 'events-manager-io'), $EMIO_Export->export_output_count), true );
					}
					if( !defined('DOING_AJAX') ) {
						$args = array('item_id'=>$EMIO_Export->ID);
						wp_redirect(add_query_arg( $args, em_wp_get_referer() ));
						exit();
					}
				}
			}
		}
	}
}
add_action('admin_init', 'EMIO_Admin_Export_Actions::init');