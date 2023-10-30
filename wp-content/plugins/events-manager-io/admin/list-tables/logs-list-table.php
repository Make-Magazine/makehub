<?php
require_once('list-table.php');
/**
 * Create a new table class that will extend the WP_List_Table
 */
class EMIO_Logs_List_Table extends EMIO_List_Table {
	
	private $io_names = array();
	private $summary_view = true;
	public $per_page_var = 'emio_logs_per_page';
	
	public function __construct($args = array()) {
		parent::__construct($args);
		if( !empty($_GET['singular']) || !empty($_GET['batch']) ){
			$this->summary_view = false;
		}
	}
	
	protected function get_views() {
		global $wpdb;
		if( $this->summary_view ) return array();
		if( !empty($_GET['batch']) ){
			$query = 'SELECT COUNT(*) FROM '.EMIO_TABLE_LOG ." WHERE io_id = %d AND uuid = UNHEX(%s)";
			$query_args = array($_GET['item_id'], $_GET['batch']);
		}elseif( !empty($_GET['item_id']) ){
			$query = 'SELECT COUNT(*) FROM '.EMIO_TABLE_LOG ." WHERE io_id = %d";
			$query_args = array($_GET['item_id']);
		}else{
			$query = 'SELECT COUNT(*) FROM '.EMIO_TABLE_LOG;
			$query_args = array();
		}
		$status_links = $this->get_views_template();
		foreach( $status_links as $filter_name => $filter_link ){
			if( !empty($query_args) ){
				if( $filter_name != 'all' ){
					$query = $query . " AND type='$filter_name'";
				}
				$the_query = $wpdb->prepare($query, $query_args);
			}else{
				if( $filter_name != 'all' ){
					$the_query = $query . " WHERE type='$filter_name'";
				}else{
					$the_query = $query;
				}
			}
			$status_links[$filter_name] = sprintf( $filter_link, $wpdb->get_var($the_query));
		}
		$filter = !empty($_GET['filter']) && isset($status_links[$_GET['filter']]) ? $_GET['filter'] : 'all';
		$status_links[$filter] = '<strong>'.$status_links[$filter].'</strong>';
		return $status_links;
	}
	
	private function get_views_template(){
		return array(
			"all" => '<a href="'. esc_url(add_query_arg('filter',null)) .'">'. __('All','events-manager-io') . ' (%d)</a>',
			"event" => '<a href="'. esc_url(add_query_arg('filter','event')) .'">'. __('Events','events-manager') . ' (%d)</a>',
			"location" => '<a href="'. esc_url(add_query_arg('filter','location')) .'">'. __('Locations','events-manager') . '(%d)</a>',
		);
	}
	
	public function extra_tablenav( $which ) {
		if( $which != 'top' ) return null;
		?>
		<div class="alignleft actions">
			<?php
			if( $this->summary_view ){
				$this->extra_tablenav_dates();
			}else{
				$this->extra_tablenav_actions();
				if( empty($_GET['batch']) ){
					$this->extra_tablenav_dates();
				}
			}
			submit_button(__('Filter', 'events-manager'), 'secondary', 'submit', false);
			?>
		</div>
		<?php
	}
	
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns(){
		$columns = array();
		if( $this->summary_view ){
			if( empty($_GET['item_id']) ){
				$action = $_GET['page'] == 'events-manager-io-import' ? __('Import', 'events-manager-io') : __('Export', 'events-manager-io');
				$columns['item'] = sprintf(__('%s Name', 'events-manager-io'), $action);
			}
			$columns['log_date'] = __('Date','events-manager-io');
			$columns['affected'] = __('Records Affected','events-manager-io');
			$columns['created'] = __('Created','events-manager-io');
			$columns['updated'] = __('Updated','events-manager-io');
			$columns['errors'] = __('Errors','events-manager-io');
		}else{
			$columns['name'] = __('Name','events-manager-io');
			if( empty($_GET['item_id']) ){
				$action = $_GET['page'] == 'events-manager-io-import' ? __('Import', 'events-manager-io') : __('Export', 'events-manager-io');
				$columns['item'] = sprintf(__('%s Name', 'events-manager-io'), $action);
			}
			$columns['log_date'] = __('Date','events-manager-io');
			$columns['type'] = __('Type','events-manager-io');
			$columns['action'] = __('Action','events-manager-io');
		}
		if( !empty($_GET['batch']) ){
			unset($columns['log_date']);
		}
		return $columns;
	}
	
	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns(){
		return array(
			'log_date' => array('log_date', false),
			'affected' => array('affected', false),
			'created' => array('created', false),
			'updated' => array('updated', false),
			'errors' => array('errors', false),
		);
	}
	
