<?php
/**
* Calendar_Admin_Page_Init class
*
* This is the init for the Calendar Addon Admin Pages.  See EE_Admin_Page_Init for method inline docs.
*
* @package          Event Espresso (calendar addon)
* @subpackage       admin/Calendar_Admin_Page_Init.core.php
* @author           Darren Ethier
*
* ------------------------------------------------------------------------
*/
class Calendar_Admin_Page_Init extends EE_Admin_Page_Init
{




    /**
     *      constructor
     *      @Constructor
     *      @access public
     *      @return void
     */
    public function __construct()
    {

        do_action('AHEE_log', __FILE__, __FUNCTION__, '');

        define('CALENDAR_PG_SLUG', 'espresso_calendar');
        define('CALENDAR_LABEL', __('Calendar', 'event_espresso'));
        define('EE_CALENDAR_ADMIN_URL', admin_url('admin.php?page=' . CALENDAR_PG_SLUG));
        define('EE_CALENDAR_ADMIN_ASSETS_PATH', EE_CALENDAR_ADMIN . 'calendar' . DS . 'assets' . DS);
        define('EE_CALENDAR_ADMIN_ASSETS_URL', EE_CALENDAR_URL . 'admin' . DS . 'calendar' . DS . 'assets' . DS);
        define('EE_CALENDAR_ADMIN_TEMPLATE_PATH', EE_CALENDAR_ADMIN . 'calendar' . DS . 'templates' . DS);
        define('EE_CALENDAR_ADMIN_TEMPLATE_URL', EE_CALENDAR_URL . 'admin' . DS . 'calendar' . DS . 'templates' . DS);

        parent::__construct();
        $this->_folder_path = EE_CALENDAR_ADMIN . 'calendar' . DS;
    }





    protected function _set_init_properties()
    {
        $this->label = CALENDAR_LABEL;
        $this->menu_label = CALENDAR_LABEL;
        $this->menu_slug = CALENDAR_PG_SLUG;
        $this->capability = 'administrator';
    }



    /**
    *       sets vars in parent for creating admin menu entry
    *
    *       @access         public
    *       @return         mixed
    */
    public function get_menu_map()
    {
        if (version_compare(EVENT_ESPRESSO_VERSION, '4.4.0.dev', '>=')) {
            return $this->_menu_map;
        } else {
            $map = array(
                'group' => 'settings',
                'menu_order' => 25,
                'show_on_menu' => true,
                'parent_slug' => 'events'
            );
            return $map;
        }
    }


    protected function _set_menu_map()
    {
        $this->_menu_map = new EE_Admin_Page_Sub_Menu(array(
            'menu_group' => 'settings',
            'menu_order' => 25,
            'show_on_menu' => true,
            'parent_slug' => 'espresso_events',
            'menu_slug' => CALENDAR_PG_SLUG,
            'menu_label' => CALENDAR_LABEL,
            'capability' => 'administrator',
            'admin_init_page' => $this
        ));
    }
}
