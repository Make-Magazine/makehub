<?php
/**
 *  EEW_Espresso_Calendar
 * Displays a month-based espresso_calendar in the sidebar
 *
 * @package     Event Espresso
 * @subpackage  espresso-calendar
 * @author      Chris Reynolds
 * @since 2.0
 */
class EEW_Espresso_Calendar extends WP_Widget
{

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        // run calendar module
        add_action('init', array( $this, 'init' ));
        // construct widget
        parent::__construct(
            'ee-calendar-widget',
            __('Event Espresso Calendar Widget', 'event_espresso'),
            array(
                'description' => __('Displays the Espresso Calendar in a widget.', 'event_espresso')
             ),
            array(
                'width' => 300,
                'height' => 350,
                'id_base' => 'ee-calendar-widget',
                'classname' => 'ee-calendar-widget'
            )
        );
    }



    /**
     * parse_request
     */
    public function init()
    {
        if (is_active_widget(false, false, $this->id_base, true)) {
            global $wp;
            EED_Espresso_Calendar::instance()->run($wp);
        }
    }



    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return string|void
     */
    public function form($instance)
    {

        EE_Registry::instance()->load_helper('Form_Fields');
        EE_Registry::instance()->load_class('Question_Option', array(), false, false, true);

        // Set up some default widget settings.
        $defaults = array(
            'title' => 'Calendar',
            'show_expired' => false,
            'category_id' => '',
            'calendar_page' => ''
        );

        $instance = wp_parse_args((array) $instance, $defaults);

        add_filter('FHEE__EEH_Form_Fields__label_html', '__return_empty_string');
        $yes_no_values = array(
            EE_Question_Option::new_instance(array( 'QSO_value' => 0, 'QSO_desc' => __('No', 'event_espresso'))),
            EE_Question_Option::new_instance(array( 'QSO_value' => 1, 'QSO_desc' => __('Yes', 'event_espresso')))
        );

        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', 'event_espresso'); ?>
            </label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" width="20" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show_expired'); ?>">
                <?php _e('Display Expired Events?', 'event_espresso'); ?>
            </label>
            <?php
                echo EEH_Form_Fields::select(
                    __('Display Expired Events?', 'event_espresso'),
                    $instance['show_expired'],
                    $yes_no_values,
                    $this->get_field_name('show_expired'),
                    $this->get_field_id('show_expired')
                );
            ?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category_id'); ?>">
                <?php _e('Single Category Name (optional)', 'event_espresso'); ?>
            </label>
            <input type="text" id="<?php echo $this->get_field_id('category_id'); ?>" name="<?php echo $this->get_field_name('category_id'); ?>" width="20" value="<?php echo $instance['category_id']; ?>" /><br/>
            <span class="small-text">
            <?php
                printf(
                    __('Enter the Category Slug from the %sEvent Categories%s page', 'event_espresso'),
                    '<a href="' . admin_url('admin.php?page=espresso_events&action=category_list') . '" target="_blank">',
                    '</a>'
                );
            ?>
            </span>
        </p>

        <?php
    }



    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $instance)
    {
        // Strip tags (if needed) and update the widget settings.
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['show_expired'] = strip_tags($new_instance['show_expired']);
        $instance['category_id'] = strip_tags($new_instance['category_id']);
        return $instance;
    }



    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        extract($args);
        // get the current post
        global $post, $is_espresso_calendar;
        if (isset($post->post_content)) {
            if (strpos($post->post_content, '[ESPRESSO_CALENDAR') === false) {
                $is_espresso_calendar = true;
                // load scripts
                wp_enqueue_style('fullcalendar');
                wp_enqueue_style('espresso_calendar');
                wp_enqueue_script('espresso_calendar');
                // Before widget (defined by themes).
                /** @var $before_widget string */
                echo $before_widget;
                // Title of widget (before and after defined by themes).
                if (! empty($instance['title'])) {
                    $title = apply_filters('widget_title', $instance['title']);
                    if (! empty($title)) {
                        /** @var $before_title string */
                        /** @var $after_title string */
                        echo $before_title . $title . $after_title;
                    }
                }
                $category_id = '';
                if (! empty($instance['category_id'])) {
                    $category_id = $instance['category_id'];
                }
                $show_expired = false;
                if (! empty($instance['show_expired'])) {
                    $show_expired = $instance['show_expired'];
                }
                // settings
                $attributes = array(
                   'event_category_id' => $category_id,
                   'show_expired' => $show_expired,
                   'cal_view' => 'month',
                   'header_left' => 'prev',
                   'header_center' => 'title',
                   'header_right' =>'next',
                   'title_format_month' => 'MMM yyyy',
                   'widget' => true,
                   'month' => apply_filters('FHEE__EE_Calendar__widget_start_month', date('n')),
                   'year' => apply_filters('FHEE__EE_Calendar__widget_start_year', date('Y')),
                );
                if (! EE_Registry::instance()->modules->EED_Espresso_Calendar instanceof EED_Espresso_Calendar) {
                    EE_Registry::instance()->modules->EED_Espresso_Calendar = new EED_Espresso_Calendar();
                }
                echo EE_Registry::instance()->modules->EED_Espresso_Calendar->display_calendar($attributes);
                // After widget (defined by themes).
                /** @var $after_widget string */
                echo $after_widget;
            }
        }
    }
}

