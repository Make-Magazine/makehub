<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Slidein;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Quince extends AbstractOptinTheme
{
    public $optin_form_name = 'Quince';

    public $default_form_image_partial;

    public $default_form_bg_image;

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_modal_effects_default',
                    'value'       => 'MOslideInUp',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#cccccc',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Sign up Now (For Free)", 'mailoptin'),
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#273238',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 25,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 22,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 17,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 17,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 15,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 15,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                // -- default for fields sections -- //

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name", 'mailoptin'),
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email", 'mailoptin'),
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#1342a1d6',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#7a7a7a',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Quince',
                    'optin_type'  => 'slidein'
                ]
            ]
        );

        add_action('mo_optin_customize_preview_init', function () {
            add_action('wp_footer', [$this, 'customizer_preview_js']);
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'inline';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'center';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#000000';
        });

        parent::__construct($optin_campaign_id);
    }

    public function features_support()
    {
        return [
            self::CTA_BUTTON_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_design_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_design_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('Get access to all our upcoming educational webinars, invitations to regional workshop meetups and more.', 'mailoptin');
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_headline_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_headline_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    public function customizer_preview_js()
    {
        if ( ! \MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {

                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (value) {
                        value.bind(function (to) {
                            $('.mo-optin-form-name-field').toggle(!to);
                        });
                    });

                })
            })(jQuery)
        </script>
        <?php
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_description_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_description_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_note_settings($settings, $CustomizerSettingsInstance)
    {
        return $settings;
    }

    /**
     * @param array $controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_note_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $controls;
    }


    /**
     * @param mixed $fields_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_fields_settings($fields_settings, $CustomizerSettingsInstance)
    {
        $fields_settings['hide_name_field']['transport'] = 'postMessage';

        return $fields_settings;
    }

    /**
     * @param array $fields_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_fields_controls($fields_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $fields_controls;
    }

    /**
     * @param mixed $configuration_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_configuration_settings($configuration_settings, $CustomizerSettingsInstance)
    {
        return $configuration_settings;
    }


    /**
     * @param array $configuration_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_configuration_controls($configuration_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $configuration_controls;
    }

    /**
     * @param mixed $output_settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_output_settings($output_settings, $CustomizerSettingsInstance)
    {
        return $output_settings;
    }


    /**
     * @param array $output_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     *
     * @return array
     */
    public function customizer_output_controls($output_controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        return $output_controls;
    }

    /**
     * Fulfil interface contract.
     */
    public function optin_script()
    {
    }

    /**
     * Template body.
     *
     * @return string
     */
    public function optin_form()
    {
        return <<<HTML
[mo-optin-form-wrapper class="quince-container"]
		<div class="quince-container_inner">
                [mo-close-optin class="quince-optin-form-close"]x[/mo-close-optin]
			<div class="quince-container_right-section">
				<div class="quince-container_right-section_inner">
                    [mo-optin-form-headline tag="div" class="quince-headline"]
                    [mo-optin-form-description class="quince-description"]
                    [mo-optin-form-cta-button class="quince-cta-btn"]
                    [mo-optin-form-fields-wrapper class="quince-container_field"]
                        [mo-optin-form-error]
                        [mo-optin-form-name-field class="quince-fields quince-field"]
                        [mo-optin-form-email-field class="quince-fields quince-field"]
                        [mo-optin-form-custom-fields class="quince-fields quince-field"]
                        [mo-mailchimp-interests]
                        [mo-optin-form-submit-button class="quince-fields quince-submit_btn"]
                    [/mo-optin-form-fields-wrapper]
                    [mo-optin-form-note class="quince-note"]
				</div>
			</div>
		</div>
[/mo-optin-form-wrapper]
HTML;

    }

    /**
     * Template CSS styling.
     *
     * @return string
     */
    public function optin_form_css()
    {
        $optin_css_id = $this->optin_css_id;
        $optin_uuid   = $this->optin_campaign_uuid;

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.quince-container * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.quince-container {
	width: 100%;
	margin: 0;
	background: #fff;
    border-radius: 3px;
	border: 2px solid #cccccc;
}

html div#$optin_uuid div#$optin_css_id .quince-container_inner {
	display: flex;
}
		
html div#$optin_uuid div#$optin_css_id .quince-container_right-section {
	margin-left: 0;
	text-align: center;
	padding: 20px;
	width: 100%;
	display: table-cell;
}
		
html div#$optin_uuid div#$optin_css_id .quince-headline {
	font-size: 17px;
	margin-bottom: 20px;
	color: #273238;
}
		
html div#$optin_uuid div#$optin_css_id .quince-container_field {
	text-align: center;
	margin-top: 30px;
	position: relative;
}
		
html div#$optin_uuid div#$optin_css_id .quince-container_field .quince-fields {
	display: block;
	padding: 15px;
	width: 100%;
	border-radius: 3px;
}
		
html div#$optin_uuid div#$optin_css_id .quince-fields.quince-submit_btn {
	background-color: #1342a1d6;
	border: 0;
	margin-top: 5px;
	font-size: 18px;
	font-weight: 700;
	color: #fff;
}

html div#$optin_uuid div#$optin_css_id .quince-cta-btn {
    border: 0;
    margin-top: 5px;
    padding: 10px;
    border-radius: 5px;
    display: block;
    width: 100%;
    font-size: 18px;
    font-weight: 700;
}
		
html div#$optin_uuid div#$optin_css_id .quince-fields.quince-field {
	border: 1px solid #cccccc;
	font-size: 18px;
	color: #444;
	text-align: center;
	margin-bottom: 15px;
}
		
html div#$optin_uuid div#$optin_css_id .quince-description {
	padding: 0;
	font-size: 15px;
	letter-spacing: 0.2px;
	line-height: 1.6;
	color: #444;
    margin-bottom: 10px;
}
		
html div#$optin_uuid div#$optin_css_id .quince-container_right-section_inner {
	padding: 0;
}
		
html div#$optin_uuid div#$optin_css_id .quince-note {
	font-weight: 400;
	cursor: pointer;
	color: #7a7a7a;
	margin-top: 10px;
}
		
html div#$optin_uuid div#$optin_css_id .mo-optin-error {
	background: #e74c3c;
    border-radius: 3px;
    padding: 7px;
    font-size: 13px;
    color: #fff;
    margin-bottom: 10px;
    display: none;
}
		
html div#$optin_uuid div#$optin_css_id .quince-fields.quince-submit_btn,
html div#$optin_uuid div#$optin_css_id .quince-note {
	cursor: pointer;
}	

html div#$optin_uuid div#$optin_css_id .quince-optin-form-close {
    right: .5em;
    top: .5em;
    padding: 0;
    margin: 0;
    text-align: center;
    position: absolute;
    cursor: pointer;
    z-index: 2;
    line-height: .5;
    font-size: 1.5em;
    text-decoration: none !important;
    color: #444444;
    font-family: Roboto, "Lato", sans-serif;
}

html div#$optin_uuid div#$optin_css_id .quince-optin-form-close:hover {
    transform: scale(1.2);
    position: absolute;
    right: .5em;
    top: .5em;
    padding: 0;
    margin: 0;
    transition: all .2s ease-out 0s;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .select-field,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .radio-field label,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .checkbox-field label{
	text-align: center;
}

CSS;

    }
}