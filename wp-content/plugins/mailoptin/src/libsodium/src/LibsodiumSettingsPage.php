<?php

namespace MailOptin\Libsodium;

use PAnD as PAnD;

class LibsodiumSettingsPage
{
    private static $license_key;
    private static $license_page_url;

    public function __construct()
    {
        self::$license_key = self::license_key();

        self::$license_page_url = MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE . '#license_settings';

        // unavailability of this class could potentially break all ajax requests.
        if (class_exists('MailOptin\Libsodium\LicenseControl')) {
            add_action('init', [__CLASS__, 'plugin_updater'], 0);
            add_action('admin_init', [__CLASS__, 'plugin_check_license'], 0);
        }

        add_action('mailoptin_admin_notices', function () {
            add_action('admin_notices', [__CLASS__, 'license_not_active_notice']);
            add_action('admin_notices', [__CLASS__, 'license_expired_notice']);
        });

        add_filter('mailoptin_settings_page', [$this, 'license_settings_tab'], 999999999);

        add_action('admin_init', [$this, 'handle_submission']);
    }

    public function handle_submission()
    {
        // listen for our activate button to be clicked
        if (isset($_POST['mo_activate_license'])) {
            self::activate_license();
        } elseif (isset($_POST['mo_deactivate_license'])) {
            self::deactivate_license();
        } // listen for our activate button to be clicked
        elseif (isset($_POST['mo_save_license'])) {
            self::save_license_key();
        }
    }

    public function license_settings_tab($settings)
    {
        ob_start();
        self::license_page();
        $page = ob_get_clean();

        $settings['license_settings'] = [
            'tab_title' => __('License', 'mailoptin'),
            [
                'section_title'         => __('Activate License Key', 'mailoptin'),
                'disable_leadbank'      => [
                    'type' => 'arbitrary',
                    'data' => $page
                ],
                'disable_submit_button' => true
            ]
        ];

        return $settings;
    }

    public static function license_key()
    {
        return trim(get_option('mo_license_key'));
    }

    /**
     * Return license price ID
     *
     * @return bool|string
     */
    public static function mo_price_id()
    {
        return get_option('mo_price_id', false);
    }

    /**
     * Is plugin license valid?
     *
     * @return bool
     */
    public static function mo_is_license_valid()
    {
        $license = get_option('mo_license_status');

        return $license == 'valid' ? true : false;
    }

    /**
     * Is plugin license invalid?
     * @return bool
     */
    public static function mo_is_license_invalid()
    {
        $license = get_option('mo_license_status');

        return $license == 'invalid';
    }

    /**
     * Is plugin license expired?
     *
     * @return bool
     */
    public static function mo_is_license_expired()
    {
        $license = get_option('mo_license_expired_status', 'false');

        return $license == 'true';
    }

