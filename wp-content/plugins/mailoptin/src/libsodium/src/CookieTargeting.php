<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class CookieTargeting
{
    public $customizer_section_id = 'mo_wp_cookie_targeting_display_rule_section';

    public function __construct()
    {
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'cookie_targeting_section'), 10, 2);

        add_filter('mo_optin_form_customizer_output_settings', [$this, 'cookie_targeting_customizer_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'cookie_targeting_controls'), 10, 4);

        add_filter('mo_optin_js_config', [$this, 'cookie_targeting_js_config'], 10, 2);
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     *
     * @return mixed
     */
    public function cookie_targeting_js_config($data, $abstractOptinFormClass)
    {
        $cookie_targeting_status   = $abstractOptinFormClass->get_customizer_value('cookie_targeting_status', false);
        $cookie_targeting_settings = $abstractOptinFormClass->get_customizer_value('cookie_targeting_settings', 'show');
        $cookie_targeting_name     = $abstractOptinFormClass->get_customizer_value('cookie_targeting_name');
        $cookie_targeting_value    = $abstractOptinFormClass->get_customizer_value('cookie_targeting_value');

        if ($cookie_targeting_status === true && ! empty($cookie_targeting_name)) {
            $data['cookie_targeting_status']   = $cookie_targeting_status;
            $data['cookie_targeting_settings'] = $cookie_targeting_settings;
            $data['cookie_targeting_name']     = $cookie_targeting_name;
            $data['cookie_targeting_value']    = $cookie_targeting_value;
        }

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function cookie_targeting_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => __("Cookie Targeting", 'mailoptin'),
                'panel' => $customizerClassInstance->display_rules_panel_id
            )
        );
    }

    public function active_sections($sections)
    {
        $sections[] = $this->customizer_section_id;

        return $sections;
    }

    /**
     * @param $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function cookie_targeting_customizer_settings($settings, $customizerSettings)
    {
        $settings['cookie_targeting_status'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['cookie_targeting_settings'] = array(
            'default'   => 'show',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['cookie_targeting_name'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['cookie_targeting_value'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function cookie_targeting_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $cookie_targeting_control_args = apply_filters(
            "mo_optin_form_customizer_cookie_targeting_controls",
            [
                'cookie_targeting_status'   => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[cookie_targeting_status]',
                    [
                        'label'       => esc_html__('Activate Rule', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[cookie_targeting_status]',
                        'type'        => 'light',// light, ios, flat
                        'description' => __('Cookie Targeting will not work if this rule is not activated', 'mailoptin'),
                        'priority'    => 10
                    ]
                ),
                'cookie_targeting_settings' => apply_filters('mo_optin_form_customizer_cookie_targeting_settings_args', [
                        'type'        => 'select',
                        'choices'     => [
                            'show' => __('Show Optin', 'mailoptin'),
                            'hide' => __('Hide Optin', 'mailoptin')
                        ],
                        'label'       => __('Display Action', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[cookie_targeting_settings]',
                        'description' => __('Decide whether to hide or show optin to users when the cookie is set.', 'mailoptin'),
                        'priority'    => 20,
                    ]
                ),
                'cookie_targeting_name'     => [
                    'type'        => 'text',
                    'label'       => __('Cookie Name', 'mailoptin'),
                    'section'     => $this->customizer_section_id,
                    'settings'    => $option_prefix . '[cookie_targeting_name]',
                    'priority'    => 30,
                    'description' => __('Specify the cookie name or key to target.', 'mailoptin')
                ],
                'cookie_targeting_value'    => [
                    'type'        => 'text',
                    'label'       => __('Cookie Value', 'mailoptin'),
                    'section'     => $this->customizer_section_id,
                    'settings'    => $option_prefix . '[cookie_targeting_value]',
                    'priority'    => 40,
                    'description' => __('Specify the value of the cookie to target.', 'mailoptin')
                ]
            ],
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_cookie_targeting_controls_addition');

        foreach ($cookie_targeting_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_cookie_targeting_controls_addition');
    }

    /**
     * @return self
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