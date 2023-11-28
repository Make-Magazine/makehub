<?php

namespace MailOptin\Libsodium\LeadBank;

// Exit if accessed directly
use MailOptin\Core\Admin\SettingsPage\AbstractSettingsPage;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use MailOptin\Core\Repositories\OptinConversionsRepository;
use W3Guy\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

class Leads extends AbstractSettingsPage
{
    /**
     * @var Leads_List
     */
    protected $conversions_instance;

    /**
     * @var Leads_Import
     */
    protected $csv_import_init;

    public function __construct()
    {
        add_action('mailoptin_leadbank_settings_page', [$this, 'init']);

        add_filter('mailoptin_settings_page', [$this, 'leadbank_settings']);

        add_action('admin_init', [$this, 'view_leadbank_subscriber_profile']);

        add_action('mailoptin_leadbank_settings_page_screen_option', function () {
            $this->csv_import_init = Leads_Import::get_instance();
        });

        add_action('mailoptin_leadbank_settings_page_screen_option', function () {
            $this->conversions_instance = Leads_List::get_instance();
        });

        add_filter('set-screen-option', array($this, 'set_screen'), 10, 3);
        add_filter('set_screen_option_conversions_per_page', array($this, 'set_screen'), 10, 3);
    }

    public function view_leadbank_subscriber_profile()
    {
        if ( ! \MailOptin\Core\current_user_has_privilege()) return;

        if (isset($_GET['mailoptin']) && isset($_GET['id']) && 'view-lead-custom-field' == $_GET['mailoptin']) {

            check_admin_referer('mo-view-leadbank-subscriber');

            $id   = absint($_GET['id']);
            $data = OptinConversionsRepository::get($id);

            $optin_id                          = absint($data['optin_id']);
            $data[__('Campaign', 'mailoptin')] = $optin_id > 0 ? OptinCampaignsRepository::get_optin_campaign_name($optin_id) : esc_html($data['optin_type']);

            unset($data['id']);
            unset($data['optin_id']);
            unset($data['optin_type']);

            if (isset($data['custom_fields'])) {
                $custom_fields = json_decode($data['custom_fields'], true);
                unset($data['custom_fields']);

                if (is_array($custom_fields)) {
                    foreach ($custom_fields as $key => $value) {
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $data[$key] = $value;
                    }
                }
            }

            $output = '<style>';
            $output .= 'table, td, th {  border: 1px solid #ddd;text-align: left;} table {border-collapse: collapse;width: 100%;} th, td { padding: 10px;}';
            $output .= '.firstHalf {width: 22%;padding: 15px;}body#error-page {max-width: 900px;padding: 1em;margin:5px}';
            $output .= 'table>tbody>:nth-child(odd) {background-color: #f9f9f9} #error-page p { margin: 0;}';
            $output .= '</style>';
            $output .= '<table><tbody>';
            foreach ($data as $key => $value) {
                $key    = str_replace('_', ' ', $key);
                $output .= '<tr>';
                $output .= sprintf('<td class="firstHalf">%s</td> <td>%s</td>', ucwords($key), $value);
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';


            wp_die($output, __('Subscribers - Leads', 'mailoptin'), ['response' => 200]);
            exit;
        }
    }

    public function init()
    {
        if (isset($_GET['import']) && $_GET['import'] == 'csv') {
            add_filter('wp_cspa_main_content_area', array($this, 'import_leads_view'), 10, 2);
        } else {
            add_filter('wp_cspa_main_content_area', array($this, 'wp_list_table'), 10, 2);
        }
    }

    public function leadbank_settings($settings)
    {
        $settings['leadbank_settings'] = apply_filters('mailoptin_settings_leadbank_settings_page', [
            'tab_title' => __('Leads (Submissions)', 'mailoptin'),
            [
                'section_title'    => __('Settings', 'mailoptin'),
                'disable_leadbank' => [
                    'type'           => 'checkbox',
                    'checkbox_label' => __('Disable', 'mailoptin'),
                    'label'          => __('Disable Leads', 'mailoptin'),
                    'description'    => sprintf(
                                            __('Check this box if you would like to disable Leads from saving subscribers.%s', 'mailoptin'),
                                            '</p><p class="description">'
                                        ) . sprintf(
                                            __('%sLeads%s are all subscribers to your optin campaigns.%s', 'mailoptin'),
                                            '<a href="' . MAILOPTIN_LEAD_BANK_SETTINGS_PAGE . '">', '</a>', '</p>'
                                        ),
                ]
            ]
        ]);

        return $settings;
    }

    /**
     * Save screen option.
     *
     * @param string $status
     * @param string $option
     * @param string $value
     *
     * @return mixed
     */
    public function set_screen($status, $option, $value)
    {
        if ('conversions_per_page' == $option) {
            return $value;
        }

        return $status;
    }

    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page_callback()
    {
        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('mo_leads');
        $instance->page_header(__('Leads', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true);
    }

    public function leadbank_disabled_notice()
    {
        if (LeadBank::is_leadbank_disabled()) {
            echo '<div style="border-left: 4px solid #dc3232;background: #fff;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);margin: 0 0 10px;padding: 10px">';
            printf(
                __('%sLeads is currently disabled%s. New subscribers will not be recorded or saved. Go to %sLeads settings%s to reactivate.', 'mailoptin'),
                '<strong>', '</strong>', '<a href="' . MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE . '">', '</a>'
            );
            echo '</div>';
        }
    }

    /**
     * Callback to output content of the import template
     *
     * @param string $content
     * @param string $option_name settings Custom_Settings_Page_Api option name.
     *
     * @return string
     */
    public function import_leads_view($content, $option_name)
    {
        if ($option_name != 'mo_leads') {
            return $content;
        }

        $this->csv_import_init->prepare_items();

        ob_start();
        ?>
        <div id="mo_csv_import">
            <?php
            $this->csv_import_init->header();
            $this->csv_import_init->display();
            ?>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Callback to output content of Email_Template_List table.
     *
     * @param string $content
     * @param string $option_name settings Custom_Settings_Page_Api option name.
     *
     * @return string
     */
    public function wp_list_table($content, $option_name)
    {
        if ($option_name != 'mo_leads') {
            return $content;
        }
        $this->conversions_instance->prepare_items();
        ob_start();
        $this->leadbank_disabled_notice();
        echo '<form method="post">';
        $this->conversions_instance->search_box(__('Search Leads', 'mailoptin'), 'leadbank_search');
        $this->conversions_instance->display();
        echo '</form>';

        return ob_get_clean();
    }


    /**
     * @return Leads
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