<?php

class GP_Limit_Checkboxes extends GWPerk {

	public $version = GP_LIMIT_CHECKBOXES_VERSION;

	protected $min_gravity_forms_version = '1.9';
	protected $min_gravity_perks_version = '2.2.5';

	public function init() {

		load_plugin_textdomain( 'gp-limit-checkboxes', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->enqueue_field_settings();

		$this->add_tooltip( "{$this->key('enable')}", '<h6>' . __( 'Limit Checkboxes Amount', 'gp-limit-checkboxes' ) . '</h6>' . __( 'Limit how many checkboxes can be checked for this field.', 'gp-limit-checkboxes' ) );
		$this->add_tooltip( "{$this->key('span_multiple_fields')}", '<h6>' . __( 'Span Multiple Fields', 'gp-limit-checkboxes' ) . '</h6>' . __( 'Apply this limit as an accumlative limit across multiple fields. For example, spanning a limit of "2" across two fields would allow you to select two checkboxes in either field or one checkbox in each field.', 'gp-limit-checkboxes' ) );

		add_filter( 'gform_register_init_scripts', array( $this, 'register_init_script' ), 10, 2 );
		add_filter( 'gform_validation', array( $this, 'validate' ) );

		// Disable validation when importing via GV Import Entries.
		add_filter( 'gravityview/import/column/checkbox/unchecked', array( $this, 'disable_validation' ) );

		// Enable validation on Gravity Flow's User Input step.
		add_filter( 'gravityflow_validation_user_input', array( $this, 'handle_gravityflow_validation' ), 10, 2 );

		add_action( 'admin_head', array( $this, 'enqueue_admin_scripts' ) );
		add_filter( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );

	}

	public function register_init_script( $form ) {

		$groups = $this->get_groups( $form );

		// JS func only cares about max limit; remove groups without a max limit set.
		for ( $i = count( $groups ) - 1; $i >= 0; $i-- ) {
			if ( ! $groups[ $i ]['max'] && $groups[ $i ]['max'] !== 0 ) {
				array_splice( $groups, $i, 1 );
			}
		}

		$triggers = $this->get_triggers( $form, $groups );
		if ( empty( $triggers ) ) {
			return;
		}

		$args = array(
			'formId'   => $form['id'],
			'groups'   => $groups,
			'triggers' => $triggers,
		);

		$script = 'new GPLimitCheckboxes( ' . json_encode( $args ) . ' );';

		GFFormDisplay::add_init_script( $form['id'], 'gp_limit_checkboxes', GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	public function get_groups( $form ) {

		$groups = array();

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$group = array(
				'min'    => $field->{$this->key( 'minimum_limit' )},
				'max'    => $field->{$this->key( 'maximum_limit' )},
				'fields' => array( $field->id ),
			);

			$span = $field->{$this->key( 'span_limit_fields' )};

			if ( ! empty( $span ) ) {
				$group['fields'] = array_merge( $group['fields'], array_map( 'intval', $span ) );
			}

			array_push( $groups, $group );

		}

		return $groups;
	}

	public function get_triggers( $form, $groups = null ) {

		if ( ! $groups ) {
			$groups = $this->get_groups( $form );
		}

		$field_ids = array();

		foreach ( $groups as $group ) {
			$field_ids = array_merge( $field_ids, $group['fields'] );
		}

		$field_ids = array_unique( $field_ids );
		$triggers  = array();

		foreach ( $field_ids as $field_id ) {
			$triggers[ $field_id ] = array(
				'fieldId'  => $field_id,
				'selector' => sprintf( '#input_%d_%d input[type="checkbox"]', $form['id'], $field_id ),
			);
		}

		return $triggers;
	}

	public function __get_triggers( $form ) {

		$triggers          = array();
		$grouped_field_ids = array(); // find all field IDs that are in a group

		foreach ( $form['fields'] as &$field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$min  = $field->{$this->key( 'minimum_limit' )};
			$max  = $field->{$this->key( 'maximum_limit' )};
			$span = $field->{$this->key( 'span_limit_fields' )};

			$trigger = array(
				'fieldId'   => $field->id,
				'selectors' => $this->get_selectors( $field->id, $field->formId ),
				'min'       => $min,
				'max'       => $max,
				'groups'    => array(),
			);

			// groups may have been defined for a trigger before the trigger field has been configured; check for this.
			if ( isset( $triggers[ $field->id ] ) ) {
				$trigger = wp_parse_args( $triggers[ $field->id ], $trigger );
			}

			// if field defines a group (aka span), add it now.
			if ( is_array( $span ) ) {

				$field_ids = array_merge( array( $field->id ), array_map( 'intval', $span ) );
				$group     = array(
					'fieldIds'  => $field_ids,
					'selectors' => $this->get_selectors( $field_ids, $field->formId ),
					'min'       => $min,
					'max'       => $max,
				);

				$trigger['groups'][] = $group;

				// add group to 'groups' property of every trigger in the group
				foreach ( $span as $field_id ) {

					if ( ! isset( $triggers[ $field_id ] ) ) {
						$triggers[ $field_id ] = array(
							'groups' => array(),
						);
					}

					$triggers[ $field_id ]['groups'][] = $group;

				}
			}

			$triggers[ $field->id ] = $trigger;

		}

		foreach ( $triggers as $trigger ) {

		}

		return $triggers;
	}

	public function __get_selectors( $field_ids, $form_id ) {

		$selectors = array();

		if ( ! is_array( $field_ids ) ) {
			$field_ids = array( $field_ids );
		}

		foreach ( $field_ids as $field_id ) {
			$selectors[] = "#input_{$form_id}_{$field_id} input[type='checkbox']";
		}

		return implode( ', ', $selectors );
	}

	public function disable_validation( $return ) {
		remove_filter( 'gform_validation', array( $this, 'validate' ) );
		return $return;
	}

	public function validate( $validation_result ) {

		$form   = $validation_result['form'];
		$groups = $this->get_groups( $form );

		// build an "empty" array with field ID keys
		$errors = array_fill_keys( wp_list_pluck( $form['fields'], 'id', 'id' ), array() );

		foreach ( $groups as $group ) {

			/**
			 * Filter the group of checkboxes that are about to be processed.
			 *
			 * @since 1.2
			 *
			 * @param array $group The current group.
			 * @param array $form  The current form object.
			 */
			$group    = apply_filters( 'gplc_group', $group, $form );
			$is_group = count( $group['fields'] ) > 1;

			if ( $this->is_group_over_max( $group, $form ) ) {

				foreach ( $group['fields'] as $field_id ) {

					$slug    = 'field_over_max';
					$message = array( _n( 'You may only select %s item.', 'You may only select %s items.', $group['max'], 'gp-limit-checkboxes' ) );

					if ( $is_group ) {
						$slug = 'group_over_max';
						array_unshift( $message, __( 'This field is part of a group.', 'gp-limit-checkboxes' ) );
					}

					$message = gf_apply_filters( array( "gplc_validation_message_{$slug}", $form['id'], $field_id ), implode( ' ', $message ), $form, $field_id, $group );
					$message = sprintf( $message, $group['max'] );

					$errors[ $field_id ][ $slug ] = $message;

				}
			}

			if ( $this->is_group_under_min( $group, $form ) ) {

				foreach ( $group['fields'] as $field_id ) {

					$slug    = 'field_under_min';
					$message = array( _n( 'You must select at least %s item.', 'You must select at least %s items.', $group['min'], 'gp-limit-checkboxes' ) );

					if ( $is_group ) {
						$slug = 'group_under_min';
						array_unshift( $message, __( 'This field is part of a group.', 'gp-limit-checkboxes' ) );
					}

					/**
					 * Filter whether the current field's minimum limit be validated.
					 *
					 * @since 1.3.2
					 *
					 * @param bool     $should_validate_min Should current field's minimum limit by validated? Defaults to `true`.
					 * @param array    $form                The form object to which the current field belongs.
					 * @param GF_Field $field               The current Checkbox field.
					 */
					$should_validate_min = gf_apply_filters( array( 'gplcb_should_validate_minimum', $form['id'], $field_id ), true, $form, GFAPI::get_field( $form, $field_id ) );
					if ( ! $should_validate_min ) {
						continue;
					}

					// group errors take priority
					if ( $slug == 'field_under_min' && isset( $errors[ $field_id ]['group_under_min'] ) ) {
						continue;
					}

					$message = gf_apply_filters( array( "gplc_validation_message_{$slug}", $form['id'], $field_id ), implode( ' ', $message ), $form, $field_id, $group );
					$message = sprintf( $message, $group['min'] );

					$errors[ $field_id ][ $slug ] = $message;

				}
			}
		}

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->should_field_be_validated( $form, $field ) ) {
				continue;
			}

			$field_errors = rgar( $errors, $field->id );

			if ( ! empty( $field_errors ) ) {
				$validation_result['is_valid'] = false;
				$field->failed_validation      = true;
				$field->validation_message     = sprintf( '<div class="gplc-validation-message">%s</div>', implode( '</div><div>', $field_errors ) );
			}
		}

		$validation_result['form'] = $form;

		return $validation_result;
	}

