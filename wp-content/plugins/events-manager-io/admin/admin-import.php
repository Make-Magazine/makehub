<?php
class EMIO_Admin_Import extends EMIO_Admin_Item {
	
	public static $type = 'import';
	public static $label_singular = 'Import';
	public static $label_plural = 'Imports';
	public static $EMIO_Item = 'EMIO_Import';
	public static $EMIO_Items = 'EMIO_Imports';
	
	/**
	 * Decides what page to show in admin area, saves data if required 
	 */
	public static function init(){
		static::$label_singular = __('Import', 'events-manager-io');
		static::$label_plural = __('Imports', 'events-manager-io');
		self::main();
	}
	
	/**
	 * @param EMIO_Import $EMIO_Import
	 * @return void
	 */
	public static function editor($EMIO_Import ){
		// @TODO validating the import here shouldn't be necessary, probably should be done in an action and then set the tab accordingly
		do_action('emio_admin_import_display_editor', $EMIO_Import);
		do_action('emio_admin_import_display_editor_'.$EMIO_Import::$format, $EMIO_Import);
		$has_mapping = $EMIO_Import::$field_mapping;
		$mapping_defined = $has_mapping && !empty($EMIO_Import->meta['field_mapping']);
		//define the active tab
		$active_tab = 'settings';
		//check import validity and output notices accordingly
		$EM_Notices = new EM_Notices(false);
        if( $EMIO_Import->ID && EMIO_Import_Admin::validate($EMIO_Import) ){
            $active_tab = !empty($_GET['tab']) ? $_GET['tab'] : 'settings';
            //if we're dealing with a mapping tab
            if( !empty($_GET['tab']) && $_GET['tab'] != 'mapping' && $has_mapping && !$mapping_defined ){
                $EM_Notices->add_alert( __('Please save your field mapping settings first, so we know what each fields in your '));
                $active_tab = 'mapping';
            }elseif( $EMIO_Import->frequency ){
            	if( !$EMIO_Import->status ){
		            //Build a notice to let user activate the import
		            $activate_button_url = add_query_arg(array('action'=>'activate', 'item_id'=> $EMIO_Import->ID, 'emio_import_nonce'=> wp_create_nonce('emio-import-activate')));
		            $status_button = '<a href="'. esc_url( $activate_button_url ) .'" class="button-primary">'.esc_html__('Activate', 'events-manager-io').'</a>';
		            $download_text = '<p><strong>'. esc_html__('This import is scheduled but not active.', 'events-manager-io') .'</strong></p>';
		            $EM_Notices->add_info( $download_text . '<p>' . $status_button . '</p>' );
	            }else{
		            $deactivate_button_url = add_query_arg(array('action'=>'deactivate', 'item_id'=> $EMIO_Import->ID, 'emio_import_nonce'=> wp_create_nonce('emio-import-deactivate')));
		            $status_button = '<a href="'. esc_url( $deactivate_button_url ) .'" class="button-primary">'.esc_html__('Deactivate', 'events-manager-io').'</a>';
		            $download_text = '<p><strong>'. esc_html__('This import is scheduled and active.', 'events-manager-io') .'</strong></p>';
		            $EM_Notices->add_confirm( $download_text . '<p>' . $status_button . '</p>' );
	            }
            }
        }elseif( !empty($EMIO_Import->errors) ){
            $EM_Notices->add_alert($EMIO_Import->errors);
        }
		echo $EM_Notices;
        // Begin page output
		$base_url = current( explode('?', $_SERVER['REQUEST_URI']));
		$base_query_args = array_intersect_key( $_GET, array_flip(array('view','page','item_id')));
		$base_url = add_query_arg($base_query_args, $base_url);
		?>
		<div class="wrap">
			<h1>
				<?php
				if( $EMIO_Import->ID ){
					echo sprintf(__('Edit %s', 'events-manager-io'), __('Import', 'events-manager-io'));
				}else{
					echo sprintf(__('Add New %s', 'events-manager-io'), __('Import', 'events-manager-io'));
				}
				?> -
				<?php echo esc_html( sprintf(__('%s Format','events-manager-io'), $EMIO_Import::$format_name) ); ?>
			</h1>
			<?php if( !empty($_GET['item_id']) ): ?>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_url(add_query_arg('tab',false, $base_url)); ?>" id="em-menu-general" class="nav-tab <?php if($active_tab == 'settings') echo 'nav-tab-active'; ?>"><?php esc_html_e('Settings','events-manager-io'); ?></a>
                <?php if( $has_mapping ): ?>
                <a href="<?php echo esc_url(add_query_arg('tab','mapping', $base_url)); ?>" id="em-menu-mapping" class="nav-tab <?php if($active_tab == 'mapping') echo 'nav-tab-active'; ?>"><?php esc_html_e('Field Mapping','events-manager-io'); ?></a>
                <?php endif; ?>
                <a href="<?php echo esc_url(add_query_arg('tab','preview', $base_url)); ?>" id="em-menu-formats" class="nav-tab <?php if($active_tab == 'preview') echo 'nav-tab-active'; ?>"><?php esc_html_e('Preview & Import','events-manager-io'); ?></a>
	            <a href="<?php echo esc_url(add_query_arg('tab','history', $base_url)); ?>" id="em-menu-formats" class="nav-tab <?php if($active_tab == 'history') echo 'nav-tab-active'; ?>"><?php esc_html_e('History','events-manager-io'); ?></a>
            </h2>
			<?php endif; ?>
			<div id="poststuff">
                <?php
                if( $active_tab == 'preview' ){
	                self::preview($EMIO_Import);
                }elseif( $active_tab == 'mapping' && $EMIO_Import::$field_mapping ){
	                self::mapping($EMIO_Import);
                }elseif( $active_tab == 'history' ){
	                self::item_history($EMIO_Import);
                }else{
                    self::settings($EMIO_Import);
                }
                ?>
			</div>
			<!-- #poststuff -->
		</div>
		<?php
	}

