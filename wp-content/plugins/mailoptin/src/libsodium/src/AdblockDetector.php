<?php

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class AdblockDetector
{
    public $customizer_section_id = 'mo_wp_adblock_display_rule_section';

    public function __construct()
    {
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'adblock_section'), 10, 2);

        add_filter('mo_optin_form_customizer_output_settings', [$this, 'adblock_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'adblock_controls'), 10, 4);

        add_filter('mo_optin_js_config', [$this, 'adblock_js_config'], 10, 2);
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     * @return mixed
     */
    public function adblock_js_config($data, $abstractOptinFormClass)
    {
        $adblock_status = $abstractOptinFormClass->get_customizer_value('adblock_status');
        $adblock_settings = $abstractOptinFormClass->get_customizer_value('adblock_settings');

        if ($adblock_status === true && !empty($adblock_settings)) {
            $data['adblock_status'] = $adblock_status;
            $data['adblock_settings'] = $adblock_settings;
        }

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function adblock_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => __("Visitors Using Adblock", 'mailoptin'),
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
    public function adblock_settings($settings, $customizerSettings)
    {
        $customizer_defaults = (new AbstractCustomizer())->customizer_defaults;

        $settings['adblock_status'] = array(
            'default' => $customizer_defaults['adblock_status'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['adblock_settings'] = array(
            'default' => $customizer_defaults['adblock_settings'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['adblock_documentation'] = array(
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
    public function adblock_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $adblock_control_args = apply_filters(
            "mo_optin_form_customizer_adblock_controls",
            array(
                'adblock_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[adblock_status]',
                    apply_filters('mo_optin_form_customizer_adblock_status_args', array(
                            'label' => esc_html__('Activate Rule', 'mailoptin'),
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[adblock_status]',
                            'type' => 'light',// light, ios, flat
                            'description' => __('Adblock detection will not work if this rule is not activated.', 'mailoptin'),
                            'priority' => 10
                        )
                    )
                ),
                'adblock_settings' => apply_filters('mo_optin_form_customizer_adblock_settings_args', array(
                        'type' => 'select',
                        'choices' => [
                            'adblock_enabled' => __('Visitors with Adblock Enabled', 'mailoptin'),
                            'adblock_disabled' => __('Visitors with Adblock Disabled', 'mailoptin')
                        ],
                        'label' => __('Choose Adblock visitors to show to', 'mailoptin'),
                        'section' => $this->customizer_section_id,
                        'settings' => $option_prefix . '[adblock_settings]',
                        'priority' => 20,
                    )
                ),
                'adblock_documentation' => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[adblock_documentation]',
                    apply_filters('mo_optin_form_customizer_adblock_documentation_args', array(
                            'content' => sprintf('<div class="mo-optin-customizer-doc"><a href="%s" target="_blank">%s</a></div>',
                                'https://mailoptin.io/article/detect-adblock-users-wordpress/?utm_source=wp_dashboard&utm_medium=adblock_rule&utm_campaign=adblock_doc_link',
                                __('View Documentation', 'mailoptin')
                            ),
                            'no_wrapper_div' => true,
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[adblock_documentation]',
                            'priority' => 30,
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_adblock_controls_addition');

        foreach ($adblock_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_adblock_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return AdblockDetector|null
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