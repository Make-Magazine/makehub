<?php
use EventEspressoCalendar\CalendarIframe;
use EventEspressoCalendar\CalendarIframeEmbedButton;

/**
 * Class  EED_Espresso_Calendar
 *
 * @package         Event Espresso
 * @subpackage      espresso-calendar
 * @author              Seth Shoultes, Chris Reynolds, Brent Christensen, Michael Nelson
 *
 * ------------------------------------------------------------------------
 */
class EED_Espresso_Calendar extends EED_Module
{

    /**
     * @var         INT $_event_category_id
     * @access  private
     */
    private $_event_category_id = 0;

    /**
     * @var  string $_output_filter
     * @access  private
     */
    private $_output_filter = '';

    /**
     * @var array $_js_options
     */
    private $_js_options = array();

    /**
     * @var boolean $iframe
     */
    private static $iframe = false;

    /**
     * @var EventEspressoCalendar\CalendarIframeEmbedButton $iframe_embed_button
     */
    private static $iframe_embed_button;



    /**
     * @return EED_Espresso_Calendar
     */
    public static function instance()
    {
        return parent::get_instance(__CLASS__);
    }



    /**
      *     set_hooks - for hooking into EE Core, other modules, etc
      *
      *  @access    public
      *  @return    void
      */
    public static function set_hooks()
    {
        EE_Config::register_route('calendar', 'EED_Espresso_Calendar', 'run');
        EE_Config::register_route('iframe', 'EED_Espresso_Calendar', 'calendar_iframe', 'calendar');
    }



     /**
      *     set_hooks_admin - for hooking into EE Admin Core, other modules, etc
      *
      *  @access    public
      *  @return    void
      */
    public static function set_hooks_admin()
    {
        // ajax hooks
        add_action('wp_ajax_get_calendar_events', array( 'EED_Espresso_Calendar', '_get_calendar_events' ));
        add_action('wp_ajax_nopriv_get_calendar_events', array( 'EED_Espresso_Calendar', '_get_calendar_events' ));
        // hook into the end of the \EE_Admin_Page::_load_page_dependencies()
        // to load assets for "espresso_calendar" page on the "default" route (action)
        if (class_exists('\EventEspresso\core\libraries\iframe_display\IframeEmbedButton')) {
            add_action(
                'FHEE__EE_Admin_Page___load_page_dependencies__after_load__espresso_events__default',
                array( 'EED_Espresso_Calendar', 'calendar_iframe_embed_button' ),
                10
            );
            // hook into the end of the \EE_Admin_Page::_load_page_dependencies()
            // to load assets for "espresso_calendar" page on the "usage" route (action)
            add_action(
                'FHEE__EE_Admin_Page___load_page_dependencies__after_load__espresso_calendar__usage',
                array( 'EED_Espresso_Calendar', 'calendar_iframe_embed_button' ),
                10
            );
        }
    }



    /**
     * @return EventEspressoCalendar\CalendarIframeEmbedButton
     */
    public static function get_iframe_embed_button()
    {
        if (! self::$iframe_embed_button instanceof CalendarIframeEmbedButton) {
            self::$iframe_embed_button = new CalendarIframeEmbedButton();
        }
        return self::$iframe_embed_button;
    }



    /**
     * calendar_iframe_embed_button
     *
     * @return    void
     * @throws \EE_Error
     */
    public static function calendar_iframe_embed_button()
    {
        $iframe_embed_button = \EED_Espresso_Calendar::get_iframe_embed_button();
        $iframe_embed_button->addEmbedButton();
    }



    /**
     * ticket_selector_iframe
     *
     * @access    public
     * @return    void
     * @throws \EE_Error
     */
    public function calendar_iframe()
    {
        EED_Espresso_Calendar::$iframe = true;
        $this->config()->tooltip->show = false;
        $calendar_iframe = new CalendarIframe(
            $this->get_calendar_js_options(
                \EED_Espresso_Calendar::getCalendarDefaults()
            )
        );
        $calendar_iframe->display();
    }



    /**
     *    _get_calendar_events
     *
     * @return array
     */
    public static function getCalendarDefaults()
    {
        return array(
                'show_expired'       => 'true',
                'cal_view'           => 'month',
                'widget'             => false,
                'month'              => date('n'),
                'year'               => date('Y'),
                'max_events_per_day' => null,
                'iframe' => EED_Espresso_Calendar::$iframe,
        );
    }



