<?php
require_once('list-table.php');
/**
 * Create a new table class that will extend the WP_List_Table
 */
class EMIO_Items_List_Table extends EMIO_List_Table {
	
	public $item_type = 'item';
	public $per_page_var = 'emio_items_per_page';
	
	public function __construct($args = array()) {
		$this->item_type = str_replace('events-manager-io-', '', $_GET['page']);
		parent::__construct($args);
	}
	
	/**
	 * Get the table data
	 *
	 * @return array
	 */
	protected function table_data(){
		$args = array();
		//set pagination and ordering
		$args['limit'] = $this->per_page;
		$args['page'] = $this->get_pagenum();
		$args['orderby'] = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby']:'name';
		$args['order'] = !empty($_REQUEST['order']) && $_REQUEST['order'] == 'desc' ? 'desc':'asc';
		//check search filters
		if( !empty($_REQUEST['frequency']) && $_REQUEST['frequency'] != 'all' ){
			if( $_REQUEST['frequency'] == 'none' ){
				$args['frequency'] = false;
			}elseif( $_REQUEST['frequency'] == 'scheduled' ){
				$args['frequency'] = true;
			}else{
				$args['frequency'] = str_replace('frequency-', '', $_REQUEST['frequency']);
			}
			//we set frequency_active not status since we're searchng by frequency
			if( !empty($_REQUEST['status']) ){
				$args['status'] = $_REQUEST['status'] == 'active' ? true:false;
			}else{
				$args['status'] = null;
			}
		}
		//check view selection
		if( !empty($_REQUEST['filter']) ){
			$args['format'] = $_REQUEST['filter'];
		}
		//Do the search
		$EMIO_Items_Class = $this->item_type == 'import' ? 'EMIO_Imports':'EMIO_Exports';
		$EMIO_Items = new $EMIO_Items_Class($args); /* @var EMIO_Items $EMIO_Items */
		$this->total_items = $EMIO_Items->total_count;
		//Prepare data
		$logs = array();
		foreach( $EMIO_Items as $EMIO_Item ){ /* @var EMIO_Item $EMIO_Item */
			$log = array();
			$log['item_id'] = $EMIO_Item->ID;
			$log['format'] = $EMIO_Item::$format_name;
			$log['name'] = '<a href="'. esc_url( add_query_arg(array('view'=>'edit', 'item_id'=>$EMIO_Item->ID, 'orderby'=>false, 'order'=>false)) ) .'">'. $EMIO_Item->name .'</a>';
			$log['last'] =  $EMIO_Item->last_update ? $EMIO_Item->last_update : '-';
			$log['status'] = $EMIO_Item->status;
			$log['source'] = '';
			if( $this->item_type == 'import' ){
				/* @var $EMIO_Item EMIO_Import */
				if( $EMIO_Item->source == 'file' ){
					$log['source'] = esc_html__('File Upload', 'events-manager-io');
					if( $EMIO_Item->get_source_filepath() ) $log['source'] .= "<br /><em>". esc_html__('Temporary File Uploaded', 'events-manager-io') ."</em>";
				}elseif( $EMIO_Item->source == 'url' ) {
					if( $EMIO_Item->get_source( false ) ){
						$log['source'] = '<a href="'. esc_url( $EMIO_Item->get_source(false) ) .'" target="_blank">'. esc_html__('URL', 'events-manager-io') .'</a>';
					}else{
						$log['source'] = esc_html__('URL', 'events-manager-io');
					}
				}
			}
			$log['frequency'] = !empty($EMIO_Item->frequency) ? esc_html(EMIO_Cron::get_name($EMIO_Item->frequency)) : esc_html__('Once','events-manager-io');
			if( $EMIO_Item->frequency_start && $EMIO_Item->frequency_end ){
				$log['frequency'] .= '<br>' . sprintf(esc_html__('%s until %s', 'events-manager-io'), $EMIO_Item->frequency_start, $EMIO_Item->frequency_end);
			}elseif( $EMIO_Item->frequency_start ){
				$log['frequency'] .= '<br>' . sprintf(esc_html__('Starts on %s', 'events-manager-io'), $EMIO_Item->frequency_start);
			}elseif( $EMIO_Item->frequency_end ){
				$log['frequency'] .= '<br>' . sprintf(esc_html__('Stops on %s', 'events-manager-io'), $EMIO_Item->frequency_end);
			}
			$log['frequency_raw'] = $EMIO_Item->frequency;
			$logs[] = $log;
		}
		return $logs;
	}
	
