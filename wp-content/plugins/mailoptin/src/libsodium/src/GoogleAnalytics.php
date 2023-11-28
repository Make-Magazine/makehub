<?php

namespace MailOptin\Libsodium;

// Exit if accessed directly
use MailOptin\Core\Admin\SettingsPage\AbstractSettingsPage;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\OptinForms\AbstractOptinForm;

if ( ! defined('ABSPATH')) {
    exit;
}

class GoogleAnalytics extends AbstractSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_connections_settings_page', [$this, 'ga_settings']);
        add_filter('mo_optin_js_config', [$this, 'js_config'], 10, 2);
    }

    public function ga_settings($arg)
    {
        if (apply_filters('mailoptin_enable_google_analytics', false)) {
            $settingsArg[] = apply_filters('mailoptin_google_analytics_settings_page', [
                'section_title' => __('Google Analytics', 'mailoptin'),
                'type'          => AbstractConnect::ANALYTICS_TYPE,
                'activate_ga'   => [
                    'type'        => 'checkbox',
                    'label'       => __('Event Tracking', 'mailoptin'),
                    'description' => sprintf(
                        __('Send optin campaigns\' impression and conversion data to Google Analytics. %sLearn more%s', 'mailoptin'),
                        '<a href="https://mailoptin.io/?p=19993" target="_blank">', '</a>'
                    )
                ]
            ]);

            return array_merge($arg, $settingsArg);
        }

        return $arg;
    }

    public function is_ga_activated()
    {
        $db_options = get_option(MAILOPTIN_CONNECTIONS_DB_OPTION_NAME);

        return ! empty($db_options['activate_ga']) && $db_options['activate_ga'] == 'true';
    }

    /**
     * @param $data
     * @param AbstractOptinForm $abstractOptinFormInstance
     *
     * @return mixed
     */
    public function js_config($data, $abstractOptinFormInstance)
    {
        if (apply_filters('mo_ga_integration_active', $this->is_ga_activated(), $abstractOptinFormInstance->optin_campaign_id, $abstractOptinFormInstance)) {
            $data['ga_active'] = true;
        }

        return $data;
    }

    /**
     * @return GoogleAnalytics
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