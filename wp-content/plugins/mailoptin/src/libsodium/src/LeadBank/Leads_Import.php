<?php

namespace MailOptin\Libsodium\LeadBank;

use MailOptin\Core\Admin\SettingsPage\AbstractSettingsPage;
use MailOptin\Core\Admin\SettingsPage\ConversionImport;

class Leads_Import extends AbstractSettingsPage
{
    public function __construct()
    {
        add_filter('mailoptin_settings_page', [$this, 'leadbank_import_leads_settings']);
        add_action('wp_cspa_form_tag', function () {
            echo 'enctype="multipart/form-data"';
        });
    }

    public function header()
    {
        ?>
        <div class="mo_csv_import-title-container">
            <h3 class="mo-leads-title">  <?php _e('Import Leads', 'mailoptin'); ?> </h3>
            <a href="<?php echo MAILOPTIN_LEAD_BANK_SETTINGS_PAGE ?>" class="button button-secondary">
                <?php _e('Back to Leads', 'mailoptin'); ?>
            </a>
        </div>
        <?php
    }

    /**
     * Handles data query and filter.
     */
    public function prepare_items()
    {
        $this->process_csv_upload();
    }

    /**
     * Process csv file upload
     * .
     */
    public function process_csv_upload()
    {
        // bail if user is not an admin or without admin privileges.
        if ( ! \MailOptin\Core\current_user_has_privilege()) return;

        // uploads leads
        if ( ! empty($_POST['upload_leads']) && $_POST['upload_leads'] == __('Upload Leads', 'mailoptin')) {

            check_admin_referer('mo_upload_leads', 'mo_upload_leads');

            $process_upload = ConversionImport::get_instance()->process_upload($_FILES['mo_csv_file']);

            //if it doesn't redirect, means there is an error hence show the error
            $this->admin_notices($process_upload);
        }

        if ( ! empty($_POST['map_leads']) && $_POST['map_leads'] == __('Start Importing', 'mailoptin')) {

            check_admin_referer('mo_map_leads', 'mo_map_leads');

            $import_leads = ConversionImport::get_instance()->import($_POST['mo_leads']);

            //if it doesn't redirect, means there is an error hence show the error
            $this->admin_notices($import_leads);
        }
    }


    public function display()
    {
        require dirname(__FILE__) . '/map-field-view.php';
    }

    public function admin_notices($message)
    {
        echo sprintf('<div class="notice notice-error"><p>%s</p></div>', esc_html($message));
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = [
            'name'            => __('Subscriber Name', 'mailoptin'),
            'email'           => __('Subscriber Email', 'mailoptin'),
            'custom_fields'   => __('Custom Field', 'mailoptin'),
            'user_agent'      => __('User Agent', 'mailoptin'),
            'conversion_page' => __('Conversion Page', 'mailoptin'),
            'referrer'        => __('Referrer', 'mailoptin'),
        ];

        return $columns;
    }


    /**
     * @return Leads_Import
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