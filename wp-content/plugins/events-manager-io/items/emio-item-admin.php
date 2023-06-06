<?php
/**
 * Class used to display admin options for a specific format type
 * @author marcus
 *
 */
class EMIO_Item_Admin {
	/**
	 * @var array
	 */
	public static $errors = array();
	
	//these variables are overriden by parent static class
	/**
	 * @var string
	 */
	public static $noun = 'Item';
	/**
	 * @var string
	 */
	public static $noun_plural = 'Items';
	/**
	 * @var string
	 */
	public static $verb = 'Itemize';
	/**
	 * @var string
	 */
	public static $past = 'Itemized';
	
	public static function i18n( $type ){
		if( $type == 'noun') return 'Item';
		if( $type == 'noun_plural') return 'Items';
		if( $type == 'verb') return 'Itemize';
		if( $type == 'past') return 'Itemized';
		return '';
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function get_post( $EMIO_Item ){
		if( !empty($_POST['emio_name']) ) $EMIO_Item->name = wp_kses_data(wp_unslash($_POST['emio_name']));
		if( !empty($_POST['emio_scope']) ) $EMIO_Item->scope = wp_kses_data($_POST['emio_scope']);
		//search filters
		if( !empty($_POST['emio_filter_scope']) ){
			if( $_POST['emio_filter_scope'] == 'custom' ){
				$EMIO_Item->filter_scope = is_array($_POST['emio_filter_scope_dates']) ? implode(',', $_POST['emio_filter_scope_dates']) : array('','');
			}else{
				$EMIO_Item->filter_scope = $_POST['emio_filter_scope'];					
			}
		}else{
			$EMIO_Item->filter_scope = null;
		}
		$EMIO_Item->filter = !empty($_POST['emio_filter']) ? wp_kses_data(wp_unslash($_POST['emio_filter'])) : null;
		$EMIO_Item->filter_limit = ( !empty($_POST['emio_filter_limit']) && is_numeric($_POST['emio_filter_limit']) ) ? absint($_POST['emio_filter_limit']) : null;
		//save taxonomies to add for each import
		$taxonomies = EM_Object::get_taxonomies();
		foreach( $taxonomies as $tax_name => $tax ){
			if( !empty($_POST['emio_taxonomies_'.$tax_name]) && is_array($_POST['emio_taxonomies_'.$tax_name]) ){
				if( !empty($tax['ms']) ) EM_Object::ms_global_switch(); //switch back if ms global mode
				//check context is correct
				if( ($EMIO_Item->scope == 'events' && !in_array(EM_POST_TYPE_EVENT, $tax['context'])) || ($EMIO_Item->scope == 'locations' && !in_array(EM_POST_TYPE_LOCATION, $tax['context'])) ){
					continue;
				}
				//get tax terms for filtering
				$tax_ids = array();
				$tax_terms = get_terms(array('orderby'=>'name','hide_empty'=>0));
				foreach( $tax_terms as $tax_term ){ /* @var $tax_term WP_Term */
					$tax_ids[] = $tax_term->term_id;
				}
				//clean array of attributes
				foreach( $_POST['emio_taxonomies_'.$tax_name] as $k => $v ){
					if( !in_array($v, $tax_ids) ){
						unset($_POST['emio_taxonomies_'.$tax_name][$k]);
					}
				}
				if( !empty($_POST['emio_taxonomies_'.$tax_name]) ) $EMIO_Item->meta['taxonomies'][$tax['name']] = $_POST['emio_taxonomies_'.$tax_name];
				if( !empty($tax['ms']) ) EM_Object::ms_global_switch_back(); //switch back if ms global mode
			}
		}
		// User ID
		if( !$EMIO_Item->user_id ) $EMIO_Item->user_id = get_current_user_id();
		do_action('emio_item_admin_get_post', $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 * @return bool
	 */
	public static function validate( $EMIO_Item ){
		//check a name has been added
		if( empty($EMIO_Item->name) ){
			$EMIO_Item->errors['name'] = __('Please provide a reference name.', 'events-manager-io');
		}
		//check a frequency is a valid cron value
		if( !empty($EMIO_Item->frequency) ){
			if( !array_key_exists($EMIO_Item->frequency, EMIO_Cron::$frequencies) ){
				$EMIO_Item->errors['frequency'] = __('Invalid frequency choice, please choose again.'. 'events-manager-io');
			}
			if( $EMIO_Item->type == 'import' && $EMIO_Item->get_source_type() == 'file' ){
				$EMIO_Item->errors['source'] = sprintf(__('A url must be provided for recurring %s.', 'events-manager-io'), strtolower(static::i18n('noun_plural')));
			}
			if( !empty($EMIO_Item->frequency_start) && !preg_match('/\d{4}-\d{2}-\d{2}/', $EMIO_Item->frequency_start) ){
				$EMIO_Item->errors['frequency_start'] = __('Please provide a valid date format.', 'events-manager-io');
			}
			if( !empty($EMIO_Item->frequency_end) && !preg_match('/\d{4}-\d{2}-\d{2}/', $EMIO_Item->frequency_end) ){
				$EMIO_Item->errors['frequency_end'] = __('Please provide a valid date format.', 'events-manager-io');
				if( !empty($EMIO_Item->frequency_start) && strtotime($EMIO_Item->frequency_end) > strtotime($EMIO_Item->frequency_start) ){
					$EMIO_Item->errors['frequency_end'] = __('Please choose an end date later than the start date.','events-manager');
				}			
			}
		}
		//filter dates
		if( $EMIO_Item->filter_scope ){
			if( !array_key_exists($EMIO_Item->filter_scope, $EMIO_Item->get_filter_scopes()) && !preg_match ( "/^([0-9]{4}-[0-9]{2}-[0-9]{2})?,([0-9]{4}-[0-9]{2}-[0-9]{2})?$/", $EMIO_Item->filter_scope )){
				$EMIO_Item->errors['filter_scope'] = __('Please provide a valid date range for filtering events.','events-manager-io');
			}
		}
		return apply_filters('emio_item_admin_validate', empty($EMIO_Item->errors), $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 * @return bool
	 */
	public static function save($EMIO_Item ){
		global $wpdb;
		$array = array (
		  'uuid' => $EMIO_Item->uuid,
		  'type' => $EMIO_Item->type,
		  'meta' => $EMIO_Item->meta,
		  'user_id' => $EMIO_Item->user_id,
		  'name' => $EMIO_Item->name,
		  'scope' => $EMIO_Item->scope,
		  'status' => $EMIO_Item->status,
		  'source' => $EMIO_Item->source,
		  'format' => $EMIO_Item::$format,
		  'filter' => $EMIO_Item->filter,
		  'filter_scope' => $EMIO_Item->filter_scope,
		  'filter_limit' => $EMIO_Item->filter_limit,
		  'frequency' => $EMIO_Item->frequency,
		  'frequency_start' => $EMIO_Item->frequency_start,
		  'frequency_end' => $EMIO_Item->frequency_end,
		  'date_modified' => current_time('mysql')
		);
		$array['meta'] = maybe_serialize($array['meta']);
		//make format array
		$array_format = array();
		foreach( $array as $k => $v ) $array_format[$k] = '%s';
		$array_format['user_id'] = $array_format['status'] = '%d';
		//add new or update
		if( $EMIO_Item->ID ){
			$result = $wpdb->update(EMIO_TABLE, $array, array('ID' => $EMIO_Item->ID), $array_format, array('ID' => '%d'));
			if( $result !== false) $result = true;
		}else{
			$array['date_created'] = current_time('mysql');
			$array_format['date_created'] = '%s';
			$result = $wpdb->insert(EMIO_TABLE, $array, $array_format);
			if( $result ){
				$EMIO_Item->ID = $wpdb->insert_id;
			}
		}
		return apply_filters('emio_item_admin_save', $result, $EMIO_Item);
	}
	
	/**
	 * Overridable, outputs a form of settings for this EMIO_Item, which would be an import/export
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function settings( $EMIO_Item ){}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_name($EMIO_Item ){
		emio_input_text(__('Reference Name', 'events-manager-io'), 'emio_name', $EMIO_Item->name, __('Something to help you remember this.', 'events-manager-io'), true);
		do_action('emio_item_admin_after_name', $EMIO_Item);
		do_action('emio_item_admin_after_name_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_scope( $EMIO_Item ){
		$values = array(
			//'all' => __('All Items', 'events-manager-io'),
		);
		if( isset($EMIO_Item::$supports['events']) ){
			$values['events'] = __('Events', 'events-manager-io');
			if( is_array($EMIO_Item::$supports['events']) && in_array('locations', $EMIO_Item::$supports['events']) && get_option('dbem_locations_enabled') ){
				$values['events+locations'] = __('Events and Locations', 'events-manager-io');
			}
		}
		if( isset($EMIO_Item::$supports['locations']) && get_option('dbem_locations_enabled') ){
			$values['locations'] = __('Locations', 'events-manager-io');
		}
		emio_input_select(__('Type', 'events-manager-io'), 'emio_scope', $values, $EMIO_Item->scope);
		do_action('emio_item_admin_after_scope', $EMIO_Item);
		do_action('emio_item_admin_after_scope_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * Outputs an input field for the source of this import.
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_source( $EMIO_Item ){
		$source_type = $EMIO_Item->get_source_type();
		$is_file = $source_type == 'file';
		?>
		<tr valign="top" id="emio_source_row">
			<th scope="row">
				<label for="emio_source_type">
					<?php if( $EMIO_Item->type == 'import' ) echo __('Import Source','events-manager-io'); ?>
					<?php if( $EMIO_Item->type == 'export' ) echo __('Export Source','events-manager-io'); ?>
				</label>
			</th>
			<td>
				<?php
				$source_options = $EMIO_Item->get_source_types();
				$source_text = $EMIO_Item->get_source(false);
				$has_group = $has_file = $has_text = false;
				//sort fields out into groups (if any) and output
				$option_groups = array();
				foreach( $source_options['fields'] as $k => $option ){
					if( $k == 'file' ) $has_file = true;
					elseif( empty($option['no_input']) ) $has_text = true;
					if( !empty($option['group']) ){
						$has_group = true;
						$option_groups[$option['group']][$k] = $option['name'];
						if( empty($source_options['groups'][$option['group']]) ) $source_options['groups'][$option['group']] = $option['group'];
					}else{
						$option_groups['single-options'][$k] = $option['name'];
						if( empty($source_options['groups']['single-options']) ) $source_options['groups']['single-options'] = __('Other Sources', 'events-manager-io');
					}
				}
				?>
				<?php if( !empty($option_groups) ): ?>
					<select name="emio_source_type" type="text" id="emio_source_type" class="<?php if( $has_text ) echo 'min-size'; elseif( $has_file ) echo 'mid-size'; else echo 'widefat'; ?>">
                    <?php
                    foreach( $option_groups as $group_key => $option_group ){
                        if( $has_group ) echo '<optgroup label="'.esc_attr($source_options['groups'][$group_key]).'">';
                        emio_input_select_items($option_group, $source_type);
                        if( $has_group ) echo '</optgroup>';
                    }
                    ?>
                    </select>
				<?php else: ?>
					<input type="hidden" name="emio_source_type" id="emio_source_type" value="<?php echo esc_attr(key($option)); ?>" />
				<?php endif; ?>
				<?php if( $has_text ): ?>
					<input type="text" name="emio_source" id="emio_source" placeholder="" class="emio-source-text <?php if( !empty($option_groups) ) echo 'min-size'; ?>" value="<?php if( !$is_file && !is_wp_error($source_text) ) echo esc_attr($source_text); ?>" />
				<?php endif; ?>
				<?php if( $has_file ): ?>
					<input type="file" name="emio_source_file" id="emio_source_file" class="emio-source-file" />
					<?php if( $is_file && !empty($EMIO_Item->meta['temp_file']) ): ?>
						<span class="emio-source-file-uploaded"><?php echo sprintf(esc_html__('File already uploaded. %s', 'events-manager-io'), '<button type="button" class="emio-upload-again">'.esc_html__('Upload again','events-manager-io').'</button>'); ?></span>
					<?php endif; ?>
				<?php endif; ?>
				<?php foreach( $source_options['fields'] as $k => $opts ): ?>
					<?php
					$k = preg_replace('/[^a-zA-Z0-9_]/','-', $k);
					$data = array();
					$data[] = !empty($opts['placeholder']) ? 'data-placeholder="'.$opts['placeholder'].'"':'';
					$data[] = !empty($opts['no_input']) ? 'data-no-input="1"':'';
					?>
					<p class="emio-source-type-text emio-source-type-<?php echo esc_attr($k); ?>"  <?php echo implode(' ', $data); ?>><?php if( !empty($opts['description']) ) echo $opts['description']; ?></p>
				<?php endforeach; ?>
				<?php
				do_action('emio_item_admin_field_source_settings', $EMIO_Item);
				do_action('emio_item_admin_field_source_settings_'.$EMIO_Item::$format, $EMIO_Item);
				?>
			</td>
		</tr>
		<?php
		do_action('emio_item_admin_after_source', $EMIO_Item);
		do_action('emio_item_admin_after_source_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_scope_location($EMIO_Item ){
		//ask if name of location is included in address, as per format property
		$fuzzy_location = $EMIO_Item->get_format_option('fuzzy_location');
		?>
		<tbody class="emio-import-fuzzy-location">
			<tr>
				<td></td>
				<td>
					<a href="#" class="emio-trigger">
						<span class="emio-trigger-show"><?php esc_html_e('Show advanced location options','events-manager-io'); ?></span>
						<span class="emio-trigger-hide"><?php esc_html_e('Hide advanced location options','events-manager-io'); ?></span>
					</a>
					<table class="emio-import-fuzzy-location" style="width:100%;">
						<tr>
							<th colspan="2" style="font-weight:normal;">
								<em><?php esc_html_e('When importing locations, inconsistent formatting or lack of geographical information may make it harder to accurately import locations, below are some settings to help us better format your locations and guess the correct geographical location.', 'events-manager-io'); ?></em>
							</th>
						</tr>
						<?php
						if( isset($fuzzy_location['default']) && $fuzzy_location['default'] !== false ){
							$value = $fuzzy_location['default'] === true ? get_option('dbem_location_default_location'): $fuzzy_location['default'];
							if( defined('EM_OPTIMIZE_SETTINGS_PAGE_LOCATIONS') && EM_OPTIMIZE_SETTINGS_PAGE_LOCATIONS ){
								emio_input_text( __( 'Default Location', 'events-manager'), 'fuzzy_location_default', $value, esc_html__('Please enter your Location ID, or leave blank for no location.','events-manager').' '.__('If there is no location associated with this event, this location will be assigned.','events-manager') );
							}else{
								$location_options = array();
								$location_options[0] = __('no default location','events-manager');
								$EM_Locations = EM_Locations::get();
								foreach($EM_Locations as $EM_Location){
									$location_options[$EM_Location->location_id] = $EM_Location->location_name;
								}
								emio_input_select( __( 'Default Location', 'events-manager'), 'fuzzy_location_default', $location_options, $value, esc_html__( 'If there is no location associated with this event, this location will be assigned.','events-manager') );
							}
						}
						if( isset($fuzzy_location['google_api']) && $fuzzy_location['google_api'] !== false ){
							$desc = __("You can use Google API to determine location data, or improve the accuracy of incomplete location information, including generating geographic coordinates if they are not supplied by the source.",'events-manager-io').' ';
							if( !EMIO_Options::get('google_server_key') ){
								echo "<tr><th>".__('Google API', 'events-manager-io')."</th><td>";
								echo "<select disabled><option>".esc_html__('Do not use Google API.','events-manager-io')."</option></select>";
								$desc = sprintf(esc_html__('A valid Google API Server key is required in your %s.','events-manager-io'), '<a href="edit.php?post_type=event&page=events-manager-options#general#emio">'.esc_html__('Settings Page','events-manager-io').'</a>');
								echo "<p><em>$desc</em></p>";
								echo '<input type="hidden" name="fuzzy_location_google_api" value="0" />';
								echo "</td></tr>";
							}else{
								$google_api_options = array(
									0 => __('Do not use Google API.','events-manager-io'), 
									1 => __('Allow Google API to automatically guess the address.','events-manager-io'), 
									2 => __('Allow Google API to improve the address information.','events-manager-io')
								);
								emio_input_select(__('Google API', 'events-manager-io'), 'fuzzy_location_google_api', $google_api_options, (int) $fuzzy_location['google_api'], $desc);
							}
						}
						$google_api_languages = array('ar' => __('Arabic'),'bg' => __('Bulgarian'),'bn' => __('Bengali'),'ca' => __('Catalan'),'cs' => __('Czech'),'da' => __('Danish'),'de' => __('German'),'el' => __('Greek'),'en' => __('English'),'en-AU' => __('English (Australian)'),'en-GB' => __('English (Great Britain)'),'es' => __('Spanish'),'eu' => __('Basque'),'eu' => __('Basque'),'fa' => __('Farsi'),'fi' => __('Finnish'),'fil' => __('Filipino'),'fr' => __('French'),'gl' => __('Galician'),'gu' => __('Gujarati'),'hi' => __('Hindi'),'hr' => __('Croatian'),'hu' => __('Hungarian'),'id' => __('Indonesian'),'it' => __('Italian'),'iw' => __('Hebrew'),'ja' => __('Japanese'),'kn' => __('Kannada'),'ko' => __('Korean'),'lt' => __('Lithuanian'),'lv' => __('Latvian'),'ml' => __('Malayalam'),'mr' => __('Marathi'),'nl' => __('Dutch'),'no' => __('Norwegian'),'pl' => __('Polish'),'pt' => __('Portuguese'),'pt-BR' => __('Portuguese (Brazil)'),'pt-PT' => __('Portuguese (Portugal)'),'ro' => __('Romanian'),'ru' => __('Russian'),'sk' => __('Slovak'),'sl' => __('Slovenian'),'sr' => __('Serbian'),'sv' => __('Swedish'),'ta' => __('Tamil'),'te' => __('Telugu'),'th' => __('Thai'),'tl' => __('Tagalog'),'tr' => __('Turkish'),'uk' => __('Ukrainian'),'vi' => __('Vietnamese'),'zh-CN' => __('Chinese (Simplified)'),'zh-TW' => __('Chinese (Traditional)'));
						//placeholder format
						if( isset($fuzzy_location['placeholder_format']) && $fuzzy_location['placeholder_format'] !== false ){
							$value = $fuzzy_location['placeholder_format'] === true ? '':$fuzzy_location['placeholder_format'];
							$desc = esc_html_x('If your locations contain a name, you can use this to define where the name %s is contained relative to the address %s. If left blank or no name placeholder is used, the first line of address will be used as a location name.', '%s represent NAME and ADDRESS which should not be translated', 'events-manager-io');
							$desc = sprintf($desc, '<code>NAME</code>', '<code>ADDRESS</code>');
							emio_input_text(__('Location Format', 'events-manager-io'), 'fuzzy_location_placeholder_format', $value, $desc);
						}
						?>
						<tbody id="fuzzy-location-non-default">
							<?php
							//address delimiter
							if( isset($fuzzy_location['delimiter']) && $fuzzy_location['delimiter'] !== false ){
								$value = $fuzzy_location['delimiter'] === true ? ',': $fuzzy_location['delimiter'];
								$desc = esc_html__('By default, we assume sections of an address are split by a comma and space, if split by another character enter it here.', 'events-manager-io');
								$desc .= '</p><p>'.sprintf(esc_html__('Example: %s', 'events-manager-io'), '<code class="emio-fuzzy-location-format-example"></code>').'</p>';
								emio_input_text(__('Location Address Delimiter', 'events-manager-io'), 'fuzzy_location_delimiter', $value, $desc);
							}
							//location address format
							if( isset($fuzzy_location['format']) && $fuzzy_location['format'] !== false ){
								$value_strings = array(
									'address' => __('Address', 'events-manager-io'),
									'town' => __('Town/City', 'events-manager-io'),
									'state' => __('State', 'events-manager-io'),
									'region' => __('Region', 'events-manager-io'),
									'postcode' => __('Postcode', 'events-manager-io'),
									'country' => __('Country', 'events-manager-io'),
								);
								$values = $fuzzy_location['format'] === true ? array('address', 'town', 'state', 'postcode', 'country') : $fuzzy_location['format'];
								foreach( array_keys($value_strings) as $v ) if( !in_array($v, $fuzzy_location['format']) ) $values[] = $v;
								?>
								<tr>
									<th scope="row"><?php esc_html_e('Location Address Format','events-manager-io'); ?></th>
									<td>
										<ul id="emio-fuzzy-location-format">
											<?php 
											foreach( $values as $format_item){
												?>
												<li>
													<span class="dashicons dashicons-sort"></span> 
													<label>
														<input type="checkbox" name="fuzzy_location_format[]" value="<?php echo esc_attr($format_item); ?>" <?php if(in_array($format_item, $fuzzy_location['format'])) echo 'checked'; ?>/>
														<?php echo esc_html($value_strings[$format_item]); ?>
													</label>
												</li>
												<?php
											}
											?>
										</ul>
										<p><em><?php echo sprintf(esc_html__('Example: %s', 'events-manager-io'), '<code class="emio-fuzzy-location-format-example"></code>'); ?></em></p>
										<p><em>
											<?php esc_html_e('Combined with the delimiter setting above, you can tell us what is included in the source addresses and the order of these items by selecting the items above and dragging/dropping them into the correct order.', 'events-manager-io'); ?>
											<?php esc_html_e('Some fields are required in order to create a location, you can only choose the order those appear in.', 'events-manager-io'); ?>
											<?php esc_html_e("If a location name isn't included, the address line will be used instead as the location name.", 'events-manager-io'); ?>
										</em></p>
									</td>
								</tr>
								<?php
							}
							if( isset($fuzzy_location['country']) && $fuzzy_location['country'] !== false ){
								$value = $fuzzy_location['country'] === true ? get_option('dbem_location_default_country'): $fuzzy_location['country'];
								$desc = __("If the imported location data doesn't include a country, a location record cannot be created unless you choose a default country for it to be in.",'events-manager-io');
								emio_input_select( __( 'Default Location Country', 'events-manager'), 'fuzzy_location_country', em_get_countries(__('no default country', 'events-manager')), $value, $desc );
							}
							do_action('emio_item_settings_scope_location_bottom_'.$EMIO_Item::$format, $EMIO_Item);
							?>
						<tbody>
					</table>
				</td>
			</tr>
		</tbody>
		<?php
		do_action('emio_item_admin_after_scope_location', $EMIO_Item);
		do_action('emio_item_admin_after_scope_location_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_ignore_uid($EMIO_Item ){
		$desc = esc_html__("(Advanced) If this source doesn't provide a static unique identifier for each event or location, set this to yes so we will not import items more than once. See our %s for a full explanation about this issue.", 'events-manager-io');
		$desc = sprintf( $desc, '<a href="http://wp-events-plugin.com/documentation/importer/unique-identifiers/">'.esc_html__('documentaion', 'events-manager-io') ).'</a>';
		emio_input_radio_binary(__('Ignore Source IDs?', 'events-manager-io'), 'emio_ignore_uid', !empty($EMIO_Item->meta['ignore_uid']), $desc);
		do_action('emio_item_admin_after_ignore_uid', $EMIO_Item);
		do_action('emio_item_admin_after_ignore_uid_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_ignore_duplicates($EMIO_Item ){
		$desc = esc_html__("If another event exists with the exact same name and times, the event to be imported will be ignored.", 'events-manager-io');
		emio_input_radio_binary(__('Ignore Duplicate Events?', 'events-manager-io'), 'emio_ignore_duplicates', !empty($EMIO_Item->meta['ignore_duplicates']), $desc);
		do_action('emio_item_admin_after_ignore_duplicates', $EMIO_Item);
		do_action('emio_item_admin_after_ignore_duplicates_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_frequency($EMIO_Item ){
		$values = array('0' => sprintf(__('None, this is a one-time %s','events-manager-io'), static::i18n('noun')));
		foreach( EMIO_Cron::$frequencies as $key => $freq ){
			$values[$key] = $freq['display']; 
		}
		?>
		<tbody class="emio-frequency">
		<?php
		$desc = esc_html__("If you'd like this %s to run on a regular basis, choose the frequency. A URL source is required.",'events-manager-io');
		emio_input_select(__('Frequency', 'events-manager-io'), 'emio_frequency', $values, $EMIO_Item->frequency, sprintf($desc, static::i18n('noun')));
		$freq_start = !empty($EMIO_Item->frequency_start) ? $EMIO_Item->frequency_start : '';
		$freq_end = !empty($EMIO_Item->frequency_end) ? $EMIO_Item->frequency_end : '';
		?>
		<tr class="emio-frequency-dates emio-frequency-option">
			<th scope="row"></th>
			<td class="em-date-range">
				<?php ob_start(); ?>
				<label for="emio_frequency_start" class="screen-reader-text"><?php echo esc_html_x('Start on','import or export', 'events-manager-io'); ?></label>		
				<input class="em-date-start em-date-input-loc" type="text" />
				<input class="em-date-input" type="hidden" name="emio_frequency_start" id="emio_frequency_start" value="<?php echo esc_attr($freq_start); ?>" />
				<?php $start_dates = ob_get_clean(); ?>
				<?php ob_start(); ?>
				<label for="emio_frequency_end" class="screen-reader-text"><?php echo esc_html_x('End on','import or export', 'events-manager-io'); ?></label>
				<input class="em-date-end em-date-input-loc" type="text" />
				<input class="em-date-input" type="hidden" name="emio_frequency_end" id="emio_frequency_end" value="<?php echo esc_attr($freq_end); ?>" />
				<?php $end_dates = ob_get_clean(); ?>
				<?php echo sprintf(esc_html__('Run between %s and %s', 'events-manager-io'), $start_dates, $end_dates ); ?>
				<p><em><?php esc_html_e('Leave start date blank to start now and/or end date blank to run indefinitely.', 'events-manager-io'); ?></em></p>
			</td>
		</tr>
		</tbody>
		<?php
		do_action('emio_item_admin_after_frequency', $EMIO_Item);
		do_action('emio_item_admin_after_frequency_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_post_status($EMIO_Item ){
		$values = array(
			'publish' => __('Publish', 'events-manager-io'),
			'pending' => __('Pending Review', 'events-manager-io'),
			'draft' => __('Draft', 'events-manager-io')
		);
		$desc = esc_html__('When your items are imported, you can choose to automatically given them a status of published, draft or pending review.','events-manager-io');
		emio_input_select(__('Import Status', 'events-manager-io'), 'emio_post_status', $values, $EMIO_Item->meta['post_status'], $desc);
		do_action('emio_item_admin_after_status', $EMIO_Item);
		do_action('emio_item_admin_after_status_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_post_update_status($EMIO_Item ){
		$values = array(
			'ignore' => __('Ignore updates', 'events-manager-io'),
			'same' => __('Update and keep previous post status', 'events-manager-io'),
			'pending' => __("Update and change to 'Pending Review' status", 'events-manager-io'),
			'draft' => __("Update and change to 'Draft' status", 'events-manager-io')
		);
		$desc = esc_html__('Choose what to do with previously imported items requiring an update. You can update and keep or change the post status or ignore updates entirely.', 'events-manager-io');
		emio_input_select(__('Import Updates', 'events-manager-io'), 'emio_post_update_status', $values, $EMIO_Item->meta['post_update_status'], $desc);
		do_action('emio_item_admin_after_status_update', $EMIO_Item);
		do_action('emio_item_admin_after_status_update_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_attachments($EMIO_Item ){
		/* @todo add ability to keep remote URL instead of downloading the image, requires changing EM so it accepts _thumbnail_url or similar meta value. */
		$values = array(
			//'remote' => __('Link to remote attachment (when possible)', 'events-manager-io'),
			'download' => __('Download featured image', 'events-manager-io'),
			'ignore' => __('Ignore featured image', 'events-manager-io'),
		);
		$desc = esc_html__("When your items are imported, you can choose to download the featured image to your site, or ignore it.", 'events-manager-io');
		emio_input_select(__('Attachment Actions', 'events-manager-io'), 'emio_attachments', $values, $EMIO_Item->meta['attachments'], $desc);
		do_action('emio_item_admin_after_attach', $EMIO_Item);
		do_action('emio_item_admin_after_attach_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 * @param string $id
	 * @param array $values
	 * @param string $label
	 * @param string $description
	 */
	public static function field_taxonomies($EMIO_Item, $id = 'emio_taxonomies_', $values = array(), $label = '', $description = '' ){
		$taxonomies = EM_Object::get_taxonomies();
		foreach( $taxonomies as $tax_name => $tax ){
			$taxonomy = get_taxonomy($tax['name']);
			$classes = array();
			if( in_array(EM_POST_TYPE_EVENT, $tax['context']) ) $classes[] = 'event-option';
			if( in_array(EM_POST_TYPE_LOCATION, $tax['context']) ) $classes[] = 'location-option';
			if( !empty($tax['ms']) ) EM_Object::ms_global_switch(); //switch back if ms global mode
			?>
			<tr class="<?php echo esc_attr( implode(' ', $classes) ); ?>" id="<?php echo esc_attr($id.$tax_name); ?>_row">
				<th scope="row">
					<label for="<?php echo esc_attr($id.$tax_name) ?>[]" for="<?php echo esc_attr($id.$tax_name) ?>"><?php echo esc_html( sprintf($label, $taxonomy->labels->name) ); ?></label>
				</th>
				<td>
					<select name="<?php echo esc_attr($id.$tax_name) ?>[]" id="<?php echo esc_attr($id.$tax_name) ?>" multiple class="widefat emio-select2">
					<?php
					$tags = get_terms($tax['name'], array('orderby'=>'name','hide_empty'=>0));
					$walker = new EM_Walker_CategoryMultiselect();
					$tax_values = !empty($values[$tax['name']]) && is_array($values[$tax['name']]) ? $values[$tax['name']]:array();
					$args_em = array( 'hide_empty' => 0, 'name' => esc_attr($id).'[]', 'hierarchical' => true, 'id' => $tax['name'], 'taxonomy' => $tax['name'], 'selected' => $tax_values, 'walker'=> $walker);
					echo walk_category_dropdown_tree($tags, 0, $args_em);
					?>
					</select>
					<?php if( is_array($description) && !empty($description[$tax['name']]) ): ?>
					<p><em><?php echo sprintf($description[$tax['name']], $taxonomy->labels->name); ?></em></p>
					<?php elseif( !empty($description) && !is_array($description) ): ?>
					<p><em><?php echo sprintf($description, $taxonomy->labels->name); ?></em></p>
					<?php endif; ?>
				</td>
			</tr>
			<?php
			if( !empty($tax['ms']) ) EM_Object::ms_global_switch_back(); //switch back if ms global mode
			do_action('emio_item_admin_after_taxonomy_'. $tax['name'], $EMIO_Item);
			do_action('emio_item_admin_after_taxonomy_'. $tax['name'] .'_'.$EMIO_Item::$format, $EMIO_Item);
		}
		do_action('emio_item_admin_after_taxonomies', $EMIO_Item);
		do_action('emio_item_admin_after_taxonomies_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_filter($EMIO_Item ){
		emio_input_text(__('Search Text'), 'emio_filter', $EMIO_Item->filter, esc_html__('Filter by items containing this text in the name or description of events and/or locations.','events-manager-io'));
		do_action('emio_item_admin_after_filter', $EMIO_Item);
		do_action('emio_item_admin_after_filter_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_filter_dates($EMIO_Item ){
		?>
		<tr class="event-option" id="emio_filter_scope_row">
			<th scope="row"><label for=""><?php esc_html_e('Event Start Date', 'events-manager-io'); ?></label></th>
			<td>
				<?php
				$scope_dates = $EMIO_Item->get_filter_scope();
				$scope_select = preg_match ( "/^([0-9]{4}-[0-9]{2}-[0-9]{2})?,([0-9]{4}-[0-9]{2}-[0-9]{2})?$/", $EMIO_Item->filter_scope ) ? 'custom':$EMIO_Item->filter_scope;
				?>
				<select name="emio_filter_scope" id="emio_filter_scope">
					<option value="0"><?php esc_html_e('All Events'); ?></option>
					<?php foreach( $EMIO_Item->get_filter_scopes() as $k => $v ): ?>
					<option value="<?php echo esc_attr($k); ?>" <?php if($scope_select == $k) echo 'selected="selected"'; ?>><?php echo esc_html($v); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="em-date-range" id="emio_filter_scope_range">
					<?php ob_start(); ?>
					<label for="emio_filter_scope_start" class="screen-reader-text"><?php esc_html_e('Events starting until','events-manager'); ?></label>		
					<input class="em-date-start em-date-input-loc" type="text" />
					<input class="em-date-input" type="hidden" name="emio_filter_scope_dates[]" id="emio_filter_scope_start" value="<?php if( is_array($scope_dates) ) echo esc_attr($scope_dates[0]); ?>" />
					<?php $start_dates = ob_get_clean(); ?>
					<?php ob_start(); ?>
					<label for="emio_filter_scope_end" class="screen-reader-text"><?php esc_html_e('Events starting until','events-manager'); ?></label>
					<input class="em-date-end em-date-input-loc" type="text" />
					<input class="em-date-input" type="hidden" name="emio_filter_scope_dates[]" id="emio_filter_scope_end" value="<?php if( is_array($scope_dates) ) echo esc_attr($scope_dates[1]); ?>" />
					<?php $end_dates = ob_get_clean(); ?>
					<?php echo sprintf(esc_html__('Filter events starting on %s and/or until %s','events-manager-io'), $start_dates, $end_dates ); ?>
				</span>
				<p><em><?php esc_html_e('You can filter events by a relative time value to date of execution. It can also be within a custom date range, or alternatively a starting or ending date if you leave either of the date fields blank.', 'events-manager-io'); ?></em></p>
			</td>
		</tr>
		<?php
		do_action('emio_item_admin_after_filter_dates', $EMIO_Item);
		do_action('emio_item_admin_after_filter_dates_'.$EMIO_Item::$format, $EMIO_Item);
	}
	
	/**
	 * @param EMIO_Item $EMIO_Item
	 */
	public static function field_filter_limit($EMIO_Item ){
		$desc = sprintf(esc_html_x('If a number is provided, only this many items will be %s each time.', 'imported or exported','events-manager-io'), static::i18n('past'));
		if( $EMIO_Item->type == 'import' ){
			$desc .= ' '. __('Previously imported items will not count towards this limit.');
		}
		emio_input_text(__('Limit'), 'emio_filter_limit', $EMIO_Item->filter_limit, $desc);
		do_action('emio_item_admin_after_filter_limit', $EMIO_Item);
		do_action('emio_item_admin_after_filter_limit_'.$EMIO_Item::$format, $EMIO_Item);
	}
}