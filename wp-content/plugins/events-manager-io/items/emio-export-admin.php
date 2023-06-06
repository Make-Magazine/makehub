<?php
class EMIO_Export_Admin extends EMIO_Item_Admin {
	
	public static function i18n( $type ){
		if( $type == 'noun') return __('Export', 'events-manager-io');
		if( $type == 'noun_plural') return __('Exports', 'events-manager-io');
		if( $type == 'verb') return __('Exporting', 'events-manager-io');
		if( $type == 'past') return __('Exported', 'events-manager-io');
		return '';
	}
	
	public static function get_post( $EMIO_Export ){
		parent::get_post( $EMIO_Export );
		if( $EMIO_Export::$method== 'pull' ){
			$EMIO_Export->source = !empty($_POST['emio_source_url']) && preg_match('/^public\-(feed|dl)$/', $_POST['emio_source_url']) ? $_POST['emio_source_url']:null;
			if( $EMIO_Export->source !== null && empty($EMIO_Export->uuid ) ){
				$EMIO_Export->uuid = wp_generate_uuid4();
			}
		}elseif( $EMIO_Export::$method== 'push' && isset($_POST['emio_source_type']) ){
			$EMIO_Export->source = wp_kses_data($_POST['emio_source_type']);
			if( !empty($_POST['emio_source']) ) $EMIO_Export->meta['source'] = wp_kses_data($_POST['emio_source']);
		}
	}
	
	/**
	 * @param EMIO_Export $EMIO_Export
	 */
	public static function settings( $EMIO_Export ){
		//build current set of fields to be output
		do_action('emio_export_admin_settings', $EMIO_Export);
		
		/* START Main Section - Generic stuff like reference name, export type, frequency of export, etc. */
		
		//output settings page for a single import/export job
		do_action('emio_export_admin_settings_before_main', $EMIO_Export);
		do_action('emio_export_admin_settings_before_main_'.$EMIO_Export::$format, $EMIO_Export);
		//Reference name
		static::field_name( $EMIO_Export );
		//What to import/export
		static::field_scope( $EMIO_Export );
		//source of imports
		static::field_source( $EMIO_Export );
		
		do_action('emio_export_admin_settings_after_main', $EMIO_Export);
		do_action('emio_export_admin_settings_after_main_'.$EMIO_Export::$format, $EMIO_Export);
		
		/* END    Main Section */
		/* START  Filter Options - What gets imported from source data */
		
		?>
		<tr><th colspan="2"><h2><?php esc_html_e('Result Filters'); ?></h2></th></tr>
		<?php
		do_action('emio_export_admin_settings_before_filters', $EMIO_Export);
		do_action('emio_export_admin_settings_before_filters_'.$EMIO_Export::$format, $EMIO_Export);
		//filter by or import into category
		if( empty($EMIO_Export->meta['taxonomies']) ) $EMIO_Export->meta['taxonomies'] = array();
		static::field_taxonomies( $EMIO_Export, 'emio_taxonomies_', $EMIO_Export->meta['taxonomies'], '%s', __('Export items with the following %s.', 'events-manager-io'));
		//general search field
		static::field_filter( $EMIO_Export );
		//date range
		static::field_filter_dates( $EMIO_Export );
		//amount of items to import/export in a job
		static::field_filter_limit( $EMIO_Export );
		/* fields to add - filter i/o results by
		 - categories to i/o (different for imports and expors, since we don't know improt cats same as source)
		 - tags to i/o (different for imports and expors, since we don't know improt cats same as source)
		 - location within an area
		 */
		do_action('emio_export_admin_settings_after_filters', $EMIO_Export);
		do_action('emio_export_admin_settings_after_filters_'.$EMIO_Export::$format, $EMIO_Export);
		
		/* END  Filter Options */
	}

	/**
	 * Outputs an input field for the source of this import.
	 * @param EMIO_Export $EMIO_Export
	 */
	public static function field_source( $EMIO_Export ){
		if( $EMIO_Export::$method== 'pull' ){
			?>
			<tr valign="top" id="emio_source_row">
				<th scope="row"><label for="emio_source_url"><?php echo __('Export Destination','events-manager-io'); ?></label></th>
				<td>
					<select name="emio_source_url" class="widefat">
						<option value="0"><?php esc_html_e('Private exportable download', 'events-manager-io'); ?></option>
						<option value="public-feed"<?php if( $EMIO_Export->source == 'public-feed' ) echo 'selected'; ?>><?php esc_html_e('Publicly accessible feed', 'events-manager-io'); ?></option>
						<option value="public-dl"<?php if( $EMIO_Export->source == 'public-dl' ) echo 'selected'; ?>><?php esc_html_e('Publicly downloadable file', 'events-manager-io'); ?></option>
					</select>
					<p><em><?php esc_html_e('Public feeds and files can be shared with others via an auto-generated URL once you save your export. This link will contain up-to-date listings according to your filters.','events-manager-io'); ?></em></p>
					<?php
					do_action('emio_export_admin_field_source_settings', $EMIO_Export);
					do_action('emio_export_admin_field_source_settings_'.$EMIO_Export::$format, $EMIO_Export);
					?>
				</td>
			</tr>
			<?php
		}else{
			//show a list of
			parent::field_source( $EMIO_Export );
		}
		do_action('emio_export_admin_after_source', $EMIO_Export);
		do_action('emio_export_admin_after_source_'.$EMIO_Export::$format, $EMIO_Export);
	}
}