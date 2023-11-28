<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Range_Value_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_X_Page_Views_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerControls;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class DisplayRules
{
    public function __construct()
    {
        add_filter('mo_optin_customizer_disable_upsell_section', '__return_true');
        add_filter('mo_email_customizer_disable_upsell_section', '__return_true');
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_filter('mo_optin_form_customizer_output_settings', [$this, 'customize_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'optin_triggers_controls'), 10, 4);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'optin_triggers_section'), 10, 2);
    }

    /**
     * @param $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function customize_settings($settings, $customizerSettings)
    {
        $optin_uuid = OptinCampaignsRepository::get_optin_campaign_uuid(
            $customizerSettings->optin_campaign_id
        );

        $click_me          = __('Click Me', 'mailoptin');
        $basic_shortcode   = "[mo-click-launch id=\"$optin_uuid\" link=\"$click_me!\"]";
        $advance_shortcode = "[mo-click-launch id=\"$optin_uuid\"]$click_me![/mo-click-launch]";
        $html_code         = "<a href=\"#\" class=\"mailoptin-click-trigger\" data-optin-uuid=\"$optin_uuid\">$click_me!</a>";

        $settings['click_launch_status'] = array(
            'default'   => apply_filters('mo_optin_form_click_launch_status', ''),
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['click_launch_basic_shortcode'] = array(
            'default'   => apply_filters('mo_optin_form_click_launch_basic_shortcode', $basic_shortcode),
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['click_launch_advance_shortcode'] = array(
            'default'   => apply_filters('mo_optin_form_click_launch_advance_shortcode', $advance_shortcode),
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['click_launch_html_code'] = array(
            'default'   => apply_filters('mo_optin_form_click_launch_html_code', $html_code),
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    public function active_sections($sections)
    {
        return $sections;
    }

    /**
     * Add optin triggers customizer sections
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function optin_triggers_section($wp_customize, $customizerClassInstance)
    {
        // hide this if optin type is either sidebar or inpost.
        if ( ! in_array($customizerClassInstance->optin_campaign_type, ['sidebar', 'inpost'])) {
            $wp_customize->add_section($customizerClassInstance->click_launch_display_rule_section_id, array(
                    'title' => __("Click Launch", 'mailoptin'),
                    'panel' => $customizerClassInstance->display_rules_panel_id
                )
            );

            $wp_customize->add_section($customizerClassInstance->exit_intent_display_rule_section_id, array(
                    'title' => __("Exit Intent", 'mailoptin'),
                    'panel' => $customizerClassInstance->display_rules_panel_id
                )
            );

            $wp_customize->add_section($customizerClassInstance->x_seconds_display_rule_section_id, array(
                    'title' => __("After 'X' seconds", 'mailoptin'),
                    'panel' => $customizerClassInstance->display_rules_panel_id
                )
            );

            $wp_customize->add_section($customizerClassInstance->x_scroll_display_rule_section_id, array(
                    'title' => __("After Scrolling Down 'X' Percent", 'mailoptin'),
                    'panel' => $customizerClassInstance->display_rules_panel_id
                )
            );
        }

        $wp_customize->add_section($customizerClassInstance->x_page_views_display_rule_section_id, array(
                'title' => __("Visitor has viewed 'X' pages", 'mailoptin'),
                'panel' => $customizerClassInstance->display_rules_panel_id
            )
        );
    }

    /**
     * Add optin triggers controls to customizer.
     *
     * @param CustomizerControls $instance
     * @param $wp_customize
     * @param $option_prefix
     * @param $customizerClassInstance
     */
    public function optin_triggers_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $this->click_launch_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance);
        $this->exit_intent_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance);
        $this->after_x_seconds_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance);
        $this->after_x_scroll_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance);
        $this->x_page_views_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance);
    }

    /**
     * Click Launch display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function click_launch_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $click_launch_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_click_launch_controls",
            array(
                'click_launch_status'            => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[click_launch_status]',
                    apply_filters('mo_optin_form_customizer_click_launch_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->click_launch_display_rule_section_id,
                            'settings'    => $option_prefix . '[click_launch_status]',
                            'type'        => 'light',// light, ios, flat
                            'description' => sprintf(
                                __('Show when a visitor clicks a link, button, image etc. %sNote:%s activating "Click Launch" will deactivate all other display rules. %sLearn more%s', 'mailoptin'),
                                '<strong>',
                                '</strong>',
                                '<a href="https://mailoptin.io/article/click-launch-optin-trigger/ " target="_blank">',
                                '</a>'
                            ),
                            'priority'    => 10
                        )
                    )
                ),
                'click_launch_basic_shortcode'   => array(
                    'type'        => 'text',
                    'input_attrs' => ['readonly' => 'readonly', 'class' => 'mo-click-select'],
                    'label'       => __('Shortcode', 'mailoptin'),
                    'section'     => $customizerClassInstance->click_launch_display_rule_section_id,
                    'settings'    => $option_prefix . '[click_launch_basic_shortcode]',
                    'description' => __('Copy/paste this shortcode to a post or page. Note: "Click Me!" is the link label or anchor text and you are free to change it.', 'mailoptin'),
                    'priority'    => 20
                ),
                'click_launch_advance_shortcode' => array(
                    'type'        => 'text',
                    'input_attrs' => ['readonly' => 'readonly', 'class' => 'mo-click-select'],
                    'label'       => __('Advance Shortcode', 'mailoptin'),
                    'section'     => $customizerClassInstance->click_launch_display_rule_section_id,
                    'settings'    => $option_prefix . '[click_launch_advance_shortcode]',
                    'description' => __('Note: you can replace "Click Me!" with any HTML button, image, text and even shortcodes.', 'mailoptin'),
                    'priority'    => 30
                ),
                'click_launch_html_code'         => array(
                    'type'        => 'text',
                    'input_attrs' => ['readonly' => 'readonly', 'class' => 'mo-click-select'],
                    'label'       => __('HTML Code', 'mailoptin'),
                    'section'     => $customizerClassInstance->click_launch_display_rule_section_id,
                    'settings'    => $option_prefix . '[click_launch_html_code]',
                    'description' => __('Copy/paste HTML link to use anywhere on your site.', 'mailoptin'),
                    'priority'    => 40
                ),
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_click_launch_controls_addition');

        foreach ($click_launch_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_click_launch_controls_addition');
    }

    /**
     * Exit intent display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function exit_intent_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $exit_intent_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_exit_intent_controls",
            array(
                'exit_intent_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[exit_intent_status]',
                    apply_filters('mo_optin_form_customizer_exit_intent_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->exit_intent_display_rule_section_id,
                            'settings'    => $option_prefix . '[exit_intent_status]',
                            'type'        => 'light',// light, ios, flat
                            'description' => __('Show when a visitor tries to leave your site.', 'mailoptin')
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_exit_intent_controls_addition');

        foreach ($exit_intent_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_exit_intent_controls_addition');
    }

    /**
     * After x seconds display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function after_x_seconds_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $after_x_seconds_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_after_x_seconds_controls",
            array(
                'x_seconds_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[x_seconds_status]',
                    apply_filters('mo_optin_form_customizer_x_seconds_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->x_seconds_display_rule_section_id,
                            'settings'    => $option_prefix . '[x_seconds_status]',
                            'type'        => 'light',// light, ios, flat
                            'description' => __('Number of seconds after page-load before optin form will display.', 'mailoptin')
                        )
                    )
                ),
                'x_seconds_value'  => new WP_Customize_Range_Value_Control(
                    $wp_customize,
                    $option_prefix . '[x_seconds_value]',
                    apply_filters('mo_optin_form_customizer_x_seconds_value_args', array(
                            'section'     => $customizerClassInstance->x_seconds_display_rule_section_id,
                            'settings'    => $option_prefix . '[x_seconds_value]',
                            'label'       => __('Show after number of seconds is', 'mailoptin'),
                            'input_attrs' => array(
                                'min'    => 1,
                                'max'    => 100,
                                'step'   => 1,
                                'suffix' => 'sec', //optional suffix
                            ),
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_x_seconds_controls_addition');

        foreach ($after_x_seconds_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_x_seconds_controls_addition');
    }

    /**
     * Page views display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function x_page_views_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $x_page_views_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_x_page_views_controls",
            array(
                'x_page_views_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[x_page_views_status]',
                    apply_filters('mo_optin_form_customizer_x_page_views_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->x_page_views_display_rule_section_id,
                            'settings'    => $option_prefix . '[x_page_views_status]',
                            'type'        => 'light',// light, ios, flat
                            'description' => __('Show when a visitor has viewed certain number of pages on your site.', 'mailoptin')
                        )
                    )
                ),
                'x_page_views'        => new WP_Customize_X_Page_Views_Control(
                    $wp_customize,
                    $option_prefix . '[x_page_views]',
                    apply_filters('mo_optin_form_customizer_x_page_views_args', array(
                            'label'          => __("Show when the visitor has page views of", 'mailoptin'),
                            'section'        => $customizerClassInstance->x_page_views_display_rule_section_id,
                            'settings'       => [
                                'x_page_views_condition' => $option_prefix . '[x_page_views_condition]',
                                'x_page_views_value'     => $option_prefix . '[x_page_views_value]'
                            ],
                            // specify the kind of input field
                            'type'           => 'number',
                            'input_attrs'    => ['size' => 2, 'maxlength' => 2, 'style' => 'width:20%'],
                            'select_attrs'   => ['style' => 'width:auto'],
                            'select_choices' => [
                                '...'           => __('Select...', 'mailoptin'),
                                'equals'        => __('Equals', 'mailoptin'),
                                'more_than'     => __('More than', 'mailoptin'),
                                'less_than'     => __('Less than', 'mailoptin'),
                                'at_least'      => __('At least', 'mailoptin'),
                                'not_more_than' => __('Not more than', 'mailoptin'),
                            ]
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_x_page_views_controls_addition');

        foreach ($x_page_views_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_x_page_views_controls_addition');
    }

    /**
     * After x scroll percentage.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function after_x_scroll_display_rule_controls($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $x_scroll_display_rule_control_args = apply_filters(
            "mo_optin_form_customizer_x_scroll_controls",
            array(
                'x_scroll_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[x_scroll_status]',
                    apply_filters('mo_optin_form_customizer_x_scroll_args', array(
                            'label'       => esc_html__('Activate Rule', 'mailoptin'),
                            'section'     => $customizerClassInstance->x_scroll_display_rule_section_id,
                            'settings'    => $option_prefix . '[x_scroll_status]',
                            'type'        => 'light',// light, ios, flat
                            'description' => __('Show after a visitor has scrolled a certain percentage.', 'mailoptin')
                        )
                    )
                ),
                'x_scroll_value'  => new WP_Customize_Range_Value_Control(
                    $wp_customize,
                    $option_prefix . '[x_scroll_value]',
                    apply_filters('mo_optin_form_customizer_x_scroll_value_args', array(
                            'section'     => $customizerClassInstance->x_scroll_display_rule_section_id,
                            'settings'    => $option_prefix . '[x_scroll_value]',
                            'label'       => __('Show optin after scroll % is at least', 'mailoptin'),
                            'input_attrs' => array(
                                'min'    => 1,
                                'max'    => 100,
                                'step'   => 1,
                                'suffix' => '%', //optional suffix
                            ),
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_x_scroll_controls_addition');

        foreach ($x_scroll_display_rule_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_x_scroll_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return DisplayRules|null
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