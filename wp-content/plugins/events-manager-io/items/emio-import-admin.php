<?php
class EMIO_Import_Admin extends EMIO_Item_Admin {
	
	/**
	 * Sets some names for error messages
	 */
	public function init(){
		//set some import-specific text variables
		self::$noun = __('Import', 'events-manager-io');
		self::$noun_plural = __('Imports', 'events-manager-io');
		self::$verb = __('Importing', 'events-manager-io');
		self::$past = __('Imported', 'events-manager-io');
	}
	
	public static function i18n( $type ){
		if( $type == 'noun') return __('Import', 'events-manager-io');
		if( $type == 'noun_plural') return __('Imports', 'events-manager-io');
		if( $type == 'verb') return __('Importing', 'events-manager-io');
		if( $type == 'past') return __('Imported', 'events-manager-io');
		return '';
	}
	
	/**
	 * @param EMIO_Import $EMIO_Import
	 */
	public static function get_post( $EMIO_Import ){
		parent::get_post( $EMIO_Import );
		if( isset($_POST['emio_post_status']) ) $EMIO_Import->meta['post_status'] = wp_kses_data($_POST['emio_post_status']);
		if( isset($_POST['emio_post_update_status']) ) $EMIO_Import->meta['post_update_status'] = wp_kses_data($_POST['emio_post_update_status']);
		if( isset($_POST['emio_attachments']) ) $EMIO_Import->meta['attachments'] = wp_kses_data($_POST['emio_attachments']);
		if( isset($_POST['emio_source_type']) ){
			if( $_POST['emio_source_type'] == 'file' ){
				//upload uniquely named file to system for usage later
				$file = !empty($_FILES['emio_source_file']) ? $_FILES['emio_source_file'] : array('size'=>0);
				if( $file['size'] > 0 ){ //if no file uploaded and we have saved field, we're good
					//chenge filename to something random and handle upload
					$file['name'] = preg_replace('/^[^\.]/', 'emio-tmp-'.sha1(wp_salt().microtime()), $file['name']);
					require_once(ABSPATH . "wp-admin" . '/includes/file.php');
					require_once(ABSPATH . "wp-admin" . '/includes/image.php');
					require_once(ABSPATH . 'wp-admin/includes/media.php');
					$source = wp_handle_upload($file, array('test_form' => false));
					if( empty($source['error']) && !empty($source['file']) ){
						//if upload was successful, try to move file to our temporary directory, otherwise, just keep it as is
						$EMIO_Import->source = 'file';
						$upload_dir = wp_upload_dir();
						if( $EMIO_Import->ID ) $EMIO_Import->flush_source(); //delete previous files first
						$EMIO_Import->meta['temp_file'] = $source['file'];
						if( file_exists($upload_dir['basedir'].'/emio-tmp/') || mkdir($upload_dir['basedir'].'/emio-tmp/') ){
							$file_location = $upload_dir['basedir'].'/emio-tmp/' . $file['name'];
							if( rename($source['file'], $file_location) ){
								$EMIO_Import->meta['temp_file'] = $file_location;
								unset($EMIO_Import->meta['source']);
							}
						}
					}else{
						$EMIO_Import->errors['source'] = $source['error'];
					}
				}
			}else{
				unset($EMIO_Import->meta['temp_file'], $EMIO_Import->meta['source']); // in case we set it before
				$EMIO_Import->source = wp_kses_data($_POST['emio_source_type']);
				if( !empty($_POST['emio_source']) ) $EMIO_Import->meta['source'] = esc_url_raw($_POST['emio_source']);
			}
		}
		if( !empty($_POST['emio_frequency']) ){
			$EMIO_Import->frequency = $_POST['emio_frequency'];
			$EMIO_Import->frequency_start = !empty($_POST['emio_frequency_start']) ? wp_kses_data($_POST['emio_frequency_start']) : null;
			$EMIO_Import->frequency_end = !empty($_POST['emio_frequency_end']) ? wp_kses_data($_POST['emio_frequency_end']) : null;
		}else{
			$EMIO_Import->frequency = $EMIO_Import->frequency_start = $EMIO_Import->frequency_end = null;
		}
		if( isset($_POST['emio_field_mapping']) ){
			$field_mapping = array();
			foreach( $_POST['emio_field_mapping'] as $field_key => $field_map ){
				if( !empty($field_map) ){
					$field_mapping[$field_key] = $field_map;
				}
			}
			$EMIO_Import->meta['field_mapping'] = $field_mapping;
		}
		if( !empty($EMIO_Import::$options['ignore_uid']) ){
			$EMIO_Import->meta['ignore_uid'] = !empty($_POST['emio_ignore_uid']);
		}
		$EMIO_Import->meta['ignore_duplicates'] = !empty($_POST['emio_ignore_duplicates']);
		if( !empty($EMIO_Import::$options['fuzzy_location']) ){
			$EMIO_Import->meta['fuzzy_location'] = array();
			if( !empty($EMIO_Import::$options['fuzzy_location']['default']) && isset($_POST['fuzzy_location_default']) ){
				$EMIO_Import->meta['fuzzy_location']['default'] = is_numeric($_POST['fuzzy_location_default']) ? absint($_POST['fuzzy_location_default']) : 0;
			}
			if( !empty($EMIO_Import::$options['fuzzy_location']['delimiter']) && isset($_POST['fuzzy_location_delimiter']) ){
				$EMIO_Import->meta['fuzzy_location']['delimiter'] = wp_kses_data($_POST['fuzzy_location_delimiter']);
			}
			if( !empty($EMIO_Import::$options['fuzzy_location']['format']) && isset($_POST['fuzzy_location_format']) && array_walk($_POST['fuzzy_location_format'],'wp_kses_data') ){
				$EMIO_Import->meta['fuzzy_location']['format'] = $_POST['fuzzy_location_format'];
			}
			if( !empty($EMIO_Import::$options['fuzzy_location']['google_api']) && isset($_POST['fuzzy_location_google_api']) ){
				$EMIO_Import->meta['fuzzy_location']['google_api'] = absint($_POST['fuzzy_location_google_api']);
			}
			if( !empty($EMIO_Import::$options['fuzzy_location']['country']) && isset($_POST['fuzzy_location_country']) ){
				$EMIO_Import->meta['fuzzy_location']['country'] = array_key_exists($_POST['fuzzy_location_country'], em_get_countries()) ? $_POST['fuzzy_location_country']:0;
			}
			if( !empty($EMIO_Import::$options['fuzzy_location']['placeholder_format']) && isset($_POST['fuzzy_location_placeholder_format']) ){
				$EMIO_Import->meta['fuzzy_location']['placeholder_format'] = wp_kses_data($_POST['fuzzy_location_placeholder_format']);
			}
		}
		//save whether to add new taxonomies
		$taxonomies = EM_Object::get_taxonomies();
		foreach( $taxonomies as $tax_name => $tax ){
			if( isset($_POST['emio_taxonomies_new_'.$tax_name]) ) {
				$EMIO_Import->meta['taxonomies_new'][ $tax['name'] ] = $_POST[ 'emio_taxonomies_new_' . $tax_name ] == 1 && current_user_can( 'edit_event_categories' );
			}
			// don't unset so admins could enable this setting for an import
		}
	}
	
	
	
