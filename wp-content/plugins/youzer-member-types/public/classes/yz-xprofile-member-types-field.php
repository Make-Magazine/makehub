<?php
/**
 * BuddyPress XProfile Classes.
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Member Types xprofile field type.
 *
 * @since 2.0.0
 */
class YZ_XProfile_Field_Type_Membertype extends BP_XProfile_Field_Type {

    /**
     * Constructor for the selectbox field type.
     *
     * @since 2.0.0
     */
    public function __construct() {
        parent::__construct();

        $this->category = _x( 'Single Fields', 'xprofile field type category', 'youzer-member-types' );
        $this->name     = _x( 'Member Types', 'xprofile field type', 'youzer-member-types' );

        $this->accepts_null_value = true;
        $this->supports_options = false;
        $this->supports_multiple_defaults = false;

        $this->set_format( '/^.+$/', 'replace' );

        /**
         * Fires inside __construct() method for BP_XProfile_Field_Type_Selectbox class.
         *
         * @since 2.0.0
         *
         * @param BP_XProfile_Field_Type_Selectbox $this Current instance of
         *                                               the field type select box.
         */
        do_action( 'bp_xprofile_field_type_member_types', $this );
    }

    /**
     * Output the edit field HTML for this field type.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.0.0
     *
     * @param array $raw_properties Optional key/value array of
     *                              {@link http://dev.w3.org/html5/markup/select.html permitted attributes}
     *                              that you want to add.
     */
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        } ?>

        <legend id="<?php bp_the_profile_field_input_name(); ?>-1">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </legend>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?> aria-labelledby="<?php bp_the_profile_field_input_name(); ?>-1" aria-describedby="<?php bp_the_profile_field_input_name(); ?>-3">
            <?php bp_the_profile_field_options( array( 'user_id' => $user_id ) ); ?>
        </select>

        <?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Output the edit field options HTML for this field type.
     *
     * BuddyPress considers a field's "options" to be, for example, the items in a selectbox.
     * These are stored separately in the database, and their templating is handled separately.
     *
     * This templating is separate from {@link BP_XProfile_Field_Type::edit_field_html()} because
     * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.0.0
     *
     * @param array $args Optional. The arguments passed to {@link bp_the_profile_field_options()}.
     */
    public function edit_field_options_html( array $args = array() ) {
        $original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

        $options = $this->field_obj->get_children();

        // Check if no member types allowed
        $no_type_allowed = yz_options( 'yz_allow_no_member_type' );
        
        $html = '';

        if ( 'on' == $no_type_allowed ) {
            $html    = '<option value="">' . /* translators: no option picked in select box */ esc_html__( '----', 'youzer-member-types' ) . '</option>';
        }

        if ( empty( $original_option_values ) && !empty( $_POST['field_' . $this->field_obj->id] ) ) {
            $original_option_values = sanitize_text_field(  $_POST['field_' . $this->field_obj->id] );
        }

        $option_values = ( $original_option_values ) ? (array) $original_option_values : array();

        $member_types = yz_get_registration_member_types();

        foreach ( $member_types as $member_type ) {

            // Init Selected
            $selected = '';

            // Run the allowed option name through the before_save filter, so we'll be sure to get a match
            $allowed_options = xprofile_sanitize_data_value_before_save( $member_type['id'] , false, false );

            // First, check to see whether the user-entered value matches
            if ( in_array( $allowed_options, (array) $option_values ) ) {
                $selected = ' selected="selected"';
            }

            // Get Member Type ID.
            $type_id = isset( $member_type['id'] ) ? $member_type['id'] : yz_get_member_type_id( $member_type['singular'] );

            $html .= apply_filters( 'bp_get_the_profile_field_options_member_type', '<option' . $selected . ' value="' . esc_attr( $type_id ) . '">' . $member_type['singular'] . '</option>', $type_id, $member_type['singular'] , $this->field_obj->id, $selected );

        }

        echo $html;
    }

    /**
     * Output HTML for this field type on the wp-admin Profile Fields screen.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.0.0
     *
     * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
     */
    public function admin_field_html( array $raw_properties = array() ) {
        ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>" class="screen-reader-text"><?php
            /* translators: accessibility text */
            esc_html_e( 'Select', 'youzer-member-types' );
        ?></label>
        <select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?>>
            <?php bp_the_profile_field_options(); ?>
        </select>

        <?php
    }

    /**
     * Output HTML for this field type's children options on the wp-admin Profile Fields "Add Field" and "Edit Field" screens.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.0.0
     *
     * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
     * @param string            $control_type  Optional. HTML input type used to render the current
     *                                         field's child options.
     */
    public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
        parent::admin_new_field_html( $current_field, 'radio' );
    }

}
