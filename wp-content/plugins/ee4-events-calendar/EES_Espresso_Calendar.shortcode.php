<?php
/*
 * ------------------------------------------------------------------------
 *
 * EE_Calendar
 *
 * @package         Event Espresso
 * @subpackage       espresso-calendar
 * @author            Seth Shoultes, Chris Reynolds, Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
class EES_Espresso_Calendar extends EES_Shortcode
{





    /**
     *  set_hooks - for hooking into EE Core, modules, etc
     *
     *  @access     public
     *  @return     void
     */
    public static function set_hooks()
    {
    }



    /**
     *  set_hooks_admin - for hooking into EE Admin Core, modules, etc
     *
     *  @access     public
     *  @return     void
     */
    public static function set_hooks_admin()
    {
    }



    /**
     *  set_definitions
     *
     *  @access     public
     *  @return     void
     */
    public static function set_definitions()
    {
    }



    /**
     *  run - initial shortcode module setup called during "wp_loaded" hook
     *  this method is primarily used for loading resources that will be required by the shortcode when it is actually processed
     *
     *  @access     public
     *  @param   WP $WP
     *  @return     void
     */
    public function run(WP $WP)
    {
        // this will trigger the EED_Espresso_Calendar module's run() method during the pre_get_posts hook point,
        // this allows us to initialize things, enqueue assets, etc,
        // as well, this saves an instantiation of the module in an array, using 'calendar' as the key, so that we can retrieve it
        add_action('pre_get_posts', array( EED_Espresso_Calendar::instance(), 'run' ));
    }



    /**
     * process_shortcode
     *
     * [ESPRESSO_CALENDAR]
     * [ESPRESSO_CALENDAR show_expired="true"]
     * [ESPRESSO_CALENDAR event_category_id="your_category_identifier"]
     *
     * @access    public
     * @param array $attributes
     * @return string
     */
    public function process_shortcode($attributes = array())
    {
        // make sure $attributes is an array
        $attributes = array_merge(\EED_Espresso_Calendar::getCalendarDefaults(), (array) $attributes);
        if (method_exists('\EES_Shortcode', 'sanitize_attributes')) {
            $attributes = \EES_Shortcode::sanitize_attributes($attributes);
        } else {
            $attributes = $this->sanitize_the_attributes($attributes);
        }
        return EED_Espresso_Calendar::instance()->display_calendar($attributes);
    }



    /**
     * temporary copy of \EES_Shortcode::sanitize_attributes()
     * for backwards compatibility sake
     *
     * @param array $attributes
     * @param array $custom_sanitization
     * @return array
     */
    private function sanitize_the_attributes(array $attributes, $custom_sanitization = array())
    {
        foreach ($attributes as $key => $value) {
// is a custom sanitization callback specified ?
            if (isset($custom_sanitization[ $key ])) {
                $callback = $custom_sanitization[ $key ];
                if ($callback === 'skip_sanitization') {
                    $attributes[ $key ] = $value;
                    continue;
                } elseif (function_exists($callback)) {
                    $attributes[ $key ] = $callback($value);
                    continue;
                }
            }
            switch (true) {
                case $value === null:
                case is_int($value):
                case is_float($value):
                    // typical booleans
                
                case in_array($value, array(true, 'true', '1', 'on', 'yes', false, 'false', '0', 'off', 'no'), true):
                    $attributes[ $key ] = $value;

                    break;
                case is_string($value):
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $attributes[ $key ] = sanitize_text_field($value);

                    break;
                case is_array($value):
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $attributes[ $key ] = $this->sanitize_the_attributes($attributes);

                    break;
                default:
                    // only remaining data types are Object and Resource
                    
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      // which are not allowed as shortcode attributes
                    $attributes[ $key ] = null;

                    break;
            }
        }
        return $attributes;
    }
}
// End of file EES_Espresso_Calendar.shortcode.php
// Location: /espresso_calendar/EES_Espresso_Calendar.shortcode.php
