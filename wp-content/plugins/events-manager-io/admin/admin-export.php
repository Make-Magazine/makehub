<?php
class EMIO_Admin_Export extends EMIO_Admin_Item {
	
	public static $type = 'export';
	public static $label_singular = 'Export';
	public static $label_plural = 'Exports';
	public static $EMIO_Item = 'EMIO_Export';
	public static $EMIO_Items = 'EMIO_Exports';
	
	/**
	 * Decides what page to show in admin area, saves data if required 
	 */
	public static function init(){
		//for now just output main
		static::$label_singular = __('Export', 'events-manager-io');
		static::$label_plural = __('Exports', 'events-manager-io');
		self::main();
	}
	
	/**
	 * Displays an export editor.
	 * @param EMIO_Export $EMIO_Export
	 */
	public static function editor( $EMIO_Export ){
		// @TODO validating the export here shouldn't be necessary, probably should be done in an action and then set the tab accordingly
		do_action('emio_admin_export_display_editor', $EMIO_Export);
		do_action('emio_admin_export_display_editor_'.$EMIO_Export::$format, $EMIO_Export);
		//define the active tab and validate export for showing export buttons
		$active_tab = 'settings';
        if( $EMIO_Export->ID ){
	        $EM_Notices = new EM_Notices(false);
        	if( EMIO_Export_Admin::validate($EMIO_Export) ){
		        $active_tab = !empty($_GET['tab']) ? $_GET['tab'] : 'settings';
        		if( $EMIO_Export::$method== 'pull' ){
	                //Build a notice to let user export the file according to their settings, either a download file button, download link or push-to-destination button
			        $download_button_url = add_query_arg( array('emio_export_nonce' => wp_create_nonce('emio-export-download'), 'action' => 'download') );
			        $download_buttons = array('<a href="'. esc_url( $download_button_url ) .'" class="button-primary">'.esc_html__('Download Export File', 'events-manager-io').'</a>');
			        if( preg_match('/^public\-(feed|dl)$/', $EMIO_Export->source) ){
			            $feed_url = $EMIO_Export->get_feed_url();
				        $download_text = '<p><strong>'. esc_html__('This export is available to be downloaded and can also be accessed publicly.', 'events-manager-io') .'</strong></p>';
				        $download_buttons[] = '<a href="'. esc_url( $feed_url ) .'" class="button-secondary" target="_blank">'.esc_html__('Visit Public URL', 'events-manager-io').'</a>';
			        }else{
				        $download_text = '<p><strong>'. esc_html__('This export is available to be downloaded.', 'events-manager-io') .'</strong></p>';
			        }
			        $EM_Notices->add_confirm( $download_text . '<p>' . implode(' ', $download_buttons) . '</p>' );
		        }else{
			        //Build a notice to let user export the file according to their settings, either a download file button, download link or push-to-destination button
			        $download_button_url = add_query_arg( array('emio_export_nonce' => wp_create_nonce('emio-export-run'), 'action' => 'run') );
			        $download_buttons = array('<a href="'. esc_url( $download_button_url ) .'" class="button-primary">'.esc_html__('Run Export', 'events-manager-io').'</a>');
			        $download_text = '<p><strong>'. esc_html__('This export is ready to be initiated.', 'events-manager-io') .'</strong></p>';
			        $EM_Notices->add_confirm( $download_text . '<p>' . implode(' ', $download_buttons) . '</p>' );
		        }
            }else{
		        $EM_Notices->add_alert( $EMIO_Export->errors );
	        }
	        echo $EM_Notices;
		}elseif( !empty($EMIO_Export->errors) ){
			$EM_Notices = new EM_Notices(false);
			$EM_Notices->add_alert($EMIO_Export->errors);
			echo $EM_Notices;
		}
		
		$base_url = current( explode('?', $_SERVER['REQUEST_URI']));
		$base_query_args = array_intersect_key( $_GET, array_flip(array('view','page','item_id')));
		$base_url = add_query_arg($base_query_args, $base_url);
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">
				<?php
				if( $EMIO_Export->ID ){
					echo sprintf(__('Edit %s', 'events-manager-io'), __('Export', 'events-manager-io'));
					echo ' - "'. esc_html($EMIO_Export->name) . '"';
				}else{
					echo sprintf(__('Add New %s', 'events-manager-io'), __('Export', 'events-manager-io'));
				}
				?> -
				<?php echo esc_html( sprintf(__('%s Format','events-manager-io'), $EMIO_Export::$format_name) ); ?>
			</h1>
			<?php do_action('emio_admin_export_header_buttons', $EMIO_Export); ?>
			<hr class="wp-header-end">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url(add_query_arg('tab',false, $base_url)); ?>" id="em-menu-general" class="nav-tab <?php if($active_tab == 'settings') echo 'nav-tab-active'; ?>"><?php esc_html_e('Settings','events-manager-io'); ?></a>
				<?php if( $EMIO_Export::$method !== 'pull' ): ?>
					<a href="<?php echo esc_url(add_query_arg('tab','history', $base_url)); ?>" id="em-menu-formats" class="nav-tab <?php if($active_tab == 'history') echo 'nav-tab-active'; ?>"><?php esc_html_e('History','events-manager-io'); ?></a>
				<?php endif; ?>
			</h2>
			<div id="poststuff">
				<?php
				if( $active_tab == 'history' ){
					self::item_history($EMIO_Export);
				}else{
					self::settings($EMIO_Export);
				}
				?>
			</div>
			<!-- #poststuff -->
		</div>
		<?php
	}

	/**
	 * @param EMIO_Export $EMIO_Export
	 */
	public static function settings( $EMIO_Export ){
	    ?>
        <form action="<?php echo esc_url(add_query_arg(array('tab' => null))); ?>" method="post" enctype="multipart/form-data" id="emio-editor" class="emio-editor-export">
            <input type="hidden" name="format" value="<?php echo esc_attr($EMIO_Export::$format); ?>" />
            <input type="hidden" name="item_id" value="<?php echo esc_attr($EMIO_Export->ID); ?>" />
            <input type="hidden" name="action" value="save" />
	        <input type="hidden" name="export" value="0" id="emio-editor-export-flag" />
			<?php echo wp_nonce_field('emio-export-edit', 'emio_export_nonce'); ?>
			<?php if( $EMIO_Export::$format ): ?>
                <table class="form-table">
                    <tbody>
					<?php EMIO_Export_Admin::settings( $EMIO_Export ); ?>
                    </tbody>
                </table>
                <p>
	                <input type="submit" id="emio-editor-submit-export" value="<?php esc_attr_e('Save and Export','events-manager-io'); ?>" class="button-primary" />
                </p>
			<?php endif; ?>
        </form>
        <?php
    }
}