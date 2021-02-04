<?php
namespace EventEspressoCalendar;

use EventEspresso\core\libraries\iframe_display\Iframe;

/**
 * Class CalendarIframe
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 *
 */
class CalendarIframe extends Iframe
{


    /**
     * CalendarIframe constructor.
     *
     * @param array $ee_calendar_js_options
     * @throws \DomainException
     */
    public function __construct($ee_calendar_js_options = array())
    {
        $this->addLocalizedVars(
            array('espresso_calendar' => $ee_calendar_js_options),
            'eeCAL'
        );
        parent::__construct(
            __('Calendar', 'event_espresso'),
            \EED_Espresso_Calendar::instance()->display_calendar(array(), false)
        );
    }


    /**
     * display
     *
     * @access    public
     * @param string $utm_content
     * @return    void
     * @throws \DomainException
     * @throws \EE_Error
     */
    public function display($utm_content = 'events_calendar')
    {
        global $wp_version;
        $this->addStylesheets(
            apply_filters(
                'FHEE__CalendarIframe__display__css',
                array(
                    'fullcalendar'      => EE_CALENDAR_URL . 'css' . DS . 'fullcalendar.css?ver=1.6.2',
                    'espresso_calendar' => EE_CALENDAR_URL . 'css' . DS . 'calendar.css?ver=' . EE_CALENDAR_VERSION,
                ),
                $this
            )
        );
        $this->addScripts(
            apply_filters(
                'FHEE__CalendarIframe__display__js',
                array(
                    'wp-hooks'            => includes_url('js/dist/hooks.min.js?ver=' . $wp_version),
                    'fullcalendar-min-js' => EE_CALENDAR_URL . 'scripts' . DS . 'fullcalendar.min.js?ver=1.6.2',
                    'espresso_calendar'   => EE_CALENDAR_URL . 'scripts' . DS
                                             . 'espresso_calendar.js?ver=' . EE_CALENDAR_VERSION,
                ),
                $this
            )
        );
        parent::display($utm_content);
    }
}