	/**
	 * @param EMIO_Import $EMIO_Import
	 */
	public static function settings( $EMIO_Import ){
		$valid = $EMIO_Import->ID && EMIO_Import_Admin::validate($EMIO_Import);
	    ?>
        <form action="<?php echo esc_url(add_query_arg(array('tab' => null))); ?>" method="post" enctype="multipart/form-data" id="emio-editor" class="emio-editor-import">
            <input type="hidden" name="format" value="<?php echo esc_attr($EMIO_Import::$format); ?>" />
            <input type="hidden" name="item_id" value="<?php echo esc_attr($EMIO_Import->ID); ?>" />
            <input type="hidden" name="action" value="save" />
	        <input type="hidden" name="preview" value="1" />
			<?php echo wp_nonce_field('emio-import-edit', 'emio_import_nonce'); ?>
			<?php if( $EMIO_Import::$format ): ?>
                <table class="form-table">
                    <tbody>
					<?php EMIO_Import_Admin::settings($EMIO_Import); ?>
                    </tbody>
                </table>
				<?php
				$btn_save = __('Save and Preview','events-manager-io');
				$btn_preview =  __('Preview','events-manager-io');
				$btn_text =  $valid ? $btn_preview : $btn_save;
				?>
                <p><input type="submit" id="emio-editor-submit" value="<?php echo esc_attr($btn_text); ?>" data-preview="<?php echo esc_attr($btn_preview); ?>" data-save="<?php echo esc_attr($btn_save); ?>" class="button-primary" /></p>
			<?php endif; ?>
        </form>
        <?php
    }

