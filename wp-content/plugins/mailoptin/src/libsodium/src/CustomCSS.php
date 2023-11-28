<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Ace_Editor_Control;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class CustomCSS
{
    public function __construct()
    {
        add_filter('mo_optin_form_customizer_design_controls', array($this, 'optin_customizer_add_design_control'), 10, 4);

        add_filter('mailoptin_email_campaign_customizer_page_settings', [$this, 'email_automation_page_settings']);
        add_filter('mailoptin_template_customizer_page_controls', [$this, 'email_automation_add_page_control'], 10, 4);
    }

    public function email_automation_page_settings($settings)
    {
        $settings['custom_css'] = [
            'type' => 'option',
            'transport' => 'refresh',
        ];

        return $settings;
    }

    /**
     * Add custom form css to email automation customizer.
     *
     * @param $page_controls
     * @param $wp_customize
     * @param $option_prefix
     * @param $customizerClassInstance
     *
     * @return mixed
     */
    public function email_automation_add_page_control($page_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $page_controls['custom_css'] = new WP_Customize_Ace_Editor_Control(
            $wp_customize,
            $option_prefix . '[custom_css]',
            apply_filters('mailoptin_template_customizer_custom_css_args', array(
                    'editor_id' => 'custom-css',
                    'language' => 'css',
                    'type' => 'textarea',
                    'label' => __('Custom CSS', 'mailoptin'),
                    'section' => $customizerClassInstance->campaign_page_section_id,
                    'settings' => $option_prefix . '[custom_css]',
                    'description' => __('Add custom CSS to email template.', 'mailoptin'),
                    'priority' => 20
                )
            )
        );

        return $page_controls;
    }

    /**
     * Add custom form css to optin customizer.
     *
     * @param $design_controls
     * @param $wp_customize
     * @param $option_prefix
     * @param $customizerClassInstance
     *
     * @return mixed
     */
    public function optin_customizer_add_design_control($design_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $optin_uuid = OptinCampaignsRepository::get_optin_campaign_uuid($customizerClassInstance->optin_campaign_id);

        $design_controls['form_custom_css'] = new WP_Customize_Ace_Editor_Control(
            $wp_customize,
            $option_prefix . '[form_custom_css]',
            apply_filters('mo_optin_form_customizer_success_js_script_args', array(
                    'editor_id' => 'form-custom-css',
                    'language' => 'css',
                    'type' => 'textarea',
                    'label' => __('Custom CSS', 'mailoptin'),
                    'section' => $customizerClassInstance->design_section_id,
                    'settings' => $option_prefix . '[form_custom_css]',
                    'description' => sprintf(
                        __('Add custom CSS to further style this optin. Each of your custom CSS statements should be on its own line and be prefixed with: <strong>div#%s</strong>', 'mailoptin'),
                        $optin_uuid
                    ),
                    'priority' => 45,
                )
            )
        );

        return $design_controls;
    }

    /**
     * Singleton poop.
     *
     * @return CustomCSS|null
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