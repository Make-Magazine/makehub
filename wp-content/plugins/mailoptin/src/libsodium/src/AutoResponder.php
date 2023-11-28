<?php
/**
 *    JavaScript Wordpress editor
 *    Author:        Ante Primorac
 *    Author URI:    http://anteprimorac.from.hr
 *    Version:        1.1
 *    License:
 *        Copyright (c) 2013 Ante Primorac
 *        Permission is hereby granted, free of charge, to any person obtaining a copy
 *        of this software and associated documentation files (the "Software"), to deal
 *        in the Software without restriction, including without limitation the rights
 *        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *        copies of the Software, and to permit persons to whom the Software is
 *        furnished to do so, subject to the following conditions:
 *
 *        The above copyright notice and this permission notice shall be included in
 *        all copies or substantial portions of the Software.
 *
 *        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *        THE SOFTWARE.
 *    Usage:
 *        server side(WP):
 *            js_wp_editor( $settings );
 *        client side(jQuery):
 *            $('textarea').wp_editor( options );
 */

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository;
use function MailOptin\Core\mo_test_admin_email;
use function MailOptin\Core\moVar;

class AutoResponder
{
    public function __construct()
    {
        add_filter('mo_optin_form_customizer_output_settings', [$this, 'autoresponder_settings'], 10, 2);
        add_action('mo_optin_after_customizer_controls', array($this, 'autoresponder_controls'), 10, 4);
        add_action('mo_optin_customizer_css_js_enqueue', [$this, 'enqueue_assets']);
        add_action('mo_optin_customizer_footer_scripts', [$this, 'js_template']);

        add_action('wp_ajax_mailoptin_send_test_autoresponder_email', [$this, 'send_test_autoresponder_email']);

        add_action('mailoptin_track_conversions', array($this, 'send_auto_responder_email'), 10, 2);
    }

    public function enqueue_assets()
    {
        wp_enqueue_script(
            'mailoptin-autoresponder-config',
            MAILOPTIN_LIBSODIUM_ASSETS_URL . 'autoresponder-config.js',
            array('customize-controls'),
            MAILOPTIN_VERSION_NUMBER
        );

        wp_localize_script('mailoptin-autoresponder-config', 'autoresponder_globals', array(
            'test_email_success_msg' => __('Test email sent to', 'mailoptin'),
            'test_email_error_msg'   => __('Failed to send test email. Please try again.', 'mailoptin')
        ));
    }

    /**
     * @param $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function autoresponder_settings($settings, $customizerSettings)
    {
        $settings['autoresponder_settings'] = array(
            'type'      => 'option',
            'transport' => 'postMessage',
            'default'   => wp_json_encode(
                [
                    'subject' => __('Welcome {{first_name}}! Thanks for Signing Up', 'mailoptin'),
                    'message' => $this->default_welcome_message()
                ]
            )
        );

        $settings['autoresponder_config_button'] = array(
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    /**
     * @param int $optin_campaign_id
     *
     * @return bool
     */
    public function autoresponder_customizer_settings($optin_campaign_id)
    {
        $result = OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'autoresponder_settings');
        if (empty($result)) return false;