	/**
	 * @param EMIO_Import $EMIO_Import
	 * @return bool
	 */
	public static function validate( $EMIO_Import ){
		//validate upload
		if( $EMIO_Import->source == 'url' && (empty($EMIO_Import->meta['source']) || !preg_match('/^https?:\/\//', $EMIO_Import->meta['source'])) ){
			$EMIO_Import->errors['source'] = __('Invalid source url provided.', 'events-manager-io');
		}elseif( $EMIO_Import->source == 'file' && empty($EMIO_Import->meta['temp_file']) && empty($EMIO_Import->errors['source']) ){
			$EMIO_Import->errors['source'] = __('No source file provided.', 'events-manager-io');
		}
		return apply_filters('emio_import_admin_validate', parent::validate( $EMIO_Import ), $EMIO_Import);
	}
	
	/**
	 * @param EMIO_Import $EMIO_Import
	 */
	public static function settings( $EMIO_Import ){
		//build current set of fields to be output
		do_action('emio_import_admin_settings', $EMIO_Import);
		
		/* START Main Section - Generic stuff like reference name, source of import, frequency of import, etc. */
		
		//output settings page for a single import/export job
		do_action('emio_import_admin_settings_before_main', $EMIO_Import);
		do_action('emio_import_admin_settings_before_main_'.$EMIO_Import::$format, $EMIO_Import);
		
		//Reference name
		static::field_name( $EMIO_Import );
		//What to import/export
		static::field_scope( $EMIO_Import );
		//Fuzzy location resolution
		if( !empty($EMIO_Import::$options['fuzzy_location']) && get_option('dbem_locations_enabled') ){
			static::field_scope_location( $EMIO_Import );
		}
		//source of imports
		static::field_source( $EMIO_Import );
		if( !empty($EMIO_Import::$options['ignore_uid']) ){
			static::field_ignore_uid( $EMIO_Import );
		}
		//frequency of import/export
		static::field_frequency( $EMIO_Import );
		
		do_action('emio_import_admin_settings_after_main', $EMIO_Import);
		do_action('emio_import_admin_settings_after_main_'.$EMIO_Import::$format, $EMIO_Import);
		
		/* END    Main Section */
		/* START  Imported Item Actions - What to do with importable data */
		
		//Actions to take for each imported items, such as applying post status, tags/categories, images etc.
		?>
		<tr><th colspan="2"><h2><?php esc_html_e('Imported Item Actions'); ?></h2></th></tr>
		<?php
		do_action('emio_import_admin_settings_before_actions', $EMIO_Import);
		do_action('emio_import_admin_settings_before_actions_'.$EMIO_Import::$format, $EMIO_Import);
		
		//what to do with duplicates
		static::field_ignore_duplicates( $EMIO_Import );
		//status of imported posts
		static::field_post_status( $EMIO_Import );
		//status of imported posts
		static::field_post_update_status( $EMIO_Import );
		//what to do with attachments
		if( !empty($EMIO_Import::$options['attachments']) ){
			static::field_attachments( $EMIO_Import );
		}
		//filter by or import into category
		if( empty($EMIO_Import->meta['taxonomies']) ) $EMIO_Import->meta['taxonomies'] = array();
		static::field_taxonomies( $EMIO_Import, 'emio_taxonomies_', $EMIO_Import->meta['taxonomies'], __('Add Import %s', 'events-manager-io'), __('Imported items will be assigned these selected %s upon import.', 'events-manager-io'));
		static::field_taxonomies_new( $EMIO_Import, 'emio_taxonomies_new_', $EMIO_Import->meta['taxonomies'], __('Create New %s', 'events-manager-io'), __('If an imported item contains new %1$s, they will be created if enabled.', 'events-manager-io'));
		
		do_action('emio_import_admin_settings_after_actions', $EMIO_Import);
		do_action('emio_import_admin_settings_after_actions_'.$EMIO_Import::$format, $EMIO_Import);
		
		/* END    Imported Item Actions */
		/* START  Filter Options - What gets imported from source data */
		
		?>
		<tr><th colspan="2"><h2><?php esc_html_e('Result Filters'); ?></h2></th></tr>
		<?php
		do_action('emio_import_admin_settings_before_filters', $EMIO_Import);
		do_action('emio_import_admin_settings_before_filters_'.$EMIO_Import::$format, $EMIO_Import);
		
		//general search field
		static::field_filter( $EMIO_Import );
		//date range
		static::field_filter_dates( $EMIO_Import );
		//amount of items to import/export in a job
		static::field_filter_limit( $EMIO_Import );
		
		/* fields to add - filter i/o results by
		 - categories to i/o (different for imports and expors, since we don't know improt cats same as source)
		 - tags to i/o (different for imports and expors, since we don't know improt cats same as source)
		 - within an area
		 */
		
		do_action('emio_import_admin_settings_after_filters', $EMIO_Import);
		do_action('emio_import_admin_settings_after_filters_'.$EMIO_Import::$format, $EMIO_Import);
		
		/* END  Filter Options */
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 * @param string $id
	 * @param array $values
	 * @param string $label
	 * @param string $description
	 */
	public static function field_taxonomies_new($EMIO_Item, $id = 'emio_taxonomies_', $values = array(), $label = '', $description = '' ){
		$taxonomies = EM_Object::get_taxonomies();
		foreach( $taxonomies as $tax_name => $tax ){
			$taxonomy = get_taxonomy($tax['name']);
			$classes = array();
			if( in_array(EM_POST_TYPE_EVENT, $tax['context']) ) $classes[] = 'event-option';
			if( in_array(EM_POST_TYPE_LOCATION, $tax['context']) ) $classes[] = 'location-option';
			?>
			<?php if( user_can($EMIO_Item->user_id, 'edit_event_categories') || current_user_can('edit_event_categories') ): ?>
				<?php emio_input_radio_binary(sprintf($label, $taxonomy->labels->name), $id.$tax_name, !empty($EMIO_Item->meta['taxonomies_new'][$tax['name']]), sprintf($description, $taxonomy->labels->name)); ?>
			<?php else: ?>
				<?php emio_input_radio_binary(sprintf($label, $taxonomy->labels->name), $id.$tax_name, !empty($EMIO_Item->meta['taxonomies_new'][$tax['name']]), sprintf($description, $taxonomy->labels->name) . '<br>' . sprintf(esc_html__('You must have permissions to create or edit %s for this option to be availble to you.', 'events-manager'), $taxonomy->labels->name), '', '' , true); ?>
			<?php endif; ?>
			<?php
			do_action('emio_item_admin_after_taxonomy_new'. $tax['name'], $EMIO_Item);
			do_action('emio_item_admin_after_taxonomy_new_'. $tax['name'] .'_'.$EMIO_Item::$format, $EMIO_Item);
		}
		do_action('emio_item_admin_after_taxonomies_new', $EMIO_Item);
		do_action('emio_item_admin_after_taxonomies_new_'.$EMIO_Item::$format, $EMIO_Item);
	}
}