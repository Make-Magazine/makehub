<?php
if (! defined('EVENT_ESPRESSO_VERSION')) {
    exit('NO direct script access allowed');
}


/**
 *  Class  EE_QuickBooks_PM_Form
 *
 *  @package        Event Espresso
 *  @subpackage     eea-quickbooks-gateway
 *  @author			Event Espresso
 *  @version        1.0.10.p
 */
class EE_QuickBooks_PM_Form extends EE_Payment_Method_Form
{
    /**
     *  OAuth v1.0a Public key title.
     *
     * @var string
     */
    protected $v1a_pub_key_title = null;

    /**
     *  OAuth v1.0a Secret key title.
     *
     * @var string
     */
    protected $v1a_secret_key_title = null;

    /**
     *  OAuth v2.0 Public key title.
     *
     * @var string
     */
    protected $v2_pub_key_title = null;

    /**
     *  OAuth v2.0 Secret key title.
     *
     * @var string
     */
    protected $v2_secret_key_title = null;


    /**
     *
     * @param EE_PMT_QuickBooks_Onsite $quickbooks_pmt
     * @param EE_Payment_Method        $qb_instance
     */
    public function __construct(EE_PMT_QuickBooks_Onsite $quickbooks_pmt, EE_Payment_Method $qb_instance)
    {
        $oauth_version = $qb_instance->get_extra_meta('oauth_version', true, 'v1a');
		$qb_connected = $quickbooks_pmt->is_oauthed();

        // API keys and their naming depend on the OAuth version (new or older accoun).
        $this->v1a_pub_key_title = sprintf(
            esc_html__('OAuth Consumer Key: %1$s', 'event_espresso'),
            $quickbooks_pmt->get_help_tab_link()
        );
        $this->v1a_secret_key_title = sprintf(
            esc_html__('OAuth Consumer Secret: %1$s', 'event_espresso'),
            $quickbooks_pmt->get_help_tab_link()
        );
        $this->v2_pub_key_title = sprintf(
            esc_html__('Client ID: %1$s', 'event_espresso'),
            $quickbooks_pmt->get_help_tab_link()
        );
        $this->v2_secret_key_title = sprintf(
            esc_html__('Client Secret: %1$s', 'event_espresso'),
            $quickbooks_pmt->get_help_tab_link()
        );

        if ($oauth_version === 'v1a') {
            $pub_key_title = $this->v1a_pub_key_title;
            $secret_key_title = $this->v1a_secret_key_title;
            $connected_on = $qb_instance->get_extra_meta('connected_on', true, '');
            $expires_on = date('Y-m-d', strtotime($connected_on . ' + 180 days'));
        } else {
            $pub_key_title = $this->v2_pub_key_title;
            $secret_key_title = $this->v2_secret_key_title;
            // Add a default lifespan. It's usually 101 days.
            $default_lifespan = strtotime('+101 days');
            $expires_in = $qb_instance->get_extra_meta('x_refresh_token_expires_on', true, $default_lifespan);
            $expires_on = date('Y-m-d', (int)$expires_in);
        }
        // Expiration time.
        $date_diff = floor((strtotime($expires_on) - time()) / (60 * 60 * 24));
		if ($date_diff <= 0) {
			// Reset the connection.
			$quickbooks_pmt->reset_qb_settings();
			$expires_in_30 = false;
		} else {
			$expires_in_30 = ($date_diff <= 30) ? true : false;
		}

		$oauth_section = new EE_Form_Section_Proper(array(
			'layout_strategy' => new EE_Template_Layout(array(
				'layout_template_file' => EEA_QUICKBOOKS_ONSITE_PM_PATH . 'templates' . DS
                    . 'quickbooks_oauth_process.template.php',
				'template_args'        => array(
					'qb_connected_no'   => EEA_QUICKBOOKS_ONSITE_PM_URL . 'lib' . DS . 'C2QB_green_btn_med_default.png',
					'qb_disconnect_ico' => EEA_QUICKBOOKS_ONSITE_PM_URL . 'lib' . DS . 'C2QB_disconnectt_ico.png',
					'qb_reconnect_ico'  => EEA_QUICKBOOKS_ONSITE_PM_URL . 'lib' . DS . 'C2QB_reconnect_ico.png',
					'connected_png'     => EEA_QUICKBOOKS_ONSITE_PM_URL . 'lib' . DS . 'connected_ico.png',
					'not_connected_png' => EEA_QUICKBOOKS_ONSITE_PM_URL . 'lib' . DS . 'not_connected_ico.png',
					'help_tab_link'     => $quickbooks_pmt->get_help_tab_link(),
					'qb_connected'      => $qb_connected,
					'expires_on'        => $expires_on,
					'expires_in_30'     => $expires_in_30   // If expires on 30 days display a Reconnect button.
				)
			))
		));

		$form_params = array(
            'payment_method_type' => $quickbooks_pmt,
			'extra_meta_inputs'   => array(
                'oauth_version' => new EE_Select_Input(
                    array(
                        'v1a' => esc_html__('OAuth 1.0a', 'event_espresso'),
                        'v2'  => esc_html__('OAuth 2.0', 'event_espresso')
                    ),
                    array(
                        'html_label_text' => sprintf(
                            esc_html__('OAuth version: %1$s', 'event_espresso'),
                            $quickbooks_pmt->get_help_tab_link()
                        ),
                        'html_help_text' => esc_html__(
                            'The version of OAuth that your app integrates with.',
                            'event_espresso'
                        ),
                        'other_html_attributes' => 'data-pm-slug="' . $qb_instance->slug() . '"'
                                                    . 'data-oauth-version="' . $oauth_version . '"',
                        'html_id'               => 'eea_quickbooks_oauth_version',
                        'default'               => 'v1a',
                        'required'              => true
                    )
                ),
				'consumer_key' => new EE_Text_Input(array(
					'html_label_text' => $pub_key_title,
					'required' => true
				)),
				'shared_secret' => new EE_Text_Input(array(
					'html_label_text' => $secret_key_title,
					'required' => true
				))
			)
		);
        parent::__construct($form_params);

        $consumer_key = $qb_instance->get_extra_meta('consumer_key', true);
		// Add QuickBooks connect button.
        if ($consumer_key) {
            $this->add_subsections(array(
                'oauth_btn' => new EE_Form_Section_HTML(
                    EEH_HTML::no_row( EEH_HTML::br(1) ) .
                    EEH_HTML::tr(
                        EEH_HTML::th(sprintf(
                            esc_html__(
                                'Connect to QuickBooks: %1$s *',
                                'event_espresso'
                            ),
                            $quickbooks_pmt->get_help_tab_link()
                        )) .
                        EEH_HTML::td(
                            $oauth_section->get_html()
                        )
                    )
                )
            ));
        }
    }