	protected function get_views() {
		global $wpdb;
		$query = 'SELECT COUNT(*) FROM '.EMIO_TABLE. ' WHERE type=%s';
		$query_args = array('type' => $this->item_type );
		$status_links = $this->get_views_template();
		foreach( $status_links as $filter_name => $filter_link ){
			$this_query = $query;
			if( $filter_name != 'all' ){
				$this_query .= " AND format=%s";
				$query_args['format'] = $filter_name;
			}
			$the_query = $wpdb->prepare($this_query, $query_args);
			$status_links[$filter_name] = sprintf( $filter_link, $wpdb->get_var($the_query) );
		}
		$filter = !empty($_GET['filter']) && isset($status_links[$_GET['filter']]) ? $_GET['filter'] : 'all';
		$status_links[$filter] = '<strong>'.$status_links[$filter].'</strong>';
		return $status_links;
	}
	
	private function get_views_template(){
		$formats = $this->item_type == 'import' ? EMIO_Imports::$formats : EMIO_Exports::$formats;
		$views = array(
			"all" => '<a href="'. esc_url(add_query_arg('filter',null)) .'">'. __('All','events-manager-io') . ' (%d)</a>',
		);
		foreach( $formats as $format_key => $EMIO_Item ){
			$views[$format_key] = '<a href="'. esc_url(add_query_arg('filter',$format_key)) .'">'. $EMIO_Item::$format_name . ' (%d)</a>';
		}
		return $views;
	}
	
	public function extra_tablenav( $which ) {
		if( $which != 'top' ) return null;
		echo wp_nonce_field('emio-items-bulk', 'emio_'. $this->item_type .'_nonce');
		?>
		<div class="alignleft actions">
			<?php
			$frequency_value = !empty($_REQUEST['frequency']) ? $_REQUEST['frequency'] : '';
			$status_value = !empty($_REQUEST['status']) ? $_REQUEST['status'] : '';
			if( $this->item_type == 'import' ){
				?>
				<label for="filter-by-frequency" class="screen-reader-text"><?php esc_html_e('Filter by Frequency', 'events-manager-io'); ?></label>
				<select id="filter-by-frequency" name="frequency">
					<optgroup label="<?php esc_html_e('General', 'events-manager-io'); ?>">
						<option value="all"><?php esc_html_e('All Frequency Types', 'events-manager-io'); ?></option>
						<option value="none" <?php if($frequency_value == 'none') echo 'selected'; ?>><?php esc_html_e('No Schedule', 'events-manager-io'); ?></option>
						<option value="scheduled" <?php if($frequency_value == 'scheduled') echo 'selected'; ?>><?php esc_html_e('All Scheduled', 'events-manager-io'); ?></option>
					</optgroup>
					<optgroup label="<?php esc_html_e('Schedules', 'events-manager-io'); ?>">
						<?php foreach( EMIO_Cron::$frequencies as $key => $freq ): ?>
							<option value="frequency-<?php echo esc_attr($key); ?>" <?php if($frequency_value == $key) echo 'selected'; ?>><?php echo esc_html($freq['display']); ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select>
				<script>
					jQuery(document).ready( function($){
						$('#filter-by-frequency').on('change', function(){
							if( $(this).val() === 'all' || $(this).val() === 'none' ){
								$('#filter-by-status').hide();
							}else{
								$('#filter-by-status').show();
							}
						}).trigger('change');
					});
				</script>
				<label for="filter-by-status" class="screen-reader-text"><?php esc_html_e('Filter by Status', 'events-manager-io'); ?></label>
				<select id="filter-by-status" name="status">
					<option value="0"><?php esc_html_e('Active and Inactive', 'events-manager-io'); ?></option>
					<option value="active" <?php if($status_value == 'active') echo 'selected'; ?>><?php esc_html_e('Active', 'events-manager-io'); ?></option>
					<option value="inactive" <?php if($status_value == 'inactive') echo 'selected'; ?>><?php esc_html_e('Inactive', 'events-manager-io'); ?></option>
				</select>
				<?php
			}
			submit_button(__('Filter', 'events-manager'), 'secondary', 'post-query-submit', false);
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
		$columns['cb'] = '<input type="checkbox" />';
		$columns['name'] = __('Name','events-manager-io');
		$columns['format'] = __('Format','events-manager-io');
		if( $this->item_type == 'import' ){
			$columns['source'] = __('Source', 'events-manager-io');
		}
		$columns['frequency'] = __('Frequency','events-manager-io');
		$columns['last'] = __('Last Run','events-manager-io');
		return $columns;
	}
	
	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns(){
		return array(
			'name' => array('name', false),
			'format' => array('format', false),
			'created' => array('created', false),
			'last' => array('last', false),
		);
	}
	