	/**
	 * Handle validation for Gravity Flow User Input step.
	 *
	 * The current implementation will simply ignore validation issues for GPLC-enabled fields if they are not editable
	 * for the current step. This does pose some issues for potential edge-cases.
	 *
	 * The core issue is that non-editable field values are not avialable in the $_POST during the submission of this
	 * step. If a Checkbox field is part of a group and one field is editable but other is not, it is possible to exceed
	 * the group's maximum or fail to meet the minimum since only the editable field will be included in the checkbox
	 * count.
	 *
	 * Additionally, since we're overwriting the validation result entirely... it is possible that there is a scenario
	 * where some other 3rd-party would want the non-editable field to be validated with their custom logic. Let's see
	 * if this presents itself before we account for it.
	 *
	 * @param $validation_result
	 * @param \Gravity_Flow_Step_User_Input $step
	 *
	 * @return mixed
	 */
	public function handle_gravityflow_validation( $validation_result, $step ) {

		$validation_result             = $this->validate( $validation_result );
		$validation_result['is_valid'] = true;

		foreach ( $validation_result['form']['fields'] as &$field ) {
			if ( $this->is_applicable_field( $field ) && ! in_array( $field->id, $step->get_editable_fields() ) ) {
				$field->failed_validation = false;
			} elseif ( $field->failed_validation ) {
				// If the field has failed validation, the validation result should be updated to 'false'.
				$validation_result['is_valid'] = false;
			}
		}

		return $validation_result;
	}