	public function column_name( $item ){
		$title = '<strong>' . $item['name'] . '</strong>';
		$actions = array();
		if( get_post_type( $item['post_id'] ) ){
			$actions = array(
				'view' => '<a href="'.get_permalink( $item['post_id'] ).'">'. __('View','events-manager-io') .'</a>',
				'edit' => '<a href="'.get_edit_post_link( $item['post_id'] ).'">'. __('Edit','events-manager-io') .'</a>',
			);
		}else{
			$msg = esc_html__('No information about this location, it was most likely deleted or never created.', 'events-manager-io');
			if( $msg != $item['name'] ){
				$actions['msg'] = "<em>$msg</em>";
			}
		}
		if( !empty($item['external_url']) ){
			$actions['external_url'] = '<a href="'.esc_url( $item['external_url'] ).'" target="_blank">'. __('View External Source','events-manager-io') .'</a>';
		}
		return $title . $this->row_actions( $actions );
	}
	
	/**
	 * Get the table data
	 *
	 * @return array
	 */
	protected function table_data(){
		global $wpdb;
		// Set defaults
		$orderby_cols = $this->get_sortable_columns();
		//get ordering
		$orderby = !empty($_GET['orderby']) && array_key_exists($_GET['orderby'], $orderby_cols) ? $_GET['orderby'] : 'log_date';
		$order = !empty($_GET['order']) && $_GET['order'] == 'asc' ? 'ASC':'DESC';
		//run query with pagination
		$offset = $this->per_page * ($this->get_pagenum() -1);
		//check type filter
		$conditions = $conditions_vars = array();
		if( !empty($_GET['item_id']) ){
			$conditions[] = 'io_id = %d';
			$conditions_vars[] = $_GET['item_id'];
		}
		if( !empty($_POST['emio_filter_scope_dates']) ){
			$dates = $_POST['emio_filter_scope_dates'];
			if( !empty($dates[0]) && !empty($dates[1]) ){
				$conditions[] = '(log_date BETWEEN %s AND %s)';
				$conditions_vars[] = $dates[0] .' 00:00:00';
				$conditions_vars[] = $dates[1] .' 23:59:59';
			}elseif( !empty($dates[0]) ){
				$conditions[] = 'log_date >= %s';
				$conditions_vars[] = $dates[0] .' 00:00:00';
			}elseif( !empty($dates[1]) ){
				$conditions[] = 'log_date <= %s';
				$conditions_vars[] = $dates[1] .' 23:59:59';
			}
		}
		if( !$this->summary_view ){
			if( !empty($_GET['batch']) ){
				$conditions[] = 'uuid = UNHEX(%s)';
				$conditions_vars[] = $_GET['batch'];
			}
			if( !empty($_GET['filter']) && array_key_exists($_GET['filter'], $this->get_views_template()) ){
				$conditions[] = "type='%s'";
				$conditions_vars[] = $_GET['filter'];
			}
			if( !empty($_POST['log_action']) ){
				$conditions[] = "action='%s'";
				$conditions_vars[] = $_POST['log_action'];
			}
		}
		$where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
		$query_args = array_merge($conditions_vars, array($this->per_page, $offset));
		if( $this->summary_view ){
			$query = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS count(*) AS affected, LOWER(HEX(uuid)) AS uuid, io_id, log_date, COUNT(IF(action='create',1, NULL)) 'created', COUNT(IF(action='update',1, NULL)) 'updated', COUNT(IF(action='error',1, NULL)) 'errors' FROM ".EMIO_TABLE_LOG ." $where GROUP BY uuid, log_date, io_id ORDER BY $orderby $order LIMIT %d OFFSET %d", $query_args);
		}else{
			$query = $wpdb->prepare('SELECT SQL_CALC_FOUND_ROWS *, LOWER(HEX(uuid)) AS uuid FROM '.EMIO_TABLE_LOG ." $where ORDER BY $orderby $order LIMIT %d OFFSET %d", $query_args);
		}
		$results = $wpdb->get_results( $query, ARRAY_A );
		$this->total_items = $wpdb->get_var('SELECT FOUND_ROWS()');
		//prepare data
		$logs = array();
		if( $this->summary_view ){
			foreach( $results as $row ){
				$logs[] = $this->summary_row( $row );
			}
		}else{
			foreach( $results as $row ){
				//$row['uuid'] = unpack('H*',str_replace('-', '', $row['uuid'])); //we don't need it atm so no point in converting
				$logs[] = $this->singular_row( $row );
			}
		}
		return $logs;
	}
	
	public function general_row( $log, $row ){
		global $wpdb;
		$io_query = 'SELECT name FROM '. EMIO_TABLE .' WHERE ID=%d';
		if( empty($_GET['item_id']) ){
			//we're viewing all imports/exports here, so we show the name of the import/export item
			$item_url = add_query_arg(array('item_id' => $row['io_id'], 'view'=>'edit', 'tab'=>'history'));
			if( !array_key_exists($row['io_id'], $this->io_names) ){
				//save item name to array for caching on this list view
				$this->io_names[$row['io_id']] = $wpdb->get_var( $wpdb->prepare($io_query, $row['io_id']) );
			}
			//show the name of this item if it exists or show as deleted.
			if( !$this->io_names[$row['io_id']] ){
				$log['item'] = '<em>'. esc_html__('Unknown (deleted)','events-manager-io') .'</em>';
			}else{
				$log['item'] = '<a href="'.esc_url($item_url).'">'.$this->io_names[$row['io_id']] .'</a>';
			}
		}
		return $log;
	}
	
