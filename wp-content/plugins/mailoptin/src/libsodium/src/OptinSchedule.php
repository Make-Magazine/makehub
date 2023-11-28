<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Single_Select_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Date_Picker_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;

class OptinSchedule
{
    public function __construct()
    {
        add_action('mo_optin_after_core_display_rules_section', array($this, 'add_section'), 10, 2);
        add_filter('mo_optin_form_customizer_output_settings', [$this, 'customize_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'add_controls'), 10, 4);
    }

    /**
     * @return array
     */
    public function timezone_list()
    {
        $timezones = ['visitors_local_time' => __("Visitor's Local Time", 'mailoptin')];
        $offsets   = [];
        $now       = new \DateTime('now', new \DateTimeZone('UTC'));

        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $now->setTimezone(new \DateTimeZone($timezone));
            $offsets[]                             = $offset = $now->getOffset();
            $formatted_offset                      = $this->format_offset($offset);
            $timezone_content                      = $this->timezone_continent($timezone);
            $timezones[$timezone_content][$offset] = $this->timezone_city($timezone) . ' (' . $formatted_offset . ')';
        }

        return $timezones;
    }

    protected function format_offset($offset)
    {
        $hours   = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));

        return sprintf('%+03d:%02d', $hours, $minutes);
    }

    protected function timezone_continent($val)
    {
        return substr($val, 0, strpos($val, '/'));
    }

    protected function timezone_city($val)
    {
        $val       = $this->format_timezone_name($val);
        $start_len = strpos($val, ' ');
        $total_len = strlen($val);

        return trim(substr($val, $start_len, $total_len));
    }

    protected function format_timezone_name($name)
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);

        return $name;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function add_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($customizerClassInstance->schedule_display_rule_section_id, array(
                'title'    => __("Schedule", 'mailoptin'),
                'panel'    => $customizerClassInstance->display_rules_panel_id,
                'priority' => 250
            )
        );
    }

    /**
     * @param array $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function customize_settings($settings, $customizerSettings)
    {
        $customizer_defaults = (new AbstractCustomizer())->customizer_defaults;

        $settings['schedule_status'] = array(
            'default'   => $customizer_defaults['schedule_status'],
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['schedule_start'] = array(
            'default'   => $customizer_defaults['schedule_start'],
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['schedule_end'] = array(
            'default'   => $customizer_defaults['schedule_end'],
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['schedule_timezone'] = array(
            'default'   => $customizer_defaults['schedule_timezone'],
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    /**
     * @param $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function add_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $schedule_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_schedule_controls",
            array(
                'schedule_status'   => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[schedule_status]',
                    apply_filters('mo_optin_form_customizer_schedule_status_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->schedule_display_rule_section_id,
                            'settings'    => $option_prefix . '[schedule_status]',
                            'description' => __('Schedule when the display of this optin will start and end.', 'mailoptin'),
                            'priority'    => 10
                        )
                    )
                ),
                'schedule_start'    => new WP_Customize_Date_Picker_Control(
                    $wp_customize,
                    $option_prefix . '[schedule_start]',
                    apply_filters('mo_optin_form_customizer_schedule_start_args', array(
                            'label'       => esc_html__('Start Time', 'mailoptin'),
                            'section'     => $customizerClassInstance->schedule_display_rule_section_id,
                            'settings'    => $option_prefix . '[schedule_start]',
                            'description' => __('Specify the date and time this optin campaign will start to display.', 'mailoptin'),
                            'priority'    => 20
                        )
                    )
                ),
                'schedule_end'      => new WP_Customize_Date_Picker_Control(
                    $wp_customize,
                    $option_prefix . '[schedule_end]',
                    apply_filters('mo_optin_form_customizer_schedule_end_args', array(
                            'label'       => esc_html__('End Time', 'mailoptin'),
                            'section'     => $customizerClassInstance->schedule_display_rule_section_id,
                            'settings'    => $option_prefix . '[schedule_end]',
                            'description' => __('Specify the date and time this optin campaign will stop to display.', 'mailoptin'),
                            'priority'    => 30
                        )
                    )
                ),
                'schedule_timezone' => new WP_Customize_Chosen_Single_Select_Control(
                    $wp_customize,
                    $option_prefix . '[schedule_timezone]',
                    apply_filters('mo_optin_form_customizer_schedule_timezone_args', array(
                            'label'       => __('Timezone', 'mailoptin'),
                            'section'     => $customizerClassInstance->schedule_display_rule_section_id,
                            'settings'    => $option_prefix . '[schedule_timezone]',
                            'description' => __('The timezone the start and end time will be based on.', 'mailoptin'),
                            'choices'     => $this->timezone_list(),
                            'attributes'  => ['placeholder_text_single' => __('Select Timezone', 'mailoptin')],
                            'priority'    => 40
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_schedule_controls_addition');

        foreach ($schedule_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_schedule_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return OptinSchedule
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}