	/**
	 *  Load the scripts needed for the admin pages.
	 *
	 * @return void
	 */
	public function enqueue_js()
    {
        $oauth_args = array(
            'qb_blocked_popups_notice' => esc_html__(
                // @codingStandardsIgnoreStart
                'The authentication process could not be executed. Please allow window popups in your browser for this website in order to process a successful authentication.',
                // @codingStandardsIgnoreEnd
                'event_espresso'
            ),
            'qb_token_error'          => esc_html__('Error getting the Request token from Intuit', 'event_espresso'),
            'unknown_container'       => esc_html__('Could not specify the parent form.', 'event_espresso'),
            'espresso_default_styles' => EE_ADMIN_URL . 'assets/ee-admin-page.css',
            'wp_stylesheet'           => includes_url('css/dashicons.min.css'),
            'v1a_pub_key_title'       => $this->v1a_pub_key_title,
            'v1a_secret_key_title'    => $this->v1a_secret_key_title,
            'v2_pub_key_title'        => $this->v2_pub_key_title,
            'v2_secret_key_title'     => $this->v2_secret_key_title
        );

        // Admin style sheets.
        wp_register_style(
            'eea_qb_admin_sheets',
            EEA_QUICKBOOKS_PM_PLUGIN_URL . 'styles' . DS . 'eea-quickbooks-admin-sheets.css',
            array(),
            EEA_QUICKBOOKS_PM_VERSION
        );
        wp_enqueue_style('eea_qb_admin_sheets' );

        // oAuth scripts.
        wp_enqueue_script(
            'eea_qb_oauth_proc_js',
            EEA_QUICKBOOKS_PM_PLUGIN_URL . 'scripts' . DS . 'eea-quickbooks-oauth-proc.js',
            array(),
            EEA_QUICKBOOKS_PM_VERSION,
            true
        );
        // Localize the script.
        wp_localize_script('eea_qb_oauth_proc_js', 'EEA_QUICKBOOKS_OAUTH_ARGS', $oauth_args);
	}


    /**
     *  Log an error and close the oAuth window with JS.
     *
     * @param string $msg
     * @return void
     */
    public static function close_oauth_window($msg = null)
    {
        $js_out = '<script type="text/javascript">';
        if (! empty($msg)) {
            $js_out .= 'if ( window.opener ) {
					try {
						window.opener.console.log("' . $msg . '");
					} catch (e) {
						console.log("' . $msg . '");
					}
				}
			';
        }
        $js_out .= 'window.opener = self;
				window.close();
			</script>';
        echo $js_out;
        die();
    }
}