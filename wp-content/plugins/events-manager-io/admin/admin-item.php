<?php
class EMIO_Admin_Item {
	
	public static $type = 'item';
	public static $label_singular = 'Item';
	public static $label_plural = 'Items';
	public static $EMIO_Item = 'EMIO_Item';
	public static $EMIO_Items = 'EMIO_Items';
	
	/**
	 * Decides what page to show in admin area, saves data if required 
	 */
	public static function init(){
		//for now just output main
		self::main();
	}
	
	public static function main(){
		global $EM_Notices; /* @var EM_Notices $EM_Notices */
		if( empty($_REQUEST['view']) ){
			$add_new_link = esc_url(add_query_arg('view', 'new'));
			$base_url = current( explode('?', $_SERVER['REQUEST_URI']));
			$base_query_args = array_intersect_key( $_GET, array_flip(array('view','page','item_id')));
			$base_url = add_query_arg($base_query_args, $base_url);
			?>
			<div class="wrap">
				<h1 class="wp-heading-inline">
					<?php echo esc_html( sprintf(__('Events Manager %s', 'events-manager-io'), static::$label_plural) ); ?>
					<?php if( !empty($_REQUEST['tab']) && $_REQUEST['tab'] == 'history' ) echo esc_html(' - '.__('History', 'events-manager-io')); ?>
				</h1>
				<a href="<?php echo $add_new_link; ?>" class="page-title-action"><?php echo esc_html(sprintf(__('Add %s','events-manager-io'), static::$label_singular)); ?></a>
				<hr class="wp-header-end">
				<?php
				static::general_tabs();
				?>
				<div id="poststuff" class="<?php if(!empty($_REQUEST['tab']) && $_REQUEST['tab'] ) echo 'emio-item-tab-'. $_REQUEST['tab']; ?>">
					<?php
					if( !empty($_REQUEST['tab']) && $_REQUEST['tab'] == 'history' ){
						static::all_history();
					}else{
						static::items();
					}
					?>
				</div>
			</div>
			<?php
		}elseif( $_REQUEST['view'] == 'new' ){
			static::formats();
		}elseif( $_REQUEST['view'] == 'edit' ){
			//if we have an ID supplied, get the import/export ID, otherwise make a blank form
			if( !empty($_REQUEST['item_id']) ){
				$EMIO_Item = EMIO_Items::load( $_REQUEST['item_id'] );
				if( $EMIO_Item === false ){
					$EM_Notices->add_error( esc_html(sprintf( __('Could not find the desired %s, please try again.', 'events-manager-io'), __('format', 'events-manager-io'))) );
					echo '<div class="wrap">'. $EM_Notices . '</div>';
					return false;
				}
			}elseif( !empty($_REQUEST['format']) ){
				if( $_REQUEST['page'] == 'events-manager-io-import' ){
					$EMIO_Item = EMIO_Imports::get_format($_REQUEST['format']);
				}else{
					$EMIO_Item = EMIO_Exports::get_format($_REQUEST['format']);
				}
				$EMIO_Item = new $EMIO_Item(); /* @var EMIO_Item $EMIO_Item */
				$ready = $EMIO_Item->is_ready();
				if( is_wp_error($ready) ){
					/* @var WP_Error $ready */
					$EM_Notices->add_error($ready->get_error_message());
					static::formats();
					return false;
				}
			}else{
				$EM_Notices->add_error(__('Please select a valid format.','events-manager-io'));
				static::formats();
				return false;
			}
			//allow other plugins to intervene before the editor is displayed.
			static::editor( $EMIO_Item );
		}
	}
	
	public static function items(){
		?>
		<div class="emio-admin-items">
			<?php
			include('list-tables/items-list-table.php');
			$EMIO_List_Table = new EMIO_Items_List_Table();
			$EMIO_List_Table->views();
			?>
			<form method="post">
				<?php
				$EMIO_List_Table->prepare_items();
				$EMIO_List_Table->display();
				?>
			</form>
		</div>
		<?php
		return;
	}
	