	public function is_group_over_max( $group, $form ) {

		if ( ! $group['max'] && $group['max'] !== 0 ) {
			return false;
		}

		$count = $this->get_checkbox_count( $group['fields'], $form );

		return $count > $group['max'];
	}

	public function is_group_under_min( $group, $form ) {

		if ( ! $group['min'] && $group['min'] !== 0 ) {
			return false;
		}

		$count = $this->get_checkbox_count( $group['fields'], $form );

		return $count < $group['min'];
	}

	public function is_field_over_max( $trigger, $form ) {
		$count = $this->get_checkbox_count( $trigger['fieldId'], $form );
		return $count > $trigger['max'];
	}

	public function is_field_under_min( $trigger, $form ) {
		$count = $this->get_checkbox_count( $trigger['fieldId'], $form );
		return $count < $trigger['min'];
	}

	public function get_checkbox_count( $field_ids, $form ) {

		if ( ! is_array( $field_ids ) ) {
			$field_ids = array( $field_ids );
		}

		$counts = $this->get_checkbox_counts( $form );
		$count  = 0;

		foreach ( $field_ids as $field_id ) {
			$count += (int) rgar( $counts, $field_id );
		}

		return $count;
	}

	public function get_checkbox_counts( $form ) {

		$counts = GFCache::get( 'gplc_counts' );
		if ( $counts !== false ) {
			return $counts;
		}

		$counts = array();

		foreach ( $form['fields'] as $field ) {

			$count = 0;

			foreach ( $_POST as $key => $value ) {
				if ( preg_match( "/input_{$field->id}_[0-9]+/", $key ) ) {
					$count++;
				}
			}

			$counts[ $field->id ] = $count;

		}

		GFCache::set( 'gplc_counts', $counts );

		return $counts;
	}

	public function get_field_limits( $trigger ) {

		$min = $trigger['min'];
		$max = $trigger['max'];

		foreach ( $trigger['groups'] as $group ) {

			if ( $group['min'] > $min ) {
				$min = $group['mint'];
			}

			if ( $group['max'] < $max ) {
				$max = $group['max'];
			}
		}

		return compact( 'min', 'max' );
	}

	public function should_field_be_validated( $form, $field ) {

		if ( ! $this->is_applicable_field( $field ) ) {
			return false;
		}

		if ( $field['pageNumber'] != GFFormDisplay::get_source_page( $form['id'] ) ) {
			return false;
		}

		if ( GFFormsModel::is_field_hidden( $form, $field, array() ) ) {
			return false;
		}

		return true;
	}

