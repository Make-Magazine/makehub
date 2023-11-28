<?php

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class NewVsReturningVisitors
{
    public $customizer_section_id = 'mo_wp_newvsreturn_display_rule_section';

    public function __construct()
    {
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'newvsreturn_section'), 10, 2);

        add_filter('mo_optin_form_customizer_output_settings', [$this, 'newvsreturn_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'newvsreturn_controls'), 10, 4);

        add_filter('mo_optin_js_config', [$this, 'newvsreturn_js_config'], 10, 2);
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     * @return mixed
     */
    public function newvsreturn_js_config($data, $abstractOptinFormClass)
    {
        $newvsreturn_status = $abstractOptinFormClass->get_customizer_value('newvsreturn_status');
        $newvsreturn_settings = $abstractOptinFormClass->get_customizer_value('newvsreturn_settings');

        if ($newvsreturn_status === true && !empty($newvsreturn_settings)) {
            $data['newvsreturn_status'] = $newvsreturn_status;
            $data['newvsreturn_settings'] = $newvsreturn_settings;
        }

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function newvsreturn_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => __("New vs Returning Visitors", 'mailoptin'),
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
     * @return mixed
     */
    public function newvsreturn_settings($settings, $customizerSettings)
    {
        $customizer_defaults = (new AbstractCustomizer())->customizer_defaults;

        $settings['newvsreturn_status'] = array(
            'default' => $customizer_defaults['newvsreturn_status'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['newvsreturn_settings'] = array(
            'default' => $customizer_defaults['newvsreturn_settings'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['newvsreturn_documentation'] = array(
            'type' => 'option',
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
    public function newvsreturn_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $newvsreturn_control_args = apply_filters(
            "mo_optin_form_customizer_newvsreturn_controls",
            array(
                'newvsreturn_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[newvsreturn_status]',
                    apply_filters('mo_optin_form_customizer_newvsreturn_status_args', array(
                            'label' => __('Activate Rule', 'mailoptin'),
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[newvsreturn_status]',
                            'type' => 'light',// light, ios, flat
                            'description' => __('Show only to new or returning visitors to your site.', 'mailoptin'),
                            'priority' => 10
                        )
                    )
                ),
                'newvsreturn_settings' => apply_filters('mo_optin_form_customizer_newvsreturn_settings_args', array(
                        'type' => 'select',
                        'choices' => [
                            'is_new' => __('New Visitors', 'mailoptin'),
                            'is_returning' => __('Returning Visitors', 'mailoptin')
                        ],
                        'label' => __('Choose who to show to', 'mailoptin'),
                        'section' => $this->customizer_section_id,
                        'settings' => $option_prefix . '[newvsreturn_settings]',
                        'priority' => 20,
                    )
                ),
                'newvsreturn_documentation' => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[newvsreturn_documentation]',
                    apply_filters('mo_optin_form_customizer_newvsreturn_documentation_args', array(
                            'content' => sprintf('<div class="mo-optin-customizer-doc"><a href="%s" target="_blank">%s</a></div>',
                                'https://mailoptin.io/article/target-new-returning-visitors-wordpress/?utm_source=wp_dashboard&utm_medium=newvsreturn_rule&utm_campaign=newvsreturn_doc_link',
                                __('View Documentation', 'mailoptin')
                            ),
                            'no_wrapper_div' => true,
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[newvsreturn_documentation]',
                            'priority' => 30,
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_newvsreturn_controls_addition');

        foreach ($newvsreturn_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_newvsreturn_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return NewVsReturningVisitors|null
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