	public function column_name( $item ){
		$title = '<strong>' . $item['name'] . '</strong>';
		$actions = array(
			'edit' => '<a href="'.esc_url( add_query_arg(array('view'=>'edit', 'item_id'=> $item['item_id'], 'orderby'=>false, 'order'=>false)) ).'" class="edit">'. __('Edit','events-manager-io') .'</a>',
			'delete' => '<a href="'.esc_url( add_query_arg(array('action'=>'delete', 'item_id'=> $item['item_id'], 'emio_'. $this->item_type.'_nonce'=> wp_create_nonce('emio-'.$this->item_type.'-delete'))) ).'" class="delete">'. __('Delete','events-manager-io') .'</a>',
			'history' => '<a href="'.esc_url( add_query_arg(array('view'=>'edit', 'tab'=> 'history', 'item_id'=> $item['item_id'], 'orderby'=>false, 'order'=>false)) ).'">'. __('History','events-manager-io') .'</a>',
		);
		if( $this->item_type == 'import' ){
			$actions['Preview'] = '<a href="'.esc_url( add_query_arg(array('view'=>'edit', 'tab'=> 'preview', 'item_id'=> $item['item_id'], 'orderby'=>false, 'order'=>false)) ).'">'. __('Preview','events-manager-io') .'</a>';
		}
		if( $item['frequency_raw'] ){
			if( $item['status'] == 1 ){
				$actions['deactivate'] = '<a href="'. esc_url( add_query_arg(array('action'=>'deactivate', 'item_id'=> $item['item_id'], 'emio_'.$this->item_type.'_nonce'=> wp_create_nonce('emio-'.$this->item_type.'-deactivate'))) ) .'" class="deactivate">'. esc_html__('Deactivate','events-manager-io') .'</a>';
			}else{
				$actions['activate'] = '<a href="'. esc_url( add_query_arg(array('action'=>'activate', 'item_id'=> $item['item_id'], 'emio_'.$this->item_type.'_nonce'=> wp_create_nonce('emio-'.$this->item_type.'-activate'))) ) .'" class="activate">'. esc_html__('Activate','events-manager-io') .'</a>';
			}
		}
		return $title . $this->row_actions( $actions );
	}
	
	/**
	 * Bulk Edit Checkbox
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf('<input type="checkbox" name="item_id[]" value="%s" />', $item['item_id']);
	}
	
	/**
	 * Returns an associative array of bulk actions
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __('Delete','events-manager-io'),
			'activate' => __('Activate','events-manager-io'),
			'deactivate' => __('Deactivate','events-manager-io'),
		);
	}
}
?>