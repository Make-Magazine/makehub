<?php

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Tagit_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class ReferrerDetection
{
    public $customizer_section_id = 'mo_wp_referrer_detection_display_rule_section';

    public function __construct()
    {
        add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
        add_action('mo_optin_after_page_user_targeting_display_rule_section', array($this, 'referrer_detection_section'), 10, 2);

        add_filter('mo_optin_form_customizer_output_settings', [$this, 'referrer_detection_customizer_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'referrer_detection_controls'), 10, 4);

        add_filter('mo_optin_js_config', [$this, 'referrer_detection_js_config'], 10, 2);
    }

    /**
     * @param array $data
     * @param AbstractOptinForm $abstractOptinFormClass
     * @return mixed
     */
    public function referrer_detection_js_config($data, $abstractOptinFormClass)
    {
        $referrer_detection_status = $abstractOptinFormClass->get_customizer_value('referrer_detection_status');
        $referrer_detection_settings = $abstractOptinFormClass->get_customizer_value('referrer_detection_settings');
        $referrer_detection_values = $abstractOptinFormClass->get_customizer_value('referrer_detection_values');

        if ($referrer_detection_status === true && !empty($referrer_detection_settings) && !empty($referrer_detection_values)) {
            $data['referrer_detection_status'] = $referrer_detection_status;
            $data['referrer_detection_settings'] = $referrer_detection_settings;
            $data['referrer_detection_values'] = $referrer_detection_values;
        }

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function referrer_detection_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => __("Referrer Detection", 'mailoptin'),
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
    public function referrer_detection_customizer_settings($settings, $customizerSettings)
    {
        $customizer_defaults = (new AbstractCustomizer())->customizer_defaults;

        $settings['referrer_detection_status'] = array(
            'default' => $customizer_defaults['referrer_detection_status'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['referrer_detection_settings'] = array(
            'default' => $customizer_defaults['referrer_detection_settings'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['referrer_detection_values'] = array(
            'default' => $customizer_defaults['referrer_detection_values'],
            'type' => 'option',
            'transport' => 'postMessage',
        );

        $settings['referrer_detection_documentation'] = array(
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
    public function referrer_detection_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $referrer_detection_control_args = apply_filters(
            "mo_optin_form_customizer_referrer_detection_controls",
            array(
                'referrer_detection_status' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[referrer_detection_status]',
                    apply_filters('mo_optin_form_customizer_referrer_detection_status_args', array(
                            'label' => esc_html__('Activate Rule', 'mailoptin'),
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[referrer_detection_status]',
                            'type' => 'light',// light, ios, flat
                            'description' => __('Referrer detection will not work if this rule is not activated', 'mailoptin'),
                            'priority' => 10
                        )
                    )
                ),
                'referrer_detection_settings' => apply_filters('mo_optin_form_customizer_referrer_detection_settings_args', array(
                        'type' => 'select',
                        'choices' => [
                            'show_to' => __('Show To', 'mailoptin'),
                            'hide_from' => __('Hide From', 'mailoptin')
                        ],
                        'label' => __('Display Type', 'mailoptin'),
                        'section' => $this->customizer_section_id,
                        'settings' => $option_prefix . '[referrer_detection_settings]',
                        'description' => __('Decide whether to hide from or show to visitor from URL specified in "Referrers".', 'mailoptin'),
                        'priority' => 20,
                    )
                ),
                'referrer_detection_values' => new WP_Customize_Tagit_Control(
                    $wp_customize,
                    $option_prefix . '[referrer_detection_values]',
                    apply_filters('mo_optin_form_customizer_referrer_detection_values_args', array(
                            'label' => esc_html__('Referrers', 'mailoptin'),
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[referrer_detection_values]',
                            'field_id' => 'mo-referrer-detection',// light, ios, flat
                            'options' => [
                                'placeholderText' => __('Type referrer address', 'mailoptin'),
                                'availableTags' => ["facebook.com", "twitter.com", "google.com", "bing.com", "pinterest.com", "yahoo.com", "youtube.com"],
                                'singleField' => true,
                                'showAutocompleteOnFocus' => true
                            ],
                            'description' => __('Type referrer URL followed by a comma or hit Enter key. Including "www.", "http://" or "https://" is not accepted.', 'mailoptin'),
                            'priority' => 30
                        )
                    )
                ),
                'referrer_detection_documentation' => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[referrer_detection_documentation]',
                    apply_filters('mo_optin_form_customizer_referrer_detection_documentation_args', array(
                            'content' => sprintf('<div class="mo-optin-customizer-doc"><a href="%s" target="_blank">%s</a></div>',
                                'https://mailoptin.io/article/referral-detection-visitors-targeting/?utm_source=wp_dashboard&utm_medium=referrer_detection&utm_campaign=referrer_detection_doc_link',
                                __('View Documentation', 'mailoptin')
                            ),
                            'no_wrapper_div' => true,
                            'section' => $this->customizer_section_id,
                            'settings' => $option_prefix . '[referrer_detection_documentation]',
                            'priority' => 30,
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_referrer_detection_controls_addition');

        foreach ($referrer_detection_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_referrer_detection_controls_addition');
    }

    /**
     * Singleton poop.
     *
     * @return ReferrerDetection|null
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