    /**
     *    _get_calendar_events
     *
     * @return string
     */
    public static function _get_calendar_events()
    {
        EED_Espresso_Calendar::$iframe = isset($_REQUEST['iframe']) && ! empty($_REQUEST['iframe'])
                ? filter_var($_REQUEST['iframe'], FILTER_VALIDATE_BOOLEAN)
                : false;
        return EED_Espresso_Calendar::instance()->get_calendar_events();
    }




    /**
     *    config
     *
     * @return EE_Calendar_Config
     */
    public function config()
    {
        return EE_Registry::instance()->addons->EE_Calendar->config();
    }





     /**
      *    run - initial module setup
      *
      * @access    public
      * @param  WP $WP
      * @return    void
      */
    public function run($WP)
    {
        if (! has_action('wp_enqueue_scripts', array( $this, 'calendar_scripts' ))) {
            add_action('wp_enqueue_scripts', array( $this, 'calendar_scripts' ));
        }
        if ($this->config()->tooltip->show) {
            add_filter('FHEE_load_qtip', '__return_true');
        }
    }






    /**
     *  calendar_scripts - Load the scripts and css
     *
     *  @access     public
     *  @return     void
     */
    public function calendar_scripts()
    {
        static $scripts_loaded = false;
        if ($scripts_loaded) {
            return;
        }
        // load base calendar style
        wp_register_style('fullcalendar', EE_CALENDAR_URL . 'css' . DS . 'fullcalendar.css');
        // Check to see if the calendar css file exists in the '/uploads/espresso/' directory
        if (is_readable(EVENT_ESPRESSO_UPLOAD_DIR . 'css' . DS . 'calendar.css')) {
            // This is the url to the css file if available
            wp_register_style('espresso_calendar', EVENT_ESPRESSO_UPLOAD_URL . 'css' . DS . 'calendar.css');
        } else {
            // EE calendar style
            wp_register_style('espresso_calendar', EE_CALENDAR_URL . 'css' . DS . 'calendar.css');
        }
        // core calendar script
        wp_register_script('fullcalendar-min-js', EE_CALENDAR_URL . 'scripts' . DS . 'fullcalendar.min.js', array( 'jquery' ), '1.6.2', true);
        wp_register_script('espresso_calendar', EE_CALENDAR_URL . 'scripts' . DS . 'espresso_calendar.js', array( 'fullcalendar-min-js', 'wp-hooks' ), EE_CALENDAR_VERSION, true);

        // get the current post
        global $post, $is_espresso_calendar;
        // check the post content for the short code
        if ($is_espresso_calendar
            || (
                isset($post->post_content)
                && strpos($post->post_content, '[ESPRESSO_CALENDAR') !== false
            )
        ) {
            wp_enqueue_style('fullcalendar');
            wp_enqueue_style('espresso_calendar');
            wp_enqueue_script('espresso_calendar');
             $scripts_loaded = true;
        }
    }