	public function summary_row( $row ){
		$log = array(
			'affected' => $row['affected'],
			'created' => $row['created'],
			'updated' => $row['updated'],
			'errors' => $row['errors'],
		);
		$date_url = add_query_arg(array('batch' => $row['uuid'], 'item_id' => $row['io_id'], 'view'=>'edit', 'tab'=>'history'));
		$link_title = esc_attr__('View all items form this date/time.', 'events-manager-io');
		$log['log_date'] = '<a href="'.esc_url($date_url).'" title="'.$link_title.'">'.$row['log_date'].'</a>';
		return $this->general_row($log, $row);
	}
	
	public function singular_row( $row ){
		$log = array('external_url' => false, 'post_id' => $row['post_id']);
		$post_type = get_post_type($row['post_id']);
		if( $row['action'] == 'error' ){
			//parse the error which is appended to the description
			$description = explode(' && ', $row['log_desc']);
			$row['log_desc'] = array_shift($description);
			$error = implode(' && ', $description);
		}
		if( $post_type == 'event' ){
			$EM_Event = em_get_event( $row['post_id'], 'post_id' );
			if( $EM_Event->post_id ){
				$link = '<a href="'. $EM_Event->get_edit_url() .'">'. $EM_Event->event_name .'</a>';
			}else{
				$link = $row['log_desc'];
				if( !$link ){
					$link = esc_html__('No information about this event, it was most likely deleted or never created.', 'events-manager-io');
				}
			}
			if( !empty($row['url']) ){
				$log['external_url'] = $row['url'];
			}elseif( !empty($EM_Event->event_attributes['event_url']) ){
				$log['external_url'] = $EM_Event->event_attributes['event_url'];
			}
			$log['type'] = __('Event', 'events-manager');
		}elseif( $post_type == 'location' ){
			$EM_Location = em_get_location( $row['post_id'], 'post_id' );
			if( $EM_Location->post_id ){
				$link = '<a href="'. $EM_Location->get_edit_url() .'">'. $EM_Location->location_name .'</a>';
			}else{
				$link = $row['log_desc'];
				if( !$link ){
					$link = esc_html__('No information about this location, it was most likely deleted or never created.', 'events-manager-io');
				}
			}
			$log['type'] = __('Location', 'events-manager');
			if( !empty($row['url']) ){
				$log['external_url'] = $row['url'];
			}elseif( !empty($EM_Location->location_attributes['location_url']) ){
				$log['external_url'] = $EM_Location->location_attributes['location_url'];
			}
		}else{
			$link = $row['log_desc'];
			if( !$link ){
				$link = esc_html(sprintf(__('Unknown %s item.', 'events-manager-io'), $row['type']));
			}
			$log['type'] = ucfirst(__($row['type'], 'events-manager'));
		}
		$log['name'] = $link;
		if( !empty($error) ){
			$log['name'] .= '<br><span style="color:red;">'.$error.'</span>';
		}
		if( empty($_GET['batch']) ){
			$date_url = add_query_arg(array('batch' => $row['uuid'], 'item_id' => $row['io_id'], 'view'=>'edit', 'tab'=>'history'));
			$link_title = esc_attr__('View all items form this date/time.', 'events-manager-io');
			$log['log_date'] = '<a href="'.esc_url($date_url).'" title="'.$link_title.'">'.$row['log_date'].'</a>';
		}
		$log['action'] = $this->column_value_action($row['action']);
		return $this->general_row($log, $row);
	}
	
	public function column_value_action( $key ){
		if( $key == 'update' ){
			return esc_html__('Updated', 'events-manager-io');
		}elseif( $key == 'create' ){
			return esc_html__('Created', 'events-manager-io');
		}elseif( $key == 'error' ){
			return esc_html__('Error', 'events-manager-io');
		}
		return $key;
	}
	
	public function extra_tablenav_actions(){
		$value = !empty($_POST['log_action']) ? $_POST['log_action'] : '';
		?>
		<div class="alignleft actions">
			<label for="filter-by-type" class="screen-reader-text"><?php esc_html_e('Filter by Action', 'events-manager-io'); ?></label>
			<select id="filter-by-type" name="log_action">
				<option value="0"><?php esc_html_e('Filter by Action', 'events-manager-io'); ?></option>
				<option value="update" <?php if($value == 'update') echo 'selected'; ?>><?php esc_html_e('Updated', 'events-manager-io'); ?></option>
				<option value="create" <?php if($value == 'create') echo 'selected'; ?>><?php esc_html_e('Created', 'events-manager-io'); ?></option>
				<option value="error" <?php if($value == 'error') echo 'selected'; ?>><?php esc_html_e('Error', 'events-manager-io'); ?></option>
			</select>
		</div>
		<?php
	}
}
?>