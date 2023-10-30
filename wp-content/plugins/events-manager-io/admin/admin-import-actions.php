<?php
class EMIO_Admin_Import_Actions extends EMIO_Admin_Actions {
	
	public static function init(){
		parent::init();
		if( !empty($_REQUEST['emio_import_nonce']) && !empty($_REQUEST['action']) ){
			global $EM_Notices; /* @var EM_Notices $EM_Notices */
			//Saving an Import
			if( $_REQUEST['action'] == 'save' ){
				if( wp_verify_nonce($_REQUEST['emio_import_nonce'], 'emio-import-edit') ){
					if( !empty($_REQUEST['item_id']) ){
						$EMIO_Import = EMIO_Imports::load($_REQUEST['item_id']);
					}else{
						$EMIO_Import = new EMIO_Import();
						if( !empty($_REQUEST['format']) ){
							$EMIO_Import = EMIO_Imports::get_format($_REQUEST['format']);
							$EMIO_Import = new $EMIO_Import();
						}
					}
					EMIO_Import_Admin::get_post( $EMIO_Import );
					if( EMIO_Import_Admin::validate( $EMIO_Import ) ){
						if( EMIO_Import_Admin::save( $EMIO_Import ) ){
							$EM_Notices->add_confirm(sprintf(__('Import %1$s.', 'events-manager-io'), __('Saved', 'events-manager-io')), true);
							if( !defined('DOING_AJAX') ) {
								$args = array('item_id'=>$EMIO_Import->ID);
								//check if field mapping is enabled for format, and if a field map has been selected yet
								if( $EMIO_Import::$field_mapping && empty($EMIO_Import->meta['field_mapping']) ){
									$args['tab'] = 'mapping';
								}else{
									//If mapping is not required for this format, or if a field mapping structure is defined already
									if( !empty($_REQUEST['preview']) ){ $args['tab'] = 'preview'; }
								}
								wp_redirect(add_query_arg( $args, em_wp_get_referer() ));
								exit();
							}
						}else{
							$EM_Notices->add_error($EMIO_Import->errors, true);
							$EMIO_Import->flush_source();
						}
					}else{
						$EM_Notices->add_error($EMIO_Import->errors, true);
						$EMIO_Import->flush_source();
					}
				}
			}
			if( $_REQUEST['action'] == 'save_field_mapping' && !empty($_REQUEST['item_id']) ){
				if( wp_verify_nonce($_REQUEST['emio_import_nonce'], 'emio-import-save-mapping-'.$_REQUEST['item_id']) ){
					$EMIO_Import = EMIO_Imports::load($_REQUEST['item_id']);
					if( !empty($_REQUEST['emio_field_mapping']) ){
						$EMIO_Import->meta['field_mapping'] = $_REQUEST['emio_field_mapping'];
						if( EMIO_Import_Admin::save( $EMIO_Import ) ){
							$EM_Notices->add_confirm(esc_html__('Field mapping successfully saved.', 'events-manager-io'), true);
							if( !defined('DOING_AJAX') ) {
								wp_redirect( add_query_arg( array('tab'=>'preview'), em_wp_get_referer() ) );
								exit();
							}
						}else{
							$EM_Notices->add_error(EMIO_Import_Admin::$errors, true);
						}
					}
				}
			}
			//importing items from preview page
			if( $_REQUEST['action'] == 'import_preview' && !empty($_REQUEST['item_id']) ){
				if( wp_verify_nonce($_REQUEST['emio_import_nonce'], 'emio-import-preview-run-'.$_REQUEST['item_id']) ){
					$EMIO_Import = EMIO_Imports::load($_REQUEST['item_id']);
					//decide if we're to grab a serialized array or not
					//right now we just do if it's from a preview, this is on the todo
					//get serialized array of import
					$items = array();
					$import_all = !empty($_REQUEST['emio_import_all']);
					$keys = !empty($_REQUEST['import_select']) && is_array($_REQUEST['import_select']) ? $_REQUEST['import_select'] : array();
					if( empty($_REQUEST['import_data']) ){
						$EM_Notices->add_error(__('No import data received. Please select items to import and try again.','events-manager-io'));
						return;
					}
					foreach( $_REQUEST['import_data'] as $import_key => $import_item ){
						if( $import_all || in_array($import_key, $keys) ){
							$items[] = json_decode(stripslashes($import_item), true);
						}
					}
					if( !empty($items) ){
						$items = $EMIO_Import->parse($items);
						if( !is_wp_error($items) ){
							$result = $EMIO_Import->run($items);
							foreach( $result as $status => $items) {
								if( $status == 'errors' ) continue;
								$number = count($items);
								if( $number == 0 ) continue;
								if( $status == 'updated' ){
									$msg = sprintf(_n('%1$d item was updated', '%1$d items were updated', $number, 'events-manager-io'), $number, "'$status'");
								}else{
									$msg = sprintf(_n('%1$d item imported with %2$s status', '%1$d items imported with %2$s status', $number, 'events-manager-io'), $number, "'$status'");
								}
								$EM_Notices->add_confirm( $msg, true );
							}
							if ( is_wp_error($result) || !empty($result['errors']) ) {
								$errors = is_wp_error($result) ? count( $result->get_error_messages() ) : count($result['errors']);
								$error_msg = _n('There was %d error during import:', 'There were %d errors during import:', $errors, 'events-manager-io');
								$EM_Notices->add_error(sprintf($error_msg, $errors), true);
								$EM_Notices->add_error($result['errors'], true);
							}
						}else{
							$EM_Notices->add_error($items->get_error_messages(), true);
						}
						if( !defined('DOING_AJAX') ) {
							wp_redirect( add_query_arg( array('tab'=>null), em_wp_get_referer() ) );
							exit();
						}
					}
				}
			}
		}
	}
	
}
add_action('admin_init', 'EMIO_Admin_Import_Actions::init');