        return json_decode($result, true);
    }

    /**
     * @param int $optin_campaign_id
     *
     * @return bool
     */
    public function is_autoresponder_active($optin_campaign_id)
    {
        return moVar($this->autoresponder_customizer_settings($optin_campaign_id), 'active');
    }

    /**
     * Click Launch display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function autoresponder_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $hide_icon_yes = $hide_icon_no = '';
        if ($this->is_autoresponder_active($customizerClassInstance->optin_campaign_id)) {
            $hide_icon_no = 'display:none';
        } else {
            $hide_icon_yes = 'display:none';
        }

        $icon = "<span id=\"mo-autores-btn-icon-no\" class=\"mo-autoresponder-config-btn dashicons dashicons-no-alt\" style=\"$hide_icon_no\"></span>";
        $icon .= "<span id=\"mo-autores-btn-icon-yes\" class=\"mo-autoresponder-config-btn dashicons dashicons-yes\" style=\"$hide_icon_yes\"></span>";

        $autoresponder_control_args = apply_filters(
            "mo_optin_form_customizer_autoresponder_controls",
            array(
                'autoresponder_settings'      => apply_filters('mo_optin_form_customizer_autoresponder_settings_args', array(
                        'type'        => 'hidden',
                        'section'     => $customizerClassInstance->success_section_id,
                        'settings'    => $option_prefix . '[autoresponder_settings]',
                        'input_attrs' => ['class' => 'mo-autoresponder-settings-field'],
                        'priority'    => 26
                    )
                ),
                'autoresponder_config_button' => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[autoresponder_config_button]',
                    apply_filters('mo_optin_form_customizer_autoresponder_config_button_args', array(
                            'content'        => sprintf(
                                '<button id="mo-configure-autoresponder" type="button" class="button customize-add-menu-button">%s %s</button>',
                                __('Configure Autoresponder', 'mailoptin'),
                                $icon
                            ),
                            'no_wrapper_div' => true,
                            'label'          => __('Autoresponder', 'mailoptin'),
                            'description'    => __('Send email to subscribers immediately after they sign up to this campaign.', 'mailoptin'),
                            'section'        => $customizerClassInstance->success_section_id,
                            'settings'       => $option_prefix . '[autoresponder_config_button]',
                            'priority'       => 24,
                        )
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_autoresponder_controls_addition');

        foreach ($autoresponder_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_autoresponder_controls_addition');
    }

    public function tag_replacer($content, $data = [])
    {
        if (empty($data)) {
            $data = [
                'firstname' => 'John',
                'lastname'  => 'Doe',
                'email'     => 'john@doe.com',
                'sitename'  => get_bloginfo('name')
            ];
        }

        return str_replace(
            [
                '{{first_name}}',
                '{{last_name}}',
                '{{email}}',
                '{{site_name}}'
            ],
            [
                $data['firstname'],
                $data['lastname'],
                $data['email'],
                $data['sitename'],
            ],
            $content
        );
    }

    public function send_test_autoresponder_email()
    {
        if ( ! is_admin()) return;

        check_ajax_referer('customizer-fetch-email-list', 'security');

        if ( ! \MailOptin\Core\current_user_has_privilege()) return;

        $admin_email = mo_test_admin_email();

        $response = $this->send($admin_email, $_POST['subject'], $_POST['message']);

        wp_send_json(['success' => (bool)$response, 'email' => $admin_email]);
    }

    /**
     * @param $lead_data
     * @param int $optin_campaign_id
     */
    public function send_auto_responder_email($lead_data, $optin_campaign_id)
    {
        $name_split = AbstractConnect::get_first_last_names($lead_data['name']);

        if ( ! $this->is_autoresponder_active($optin_campaign_id)) return;

        $autoresponder_cutomizer_data = $this->autoresponder_customizer_settings($optin_campaign_id);

        $subject = $autoresponder_cutomizer_data['subject'];
        $message = $autoresponder_cutomizer_data['message'];

        $tag_replacer = [
            'firstname' => $name_split[0],
            'lastname'  => $name_split[1],
            'email'     => $lead_data['email'],
            'sitename'  => get_bloginfo('name')
        ];

        if ( ! $this->is_autoresponder_active($optin_campaign_id)) return;

        $this->send($lead_data['email'], $subject, $message, $tag_replacer, $lead_data);
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param array $tag_replacer
     * @param array $lead_data
     */
    public function send($email, $subject, $message, $tag_replacer = [], $lead_data = [])
    {
        $subject         = $this->tag_replacer(sanitize_text_field($subject), $tag_replacer);
        $message         = $this->tag_replacer(stripslashes($message), $tag_replacer);
        $plugin_settings = new Settings();
        $from_name       = $plugin_settings->from_name();
        $from_email      = $plugin_settings->from_email();

        if (empty($subject) || empty($message) || empty($from_name) || empty($from_email)) return;

        $from_name  = apply_filters('mo_optin_autoresponder_from_name', $from_name, $lead_data);
        $from_email = apply_filters('mo_optin_autoresponder_from_email', $from_email, $lead_data);
        $headers    = ["Content-Type: text/html", "From: $from_name <$from_email>"];

        $response = wp_mail($email, $subject, $message, $headers);

        if ( ! $response) {
            $headers  = ["Content-Type: text/html"];
            $response = wp_mail($email, $subject, $message, $headers);
        }

        return $response;
    }

    public function js_template()
    {
        ?>
        <script type="text/html" id="tmpl-mo-autores-config">
            <div class="mo-autoresponder-modal-overlay">
                <div class="mo-autoresponder-modal">
                    <div class="mo-autoresponder-modal_inner">
                        <div class="mo-autoresponder-modal-top-header-section mo-autoresponder-modal-clearfix">
                            <div class="mo-autoresponder-modal-top-header-inner mo-autoresponder-modal-clearfix">
                                <div class="mo-autoresponder-modal-float-right">
                                    <a href="#" class="mo-autoresponder-modal-view-tags">
                                        <?php _e('View available tags', 'mailoptin'); ?>
                                        <span class="mo-autoresponder-tooltipster dashicons dashicons-editor-help"></span>
                                    </a>
                                </div>

                                <div style="display:none">
                                    <div id="mo-autoresponder-tooltipster-content">
                                        <p>
                                            <code style="color:#800;margin:0;padding:0;">{{{data.firstname_tag}}}</code> : <?php _e('First name of subscriber.', 'mailoptin'); ?>
                                        </p>
                                        <p>
                                            <code style="color:#800;margin:0;padding:0;">{{{data.lastname_tag}}}</code> : <?php _e('Last name of subscriber.', 'mailoptin'); ?>
                                        </p>
                                        <p>
                                            <code style="color:#800;margin:0;padding:0;">{{{data.email_tag}}}</code> : <?php _e('Email address of subscriber.', 'mailoptin'); ?>
                                        </p>
                                        <p>
                                            <code style="color:#800;margin:0;padding:0;">{{{data.site_name}}}</code> : <?php _e('Site title or name.', 'mailoptin'); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="mo-autoresponder-modal-float-left">
                                    <span id="mo-autoresponder-send-test-mail-spinner" class="spinner" style="visibility:hidden;float:none;"></span>
                                    <input id="mo-autoresponder-modal-send-test-mail" type="button" value="<?php _e('Send Test Email', 'mailoptin'); ?>" class="button button-secondary mo-autoresponder-btn">
                                    <input id="mo-autoresponder-modal-save" type="button" value="<?php _e('Save Changes', 'mailoptin'); ?>" class="button button-primary mo-autoresponder-btn">
                                    <a href="#" class="mo-autoresponder-modal-close">×</a>
                                </div>
                            </div>
                        </div>
                        <div class="mo-autoresponder-modal-main-section">
                            <div class="mo-autoresponder-modal-form-group">
                                <div class="mo-autoresponder-modal-validated-input" style="width: 400px;">
                                    <label for="mo-autoresponder-activate" class="customize-control-title" style="float: left;"><?php _e('Activate Autoresponder', 'mailoptin'); ?></label>
                                    <input id="mo-autoresponder-activate" type="checkbox" class="tgl tgl-light" <# if(data.active === true) { #> checked=checked <# } #>>
                                    <label for="mo-autoresponder-activate" style="margin:auto;" class="tgl-btn"></label>
                                </div>
                            </div>

                            <div class="mo-autoresponder-modal-form-group">
                                <label for="mo-autoresponder-subject" class="customize-control-title"><?php _e('Subject', 'mailoptin'); ?></label>
                                <div class="mo-autoresponder-modal-validated-input">
                                    <div class="mo-autoresponder-modal-desc"><?php _e('This will be the subject of your autoresponder email.', 'mailoptin'); ?></div>
                                    <input type="text" class="mo-autoresponder-modal-field" placeholder="<?php _e('Write a subject', 'mailoptin'); ?>" value="{{{data.subject}}}" id="mo-autoresponder-subject">
                                </div>
                            </div>

                            <div class="mo-autoresponder-modal-form-group">
                                <label for="mo_autoresponder_message" class="customize-control-title"><?php _e('Message', 'mailoptin'); ?></label>
                                <div class="mo-autoresponder-modal-validated-input">
                                    <div class="mo-autoresponder-modal-desc"><?php _e('Enter the content or message of your autoresponder email.', 'mailoptin'); ?></div>
                                    <textarea style="height: 300px" class="mo-autoresponder-modal-field" id="mo_autoresponder_message">{{{data.message}}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </script>
        <?php
    }

    public function default_welcome_message()
    {
        return <<<HTML
<div style="max-width: 560px; padding: 20px; background: #ffffff; border-radius: 5px; margin: 40px auto;">
    <div style="color: #444444; font-weight: normal;">
        <div style="text-align: center; font-weight: 600; font-size: 26px; padding: 10px 0; border-bottom: solid 3px #eeeeee;">{{site_name}}</div>
        <div style="clear: both;"></div>
    </div>
    <div style="padding: 0 30px; border-bottom: 3px solid #eeeeee;">
        <div style="padding: 30px 0px; font-size: 24px; line-height: 40px; text-align: center;">Thank you for signing up!</div>
        <div style="padding: 20px;">
            <p style="text-align: center;">We will send you weekly updates.</p>
            <p style="text-align: center;">Hope you will enjoy it!</p>

        </div>
    </div>
    <div style="color: #999; padding: 10px 30px 20px; text-align: center;">
        <div>The {{site_name}} Team</div>
    </div>
</div>
HTML;
    }

    /**
     * Singleton poop.
     *
     * @return AutoResponder|null
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