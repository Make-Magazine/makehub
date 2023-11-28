<?php

namespace MailOptin\Libsodium\LeadBank;

use MailOptin\Core\Admin\SettingsPage\ConversionExport;
use MailOptin\Core\Core;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use MailOptin\Core\Repositories\OptinConversionsRepository as ConversionsRepository;

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Leads_List extends \WP_List_Table
{
    private $table;

    /** @var \wpdb */
    private $wpdb;

    /**
     * Class constructor
     */
    public function __construct($wpdb)
    {
        $this->wpdb  = $wpdb;
        $this->table = $this->wpdb->prefix . Core::conversions_table_name;
        parent::__construct(array(
            'singular' => __('conversion', 'mailoptin'), //singular name of the listed records
            'plural'   => __('conversions', 'mailoptin'), //plural name of the listed records
            'ajax'     => true //does this table support ajax?
        ));
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count()
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM $this->table";
        if ( ! empty($_POST['s'])) {
            $sql .= ' WHERE name LIKE %s';
            $sql .= ' OR email LIKE %s';

            $search = '%' . $wpdb->esc_like(sanitize_text_field($_POST['s'])) . '%';

            $sql = $wpdb->prepare($sql, $search, $search);
        }

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no conversion is available */
    public function no_items()
    {
        printf(
            __('No conversion has been recorded yet for any optin campaign. %sLearn more%s.', 'mailoptin'),
            '<a href="https://mailoptin.io/article/using-leadbank-to-capture-leads/" target="_blank">', '</a>');
    }

    public function view_profile_url($id)
    {
        return wp_nonce_url(
            add_query_arg(
                ['mailoptin' => 'view-lead-custom-field', 'id' => $id],
                admin_url()
            ),
            'mo-view-leadbank-subscriber'
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'             => '<input type="checkbox" />',
            'email'          => __('Subscriber Email', 'mailoptin'),
            'name'           => __('Subscriber Name', 'mailoptin'),
            'optin_campaign' => __('Source', 'mailoptin'),
            'date_added'     => __('Date & Time', 'mailoptin'),
            'view_profile'   => '',
        );

        return $columns;
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="conversions_ids[]" value="%s" />', $item['id']
        );
    }

    /**
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_view_profile($item)
    {
        return sprintf(
            '<a target="_blank" class="button mo-open-link-fancybox" href="%s">%s</a>',
            $this->view_profile_url(absint($item['id'])),
            __('View Details', 'mailoptin')
        );
    }

    /**
     * Column for optin campaign
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_optin_campaign($item)
    {
        $optin_id = absint($item['optin_id']);

        return $optin_id > 0 ? OptinCampaignsRepository::get_optin_campaign_name($optin_id) : esc_html($item['optin_type']);
    }

    public function column_email($item)
    {
        $id       = absint($item['id']);
        $view_url = $this->view_profile_url($id);

        $delete_href = add_query_arg(
            [
                'action'        => 'delete',
                'conversion-id' => $id,
                '_wpnonce'      => wp_create_nonce('mailoptin_delete_conversion')
            ],
            MAILOPTIN_LEAD_BANK_SETTINGS_PAGE
        );

        $actions = [
            'view'   => sprintf('<a class="mo-open-link-fancybox" href="%s" target="_blank">%s</a>', $view_url, __('View', 'mailoptin')),
            'delete' => sprintf('<a href="%s" class="mo-delete-prompt">%s</a>', $delete_href, __('Delete', 'mailoptin'))
        ];

        return sprintf('<a class="mo-open-link-fancybox" href="%s" target="_blank">%s</a>', $view_url, $item['email']) . $this->row_actions($actions);
    }

    /**
     * Column for conversion page
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_conversion_page($item)
    {
        $url = esc_url($item['conversion_page']);

        return "<a href='$url' target='_blank'>$url</a>";
    }

    /**
     * Column for referrer
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_referrer($item)
    {
        $url = esc_url($item['referrer']);

        return "<a href='$url' target='_blank'>$url</a>";
    }

    /**
     * Column for other conversion columns
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        if ($column_name == 'date_added') {
            // gmt is true because we save the lead bank time with user offset/timezone already.
            // in hindsight, we should have saved it as GMT
            return date_i18n('F d, Y g:i A', strtotime($item['date_added']), true);
        }

        return $item[$column_name];
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'bulk-delete'       => __('Delete', 'mailoptin'),
            'conversion-export' => __('Export', 'mailoptin'),
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();
        /** Process bulk action */
        $this->process_bulk_action();
        $per_page     = $this->get_items_per_page('conversions_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ));

        $this->items = ConversionsRepository::get_conversions($per_page, $current_page);
    }

    /**
     * Add export all leads button to before and after conversion list table.
     *
     * @param string $which
     */
    public function extra_tablenav($which)
    {
        $import_leads_url = add_query_arg('import', 'csv', MAILOPTIN_LEAD_BANK_SETTINGS_PAGE);
        // no need for <form> because in UI, there already exist
        ?>
        <a href="<?php echo $import_leads_url ?>" class="button button-secondary">
            <?php _e('Import Leads', 'mailoptin'); ?>
        </a>
        <?php wp_nonce_field('mo_export_all_leads', 'mo_export_all_leads'); ?>
        <?php echo '<span>' . get_submit_button(__('Export All Leads', 'mailoptin'), 'secondary', 'export_all_leads', false) . '</span>'; ?>
        <?php
    }


    /**
     * Process bulk action.
     */
    public function process_bulk_action()
    {
        // bail if user is not an admin or without admin privileges.
        if ( ! \MailOptin\Core\current_user_has_privilege()) return;

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if ( ! wp_verify_nonce($nonce, 'mailoptin_delete_conversion')) {
                wp_nonce_ays('mailoptin_delete_conversion');
            } else {
                ConversionsRepository::delete(absint($_GET['conversion-id']));
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_safe_redirect(esc_url_raw(MAILOPTIN_LEAD_BANK_SETTINGS_PAGE));
                exit;
            }
        }
        // If the delete bulk action is triggered
        if ('bulk-delete' === $this->current_action()) {

            check_admin_referer('bulk-' . $this->_args['plural']);

            $delete_ids = array_map('absint', $_POST['conversions_ids']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                ConversionsRepository::delete($id);
            }
            wp_safe_redirect(esc_url_raw(add_query_arg()));
            exit;
        }


        // export all optin conversions
        if ( ! empty($_POST['export_all_leads']) && $_POST['export_all_leads'] == __('Export All Leads', 'mailoptin')) {
            check_admin_referer('mo_export_all_leads', 'mo_export_all_leads');
            ConversionExport::get_instance()->all();
        } // If the conversion export bulk action is triggered
        elseif ('conversion-export' === $this->current_action()) {
            $conversions_ids = esc_sql($_POST['conversions_ids']);
            // loop over the array of record IDs and delete them
            ConversionExport::get_instance()->selected_ids($conversions_ids);
        }
    }

    public function display()
    {
        do_action('mo_leadbank_settings_page_before_listing');

        $singular = $this->_args['singular'];

        $this->display_tablenav('top');

        $this->screen->render_screen_reader_content('heading_list');
        ?>
        <table class="wp-list-table <?php echo implode(' ', $this->get_table_classes()); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"<?php
            if ($singular) {
                echo " data-wp-lists='list:$singular'";
            } ?>>
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers(false); ?>
            </tr>
            </tfoot>

        </table>
        <?php
        $this->display_tablenav('bottom');
    }

    /**
     * @return Leads_List
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self($GLOBALS['wpdb']);
        }

        return $instance;
    }
}