    /**
     * Gets the HTML for the calendar filters area
     * @param array $ee_calendar_js_options mess of options which will eb passed onto js
     * which we might want in here. (But using the config object directly might be nice,
     * because it's well-defined... however, unfortunately settings in it might be overridden
     * by shortcode attributes, which you can access in this array, if you know their key)
     * @return string
     */
    private function _get_filter_html($ee_calendar_js_options = array())
    {
        $output_filter = '';
        if (! $ee_calendar_js_options['widget']) {
            // Query for Select box filters
            $ee_terms = EEM_Term::instance()->get_all(array(
                'order_by' => array( 'name' => 'ASC' ),
                array( 'Term_Taxonomy.taxonomy' => 'espresso_event_categories',
            ) ));
            $venues = EEM_Venue::instance()->get_all(array( 'order_by' => array( 'VNU_name' => 'ASC' ) ));

            if (! empty($venues) || !empty($ee_terms)) {
                ob_start();

            // Category legend
                if ($this->config()->display->enable_category_legend) {
                    echo '
			<div id="espresso-category-legend">
				<p class="smaller-text lt-grey-txt">' .  __('Click to select a category:', 'event_espresso') . '</p>
				<ul id="ee-category-legend-ul">';

                    foreach ($ee_terms as $ee_term) {
                        if ($ee_term instanceof EE_Term) {
                            /*@var $ee_term EE_Term */
                            $catcode = $ee_term->ID();
                            $bg = $ee_term->get_extra_meta('background_color', true, $this->config()->display->event_background);
                            $fontcolor =$ee_term->get_extra_meta('text_color', true, $this->config()->display->event_text_color);
                            $use_bg = $ee_term->get_extra_meta('use_color_picker', true);
                            $caturl = esc_url(add_query_arg('event_category_id', $ee_term->slug()));
                            if ($use_bg) {
                                echo '
							<li id="ee-category-legend-li-'.$catcode.'" class="has-sub" style="background: ' . $bg . ';">
								<span class="ee-category"><a href="'. $caturl .'#espresso_calendar" style="color: ' . $fontcolor . ';">'.$ee_term->name().'</a></span>
							</li>';
                            } else {
                                echo '
							<li id="ee-category-li-'.$catcode.'" class="has-sub" style="background: #f3f3f3;" >
								<span class="ee-category"><a href="'. $caturl .'#espresso_calendar">'.$ee_term->name().'</a></span>
							</li>';
                            }
                        }
                    }
                    echo '</ul>
				</div>
				<div class="clear"></div>
				';
                }

            // Filter dropdowns
                if ($this->config()->display->enable_calendar_filters) {
                    ?>
                    <!-- select box filters -->
                    <div class="ee-filter-form">
                    <form name="filter-calendar-form" id="filter-calendar-form" method="post" action="#espresso_calendar">
                    <?php if (! empty($ee_terms)) { ?>
                    <label for="ee-category-submit"></label>
                    <select id="ee-category-submit" class="submit-this ee-category-select" name="event_category_id">
                    <option id="option" class="ee_select" value=""><?php echo __('Select a Category', 'event_espresso'); ?></option>
                    <option class="ee_filter_show_all" value=""><?php echo __('Show All', 'event_espresso'); ?></option>
                        <?php
                        foreach ($ee_terms as $term) {
                            if ($term instanceof EE_Term) {
                                $selected = in_array($ee_calendar_js_options['event_category_id'], array( $term->slug(), $term->ID() )) ? 'selected="selected"' : '';
                                echo '<option ' . $selected . ' value="' . $term->slug() . '">' . $term->name() . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php }?>

                    <?php if (! empty($venues)) { ?>
                    <label for="ee-venue-submit"></label>
                    <select id="ee-venue-submit" class="submit-this ee-venue-select" name="event_venue_id">
                    <option class="ee_select" value=""><?php echo __('Select a Venue', 'event_espresso'); ?></option>
                    <option class="ee_filter_show_all" value=""><?php echo __('Show All', 'event_espresso'); ?></option>
                        <?php
                        foreach ($venues as $venue) {
                            if ($venue instanceof EE_Venue && $venue->status() === 'publish') {
                                $selected = in_array($ee_calendar_js_options['event_venue_id'], array( $venue->identifier(), $venue->ID() )) ? ' selected="selected"' : '';
                                echo '<option' . $selected . ' value="' . $venue->identifier() . '">' . stripslashes($venue->name()) . '</option>';
                            }
                        }?>
                    </select>
                    <?php }?>
                    </form>
                    </div>
                    <?php
                }
                $output_filter = ob_get_contents();
                ob_end_clean();
            }
        }

        return $output_filter;
    }



    /**
     *    display_calendar
     *
     * @access    public
     * @param $ee_calendar_js_options
     * @return    string
     */
    public function get_calendar_js_options($ee_calendar_js_options)
    {
        if (empty($this->_js_options)) {
            // get calendar options
            $calendar_config = $this->config()->to_flat_array();
            // merge incoming shortcode attributes with calendar config
            $ee_calendar_js_options = array_merge($calendar_config, $ee_calendar_js_options);
            // if the user has changed the filters, those should override whatever the admin specified in the shortcode
            $js_option_event_category_id = isset($ee_calendar_js_options['event_category_id'])
                    ? $ee_calendar_js_options['event_category_id']
                    : null;
            $js_option_event_venue_id = isset($ee_calendar_js_options['event_venue_id'])
                    ? $ee_calendar_js_options['event_venue_id']
                    : null;
            // setup an array with overridden values in it
            $overrides = array(
                    'event_category_id' => isset($_REQUEST['event_category_id'])
                            ? sanitize_key($_REQUEST['event_category_id'])
                            : $js_option_event_category_id,
                    'event_venue_id'    => isset($_REQUEST['event_venue_id'])
                            ? sanitize_key($_REQUEST['event_venue_id'])
                            : $js_option_event_venue_id,
                    'month'             => isset($_REQUEST['month'])
                            ? sanitize_text_field($_REQUEST['month'])
                            : $ee_calendar_js_options['month'],
                    'year'              => isset($_REQUEST['year'])
                            ? sanitize_text_field($_REQUEST['year'])
                            : $ee_calendar_js_options['year'],
            );
            // merge overrides into options
            $ee_calendar_js_options = array_merge($ee_calendar_js_options, $overrides);
            // set and format month param
            if (! is_int($ee_calendar_js_options['month']) && strtotime($ee_calendar_js_options['month'])) {
                $ee_calendar_js_options['month'] = date('n', strtotime($ee_calendar_js_options['month']));
            }
            // weed out any attempts to use month=potato or something similar
            $ee_calendar_js_options['month'] = is_numeric($ee_calendar_js_options['month'])
                                               && $ee_calendar_js_options['month'] > 0
                                               && $ee_calendar_js_options['month'] < 13
                    ? $ee_calendar_js_options['month']
                    : date('n');
            // fullcalendar uses 0-based value for month
            $ee_calendar_js_options['month']--;
            // set and format year param
            $ee_calendar_js_options['year'] = isset($ee_calendar_js_options['year'])
                                              && is_numeric($ee_calendar_js_options['year'])
                    ? $ee_calendar_js_options['year']
                    : date('Y');
            // add calendar filters
            $this->_output_filter = $this->_get_filter_html($ee_calendar_js_options);
            // grab some request vars
            $this->_event_category_id = isset($ee_calendar_js_options['event_category_id'])
                                        && ! empty($ee_calendar_js_options['event_category_id'])
                    ? $ee_calendar_js_options['event_category_id']
                    : '';
            // i18n some strings
            $ee_calendar_js_options['view_more_text'] = __('View More', 'event_espresso');
            $ee_calendar_js_options['month_names'] = array(
                    __('January', 'event_espresso'),
                    __('February', 'event_espresso'),
                    __('March', 'event_espresso'),
                    __('April', 'event_espresso'),
                    __('May', 'event_espresso'),
                    __('June', 'event_espresso'),
                    __('July', 'event_espresso'),
                    __('August', 'event_espresso'),
                    __('September', 'event_espresso'),
                    __('October', 'event_espresso'),
                    __('November', 'event_espresso'),
                    __('December', 'event_espresso')
            );
            $ee_calendar_js_options['month_names_short'] = array(
                    __('Jan', 'event_espresso'),
                    __('Feb', 'event_espresso'),
                    __('Mar', 'event_espresso'),
                    __('Apr', 'event_espresso'),
                    __('May', 'event_espresso'),
                    __('Jun', 'event_espresso'),
                    __('Jul', 'event_espresso'),
                    __('Aug', 'event_espresso'),
                    __('Sep', 'event_espresso'),
                    __('Oct', 'event_espresso'),
                    __('Nov', 'event_espresso'),
                    __('Dec', 'event_espresso')
            );
            $ee_calendar_js_options['day_names'] = array(
                    __('Sunday', 'event_espresso'),
                    __('Monday', 'event_espresso'),
                    __('Tuesday', 'event_espresso'),
                    __('Wednesday', 'event_espresso'),
                    __('Thursday', 'event_espresso'),
                    __('Friday', 'event_espresso'),
                    __('Saturday', 'event_espresso')
            );
            $ee_calendar_js_options['day_names_short'] = array(
                    __('Sun', 'event_espresso'),
                    __('Mon', 'event_espresso'),
                    __('Tue', 'event_espresso'),
                    __('Wed', 'event_espresso'),
                    __('Thu', 'event_espresso'),
                    __('Fri', 'event_espresso'),
                    __('Sat', 'event_espresso')
            );
            // featured image thumbnail settings
            $ee_calendar_js_options['thumbnail_size_w'] = get_option('thumbnail_size_w');
            $ee_calendar_js_options['thumbnail_size_h'] = get_option('thumbnail_size_h');
            // Get current page protocol
            $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
            // Output admin-ajax.php URL with same protocol as current page
            $ee_calendar_js_options['ajax_url'] = admin_url('admin-ajax.php', $protocol);
            $this->_js_options = $ee_calendar_js_options;
        }
        return $this->_js_options;
    }



    /**
     *    display_calendar
     *
     * @access public
     * @param array $ee_calendar_js_options
     * @param bool $localize_vars
     * @return string
     */
    public function display_calendar(array $ee_calendar_js_options, $localize_vars = true)
    {
        if ($localize_vars) {
            $this->get_calendar_js_options($ee_calendar_js_options);
            wp_localize_script('espresso_calendar', 'eeCAL', $this->_js_options);
        }
        $calendar_class = isset($this->_js_options['widget']) && $this->_js_options['widget']
                ? 'calendar_widget'
                : 'calendar_fullsize';
        $html = apply_filters('FHEE__EE_Calendar__display_calendar__before', '');
        $html .= apply_filters('FHEE__EE_Calendar__display_calendar__output_filter', $this->_output_filter);
        $html .= '
	<div id="espresso_calendar" class="' . $calendar_class . '"></div>
	<div style="clear:both;" ></div>
	<div id="espresso_calendar_images" ></div>';
        $html .= apply_filters('FHEE__EE_Calendar__display_calendar__after', '');
        if (// this is not an iframe
            ! EED_Espresso_Calendar::$iframe
            // and \EEH_Template::powered_by_event_espresso() is available
            && method_exists('EEH_Template', 'powered_by_event_espresso')
        ) {
            // add powered by EE attribution link
            $html .= \EEH_Template::powered_by_event_espresso(
                '',
                '',
                array('utm_content' => 'events_calendar')
            );
        }
        return $html;
    }



    /**
     *  get_calendar_events
     *
     *  @access     public
     *  @return     string
     */
    public function get_calendar_events()
    {
//  $this->timer->start();
        remove_shortcode('LISTATTENDEES');
        $month = date('m');
        $year = date('Y');
        $start_datetime = isset($_REQUEST['start_date'])
            ? date('Y-m-d H:i:s', absint($_REQUEST['start_date']))
            : date('Y-m-d H:i:s', mktime(0, 0, 0, $month, 1, $year));
        $end_datetime = isset($_REQUEST['end_date'])
            ? date('Y-m-d H:i:s', absint($_REQUEST['end_date']))
            : date('Y-m-t H:i:s', mktime(0, 0, 0, $month, 1, $year));
        $show_expired = isset($_REQUEST['show_expired'])
            ? sanitize_key($_REQUEST['show_expired'])
            : 'true';
        $venue_id_or_slug = isset($_REQUEST['event_venue_id']) && ! empty($_REQUEST['event_venue_id'])
            ? sanitize_key($_REQUEST['event_venue_id'])
            : null;

        $category_id_or_slug = isset($_REQUEST['event_category_id']) && ! empty($_REQUEST['event_category_id'])
            ? $_REQUEST['event_category_id']
            : $this->_event_category_id;

        if ($category_id_or_slug) {
            // Allow for multiple categories
            $category_id_or_slug = explode(',', $category_id_or_slug);
            foreach ($category_id_or_slug as $key => $value) {
                // sanitize all of the values
                $category_id_or_slug[ $key ] = sanitize_key($value);
            }

            // Set the category (or categories) within the query
            $where_params['OR*category'] = array(
                'Event.Term_Taxonomy.Term.slug'    => array( 'IN', $category_id_or_slug),
                'Event.Term_Taxonomy.Term.term_id' => array( 'IN', $category_id_or_slug)
            );

            // Single cateogry passed to the calendar?
            if (count($category_id_or_slug) == 1) {
                // Pull the category id or slug from the array
                $ee_term_id = $category_id_or_slug[0];

                // Check if we have an ID or a slug
                if (! is_int($ee_term_id)) {
                    // Not an int so must be the slug
                    $ee_term = get_term_by('slug', $ee_term_id, 'espresso_event_categories');
                    $ee_term_id = $ee_term instanceof WP_Term
                        ? $ee_term->term_id
                        : null;
                }

                // Check we have a term_id to use before adding to the where_params
                if ($ee_term_id) {
                    $where_params['OR*category']['Event.Term_Taxonomy.parent'] = $ee_term_id;
                }
            }
        }

        if ($venue_id_or_slug) {
            $where_params['OR*venue'] = array(
                'Event.Venue.VNU_ID'         => $venue_id_or_slug,
                'Event.Venue.VNU_identifier' => $venue_id_or_slug
            );
        }
        // setup start date and end date in a timestamp with the correct offset for the site.

        // this accounts for whether we're working with the new datetime paradigm introduce in EE 4.7 or not.
        $use_offset = ! method_exists('EEM_Datetime', 'current_time_for_query');
        $start_date = new DateTime("now");
        $start_date->setTimestamp(strtotime($start_datetime));
        $start_datetime = $use_offset
            ? (int) $start_date->format('U') + (int) ( get_option('gmt_offset') * HOUR_IN_SECONDS )
            : $start_date->format('U');

        $end_date = new DateTime("now");
        $end_date->setTimestamp(strtotime($end_datetime));
        $end_datetime = $use_offset
            ? (int) $end_date->format('U') + (int) ( get_option('gmt_offset') * HOUR_IN_SECONDS )
            : $end_date->format('U');

        $today = new DateTime(date('Y-m-d'));
        $today = $use_offset
            ? (int) $today->format('U') + (int) ( get_option('gmt_offset') * HOUR_IN_SECONDS )
            : $today->format('U');

        // EVENT STATUS
        // to remove specific event statuses from the just the calendar, create a filter in your functions.php file like the following:
//      function espresso_remove_sold_out_events_from_calendar( $public_event_stati ) {
//          unset( $public_event_stati[ EEM_Event::sold_out ] );
//          return $public_event_stati;
//      }
//      add_filter( 'AFEE__EED_Espresso_Calendar__get_calendar_events__public_event_stati', 'espresso_remove_sold_out_events_from_calendar', 10, 1 );
        // to remove Cancelled events from the entire frontend, copy the following filter to your functions.php file
        // add_filter( 'AFEE__EEM_Event__construct___custom_stati__cancelled__Public', '__return_false' );
        // to remove Postponed events from the entire frontend, copy the following filter to your functions.php file
        // add_filter( 'AFEE__EEM_Event__construct___custom_stati__postponed__Public', '__return_false' );
        // to remove Sold Out events from the entire frontend, copy the following filter to your functions.php file
        //  add_filter( 'AFEE__EEM_Event__construct___custom_stati__sold_out__Public', '__return_false' );

        // where post_status is public ( publish, cancelled, postponed, sold_out )
        if (method_exists(EEM_Event::instance(), 'public_event_stati')) {
            $public_event_stati = EEM_Event::instance()->public_event_stati();
        } else {
            $public_event_stati = get_post_stati(array( 'public' => true ));
            foreach (EEM_Event::instance()->get_custom_post_statuses() as $custom_post_status) {
                $public_event_stati[] = strtolower(str_replace(' ', '_', $custom_post_status));
            }
        }
        // Add private events if the current user can view them.
        if (EE_Registry::instance()->CAP->current_user_can('ee_read_private_events', 'get_events')) {
            $public_event_stati[] = EEM_Event::post_status_private;
        }
        $where_params['Event.status'] = array(
            'IN',
            apply_filters(
                'AFEE__EED_Espresso_Calendar__get_calendar_events__public_event_stati',
                $public_event_stati
            )
        );
        $where_params['DTT_EVT_start']= array('<=',$end_datetime);
        $where_params['DTT_EVT_end'] = array('>=',$start_datetime);
        if ($show_expired == 'false' || $show_expired == false) {
            $where_params['DTT_EVT_end*3'] = array('>=',$today );
            $where_params['Ticket.TKT_end_date'] = array('>=',$today);
        }
        $datetime_objs = EEM_Datetime::instance()->get_all(
            apply_filters(
                'FHEE__EED_Espresso_Calendar__get_calendar_events__query_params',
                array( $where_params, 'order_by'=>array( 'DTT_EVT_start'=>'ASC' ) ),
                $category_id_or_slug,
                $venue_id_or_slug,
                $public_event_stati,
                $start_date,
                $end_date,
                $show_expired
            )
        );
        /* @var $datetime_objs EE_Datetime[] */

        $datetime_objs = apply_filters(
            'FHEE__EED_Espresso_Calendar__get_calendar_events__query_results',
            $datetime_objs,
            $category_id_or_slug,
            $venue_id_or_slug,
            $public_event_stati,
            $start_date,
            $end_date,
            $show_expired
        );

        //  $this->timer->stop();
        //  echo $this->timer->get_elapse( __LINE__ );

        // in case there are any shortcodes in the event descriptions,
        // we need to temporarily swap out the global $post object
        // and replace it with the current event object
        // but don't worry, we'll reset the global $post when we are done
        global $post;
        $original_global_post = $post;
        $calendar_datetimes_for_json = array();
        foreach ($datetime_objs as $datetime) {
            if ($datetime instanceof EE_Datetime) {
                /* @var $datetime EE_Datetime */
                $calendar_datetime = apply_filters(
                    'FHEE__EED_Espresso_Calendar__get_calendar_events__new_datetime_object',
                    new EE_Datetime_In_Calendar($datetime),
                    $datetime
                );
                //  $this->timer->start();
                $event = $datetime->event();
                /* @var $event EE_Event */
                if (! $event instanceof EE_Event) {
                    EE_Error::add_error(
                        sprintf(
                            __("Datetime data for datetime with ID %d has no associated event!", "event_espresso"),
                            $datetime->ID()
                        ),
                        __FILE__,
                        __FUNCTION__,
                        __LINE__
                    );
                    continue;
                }
                // replace global $post
                $post = $event;
                // Check for password protected post content
                $pswrd_required = post_password_required($event->ID());
                // Get details about the category of the event
                if (! $this->config()->display->disable_categories) {
                     $categories= $event->get_all_event_categories();
                    // any term_taxonomies set for this event?
                    if ($categories) {
                        $primary_cat = reset($categories);
                        if ($primary_cat->get_extra_meta('use_color_picker', true, false)) {
                            $calendar_datetime->set_color($primary_cat->get_extra_meta('background_color', true, null));
                            $calendar_datetime->set_textColor($primary_cat->get_extra_meta('text_color', true, null));
                        }
                        $calendar_datetime->set_eventType($primary_cat->slug());
                        if ($this->config()->display->enable_cat_classes) {
                            foreach ($categories as $category) {
                                if ($category instanceof EE_Term) {
                                    $calendar_datetime->add_classname($category->slug());
                                }
                            }
                        } else {
                            $calendar_datetime->add_classname($primary_cat->slug());
                        }
                    }
                }

                if ($event->is_sold_out() ||
                    $datetime->sold_out() ||
                    $datetime->total_tickets_available_at_this_datetime() === 0
                ) {
                    $calendar_datetime->add_classname('sold-out');
                }

                if ($datetime->is_expired()) {
                    $calendar_datetime->add_classname('expired');
                }

                $startTime =  '<span class="event-start-time">' . $datetime->start_time($this->config()->time->format) . '</span>';
                $endTime = '<span class="event-end-time">' . $datetime->end_time($this->config()->time->format) . '</span>';

                if (! $pswrd_required && $startTime && $this->config()->time->show) {
                    $event_time_html = '<span class="time-display-block">' . $startTime;
                    $event_time_html .= $endTime ? ' - ' . $endTime : '';
                    $event_time_html .= '</span>';
                } else {
                    $event_time_html = false;
                }

                $event_time_html = apply_filters(
                    'FHEE__EE_Calendar__get_calendar_events__event_time_html',
                    $event_time_html,
                    $datetime,
                    $event
                );

                $calendar_datetime->set_event_time($event_time_html);

                // Add thumb to event
                if ($this->config()->display->enable_calendar_thumbs) {
                    $thumbnail_url = $event->feature_image_url('thumbnail');
                    if ($thumbnail_url) {
                        $calendar_datetime->set_event_img_thumb('
						<span class="thumb-wrap">
							<img id="ee-event-thumb-' . $datetime->ID() . '" class="ee-event-thumb" src="' . $thumbnail_url . '" alt="image of ' . $event->name() . '" />
						</span>');
                            $calendar_datetime->add_classname('event-has-thumb');
                    }
                }

                // $this->timer->stop();
                // echo $this->timer->get_elapse( __LINE__ );
                // $this->timer->start();
                if ($this->config()->tooltip->show) {
                    // Gets the description of the event. This can be used for hover effects such as jQuery Tooltips or QTip
                    $description = $event->short_description(55, null, true);
                    if (empty($description)) {
                        $description = $event->description_filtered();
                        // better more tag detection (stolen from WP core)
                        if (preg_match('/<!--more(.*?)?-->/', $description, $matches)) {
                            $description = explode($matches[0], $description, 2);
                            $description = array_shift($description);
                            $description = wp_strip_all_tags($description);
                        }
                        $description = do_shortcode($description);
                    } else {
                        $description = wp_strip_all_tags($description);
                    }
                    $description = ! $pswrd_required ? $description : '';
                    // and just in case it's still too long, or somebody forgot to use the more tag...
                    // if word count is set to 0, set no limit
                    $calendar_datetime->set_description($description);
                    // tooltip wrapper
                    $tooltip_html = '<div class="qtip_info">';
                    // show time ?
                    $tooltip_html .= ! $pswrd_required && $startTime && $this->config()->time->show
                        ? '<p class="time_cal_qtip">' . __('Event Time: ', 'event_espresso') . $startTime . ' - ' . $endTime . '</p>'
                        : '';

                    // add attendee limit if set
                    if (! $pswrd_required && $this->config()->display->show_attendee_limit) {
                        if ($datetime->total_tickets_available_at_this_datetime() == -1) {
                            $attendee_limit_text = __('Available Spaces: unlimited', 'event_espresso');
                        } else {
                            $attendee_limit_text = __('Registrations / Spaces: ', 'event_espresso') . $datetime->sold() . ' / ';
                            $attendee_limit_text .= apply_filters('FHEE__EE_Calendar__tooltip_datetime_available_spaces', $datetime->total_tickets_available_at_this_datetime(), $datetime);
                        }
                        $tooltip_html .= ' <p class="attendee_limit_qtip">' .$attendee_limit_text . '</p>';
                    }

                    // reg open
                    if ($event->is_sold_out()
                        || $datetime->sold_out()
                        || $datetime->total_tickets_available_at_this_datetime() === 0
                    ) {
                        $tooltip_reg_btn_html = '<div class="sold-out-dv">';
                        $tooltip_reg_btn_html .= esc_html__('Sold Out', 'event_espresso');
                        $tooltip_reg_btn_html .= '</div>';
                    } elseif ($event->is_cancelled()) {
                        $tooltip_reg_btn_html = '<div class="sold-out-dv">';
                        $tooltip_reg_btn_html .= esc_html__('Registration Closed', 'event_espresso');
                        $tooltip_reg_btn_html .= '</div>';
                    } else {
                        $tooltip_reg_btn_html = '<a class="reg-now-btn" href="';
                        $tooltip_reg_btn_html .= apply_filters(
                            'FHEE__EE_Calendar__tooltip_event_permalink',
                            $event->get_permalink(),
                            $event,
                            $datetime
                        );
                        $tooltip_reg_btn_html .= '">';
                        $tooltip_reg_btn_html .=  $event->display_ticket_selector() && ! $event->is_expired()
                            ? esc_html__('Register Now', 'event_espresso')
                            : esc_html__('View Details', 'event_espresso');
                        $tooltip_reg_btn_html .= '</a>';
                    }
                    // allow users to show a reg now button (or whatever they want) regardless of event status
                    $tooltip_html .= apply_filters(
                        'FHEE__EE_Calendar__get_calendar_events__tooltip_reg_btn_html',
                        $tooltip_reg_btn_html,
                        $event,
                        $datetime
                    );

                    $tooltip_html .= '<div class="clear"></div>';
                    $tooltip_html .= '</div>';
                    $calendar_datetime->set_tooltip($tooltip_html);
                    // Position my top left...
                    $calendar_datetime->set_tooltip_my(
                        $this->config()->tooltip->pos_my_1 . $this->config()->tooltip->pos_my_2
                    );
                    $calendar_datetime->set_tooltip_at(
                        $this->config()->tooltip->pos_at_1 . $this->config()->tooltip->pos_at_2
                    );
                    $calendar_datetime->set_tooltip_style($this->config()->tooltip->style . ' qtip-shadow');
                    $calendar_datetime->set_show_tooltips(true);
                } else {
                    $calendar_datetime->set_show_tooltips(false);
                }

                $calendar_datetime = apply_filters('FHEE__EE_Calendar__get_calendar_events__calendar_datetime', $calendar_datetime, $datetime);

                if ($calendar_datetime instanceof EE_Datetime_In_Calendar) {
                    $calendar_datetimes_for_json [] = $calendar_datetime->to_array_for_json();
                }

                //          $this->timer->stop();
                //          echo $this->timer->get_elapse( __LINE__ );

                // reset global post back to original
                $post = $original_global_post;
            }
        }
        echo json_encode($calendar_datetimes_for_json);
        die();
    }
}
