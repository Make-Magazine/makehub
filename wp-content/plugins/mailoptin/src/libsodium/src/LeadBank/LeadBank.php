<?php

namespace MailOptin\Libsodium\LeadBank;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinConversionsRepository;

class LeadBank
{
    public function __construct()
    {
        $this->hook();
    }

    public function hook()
    {
        Leads::get_instance();
        add_filter('mo_optin_form_customizer_integration_controls', array($this, 'add_control'), 10, 4);
        add_filter('wp_privacy_personal_data_exporters', [$this, 'wp_export_data']);
        add_filter('wp_privacy_personal_data_erasers', [$this, 'wp_erase_data']);
    }

    public function wp_erase_data($erasers)
    {
        $erasers['mailoptin-leadbank'] = [
            'eraser_friendly_name' => __('MailOptin Leads', 'mailoptin'),
            'callback'             => [$this, 'annonymize_leadbank_data']
        ];

        return $erasers;
    }

    public function annonymize_leadbank_data($email_address)
    {
        if (empty($email_address)) {
            return [
                'items_removed'  => false,
                'items_retained' => false,
                'messages'       => [],
                'done'           => true,
            ];
        }

        $leads = OptinConversionsRepository::get_conversions(null, 1, $email_address);

        $items_removed  = false;
        $items_retained = false;

        if ( ! empty($leads) && is_array($leads)) {

            foreach ($leads as $lead) {

                $id = $lead['id'];

                if (apply_filters('mo_leadbank_delete_personal_data', false)) {
                    $result = OptinConversionsRepository::delete($id);
                } else {
                    $result = OptinConversionsRepository::update($id, [
                        'name'       => __('Anonymous', 'mailoptin'),
                        'email'      => wp_privacy_anonymize_data('email', $lead['email']),
                        'user_agent' => wp_privacy_anonymize_data('text', $lead['user_agent']),
                    ]);
                }

                if ($result) {
                    $items_removed = true;
                } else {
                    $items_retained = true;
                }
            }
        }

        return [
            'items_removed'  => $items_removed,
            'items_retained' => $items_retained,
            'messages'       => [],
            'done'           => true,
        ];
    }

    public function wp_export_data($exporters)
    {
        $exporters[] = array(
            'exporter_friendly_name' => __('MailOptin Leads', 'mailoptin'),
            'callback'               => function ($email_address) {
                $data_to_export = [];

                $leads = OptinConversionsRepository::get_conversions(null, 1, $email_address);

                if ( ! empty($leads) && is_array($leads)) {

                    foreach ($leads as $lead) {

                        $lead_data_to_export = [
                            [
                                'name'  => __('Name', 'mailoptin'),
                                'value' => $lead['name'],
                            ],
                            [
                                'name'  => __('Email', 'mailoptin'),
                                'value' => $lead['email'],
                            ],
                            [
                                'name'  => __('User Agent', 'mailoptin'),
                                'value' => $lead['user_agent'],
                            ],
                            [
                                'name'  => __('Date Added', 'mailoptin'),
                                'value' => $lead['date_added'],
                            ]
                        ];

                        $data_to_export[] = [
                            'group_id'    => 'leadbank',
                            'group_label' => __('MailOptin Leads', 'mailoptin'),
                            'item_id'     => "conversion-{$lead['id']}",
                            'data'        => $lead_data_to_export
                        ];
                    }
                }

                return [
                    'data' => $data_to_export,
                    'done' => true,
                ];
            }
        );

        return $exporters;
    }

    public static function is_leadbank_disabled()
    {
        $setting = Settings::instance()->disable_leadbank();

        return ! empty($setting) && $setting == 'true';
    }

    /**
     * Add custom form css to customizer.
     *
     * @param $wp_customize
     * @param $option_prefix
     * @param $customizerClassInstance
     *
     * @return mixed
     */
    public function add_control($integration_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        if (self::is_leadbank_disabled()) return $integration_controls;

        $integration_controls['lead_bank_only'] = new WP_Customize_Toggle_Control(
            $wp_customize,
            $option_prefix . '[lead_bank_only]',
            apply_filters('mo_optin_form_customizer_lead_bank_only_args', array(
                    'label'       => __('MailOptin Leads Only', 'mailoptin'),
                    'section'     => $customizerClassInstance->integration_section_id,
                    'settings'    => $option_prefix . '[lead_bank_only]',
                    'description' => sprintf(
                        __('%sLeads%s stores all subscribers and conversion data. Enable this setting if you would rather use only Leads and forget about connecting to an email marketing service.'),
                        '<a href="' . MAILOPTIN_LEAD_BANK_SETTINGS_PAGE . '" target="_blank">', '</a>'
                    ),
                    'priority'    => 10,
                    'type'        => 'light',
                )
            )
        );

        return $integration_controls;
    }

    /**
     * Singleton poop.
     *
     * @return LeadBank|null
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