	/**
	 * @param EMIO_Import $EMIO_Import
	 */
	public static function mapping( $EMIO_Import ){
	    //we need an uploaded file to map to, otherwise, exit early
		if( !$EMIO_Import->get_source(false) ){
		    echo "<p>".esc_html__('Please upload a file in the settings tab in order to map your fields.','events-manager-io')."</p>";
		    return;
		}
		//get the default field map, which is either stored in the import object or we use the default settings
		$import_field_mapping = !empty($EMIO_Import->meta['field_mapping']) ? $EMIO_Import->meta['field_mapping'] : $EMIO_Import::$field_mapping_default;
		$excerpt = $EMIO_Import->import_data( 1 );
		//handle some errors
		if( is_wp_error($excerpt) ){ /* @var WP_Error $excerpt */
		    $EM_Notices = new EM_Notices(false);
		    $EM_Notices->add_error($excerpt->get_error_messages());
		    echo $EM_Notices;
        }
        if( !is_array($excerpt) || empty($excerpt['headers']) ){
		    echo '<p><em>'.esc_html__('No headers provided in source file for field mapping.', 'events-manager-io').'</em></p>';
		    return;
        }
		$fields_mapping_index_labels = array(
			'event' => __('Event', 'events-manager-io'),
			'location' => __('Locations', 'events-manager-io')
		);
		$field_mapping_labels = array(
			'event' => array(
				'uid'=> __('Event ID','events-manager-io'),
				'slug'=> __('Event Slug','events-manager-io'),
				'name'=> __('Event Name','events-manager-io'),
				'start' => __('Event Start Date/Time','events-manager-io'),
				'end' => __('Event End Date/Time','events-manager-io'),
				'timezone'=> __('Event Timezone','events-manager-io'),
				'all_day'=> __('All Day Event (true/false)','events-manager-io'),
				'content'=> __('Event Description','events-manager-io'),
				'image' => __('Event Image','events-manager-io'),
				'categories' => __('Event Categories','events-manager-io'),
				'tags' => __('Event Tags','events-manager-io'),
				'start_time'=> __('Start Time','events-manager-io'),
				'end_time'=> __('End Time','events-manager-io'),
				'start_date'=> __('Start Date','events-manager-io'),
				'end_date'=> __('End Date','events-manager-io'),
				//meta
				'meta' => __('Event Meta (Array or Serialized)', 'events-manager-io'),
				'meta/event_url' => __('Event URL (External Meta)', 'events-manager-io'),
				'meta/bookings_url' => __('Bookings URL (External Meta)', 'events-manager-io'),
				'meta/bookings_price' => __('Bookings Price (External Meta)', 'events-manager-io'),
				'meta/bookings_currency' => __('Bookings Currency (External Meta)', 'events-manager-io'),
				'meta/bookings_spaces' => __('Bookings Spaces (External Meta)', 'events-manager-io'),
				'meta/bookings_available' => __('Bookings Available Spaces (External Meta)', 'events-manager-io'),
				'meta/bookings_confirmed' => __('Bookings Confirmed (External Meta)', 'events-manager-io'),
				/*
				 'recurrence_interval'=> __('Recurrence Interval','events-manager-io'), //every x day(s)/week(s)/month(s)
				 'recurrence_freq'=> __('Recurrence Frequency','events-manager-io'), //daily,weekly,monthly?
				 'recurrence_days'=> __('Recurrence Days','events-manager-io'), //each event spans x days
				 'recurrence_byday'=> __('Recurrence Days of Week','events-manager-io'), //if weekly or monthly, what days of the week?
				 'recurrence_byweekno'=> __('Recurrence By Week Number','events-manager-io'), //if monthly which week (-1 is last)
				 */
			),
			'location' => array(
				'location'=> __('Location (Full Name/Address)','events-manager-io'),
				'uid'=> __('Location ID','events-manager-io'),
				'slug'=> __('Location Slug','events-manager-io'),
				'name'=> __('Location Name','events-manager-io'),
				'address'=> __('Address','events-manager-io'),
				'town'=> __('Town/City','events-manager-io'),
				'state'=> __('State','events-manager-io'),
				'postcode'=> __('Zip Code','events-manager-io'),
				'region'=> __('Region','events-manager-io'),
				'country'=> __('Country','events-manager-io'),
				'latitude'=> __('Longitude','events-manager-io'),
				'longitude'=> __('Latitude','events-manager-io'),
				'content'=> __('Location Description','events-manager-io'),
				'image' => __('Location Image','events-manager-io'),
				'categories' => __('Location Categories','events-manager-io'),
				'tags' => __('Locatio Tags','events-manager-io'),
				//Meta
				'meta' => __('Location Meta (Array or Serialized)', 'events-manager-io'),
				'meta/location_url' => __('Location URL (External Meta)', 'events-manager-io'),
			)
		);
		?>
        <div id="emio-import-mapping">
            <p><?php esc_html_e('Below you will see how imported items will map to event and location fields whilst being imported.','events-manager-io'); ?></p>
            <form action="" method="post">
                <input type="hidden" name="format" value="<?php echo esc_attr($EMIO_Import::$format); ?>" />
                <input type="hidden" name="item_id" value="<?php echo esc_attr($EMIO_Import->ID); ?>" />
                <input type="hidden" name="action" value="save_field_mapping" />
	            <?php echo wp_nonce_field('emio-import-save-mapping-'.$EMIO_Import->ID, 'emio_import_nonce'); ?>
                <div class="table-wrap">
                    <table class="emio-import-field-mapping">
                        <thead>
                        <tr>
                            <th><?php esc_html_e('Found Field','events-manager-io'); ?></th>
                            <th><?php esc_html_e('Mapped Field','events-manager-io'); ?></th>
                            <th><?php esc_html_e('Sample Data','events-manager-io'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $excerpt['headers'] as $header_key => $header ): ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($header); ?></th>
                                <td scope="row">
                                    <select name="emio_field_mapping[<?php echo esc_attr($header); ?>]">
                                        <option value="0"><?php esc_html_e('Ignore this field', 'events-manager-io'); ?></option>
                                        <?php foreach( $field_mapping_labels as $type => $fields ): ?>
                                            <optgroup label="<?php echo esc_attr($fields_mapping_index_labels[$type]); ?>">
                                                <?php foreach( $fields as $field_key => $label ): ?>
                                                    <?php
                                                    $selected = array_key_exists((string) $header, $import_field_mapping) && $import_field_mapping[$header] == $type.'/'.$field_key ? ' selected="selected"' : '';
                                                    ?>
                                                    <option value="<?php echo esc_attr($type.'/'.$field_key); ?>"<?php echo $selected; ?>><?php echo esc_html($label); ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php
                                    if( !empty($excerpt['data'][0][$header]) ){
                                        $sample = strlen($excerpt['data'][0][$header]) > 50 ? substr($excerpt['data'][0][$header], 0, 50).'...' : $excerpt['data'][0][$header];
                                        echo esc_html($sample);
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p>
                    <button type="submit" class="button-primary"><?php esc_html_e('Save Field Mapping', 'events-manager-io'); ?></button>
                </p>
            </form>
        </div>
		<?php
	}
	
	/**
	 * @param EMIO_Import $EMIO_Import
	 */
	public static function preview( $EMIO_Import ){
		//before running the import, we need to do a max_input_vars check to warn user of limits
		$max_input_vars = absint(ini_get('max_input_vars')) + 1;
		if( $max_input_vars < $EMIO_Import->filter_limit ){
			$import_limit = $EMIO_Import->filter_limit;
			$EMIO_Import->filter_limit = $max_input_vars + 1;
		}
		//run import and output preview table
		$items = $EMIO_Import->get( true );
		if( is_wp_error($items) ){ /* @var $items WP_Error */
			//create EM_Notices without global, to prevent repeating previous errors
			$EM_Notices = new EM_Notices( false );
			$EM_Notices->add_error($items->get_error_messages());
			echo $EM_Notices; 
			return;
		}
		//reset import limit to what it previously was
		if( $max_input_vars < $EMIO_Import->filter_limit ){
			$EMIO_Import->filter_limit = $import_limit;
		}
		$item_type = $EMIO_Import->scope == 'locations' ? __('Location', 'events-manager') : __('Event', 'events-manager');
		$msg_duplicate = sprintf(__('This %s is a duplicate from another import and will be ignored.', 'events-manager-io'), $item_type);
		$msg_skip = sprintf(__('This %s was imported before and has not changed.', 'events-manager-io'), $item_type);
		$msg_trashed = sprintf(__('This %s was previously imported, but has been deleted on this site, therefore it will be ignored.', 'events-manager-io'), $item_type);
		$msg_update = sprintf(__('This %s was previously imported, but the source has changed and the %s on this site will be updated.', 'events-manager-io'), $item_type, $item_type);
		if( $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ){
			$msg_skip_loc = __('This location already exists and will be used for this event.', 'events-manager-io');
			$msg_trashed_loc = sprintf(__('This %s was previously imported, but has been deleted on this site, it will be recreated.', 'events-manager-io'), __('Location','events-manager'));
			$msg_update_loc = sprintf(__('This %s was previously imported, but the source has changed and the %s on this site will be updated.', 'events-manager-io'), __('Location','events-manager'), __('Location','events-manager'));
			$msg_create_loc = sprintf(__('This %s does not exist and will be created.', 'events-manager-io'), __('Location','events-manager'));
		}
		if( $EMIO_Import->frequency && $EMIO_Import->status ){
			if( $EMIO_Import->frequency_start && $EMIO_Import->frequency_end ){
				$frequency_desc = esc_html__('This import will run automatically %s between %s and %s.', 'events-manager-io');
				$frequency_desc = sprintf( $frequency_desc , EMIO_Cron::$frequencies[$EMIO_Import->frequency]['display'], $EMIO_Import->frequency_start, $EMIO_Import->frequency_end);
			}elseif( $EMIO_Import->frequency_start ){
				$frequency_desc = esc_html__('This import will run automatically %s from %s.', 'events-manager-io');
				$frequency_desc = sprintf( $frequency_desc , EMIO_Cron::$frequencies[$EMIO_Import->frequency]['display'], $EMIO_Import->frequency_start);
			}elseif( $EMIO_Import->frequency_end ){
				$frequency_desc = esc_html__('This import will run automatically %s until %s.', 'events-manager-io');
				$frequency_desc = sprintf( $frequency_desc , EMIO_Cron::$frequencies[$EMIO_Import->frequency]['display'], $EMIO_Import->frequency_end);
			}else{
				$frequency_desc = esc_html__('This import will run automatically %s until you deactivate it.', 'events-manager-io');
				$frequency_desc = sprintf( $frequency_desc , EMIO_Cron::$frequencies[$EMIO_Import->frequency]['display']);
			}
			$frequency_desc .= ' '. esc_html__('These previewed items would be the next ones to be imported on the next sheduled run.', 'events-manager-io');
		}
		?>
        <div id="emio-import-results">
            <div id="emio-import-preview">
	            <?php if( !empty($frequency_desc) ): ?>
				<p><em><?php echo $frequency_desc; ?></em></p>
	            <?php endif; ?>
                <?php if( count($items) == $max_input_vars ) : array_pop($items); ?>
                <p style="font-weight:bold; color:red;"><?php echo sprintf(esc_html__('Only %d items can be imported at a time via preview (this does not apply to scheduled imports), because a PHP setting called max_input_vars will only allow that many. To get around this, either contact your host to increase this limit, or set the import limit above to %d and try importing again. Previously imported items will be ignored and you can continue importing %d new events each time.', 'events-manager-io'), $max_input_vars - 1, $max_input_vars - 1, $max_input_vars - 1); ?></p>
                <?php endif; ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="table-wrap">
                        <table id="emio-import-preview-results" class="widefat post ">
                            <thead>
                                <tr>
                                    <td id="cb" class="manage-column column-cb check-column">
                                        <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e('Select All', 'events-manager-io'); ?></label>
                                        <input id="cb-select-all-1" name="emio_import_all" class="emio-import-item emio-import-item-all" type="checkbox" checked />
                                    </td>
                                    <?php ob_start(); ?>
                                    <?php if( $EMIO_Import->scope == 'events' || $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ): ?>
                                    <th class="manage-column" scope="col"><?php esc_html_e('Event Name','events-manager'); ?></th>
                                    <th class="manage-column" scope="col"><?php esc_html_e('Date and Time','events-manager'); ?></th>
                                    <?php endif; ?>
                                    <?php if( $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ) : ?>
                                    <th class="manage-column location-status" scope="col"></th>
                                    <?php endif; ?>
                                    <?php if( $EMIO_Import->scope == 'locations' || $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ): ?>
                                    <th class="manage-column" scope="col"><?php esc_html_e('Location','events-manager'); ?></th>
                                    <?php endif; ?>
                                    <?php $thead = ob_get_clean(); echo $thead; ?>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td id="cb" class="manage-column column-cb check-column">
                                        <label class="screen-reader-text" for="cb-select-all-2"><?php esc_html_e('Select All', 'events-manager-io'); ?></label>
                                        <input id="cb-select-all-2" class="emio-import-item emio-import-item-all" type="checkbox" checked />
                                    </td>
                                    <?php echo $thead; ?>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php foreach( $items as $key => $item ) : /* @var EMIO_Object $item */ ?>
                                <tr <?php if( !empty($item->skip) ) echo 'class="skip"'; ?>">
                                    <?php
                                    $serialized_data = array();
                                    if( empty($item->skip) ){
                                        $serialized_data = json_encode($item->to_array());
                                    }
                                    if( $EMIO_Import->scope == 'locations' ){
                                        $EM_Location = $item->object; /* @var $EM_Location EM_Location */
                                    }else{
                                        $EM_Event = $item->object; /* @var $EM_Event EM_Event */
                                        $EM_Location = $EM_Event->get_location(); /* @var $EM_Location EM_Location */
                                    }
                                    ?>
                                    <th scope="row" class="check-column">
                                        <?php if( !empty($item->skip) && !empty($item->duplicate) && $item->object->post_id ) : ?>
                                        <span class="dashicons dashicons-admin-page" style="margin-left:6px; font-size:20px;" title="<?php echo esc_attr($msg_duplicate); ?>"></span>
                                        <?php elseif( !empty($item->skip) && $item->object->post_id ) : ?>
                                        <span class="dashicons dashicons-yes" style="margin-left:6px; font-size:20px;" title="<?php echo esc_attr($msg_skip); ?>"></span>
                                        <?php elseif( !empty($item->skip) && !empty($item->deleted) ) : ?>
                                        <span class="dashicons dashicons-trash" style="margin-left:6px; font-size:20px;" title="<?php echo esc_attr($msg_trashed); ?>"></span>
                                        <?php elseif(!$item->object->validate()) : ?>
                                        <span class="dashicons dashicons-warning" style="margin-left:8px;" title="<?php echo esc_attr(implode(',', $item->object->get_errors())) ?>"></span>
                                        <?php else: ?>
                                        <label class="screen-reader-text" for="cb-import-<?php echo esc_attr($key); ?>"><?php echo esc_html(sprintf(__('Select %s', 'events-manager-io'), __('Import','events-manager-io'))); ?></label>
                                        <input type="checkbox" class="emio-import-item" name="import_select[]" value="<?php echo esc_attr($key); ?>" id="cb-import-<?php echo esc_attr($key); ?>"  checked />
                                        <?php endif; ?>
                                        <?php if( !empty($serialized_data) ): ?>
                                        <input type="hidden" name="import_data[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($serialized_data); ?>" />
                                        <?php endif; ?>
                                    </th>
                                    <?php if( $EMIO_Import->scope == 'events' || $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ): ?>
                                        <td scope="row">
                                            <?php if( !empty($item->updated) ): ?>
                                            <span class="dashicons dashicons-update" title="<?php echo esc_attr($msg_update); ?>"></span>
                                            <?php endif; ?>
                                            <?php
                                                if( !empty($EM_Event->post_id)  ){
                                                    echo '<a href="'.esc_url($EM_Event->get_permalink()).'">'.esc_html($EM_Event->event_name).'</a>';
                                                }else{
                                                    echo esc_html($EM_Event->event_name);
                                                }
                                            ?>
                                        </td>
                                        <td scope="row">
                                            <?php
                                            //get meta value to see if post has location, otherwise
                                            $localised_start_date = $EM_Event->start()->i18n(get_option('date_format'));
                                            $localised_end_date = $EM_Event->end()->i18n(get_option('date_format'));
                                            echo $localised_start_date;
                                            echo ($localised_end_date != $localised_start_date) ? " - $localised_end_date":'';
                                            echo "<br />";
                                            if(!$EM_Event->event_all_day){
                                                echo $EM_Event->start()->i18n(get_option('time_format')) . " - " . $EM_Event->end()->i18n(get_option('time_format'));
                                            }else{
                                                echo get_option('dbem_event_all_day_message');
                                            }
                                            if( $EM_Event->get_timezone()->getName() != EM_DateTimeZone::create()->getName() ) echo '<span class="dashicons dashicons-info" style="font-size:16px; color:#ccc; padding-top:2px;" title="'.esc_attr(str_replace('_', ' ', $EM_Event->event_timezone)).'"></span>';
                                            ?>
                                        </td>
                                        <?php if( $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ): /* @var $item EM_Location */ ?>
                                        <td scope="row" class="location-status <?php if( !empty($item->location->skip) ) echo 'skip'; ?>">
                                            <?php if( $EM_Location->location_id ): ?>
                                                <?php if( !empty($item->location->skip) && $EM_Location->post_id ) : ?>
                                                <span class="dashicons dashicons-yes" title="<?php echo esc_attr($msg_skip_loc); ?>"></span>
                                                <?php elseif( !empty($item->location->updated) ): ?>
                                                <span class="dashicons dashicons-update" title="<?php echo esc_attr($msg_update_loc); ?>"></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if( !empty($item->location->deleted) ) : ?>
                                                <span class="dashicons dashicons-trash" title="<?php echo esc_attr($msg_trashed_loc); ?>"></span>
                                                <?php elseif( !empty($EM_Event->updated) && empty($item->location->deleted) ): ?>
                                                <span class="dashicons dashicons-plus-alt" title="<?php echo esc_attr($msg_create_loc); ?>"></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td scope="row" class="location <?php if( !empty($item->location->skip) ) echo 'skip'; ?>">
                                            <?php
                                                if( (string) $EM_Event->location_id === '0' && !get_option('dbem_require_location') ){
                                                    echo "<strong>" . esc_html__('No Location', 'events-manager-io') . "</strong>";
                                                }else{
                                                    echo self::get_location_preview($item);
                                                }
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                    <?php elseif( $EMIO_Import->scope == 'locations' ): ?>
                                        <td scope="row" class="location">
                                            <?php if( !empty($item->skip) && $item->object->post_id ) : ?>
                                            <span class="dashicons dashicons-yes" style="margin-left:6px; font-size:20px;" title="<?php echo esc_attr($msg_skip); ?>"></span>
                                            <?php elseif( !empty($item->skip) && !empty($item->deleted) ) : ?>
                                            <span class="dashicons dashicons-trash" style="margin-left:6px; font-size:20px;" title="<?php echo esc_attr($msg_trashed); ?>"></span>
                                            <?php elseif( !empty($item->updated) ): ?>
                                            <span class="dashicons dashicons-update" title="<?php echo esc_attr($msg_update); ?>"></span>
                                            <?php endif; ?>
                                            <?php echo self::get_location_preview($item); ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                                <?php
                                if( empty($items) ){
                                    $cols = $EMIO_Import->scope == 'location' ? 2 : 3;
                                    if( $EMIO_Import->scope == 'all' || $EMIO_Import->scope == 'events+locations' ) $cols = 5;
                                    echo '<tr><td colspan="'.$cols.'"><em>'. esc_html__('No items found, if you are using result filters, try making them less specific.', 'events-manager-io') .'</em></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="emio-import-actions">
                        <input type="hidden" name="item_id" value="<?php echo esc_attr($EMIO_Import->ID); ?>" />
                        <input type="hidden" name="action" value="import_preview" />
                        <?php echo wp_nonce_field('emio-import-preview-run-'.$EMIO_Import->ID, 'emio_import_nonce'); ?>
                        <input id="emio-import-button" type="submit" value="<?php esc_attr_e('Import All','events-manager-io'); ?>" data-selected="<?php esc_attr_e('Import Selected (%d)','events-manager-io'); ?>" data-selected-all="<?php esc_attr_e('Import All','events-manager-io'); ?>" class="button-primary" />
                    </div>
                </form>
            </div>
        </div>
		<?php 
	}
	/**
	 * Decides what page to show in admin area, saves data if required
	 * @param EMIO_Event|EMIO_Location $EMIO_Object
	 * @return string
	 */
	public static function get_location_preview( $EMIO_Object ){
		if( get_class($EMIO_Object) == 'EMIO_Event' ){
			$EMIO_Location = $EMIO_Object->location;
			$EM_Location = $EMIO_Object->object->get_location();
		}else{
			$EMIO_Location = $EMIO_Object;
			$EM_Location = $EMIO_Location->object;
		}
		if( !empty($EMIO_Location->deleted) && $EMIO_Object->skip ){
			$msg_trashed = sprintf(__('This %s was previously imported, but has been deleted on this site, therefore it will be ignored.', 'events-manager-io'), __('location', 'events-manager-io'));
			return '<em>'. $msg_trashed . '</em>';
		}
		if( $EM_Location->post_id ){
			$location_name = '<a href="'.$EM_Location->get_permalink().'">'.esc_html($EM_Location->location_name).'</a>';
		}else{
			$location_name = esc_html($EM_Location->location_name);
		}
		//create coordinate placeholder
		if( !empty($EM_Location->location_latitude) && !empty($EM_Location->location_longitude) ){
			$location_coords = '<a target="_blank" href="https://www.google.com/maps/search/'.$EM_Location->location_latitude.','.$EM_Location->location_longitude.'"><span class="dashicons dashicons-location" title="Location available ('.$EM_Location->location_latitude.','.$EM_Location->location_longitude.')"></span></a>';
		}else{
			$location_coords = '<span class="dashicons dashicons-location coords-404" title="'.esc_attr__('No coordinates found','events-manager-io').'"></span>';
		}
		//build helpful string of location to see what part of a location each section belongs to
		$location_array = array();
		if( !empty($EM_Location->location_address) ) $location_array[] = '<span title="'.esc_attr__('Address', 'events-manager').'">'.esc_html($EM_Location->location_address).'</span>';
		if( !empty($EM_Location->location_town) ) $location_array[] = '<span title="'.esc_attr__('City/Town', 'events-manager').'">'.esc_html($EM_Location->location_town).'</span>';
		if( !empty($EM_Location->location_state) ) $location_array[] = '<span title="'.esc_attr__('State/County', 'events-manager').'">'.esc_html($EM_Location->location_state).'</span>';
		if( !empty($EM_Location->location_postcode) ) $location_array[] = '<span title="'.esc_attr__('Postcode', 'events-manager').'">'.esc_html($EM_Location->location_postcode).'</span>';
		if( !empty($EM_Location->location_region) ) $location_array[] = '<span title="'.esc_attr__('Region', 'events-manager').'">'.esc_html($EM_Location->location_region).'</span>';
		if( !empty($EM_Location->location_country) ) $location_array[] = '<span title="'.esc_attr__('Country', 'events-manager').'">'.esc_html($EM_Location->get_country()).'</span>';
		//output location string info
		return "<strong>" . $location_name . "</strong> $location_coords<div>" . implode(' ,&nbsp;&nbsp;', $location_array).'</div>';
	}
}