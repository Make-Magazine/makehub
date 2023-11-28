<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class ContentLocker
{
    public function __construct()
    {
        add_filter('mo_optin_form_customizer_configuration_controls', array($this, 'controls'), 10, 4);

        add_filter('mo_optin_form_customizer_configuration_settings', [$this, 'control_settings']);

        add_filter('mo_optin_js_config', [$this, 'js_config'], 10, 2);
    }

    public function control_settings($settings)
    {
        $settings['inpost_content_locking_activate'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['inpost_content_locking_style'] = array(
            'default'   => 'obfuscation',
            'type'      => 'option',
            'transport' => 'postMessage',
        );
    
        $settings['inpost_content_locking_selector'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    /**
     * Add custom form css to email automation customizer.
     *
     * @param $controls
     * @param $wp_customize
     * @param $option_prefix
     * @param $customizerClassInstance
     *
     * @return mixed
     */
    public function controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        if ('inpost' == $customizerClassInstance->optin_campaign_type) {
            $controls['inpost_content_locking_activate'] = new WP_Customize_Toggle_Control(
                $wp_customize,
                $option_prefix . '[inpost_content_locking_activate]',
                apply_filters('mo_optin_form_customizer_inpost_content_locking_activate_args', array(
                        'label'    => __('Lock Content Below Optin', 'mailoptin'),
                        'section'  => $customizerClassInstance->configuration_section_id,
                        'settings' => $option_prefix . '[inpost_content_locking_activate]',
                        'type'     => 'light',
                        'priority' => 16,
                    )
                )
            );
            $controls['inpost_content_locking_style']    = apply_filters('mo_optin_form_customizer_inpost_content_locking_style_args', array(
                    'type'        => 'select',
                    'description' => esc_html__('Select how the content will be protected.', 'mailoptin'),
                    'choices'     => [
                        'obfuscation' => esc_html__('Obfuscation', 'mailoptin'),
                        'removal'     => esc_html__('Removal', 'mailoptin'),
                    ],
                    'label'       => esc_html__('Content Locking Style', 'mailoptin'),
                    'section'     => $customizerClassInstance->configuration_section_id,
                    'settings'    => $option_prefix . '[inpost_content_locking_style]',
                    'priority'    => 17,
                )
            );
            $controls['inpost_content_locking_selector']    = apply_filters('mo_optin_form_customizer_inpost_content_locking_selector_args', array(
                    'type'        => 'textarea',
                    'description' => esc_html__('Add comma separated list of css selectors', 'mailoptin'),
                    'label'       => esc_html__('Content Locking CSS Selectors', 'mailoptin'),
                    'section'     => $customizerClassInstance->configuration_section_id,
                    'settings'    => $option_prefix . '[inpost_content_locking_selector]',
                    'priority'    => 19,
                )
            );

        }

        return $controls;
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     *
     * @return mixed
     */
    public function js_config($data, $abstractOptinFormClass)
    {
        $content_lock_status = $abstractOptinFormClass->get_customizer_value('inpost_content_locking_activate', 'false');
        $content_lock_style  = $abstractOptinFormClass->get_customizer_value('inpost_content_locking_style', 'obfuscation');
        $content_lock_selector  = $abstractOptinFormClass->get_customizer_value('inpost_content_locking_selector', '');

        if ($content_lock_status === true && ! empty($content_lock_style)) {
            $data['content_lock_status'] = $content_lock_status;
            $data['content_lock_style']  = $content_lock_style;
            $data['content_lock_selector']  = $content_lock_selector;
        }

        return $data;
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