    /**
     * Is license empty?
     *
     * @return bool
     */
    public static function mo_is_license_empty()
    {
        $license = self::license_key();
        if (false == $license || empty($license)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Was license once active?
     */
    public static function mo_once_active()
    {
        return get_option('mo_license_once_active', false) == 'true' ? true : false;
    }

    /**
     * @return LicenseControl
     */
    public static function license_control_instance()
    {
        return LicenseControl::get_instance(self::$license_key);
    }

    /**
     * Check if the plugin license is active
     *
     * @param bool $force if true, re-validate license.
     */
    public static function plugin_check_license($force = false)
    {
        if (defined('DOING_AJAX')) return;

        // only check license if transient doesn't exist
        if (false === get_transient('mo_license_check') || $force === true) {

            $response = self::license_control_instance()->check_license();

            if ( ! empty($response->license)) {

                if ($response->license == 'site_inactive') {
                    self::activate_license('', true);
                }

                if ($response->license == 'valid') {
                    update_option('mo_price_id', $response->price_id);
                    update_option('mo_license_once_active', 'true');
                    update_option('mo_license_expired_status', 'false');
                }

                if (in_array($response->license, ['expired', 'disabled'])) {
                    update_option('mo_license_expired_status', 'true');
                }

                // $response->license could be valid/invalid/expired.
                update_option('mo_license_status', $response->license);
            }

            set_transient('mo_license_check', 'active', DAY_IN_SECONDS);
        }

        if ($force === true) {
            wp_safe_redirect(self::$license_page_url);
            exit;
        }
    }

    /**
     * License settings page
     */
    public static function license_page()
    {
        $license = self::license_key();

        if (isset($_GET['license-settings-updated']) && $_GET['license-settings-updated']) {
            add_settings_error('moLicenseSettingsError', 'changes_saved', __('License key updated successfully', 'mailoptin'), 'updated');
        } elseif (isset($_GET['license']) && $_GET['license'] == 'activated') {
            add_settings_error('moLicenseSettingsError', 'valid_license', __('License key activation successful.', 'mailoptin'), 'updated');
        } elseif (isset($_GET['license']) && $_GET['license'] == 'deactivated') {
            add_settings_error('moLicenseSettingsError', 'invalid_license', __('License key deactivation successful.', 'mailoptin'), 'updated');
        }
        ?>
        <!--	Output Settings error	-->
        <?php settings_errors(); ?>
        <?php self::license_banner(); ?>
        <form method="post">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row" valign="top">
                        <?php _e('License Key'); ?>
                    </th>
                    <td>
                        <input id="mo_license_key" name="mo_license_key" type="text" class="regular-text" value="<?php esc_attr_e($license); ?>">
                    </td>
                </tr>
                <?php if (false !== $license) { ?>
                    <tr valign="top" id="license_Activate_th">
                        <th scope="row" valign="top">
                            <?php _e('Activate License', 'mailoptin'); ?>
                        </th>
                        <td>
                            <?php if (self::mo_is_license_valid()) { ?>
                                <span style="color:green;"><?php _e('active'); ?></span>
                                <input type="submit" class="button-secondary" name="mo_deactivate_license" value="<?php _e('Deactivate License'); ?>"/>
                                <?php
                            } else {
                                ?>
                                <input type="submit" class="button-secondary" name="mo_activate_license" value="<?php _e('Activate License'); ?>"/>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php wp_nonce_field('mo_plugin_nonce', 'mo_plugin_nonce'); ?>
            <?php submit_button(null, 'primary', 'mo_save_license'); ?>
        </form>

        <script type="text/javascript">
            (function ($) {
                field = $('input#mo_license_key');
                var initial_value = field.val();
                field.change(function () {
                    $(this).val() != initial_value ? $('tr#license_Activate_th').hide() : $('tr#license_Activate_th').show();
                });
            })(jQuery);

        </script>
        <?php
    }

    /**
     * Save License key to DB
     */
    public static function save_license_key()
    {
        // run a quick security check
        if ( ! check_admin_referer('mo_plugin_nonce', 'mo_plugin_nonce')) {
            return;
        }

        $old = self::$license_key;
        $new = trim(sanitize_key($_POST['mo_license_key']));

        if ($old && $old != $new) {
            delete_option('mo_license_status'); // new license has been entered, so must reactivate
        }

        update_option('mo_license_key', $new);

        if ( ! empty($new)) {
            self::activate_license($new, true);
        }

        wp_safe_redirect(esc_url_raw(add_query_arg('license-settings-updated', 'true', MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE)));
        exit;
    }

    /**
     * Activate License key
     *
     * @param string $license_key
     */
    public static function activate_license($license_key = '', $no_redirect = false)
    {
        $response = self::license_control_instance()->activate_license($license_key);

        if (is_wp_error($response)) {
            add_settings_error('moLicenseSettingsError', 'activation_error', $response->get_error_message());

            return;
        }

        // $response->license will be either "valid" or "invalid" or even "expired"
        update_option('mo_license_status', $response->license);
        update_option('mo_price_id', $response->price_id);

        if ($response->license == 'invalid') {
            add_settings_error('moLicenseSettingsError', 'invalid_license', 'License key entered is invalid.');
        } elseif ($response->license == 'expired') {
            update_option('mo_license_expired_status', 'true');
        } elseif ($response->license == 'valid') {
            //first time activation
            add_option('mo_license_once_active', 'true');
            update_option('mo_license_expired_status', 'false');

            if ($no_redirect === false) {
                wp_safe_redirect(add_query_arg('license', 'activated', MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE));
                exit;
            }
        }
    }

    /**
     * Plugin update method
     */
    public static function plugin_updater()
    {
        if (defined('DOING_AJAX')) return;

        self::license_control_instance()->plugin_updater();
    }

    /**
     * Deactivate license
     */
    public static function deactivate_license()
    {
        $response = self::license_control_instance()->deactivate_license();

        if (is_wp_error($response)) {
            add_settings_error('moLicenseSettingsError', 'deactivation_error', $response->get_error_message());

            return;
        }

        // $response->license will be either "deactivated" or "failed"
        if ($response->license == 'deactivated') {
            delete_option('mo_license_status');
        }
        wp_safe_redirect(add_query_arg('license', 'deactivated', MAILOPTIN_SETTINGS_SETTINGS_GENERAL_PAGE));
        exit;
    }

    /**
     * License Banner
     */
    public static function license_banner()
    {
        if (self::mo_is_license_empty()) {
            echo '<div class="mo-banner">' . __('Enter a License Key', 'mailoptin') . '</div><br/><br/><br/><br/>';
        } elseif (self::mo_is_license_valid()) {
            echo '<div class="mo-banner">' . __('You have an active License', 'mailoptin') . '</div><br/><br/><br/><br/>';
        } elseif (self::mo_is_license_invalid()) {
            echo '<div class="mo-banner">' . __('License key is invalid.', 'mailoptin') . '</div><br/><br/><br/><br/>';
        } elseif (self::mo_is_license_expired()) {
            echo '<div class="mo-banner">' . __('License key is expired.', 'mailoptin') . '</div><br/><br/><br/><br/>';
        }
    }

    public static function license_not_active_notice()
    {
        if ( ! is_super_admin(get_current_user_id())) {
            return;
        }

        if (self::mo_is_license_valid()) {
            return;
        }

        // bail if license is expired as this has its own admin notice.
        if (self::mo_is_license_expired()) {
            return;
        }

        echo '<div class="error notice"><p>' . sprintf(__('%sWelcome to MailOptin Premium!%s Please %s or %s to enable automatic updates.', 'mailoptin'),
                '<strong>',
                '</strong>',
                '<a href="' . self::$license_page_url . '">' . __('activate your license key', 'mailoptin') . '</a>',
                '<a target="_blank" href="https://my.mailoptin.io">' . __('renew it', 'mailoptin') . '</a>') . '</p></div>';
    }

    public static function license_expired_notice()
    {
        if ( ! PAnD::is_admin_notice_active('mo-license-expired-notice-14')) {
            return;
        }

        if ( ! is_super_admin(get_current_user_id())) {
            return;
        }

        if (self::mo_is_license_valid()) {
            return;
        }

        // bail if license not expired.
        if ( ! self::mo_is_license_expired()) {
            return;
        }

        $licenseKey  = self::$license_key;
        $download_id = EDD_MO_ITEM_ID;
        $renew_url   = ! empty(self::$license_key) ? "https://my.mailoptin.io/checkout/?edd_license_key={$licenseKey}&download_id={$download_id}" : 'https://my.mailoptin.io';

        echo '<div data-dismissible="mo-license-expired-notice-14" class="error notice is-dismissible"><p>' .
             sprintf(
                 __('Your MailOptin license has expired and you have been downgraded to the LITE version. %sClick here to renew your license%s to regain all data and features. %sLearn more%s', 'mailoptin'),
                 "<a target='_blank' href=\"$renew_url\"><strong>", '</strong></a>',
                 '<a target="_blank" href="https://mailoptin.io/article/cancel-subscription-expires/">',
                 '</a>'
             ) . '</p></div>';
    }

    /**
     * @return LibsodiumSettingsPage
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