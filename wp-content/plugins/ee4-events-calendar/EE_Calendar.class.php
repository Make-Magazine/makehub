<?php
define('EE_CALENDAR_BASENAME', plugin_basename(EE_CALENDAR_PLUGIN_FILE));
define('EE_CALENDAR_PATH', plugin_dir_path(__FILE__));
define('EE_CALENDAR_URL', plugin_dir_url(__FILE__));
define('EE_CALENDAR_ADMIN', EE_CALENDAR_PATH . 'admin' . DS);
define('EE_CALENDAR_DMS_PATH', EE_CALENDAR_PATH . 'data_migration_scripts' . DS);

/**
 * Class  EE_Calendar
 *
 * @package         Event Espresso
 * @subpackage      espresso-calendar
 * @author              Seth Shoultes, Chris Reynolds, Brent Christensen, Michael Nelson
 *
 * ------------------------------------------------------------------------
 */
class EE_Calendar extends EE_Addon
{



    /**
     * @var string activation_indicator_option_name
     */
    const activation_indicator_option_name = 'ee_espresso_calendar_activation';



    /**
     * register_addon
     */
    public static function register_addon()
    {
        EE_Psr4AutoloaderInit::psr4_loader()->addNamespace('EventEspressoCalendar', EE_CALENDAR_PATH);
// define the plugin directory path and URL
        // register addon via Plugin API
        EE_Register_Addon::register('Calendar', array(
                'version'                   => EE_CALENDAR_VERSION,
                'min_core_version' => '4.3.0',
                'main_file_path'        => EE_CALENDAR_PLUGIN_FILE,
                'admin_path'            => EE_CALENDAR_ADMIN . 'calendar' . DS,
                'admin_callback'        => 'additional_admin_hooks',
                'config_class'          => 'EE_Calendar_Config',
                'config_name'       => 'EE_Calendar',
                'autoloader_paths' => array(
                    'EE_Calendar'                           => EE_CALENDAR_PATH . 'EE_Calendar.class.php',
                    'EE_Calendar_Config'            => EE_CALENDAR_PATH . 'EE_Calendar_Config.php',
                    'EE_Datetime_In_Calendar'   => EE_CALENDAR_PATH . 'EE_Datetime_In_Calendar.class.php',
                    'Calendar_Admin_Page'       => EE_CALENDAR_ADMIN . 'calendar' . DS . 'Calendar_Admin_Page.core.php',
                    'Calendar_Admin_Page_Init'  => EE_CALENDAR_ADMIN . 'calendar' . DS . 'Calendar_Admin_Page_Init.core.php',
                ),
                'dms_paths'             => array( EE_CALENDAR_DMS_PATH ),
                'module_paths'      => array( EE_CALENDAR_PATH . 'EED_Espresso_Calendar.module.php' ),
                'shortcode_paths'   => array( EE_CALENDAR_PATH . 'EES_Espresso_Calendar.shortcode.php' ),
                'widget_paths'      => array( EE_CALENDAR_PATH . 'EEW_Espresso_Calendar.widget.php' ),
                // if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
                'pue_options'           => array(
                    'pue_plugin_slug' => 'ee4-events-calendar',
                    'checkPeriod' => '24',
                    'use_wp_update' => false
                )
            ));
    }



    /**
     *  additional_admin_hooks
     *
     *  @access     public
     *  @return     void
     */
    public function additional_admin_hooks()
    {
        // is admin and not in M-Mode ?
        if (is_admin() && ! EE_Maintenance_Mode::instance()->level()) {
            add_filter('plugin_action_links', array( $this, 'plugin_actions' ), 10, 2);
            add_action('action_hook_espresso_featured_image_add_to_meta_box', array( $this, 'add_to_featured_image_meta_box' ));
        }
    }



    /**
     * plugin_actions
     *
     * Add a settings link to the Plugins page, so people can go straight from the plugin page to the settings page.
     * @param $links
     * @param $file
     * @return array
     */
    public function plugin_actions($links, $file)
    {
        if ($file == EE_CALENDAR_BASENAME) {
// before other links
            array_unshift($links, '<a href="admin.php?page=espresso_calendar">' . __('Settings', 'event_espresso') . '</a>');
        }
        return $links;
    }



    /**
     * add_to_featured_image_meta_box
     * @param $event_meta
     */
    public function add_to_featured_image_meta_box($event_meta)
    {
        EE_Registry::instance()->load_helper('Form_Fields');
        $html = '<p>';
        $html .= EEH_Form_Fields::select(// question
            __('Add image to event calendar', 'event_espresso'), // answer
            isset($event_meta['display_thumb_in_calendar']) ? $event_meta['display_thumb_in_calendar'] : '', // options
            array(
                array('id' => true, 'text' => __('Yes', 'event_espresso')),
                array('id' => false, 'text' => __('No', 'event_espresso'))
            ), // name
            'show_on_calendar', // css id
            'show_on_calendar'
        );
        $html .= '</p>';
        echo $html;
    }
}
// End of file EE_Calendar.class.php
// Location: wp-content/plugins/espresso-calendar/EE_Calendar.class.php
