<?php
/**
 * BuddyPress XProfile Classes.
 *
 */
// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * faire_list xprofile field type.
 *
 * @since 2.0.0
 */
class MAKE_XProfile_Field_Type_FaireList extends BP_XProfile_Field_Type {

    /**
     * Constructor for the URL field type
     *
     * @since 2.1.0
     */
    public function __construct() {
        parent::__construct();

        $this->category = _x('Single Fields', 'xprofile field type category', 'makehub');
        $this->name = _x('Maker Faire List', 'xprofile field type', 'makehub');

        $this->accepts_null_value = true;
        $this->supports_options = false;

        do_action('bp_xprofile_field_type_faire_list', $this);
    }

    /**
     * Output the edit field HTML for this field type.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.1.0
     *
     * @param array $raw_properties Optional key/value array of
     *                              {@link http://dev.w3.org/html5/markup/input.number.html permitted attributes}
     *                              that you want to add.
     */
    public function edit_field_html(array $raw_properties = array()) {
        global $field;

        $data = empty($field->data) ? array(
            'location' => self::get_location_value($field->id),
            'year' => self::get_year_value($field->id),
            'image' => self::get_image_value($field->id),
            'title' => self::get_title_value($field->id),
            'link' => self::get_link_value($field->id),
                ) : maybe_unserialize($field->data->value);
        
        // make sure data is always array. In case some one changed the field type, do not throw error.
        if (!is_array($data)) {
            $data = array(
                'location' => '',
                'year' => '',
                'image' => '',
                'title' => '',
                'link' => '',
            );
        }else{
            if(!isset($data['location'])){
                if(isset($data[0])){
                    $data = array(
                        'location' => $data[0],
                        'year' => $data[1],
                        'image' => $data[2],
                        'title' => $data[3],
                        'link' => $data[4],
                    );
                }
            }
        }

        if (isset($raw_properties['user_id'])) {
            unset($raw_properties['user_id']);
        }

        $field_name = bp_get_the_profile_field_input_name();
        $value = bp_get_the_profile_field_edit_value();

        $is_array = is_array($value);
        $location = $is_array && isset($value['location']) ? $value['location'] : $data['location'];
        $year     = $is_array && isset($value['year']) ? $value['year'] : $data['year'];
        $image    = $is_array && isset($value['image']) ? $value['image'] : $data['image'];
        $title    = $is_array && isset($value['title']) ? $value['title'] : $data['title'];
        $link     = $is_array && isset($value['link']) ? $value['link'] : $data['link'];

        $type = self::get_value_type($field->id);

        $location_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[location]',
                            'id' => $field_name . '[location]',
                            'value' => $location,
                        ),
                        $raw_properties
        ));

        $year_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[year]',
                            'id' => $field_name . '[year]',
                            'value' => $year,
                        ),
                        $raw_properties
        ));

        $image_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'file',
                            'name' => $field_name . '[image]',
                            'id' => $field_name . '[image]',
                            'value' => $image,
                        ),
                        $raw_properties
        ));

        $title_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[title]',
                            'id' => $field_name . '[title]',
                            'value' => $title,
                        ),
                        $raw_properties
        ));

        $links_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[links]',
                            'id' => $field_name . '[links]',
                            'value' => $link,
                        ),
                        $raw_properties
        ));
        ?>

        <legend id="<?php bp_the_profile_field_input_name(); ?>-1">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </legend>

        <?php
        // Errors.
        do_action(bp_get_the_profile_field_errors_action());
        // Input.                
        ?>
        <div class="bpxcftr-fair-list-edit-field bpxcftr-faire-list-edit-field-<?php echo esc_attr($type); ?>">
            <input <?php echo $location_atts; ?> />Faire Location
            <input <?php echo $year_atts; ?> />Faire Year<br/>
            <!-- Project Image -->
            <?php
            $name = $field_name . '[image]';
            $fieldID = $field_name . '[image]';

            // we user '-' to trigger the save action.
            $edit_value = !empty($image) ? $image : '-';

            $has_file = false;
            // for backward compatibility, check against '-'.
            if ($image && $image != '-') {
                $has_file = true;
            }

            if (( empty($image) || '-' == $image ) && xprofile_check_is_required_field($fieldID)) {
                $raw_properties['required'] = true;
            }
            ?>
            <input <?php echo $image_atts; ?> />Project Image<br/>

            <?php if ($has_file) : ?>
                <p>
                    <?php echo $image; ?>
                </p>

                <label>
                    <input type="checkbox" name="<?php echo $name; ?>_delete" value="1"/> <?php _e('Check this to delete this file', 'bp-xprofile-custom-field-types'); ?>
                </label>

            <?php endif; ?>

            <input type="hidden" value="<?php echo esc_attr($edit_value); ?>" name="<?php echo esc_attr($name . '[title]'); ?>"/>

            <!-- Project title and URL link -->            
            <input <?php echo $title_atts; ?> />Project Title<br/>
            <input <?php echo $links_atts; ?> />Project Link<br/>
        </div>

        <?php if (bp_get_the_profile_field_description()) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
        <?php endif; ?>

        <?php
    }

    /**
     * Output HTML for this field type on the wp-admin Profile Fields screen.
     *
     * Must be used inside the {@link bp_profile_fields()} template loop.
     *
     * @since 2.1.0
     *
     * @param array $raw_properties Optional key/value array of permitted
     *                              attributes that you want to add.
     */
    public function admin_field_html(array $raw_properties = array()) {
        global $field;

        $field_name = bp_get_the_profile_field_input_name();

        $location_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[location]',
                            'id' => $field_name . '[location]'
                        ),
                        $raw_properties
        ));

        $year_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[year]',
                            'id' => $field_name . '[year]'
                        ),
                        $raw_properties
        ));

        $image_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'file',
                            'name' => $field_name . '[image]',
                            'id' => $field_name . '[image]'
                        ),
                        $raw_properties
        ));

        $title_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[title]',
                            'id' => $field_name . '[title]'
                        ),
                        $raw_properties
        ));

        $links_atts = $this->get_edit_field_html_elements(array_merge(
                        array(
                            'type' => 'text',
                            'name' => $field_name . '[links]',
                            'id' => $field_name . '[links]'
                        ),
                        $raw_properties
        ));
        ?>
        <div class="bpxcftr-faire-list-edit-field">
            <input <?php echo $location_atts; ?> />Faire Location<br/>
            <input <?php echo $year_atts; ?> />Faire Year<br/>
            
            <!-- Project Image -->            
            <input <?php echo $image_atts; ?> />Project Image<br/>
		
            <!-- Project title and URL link -->            
            <input <?php echo $title_atts; ?> />Project Title<br/>
            <input <?php echo $links_atts; ?> />Project Link
        </div>

        <?php
    }

    /**
     * This method usually outputs HTML for this field type's children options
     * on the wp-admin Profile Fields "Add Field" and "Edit Field" screens, but
     * for this field type, we don't want it, so it's stubbed out.
     *
     * @since 2.1.0
     *
     * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
     * @param string            $control_type  Optional. HTML input type used to render the current
     *                                         field's child options.
     */
    public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {
    }
    
    /**
     * Modify submitted URL values before validation.
     *
     * The URL validation regex requires a http(s) protocol, so that all
     * values saved in the database are fully-formed URLs. However, we
     * still want to allow users to enter URLs without a protocol, for a
     * better user experience. So we catch submitted URL values, and if
     * the protocol is missing, we prepend 'http://' before passing to
     * is_valid().
     *
     * @since 2.1.0
     * @since 2.4.0 Added the `$field_id` parameter.
     *
     * @param string     $submitted_value Raw value submitted by the user.
     * @param string|int $field_id        Optional. ID of the field.
     * @return string
     */
    /*
    public static function pre_validate_filter($submitted_value = '', $field_id = '') {

        // Allow empty URL values.
        if (empty($submitted_value)) {
            return '';
        }

        // Run some checks on the submitted value.
        if (false === strpos($submitted_value, ':') &&
                substr($submitted_value, 0, 1) !== '/' &&
                substr($submitted_value, 0, 1) !== '#' &&
                !preg_match('/^[a-z0-9-]+?\.php/i', $submitted_value)
        ) {
            $submitted_value = 'http://' . $submitted_value;
        }

        return $submitted_value;
    }*/

    /**
     * Format URL values for display.
     *
     * @since 2.1.0
     * @since 2.4.0 Added the `$field_id` parameter.
     *
     * @param string     $field_value The URL value, as saved in the database.
     * @param string|int $field_id    Optional. ID of the field.
     * @return string URL converted to a link.
     */
    public static function display_filter($field_value, $field_id = '') {
        if ( empty( $field_value ) ) {
			return $field_value;
		}
        /*$link = strip_tags($field_value);
        $no_scheme = preg_replace('#^https?://#', '', rtrim($link, '/'));
        $url_text = str_replace($link, $no_scheme, $field_value);
        return '<a href="' . esc_url($field_value) . '" rel="nofollow">' . esc_html($url_text) . '</a>';*/
        return $field_value;
    }

    /**
     * Get the allowed value type(can be integer|numeric|string).
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_value_type($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'value_type', true);
    }

    /**
     * Get the Link Value.
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_link_value($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'link_value', true);
    }

    /**
     * Get the Title Value.
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_title_value($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'title_value', true);
    }

    /**
     * Get the Image Value.
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_image_value($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'image_value', true);
    }

    /**
     * Get the Year Value.
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_year_value($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'year_value', true);
    }

    /**
     * Get the Location Value.
     *
     * @param int $field_id field id.
     *
     * @return float|int
     */
    private static function get_location_value($field_id) {
        return bp_xprofile_get_meta($field_id, 'field', 'location_value', true);
    }

}