	public function field_settings_ui() {
		?>
			<style type="text/css">
				#gws_field_tab .gwp-option label { margin: 0 !important; }
				#gws_field_tab .gwp-option input[type="text"] { margin-right: 100px; }
			</style>

			<li class="<?php echo $this->key( 'setting' ); ?> gwp_field_setting field_setting gp-field-setting" style="display:none;">
				<input type="checkbox" id="<?php echo $this->key( 'enable' ); ?>" onclick="gperk.toggleSettings('<?php echo $this->key( 'enable' ); ?>', '<?php echo $this->key( 'settings' ); ?>');" value="1">
				<label for="<?php echo $this->key( 'enable' ); ?>" class="inline">
					<?php _e( 'Limit number of checked checkboxes.', 'gp-limit-checkboxes' ); ?>
					<?php gform_tooltip( $this->key( 'enable' ) ); ?>
				</label>

				<div id="<?php echo $this->key( 'settings' ); ?>" class="gp-child-settings" style="display: none;">
					<div class="gwp-option gp-field-setting">
						<div class="gp-group gp-row">
							<label for="<?php echo $this->key( 'minimum_limit' ); ?>" class="inline" style="width:100px;">
								<?php _e( 'Minimum Limit', 'gp-limit-checkboxes' ); ?>
							</label>
							<input type="text" id="<?php echo $this->key( 'minimum_limit' ); ?>" onchange="SetFieldProperty('<?php echo $this->key( 'minimum_limit' ); ?>', jQuery(this).val());" style="width:60px;">
						</div>

						<div class="gp-group gp-row">
							<label for="<?php echo $this->key( 'maximum_limit' ); ?>" class="inline" style="width:100px;">
								<?php _e( 'Maximum Limit', 'gp-limit-checkboxes' ); ?>
							</label>
							<input type="text" id="<?php echo $this->key( 'maximum_limit' ); ?>" onchange="SetFieldProperty('<?php echo $this->key( 'maximum_limit' ); ?>', jQuery(this).val());" style="width:60px;">
						</div>

						<div class="gp-row">
							<input type="checkbox" id="<?php echo $this->key( 'span_multiple_fields' ); ?>"
								   onclick="
										   gperk.toggleSettings('<?php echo $this->key( 'span_multiple_fields' ); ?>', '<?php echo $this->key( 'multiple_fields_settings' ); ?>');
										   SetFieldProperty( '<?php echo $this->key( 'span_limit_fields' ); ?>', [] );
										   "
								   value="1">
							<label for="<?php echo $this->key( 'span_multiple_fields' ); ?>" class="inline">
								<?php _e( 'Span limit across multiple fields.', 'gp-limit-checkboxes' ); ?>
								<?php gform_tooltip( $this->key( 'span_multiple_fields' ) ); ?>
							</label>

							<div id="<?php echo $this->key( 'multiple_fields_settings' ); ?>" class="gws-child-settings gp-child-settings" style="display: none;"></div>
						</div>
					</div>
				</div>
			</li>
		<?php
	}

