<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Ace_Editor_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerControls;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class AfterConversion
{
    public static $settings_section_title;

    public static function init()
    {
        self::$settings_section_title = __('Email Notification of New Lead Settings', 'mailoptin');
        add_filter('mo_optin_form_customizer_success_settings', [__CLASS__, 'after_conversion_settings'], 10, 4);
        add_filter('mo_optin_form_customizer_success_controls', [__CLASS__, 'after_conversion_controls'], 10, 4);

        add_filter('mailoptin_settings_optin_campaign_settings_page', [__CLASS__, 'af_email_notification_settings']);
        add_action('mailoptin_track_conversions', [__CLASS__, 'af_send_email_notification'], 10, 2);

        add_action('wp_cspa_header_before_box_content', [__CLASS__, 'meta_section_description'], 10, 2);
    }

    public static function meta_section_description($section_title, $option_name)
    {
        if ($section_title !== self::$settings_section_title || $option_name != MAILOPTIN_SETTINGS_DB_OPTION_NAME) return;

        printf(
            __('%sCustomize the content of the email sent whenever there is a new subscriber.%s', 'mailoptin'),
            '<p class="description">', '</p>'
        );

        printf(
            __('%sThe notification email is only sent when you set an admin address it will be delivered to in "After Conversion" section in the form builder. 
            %sLearn more%s', 'mailoptin'),
            '<p class="description">', '<a href="https://mailoptin.io/?p=19651" target="_blank">', '</a></p>'
        );
    }

    private static function af_default_email_body()
    {
        return sprintf(
            __("%s\n\n -- \n\nThis e-mail was sent by %s plugin on %s (%s)", 'mailoptin'), '[LEAD_DATA]', 'MailOptin', get_bloginfo('name'), site_url()
        );
    }

    private static function af_default_subject()
    {
        return sprintf(__('New MailOptin Lead via %s', 'mailoptin'), '[OPTIN_CAMPAIGN]');
    }

    public static function af_send_email_notification($lead_data, $optin_campaign_id)
    {
        $emails = trim(OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'email_notification'));

        if (empty($emails)) return;

        $optin_campaign_name = OptinCampaignsRepository::get_optin_campaign_name($optin_campaign_id);

        if (strpos($emails, ',') !== false) {
            $emails = array_map('sanitize_email', explode(',', $emails));
        }

        $subject = Settings::instance()->afems_email_subject();
        if (empty($subject)) {
            $subject = self::af_default_subject();
        }

        $message_template = Settings::instance()->afems_message_contents();
        if (empty($message_template)) {
            $message_template = self::af_default_email_body();
        }

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
        ];

        $message = '';

        if (is_array($lead_data)) {
            unset($lead_data['optin_campaign_id']);
            unset($lead_data['optin_campaign_type']);

            foreach ($lead_data as $key => $value) {

                if ('custom_fields' == $key) {
                    $data = json_decode($value, true);
                    if ( ! empty($data)) {
                        foreach ($data as $key2 => $value2) {
                            $message .= '<p>' . ucfirst($key2) . ': ' . $value2 . '</p>';
                        }
                    }

                    continue;
                }

                $key     = str_replace('_', ' ', $key);
                $message .= '<p>' . ucfirst($key) . ': ' . $value . '</p>';
            }
        }

        $subject          = str_replace('[OPTIN_CAMPAIGN]', $optin_campaign_name, $subject);
        $message_template = str_replace('[LEAD_DATA]', $message, $message_template);

        wp_mail($emails, $subject, $message_template, $headers);
    }

    public static function af_email_notification_settings($settings)
    {
        $settings[] = [
            'section_title'          => self::$settings_section_title,
            'afems_email_subject'    => [
                'type'  => 'text',
                'label' => __('Email Subject', 'mailoptin'),
                'value' => self::af_default_subject(),
            ],
            'afems_message_contents' => [
                'type'  => 'textarea',
                'rows'  => 10,
                'value' => self::af_default_email_body(),
                'label' => __('Email Message', 'mailoptin')
            ],
            'afems_shortcodes_help'  => [
                'type'        => 'custom_field_block',
                'data'        => "<strong>" . __('Shortcode Help Guide', 'mailoptin') . '</strong>',
                'description' => '<p>' . sprintf('%s : Name of optin campaign.', '<code style="color:#800;margin:0;padding:0;">[OPTIN_CAMPAIGN]</code>') . '</p>
<p>' . sprintf('%s : Data of new lead or subscriber.', '<code style="color:#800;margin:0;padding:0;">[LEAD_DATA]</code>') . '</p>',
            ]
        ];

        return $settings;
    }

    public static function after_conversion_settings($settings)
    {
        $settings['state_after_conversion'] = array(
            'default'   => (new AbstractCustomizer())->customizer_defaults['state_after_conversion'],
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['email_notification'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage'
        );

        return $settings;
    }

    /**
     * @param CustomizerControls $instance
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public static function after_conversion_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        if ( ! in_array($customizerClassInstance->optin_campaign_type, ['bar', 'lightbox', 'slidein'])) {
            $controls['state_after_conversion'] = apply_filters('mo_optin_form_customizer_state_after_conversion_args', array(
                    'type'        => 'select',
                    'label'       => __('State After Conversion', 'mailoptin'),
                    'section'     => $customizerClassInstance->success_section_id,
                    'settings'    => $option_prefix . '[state_after_conversion]',
                    'description' => sprintf(
                        __('Choose state of optin form to users who are already subscribed. %sLearn more%s', 'mailoptin'),
                        '<a target="_blank" href="https://mailoptin.io/article/state-after-conversion/">', '</a>'
                    ),
                    'choices'     => [
                        'success_message_shown' => __('Success Message Shown', 'mailoptin'),
                        'optin_form_hidden'     => __('Optin Form Hidden', 'mailoptin'),
                        'optin_form_shown'      => __('Optin Form Shown', 'mailoptin'),
                    ],
                    'priority'    => 25,
                )
            );
        }

        $controls['pass_lead_data_redirect_url'] = new WP_Customize_Toggle_Control(
            $wp_customize,
            $option_prefix . '[pass_lead_data_redirect_url]',
            apply_filters('mo_optin_form_customizer_pass_lead_data_redirect_url_args', array(
                    'label'       => __('Pass Lead Data to Redirect URL', 'mailoptin'),
                    'description' => __('Enabling this will add lead name and email address as a query parameter to the redirect URL you specified above.', 'mailoptin'),
                    'section'     => $customizerClassInstance->success_section_id,
                    'settings'    => $option_prefix . '[pass_lead_data_redirect_url]',
                    'type'        => 'light',
                    'priority'    => 22,
                )
            )
        );

        $controls['email_notification'] = apply_filters('mo_optin_form_customizer_email_notification_args', array(
                'type'        => 'text',
                'label'       => __('Email Notification of New Lead', 'mailoptin'),
                'section'     => $customizerClassInstance->success_section_id,
                'settings'    => $option_prefix . '[email_notification]',
                'input_attrs' => ['placeholder' => __('Enter Email Address', 'mailoptin')],
                'description' => sprintf(
                    __('Add multiple email address separated by a comma. Leave blank to disable. %sCustomize the email%s in "Optin Campaign" settings.', 'mailoptin'),
                    '<a target="_blank" href="' . MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE . '#optin_campaign_settings?afems_email_subject_row">', '</a>'
                ),
                'priority'    => 35,
            )
        );

        $controls['success_js_script'] = new WP_Customize_Ace_Editor_Control(
            $wp_customize,
            $option_prefix . '[success_js_script]',
            apply_filters('mo_optin_form_customizer_success_js_script_args', array(
                    'editor_id'   => 'success-js-script',
                    'label'       => __('Success Triggered Script', 'mailoptin'),
                    'description' => sprintf(
                        __('Enter conversion tracking, pixel code or whatever script that you want triggered when visitors subscribes to your campaign. %3$s Use %1$s[NAME]%2$s and %1$s[EMAIL]%2$s to pass in subscriber\'s name and email address', 'mailoptin'),
                        '<code>', '</code>', '<br><br>'),
                    'section'     => $customizerClassInstance->success_section_id,
                    'settings'    => $option_prefix . '[success_js_script]',
                    'language'    => 'html',
                    'priority'    => 50,
                )
            )
        );

        return $controls;
    }
}