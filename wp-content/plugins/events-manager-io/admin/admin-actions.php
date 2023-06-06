<?php
class EMIO_Admin_Actions {
	
	/**
	 * Handles actions for both imports and exports. 
	 */
	public static function init(){
		if( !empty($_REQUEST['emio_import_nonce']) ){
			$action_type = 'import';
			$nonce = $_REQUEST['emio_import_nonce'];
		}
		if( !empty($_REQUEST['emio_export_nonce']) ){
			$action_type = 'export';
			$nonce = $_REQUEST['emio_export_nonce'];
		}
		if( !empty($action_type) && !empty($_REQUEST['action']) ){
			global $EM_Notices; /* @var EM_Notices $EM_Notices */
			//Activate/Deactivate recurring imports/exports
			if( in_array($_REQUEST['action'], array('activate','delete','deactivate')) && !empty($_REQUEST['item_id']) ){
				if( wp_verify_nonce($nonce, "emio-$action_type-".$_REQUEST['action']) || (wp_verify_nonce($nonce, 'emio-items-bulk') && is_array($_REQUEST['item_id'])) ){
					$result = call_user_func('EMIO_Admin_'.ucfirst($action_type).'_Actions::'.$_REQUEST['action'], $_REQUEST['item_id']);
					if( $result ){
						$action = array(
							'activate' => __('activated', 'events-manager-io'),
							'deactivate' => __('deactivated', 'events-manager-io'),
							'delete' => __('deleted', 'events-manager-io'),
						);
						if( $action_type == 'import' ){
							$msg = sprintf(_nx('Import %s.', 'Imports %s.', $result, 'activated, deactivated, deleted', 'events-manager-io'), $action[$_REQUEST['action']]);
						}else{
							$msg = sprintf(_nx('Export %s.', 'Exports %s.', $result, 'activated, deactivated, deleted', 'events-manager-io'), $action[$_REQUEST['action']]);
						}
					}else{
						$action = array(
							'activate' => __('activate', 'events-manager-io'),
							'deactivate' => __('deactivate', 'events-manager-io'),
							'delete' => __('delete', 'events-manager-io'),
						);
						$msg = sprintf(_x('An error occurred. Could not %s item(s).', 'events-manager-io'),  $action[$_REQUEST['action']]);
					}
					if( defined('DOING_AJAX') ) {
						if( $result ){
							$return = array( 'result'=>true, 'message'=> $msg );
						}else{
							$return = array( 'result'=>false, 'message'=> $msg );
						}
						echo json_encode($return);
						die();
					}else{
						if( $result ){
							$EM_Notices->add_confirm( $msg, true );
						}else{
							$EM_Notices->add_error( $msg, true );
						}
						wp_redirect(em_wp_get_referer());
						exit();
					}
				}
			}
		}
	}
	
	public static function delete( $ids = array() ){
		global $wpdb;
		$ids = self::clean_ids($ids);
		if( !empty($ids) ){
			$sql_in = array();
			foreach( $ids as $item_id ){
				$EMIO_Item = EMIO_Items::load($item_id);
				if( $EMIO_Item->type == 'import' ){
					$EMIO_Item->flush_source(false);
				}
				$sql_in[] = $EMIO_Item->ID;
			}
			$result = array();
			$sql = "DELETE FROM ". EMIO_TABLE ." WHERE ID IN (". implode(',', $sql_in) .")";
			$result[EMIO_TABLE] = $wpdb->query($sql);
			$sql = "DELETE FROM ". EMIO_TABLE_LOG ." WHERE io_id IN (". implode(',', $sql_in) .")";
			$result[EMIO_TABLE_LOG] = $wpdb->query($sql);
			if( $EMIO_Item->type == 'import' ){
				$sql = "DELETE FROM ". EMIO_TABLE_SYNC ." WHERE io_id IN (". implode(',', $sql_in) .")";
				$result[EMIO_TABLE_LOG] = $wpdb->query($sql);
			}
		}
		return 0;
	}
	
	public static function activate( $item_ids = array() ){
		return self::set_status(1, $item_ids);
	}
	
	public static function deactivate( $item_ids = array() ){
		return self::set_status(0, $item_ids);
	}

	/**
	 * Change the status of one or more imports. Returns the number of rows affected, or false if there was a database error (check $wpdb global for error).
	 * @param number $status
	 * @param mixed $ids
	 * @return mixed
	 */
	public static function set_status( $status = 0, $ids = array() ){
		global $wpdb;
		$ids = self::clean_ids($ids);
		if( !empty($ids) ){
			$sql_in = array();
			foreach( $ids as $id ){
				$sql_in[] = $wpdb->prepare('%d', $id);
			}
			$sql = $wpdb->prepare("UPDATE ". EMIO_TABLE ." SET status = %d WHERE ID IN (". implode(',', $sql_in) .")", $status);
			return $wpdb->query($sql);
		}
		return 0;
	}
	
	/**
	 * Convert the supplied IDs to an array of numbers only.
	 * @param string $ids
	 * @return array
	 */
	public static function clean_ids( $ids = array() ){
		$clean_ids = array();
		//This is in the list of atts we want cleaned
		if( is_numeric($ids) ){
			$clean_ids[] = (int) $ids;
		}elseif( EM_Object::array_is_numeric($ids) ){
			$clean_ids = $ids;
		}elseif( !is_array($ids) && preg_match('/^( ?[\-0-9] ?,?)+$/', $ids) ){
			$clean_ids = explode(',', str_replace(' ','',$ids));
		}
		return $clean_ids;
	}
	
}