	public function field_settings_js() {
		?>
			<script>
				(function($) {
					$(document).bind('gform_load_field_settings', function(e, field, form) {
						// We only want to allow checkbox variant field types.
						if (field.type != 'checkbox' && field.inputType != 'checkbox') {
							$('.<?php echo $this->key( 'setting' ); ?>').hide();
							return;
						} else {
							$('.<?php echo $this->key( 'setting' ); ?>').show();
						}

						var checkboxFields = getCheckboxFields(form, field);

						gperk.toggleSettings("<?php echo $this->key( 'enable' ); ?>", "<?php echo $this->key( 'settings' ); ?>", field["<?php echo $this->key( 'enable' ); ?>"]);
						gperk.toggleSettings("<?php echo $this->key( 'span_multiple_fields' ); ?>", "<?php echo $this->key( 'multiple_fields_settings' ); ?>", field["<?php echo $this->key( 'span_multiple_fields' ); ?>"]);

						$("#<?php echo $this->key( 'minimum_limit' ); ?>").val(field["<?php echo $this->key( 'minimum_limit' ); ?>"]);
						$("#<?php echo $this->key( 'maximum_limit' ); ?>").val(field["<?php echo $this->key( 'maximum_limit' ); ?>"]);
						
						setSelectOptions(checkboxFields, field);

						// All setTimeouts are set as I was hitting an issue where the fieldSettings dom object
						// was not added before the select fields were being dynamically set.
						setTimeout(function() {
							if ($("#gwlimitcheckboxes_settings .asmContainer").length > 0)
								return;

							jQuery(".<?php echo $this->slug; ?>_"+field.id).asmSelect({
								addItemTarget: 'bottom',
								animate: true,
								highlight: true,
								sortable: false
							});
						}, 10);
					});

					// Loop through the currently set fields and grab all field's that are some form of checkbox variant,
					// unless the current field is being viewed.
					function getCheckboxFields(form, currentField) {
						var checkboxFields = new Object();

						$.each(form.fields, function(fieldIndex, field) {
							if (currentField.id != field.id && (field.type == 'checkbox' || field.inputType == 'checkbox'))
								checkboxFields[field.id] = field.adminLabel ? field.adminLabel : field.label;
						});

						return checkboxFields;
					}

					function setSelectOptions(checkboxFields, field) {
						var option, html;
						
						var selectContainer = $('#<?php echo $this->key( 'multiple_fields_settings' ); ?>');
						
						html = '<select class="<?php echo $this->slug; ?>_'+field.id+'" id="<?php echo $this->key( 'span_limit_fields' ); ?>" multiple="multiple" title="Select a Field" onchange="SetFieldProperty(\'<?php echo $this->key( 'span_limit_fields' ); ?>\', jQuery(this).val());">';

						$.each(checkboxFields, function(fieldId, fieldLabel) {
							
							var spanLimitFields = field['<?php echo $this->key( 'span_limit_fields' ); ?>'],
								selected = isFieldSelected( fieldId, spanLimitFields ) ? 'selected="selected"' : '';
							
							// add default label for unlabeled fields
							if( ! fieldLabel )
								fieldLabel = '(unlabeled) ID: ' + fieldId;
								
							html += "<option id='field-id-" + fieldId + "' value='" + fieldId + "'" + selected + ">" + truncateRuleText( fieldLabel, 35 ) + "</option>";
							
						});
						
						html += '</select>';
						
						selectContainer.html( html );
						$(".<?php echo $this->slug; ?>_" + field.id).val(field["<?php echo $this->key( 'span_limit_fields' ); ?>"]);
					}
					
					function isFieldSelected( fieldId, spanLimitFields ) {
						return $.inArray( fieldId, spanLimitFields ) != -1;
					}

					function truncateRuleText( text, length ) {

						if( ! text || text.length <= length ) {
							return text;
						}

						var halfLength = length / 2;

						return text.substr( 0, halfLength ).trim() + '...' + text.substr( text.length - ( halfLength - 1 ), halfLength ).trim();

					}
					
				} )( jQuery );
			</script>
		<?php
	}

	public function enqueue_admin_scripts() {

		if ( ! GFCommon::is_form_editor() ) {
			return;
		}

		wp_enqueue_style( 'gwp-asmselect' );
		wp_enqueue_script( 'gwp-asmselect' );

	}

	public function enqueue_form_scripts( $form ) {
		if ( $this->is_applicable_form( $form ) ) {
			wp_enqueue_script( 'gp-limit-checkboxes', $this->get_base_url() . '/js/gp-limit-checkboxes.js', array( 'jquery', 'gform_gravityforms' ), $this->version );
		}
	}

	public function is_applicable_form( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field ) ) {
				return true;
			}
		}
		return false;
	}

	public function is_applicable_field( $field ) {
		return $field->get_input_type() === 'checkbox' && rgar( $field, $this->key( 'enable' ) ) && GFFormDisplay::is_field_validation_supported( $field );
	}


	/**
	 * DEPRECATED
	 */

	public function is_limit_checkbox_field( $field ) {
		_deprecated_function( array( $this, 'is_limit_checkbox_field' ), '1.2', array( $this, 'is_applicable_field' ) );
		return GFFormsModel::get_input_type( $field ) === 'checkbox' && rgar( $field, $this->key( 'enable' ) );
	}

}

class GWLimitCheckboxes extends GP_Limit_Checkboxes { }

function gp_limit_checkboxes() {
	// @todo When we convert this to GP_Plugin, we will need to fetch the existing instance.
	return new GP_Limit_Checkboxes( 'gwlimitcheckboxes/gwlimitcheckboxes.php' );
}
