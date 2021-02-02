<?php

namespace EventEspressoCalendar;

use EventEspresso\core\libraries\iframe_display\IframeEmbedButton;

/**
 * Class CalendarIframeEmbedButton
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 *
 */
class CalendarIframeEmbedButton extends IframeEmbedButton
{

    /**
     * CalendarIframeEmbedButton constructor.
     */
    public function __construct()
    {
        parent::__construct(
            esc_html__('Events Calendar', 'event_espresso'),
            'calendar'
        );
    }


    public function addEmbedButton()
    {
        add_filter(
            'FHEE__Calendar_Admin_Page___usage__calendar_usage_info__template_args',
            array($this, 'addCalendarUsageIframeEmbedButtonSection')
        );
        // iframe embed buttons
        $this->addFilterIframeEmbedButton(
            'FHEE__EventEspresso_core_libraries_iframe_display_IframeEmbedButton__addIframeEmbedButtonsSection__embed_buttons'
        );
        \EE_Registry::$i18n_js_strings['iframe_embed_title'] = __(
            'copy and paste the following into any other site\'s content to display the event calendar:',
            'event_espresso'
        );
        add_action('admin_enqueue_scripts', array($this, 'embedButtonAssets'));
    }


    /**
     * Adds a template arg for adding the iframe embed code button section to the calendar admin page
     * return array
     *
     * @param array $template_args
     * @return array
     */
    public function addCalendarUsageIframeEmbedButtonSection(array $template_args)
    {
        $template_args['iframe_embed_buttons_section'] = $this->addIframeEmbedButtonsSection(
            array('calendar' => $this->embedButtonHtml())
        );
        return $template_args;
    }
}
