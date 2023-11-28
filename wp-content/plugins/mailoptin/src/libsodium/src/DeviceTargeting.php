<?php

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class DeviceTargeting
{
    public $customizer_section_id = 'mo_wp_device_targeting_display_rule_section';

    public function __construct()
    {
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'device_targeting_section'), 8, 2);

        add_filter('mo_optin_form_customizer_output_settings', [$this, 'device_targeting_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'device_targeting_controls'), 10, 4);

        add_filter('mo_optin_js_config', [$this, 'device_targeting_js_config'], 10, 2);
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     *
     * @return mixed
     */
    public function device_targeting_js_config($data, $abstractOptinFormClass)
    {
        $device_targeting_hide_desktop = $abstractOptinFormClass->get_customizer_value('device_targeting_hide_desktop', false);
        $device_targeting_hide_tablet  = $abstractOptinFormClass->get_customizer_value('device_targeting_hide_tablet', false);
        $device_targeting_hide_mobile  = $abstractOptinFormClass->get_customizer_value('device_targeting_hide_mobile', false);

        $data['device_targeting_hide_desktop'] = $device_targeting_hide_desktop;
        $data['device_targeting_hide_tablet']  = $device_targeting_hide_tablet;
        $data['device_targeting_hide_mobile']  = $device_targeting_hide_mobile;

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function device_targeting_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => __("Device Targeting", 'mailoptin'),
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
    public function device_targeting_settings($settings, $customizerSettings)
    {
        $settings['device_targeting_hide_mobile'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['device_targeting_hide_tablet'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['device_targeting_hide_desktop'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    /**
     * Click Launch display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function device_targeting_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $device_targeting_control_args = apply_filters(
            "mo_optin_form_customizer_device_targeting_controls",
            array(
                'device_targeting_hide_desktop' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[device_targeting_hide_desktop]',
                    apply_filters('mo_optin_form_customizer_device_targeting_hide_desktop_args', array(
                            'label'    => esc_html__('Hide on Desktop', 'mailoptin'),
                            'section'  => $this->customizer_section_id,
                            'settings' => $option_prefix . '[device_targeting_hide_desktop]',
                            'type'     => 'flat',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'device_targeting_hide_tablet'  => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[device_targeting_hide_tablet]',
                    apply_filters('mo_optin_form_customizer_device_targeting_hide_tablet_args', array(
                            'label'    => esc_html__('Hide on Tablet', 'mailoptin'),
                            'section'  => $this->customizer_section_id,
                            'settings' => $option_prefix . '[device_targeting_hide_tablet]',
                            'type'     => 'flat',// light, ios, flat
                            'priority' => 10
                        )
                    )
                ),
                'device_targeting_hide_mobile'  => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[device_targeting_hide_mobile]',
                    apply_filters('mo_optin_form_customizer_device_targeting_hide_mobile_args', array(
                            'label'    => esc_html__('Hide on Mobile', 'mailoptin'),
                            'section'  => $this->customizer_section_id,
                            'settings' => $option_prefix . '[device_targeting_hide_mobile]',
                            'type'     => 'flat',// light, ios, flat
                            'priority' => 10
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_device_targeting_controls_addition');

        foreach ($device_targeting_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_device_targeting_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return DeviceTargeting|null
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