	public static function formats(){
		global $EM_Notices;
		$formats = $_REQUEST['page'] == 'events-manager-io-import' ? EMIO_Imports::$formats : EMIO_Exports::$formats;
		?>
		<div class="wrap">
			<h1>
				<?php echo sprintf(__('Add New %s', 'events-manager-io'), self::$label_singular); ?>
			</h1>
			<?php echo $EM_Notices; ?>
			<div id="poststuff">
				<p><?php esc_html_e('Please choose your desired format from the list below.', 'events-manager-io'); ?>
				<ul class="emio-item-formats">
					<?php foreach( $formats as $format => $EMIO_Item ){ ?>
						<li>
							<a href="<?php echo esc_url(add_query_arg( array( 'format'=> $EMIO_Item::$format , 'view' => 'edit' ) )); ?>">
								<?php echo esc_html($EMIO_Item::$format_name); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div> <!-- #poststuff -->
		</div>
		<?php
	}
	
	public static function editor( $EMIO_Item ){
		return false;
	}
	
	public static function general_tabs(){
		$tabs = array(
			'items' => array(
				'name' => static::$label_plural,
			),
			'history' => array(
				'name' => sprintf(__('%s History (All)','events-manager-io'), static::$label_singular),
			),
		);
		$base_url = add_query_arg( array('singular' => false) );
		$tabs = apply_filters('emio_admin_item_general_tabs', $tabs, $base_url);
		echo self::generate_admin_tabs( $tabs, $base_url );
		return;
	}
	
	public static function generate_admin_tabs( $tabs = array(), $base_url = false, $request_flag = 'tab' ){
		if( empty($tabs) ) return '';
		$tabs = apply_filters('em_generate_admin_tabs', $tabs, $base_url, $request_flag);
		$tab_html = '<h2 class="nav-tab-wrapper">';
		$single_tab_html = '<a href="%s" id="em-menu-general" class="nav-tab %s">%s</a>';
		//get the active tab name
		if( !empty($_REQUEST[$request_flag]) && array_key_exists($_REQUEST[$request_flag], $tabs) ){
			$active_tab = $_REQUEST[$request_flag];
		}else{
			$active_tab = key($tabs);
		}
		//generate
		$tab_count = 0;
		foreach( $tabs as $tab_key => $tab ){
			$active_flag = $active_tab == $tab_key ? 'nav-tab-active' : '';
			if( empty($tab['url']) ){
				if( $tab_count == 0 ){
					$tab['url'] = $tabs[$tab_key] = add_query_arg( array('tab' => false), $base_url );
				}else{
					$tab['url'] = $tabs[$tab_key] = add_query_arg( array('tab' => $tab_key), $base_url );
				}
			}
			$tab_html .= sprintf($single_tab_html, esc_url($tab['url']), $active_flag, esc_html($tab['name']) );
			$tab_count++;
		}
		$tab_html .= '</h2>';
		return $tab_html;
	}
	
	public static function all_history(){
		?>
		<div class="emio-history">
			<?php
			if( !empty($_REQUEST['singular']) ){
				$description = esc_html__('All %s', 'events-manager-io');
				echo '<h3>'. sprintf($description, static::$label_plural) .'</h3> ';
				echo '<a href="'. esc_url( add_query_arg(array('singular'=>false, 'filter'=>false)) ) .'" class="page-title-action">'. esc_html__('View Grouped', 'events-manager-io').'</a><hr>';
			}else{
				$description = esc_html__('%s Grouped By Date/Time', 'events-manager-io');
				echo '<h3>'. sprintf($description, static::$label_plural) .'</h3> ';
				echo '<a href="'. esc_url( add_query_arg(array('singular'=>1)) ) .'" class="page-title-action">'. esc_html__('View Ungrouped', 'events-manager-io').'</a><hr>';
			}
			include('list-tables/logs-list-table.php');
			$EMIO_List_Table = new EMIO_Logs_List_Table();
			$EMIO_List_Table->views();
			?>
			<form method="post">
				<?php
				$EMIO_List_Table->prepare_items();
				$EMIO_List_Table->display();
				?>
			</form>
		</div>
		<?php
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function item_history($EMIO_Item ){
		global $wpdb;
		?>
		<div class="emio-history">
			<?php
			//if we're in a batch, also get batch info
			if( !empty( $_REQUEST['batch'] ) ){
				$query = $wpdb->prepare('SELECT log_date FROM '. EMIO_TABLE_LOG .' WHERE io_id=%d AND uuid=%s ORDER BY log_date ASC LIMIT 1', $EMIO_Item->ID, $_REQUEST['batch']);
				$batch_date = $wpdb->get_var($query);
				$EM_DateTime = new EM_DateTime( $batch_date );
				$description = esc_html__('%s from %s at %s', 'events-manager-io');
				echo '<h3>'. sprintf($description, static::$label_plural, $EM_DateTime->getDate(), $EM_DateTime->getTime()) .'</h3> ';
				echo '<a href="'. esc_url( add_query_arg(array('batch'=>false)) ) .'" class="page-title-action">'. esc_html__('View All', 'events-manager-io').'</a><hr>';
			}else{
				if( !empty($_REQUEST['singular']) ){
					$description = esc_html__('All %s', 'events-manager-io');
					echo '<h3>'. sprintf($description, static::$label_plural) .'</h3> ';
					echo '<a href="'. esc_url( add_query_arg(array('singular'=>false, 'filter'=>false)) ) .'" class="page-title-action">'. esc_html__('View Grouped', 'events-manager-io').'</a><hr>';
				}else{
					$description = esc_html__('%s Grouped By Date/Time', 'events-manager-io');
					echo '<h3>'. sprintf($description, static::$label_plural) .'</h3> ';
					echo '<a href="'. esc_url( add_query_arg(array('singular'=>1, 'orderby'=>null)) ) .'" class="page-title-action">'. esc_html__('View Ungrouped', 'events-manager-io').'</a><hr>';
				}
			}
			include('list-tables/logs-list-table.php');
			$EMIO_List_Table = new EMIO_Logs_List_Table();
			$EMIO_List_Table->views();
			?>
			<form method="post">
				<?php
				$EMIO_List_Table->prepare_items();
				$EMIO_List_Table->display();
				?>
			</form>
		</div>
		<?php
		return;
	}
}