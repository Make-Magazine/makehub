<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class EMIO_List_Table extends WP_List_Table {
	
	public $per_page = 20;
	public $total_items = 0;
	public $per_page_var = 'emio_logs_per_page';
	public $has_checkboxes = true;
	
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items(){
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		
		$this->per_page = $this->get_items_per_page( $this->per_page_var, 20 );
		$this->items = $this->table_data();
		
		$this->set_pagination_args( array(
			'total_items' => $this->total_items,
			'per_page'    => $this->per_page,
		) );
		
		$this->_column_headers = array($columns, $hidden, $sortable);
	}
	
	/**
	 * Define which columns are hidden
	 *
	 * @return array
	 */
	public function get_hidden_columns(){
		return array();
	}
	
	/**
	 * Should be overriden, obtains data for populating the table.
	 * @return array
	 */
	protected function table_data(){
		return array();
	}
	
	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ){
		if( array_key_exists($column_name, $item) ){
			return $item[$column_name];
		}
		return print_r( $item, true ) ;
	}
	
	public function extra_tablenav_dates(){
		$scope_dates = !empty($_POST['emio_filter_scope_dates']) && is_array($_POST['emio_filter_scope_dates']) ? $_POST['emio_filter_scope_dates'] : array('','');
		?>
		<div class="alignleft actions daterangeactions">
			<span class="em-date-range" id="emio_filter_scope_range">
				<?php ob_start(); ?>
				<label for="emio_filter_scope_start" class="screen-reader-text"><?php esc_html_e('Logs starting from','events-manager-io'); ?></label>
				<input class="em-date-start em-date-input-loc" type="text" />
				<input class="em-date-input" type="hidden" name="emio_filter_scope_dates[]" id="emio_filter_scope_start" value="<?php echo esc_attr($scope_dates[0]); ?>" />
				<?php $start_dates = ob_get_clean(); ?>
				<?php ob_start(); ?>
				<label for="emio_filter_scope_end" class="screen-reader-text"><?php esc_html_e('Logs starting until','events-manager-io'); ?></label>
				<input class="em-date-end em-date-input-loc" type="text" />
				<input class="em-date-input" type="hidden" name="emio_filter_scope_dates[]" id="emio_filter_scope_end" value="<?php echo esc_attr($scope_dates[1]); ?>" />
				<?php $end_dates = ob_get_clean(); ?>
				<?php echo sprintf(esc_html__('Logs from %s and/or until %s','events-manager-io'), $start_dates, $end_dates ); ?>
			</span>
		</div>
		<?php
	}
}