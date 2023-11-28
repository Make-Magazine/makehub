<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Pansy extends AbstractOptinTheme
{
    public $optin_form_name = 'Pansy';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#fff',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#3f96cc',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Join 30,000 Bloggers", 'mailoptin'),
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],


                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Times+New+Roman',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 30,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 28,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 26,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                // -- default for description sections -- //

                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Times+New+Roman',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 13,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                // -- default for fields sections -- //

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name", 'mailoptin'),
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email", 'mailoptin'),
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#fec32d',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('Your information will never be shared', 'mailoptin'),
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],


                [
                    'name'        => 'mo_optin_branding_outside_form',
                    'value'       => true,
                    'optin_class' => 'Gridgum',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 12,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 12,
                    'optin_class' => 'Pansy',
                    'optin_type'  => 'inpost'
                ]
            ]
        );

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 400;
            $config['height'] = 326;

            return $config;
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'block';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'left';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#555555';
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
        $settings['pansy_header_bg_color'] = array(
            'default'           => '#3f96cc',
            'type'              => 'option',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        );

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
        $controls['pansy_header_bg_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[pansy_header_bg_color]',
            array(
                'label'    => __('Header Background Color', 'mailoptin'),
                'section'  => $customizerClassInstance->design_section_id,
                'settings' => $option_prefix . '[pansy_header_bg_color]',
                'priority' => 40,
            )
        );

        return $controls;
    }

    /**
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('And get our latest content in your inbox', 'mailoptin');
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
[mo-optin-form-wrapper class="pansy-container"]
<div class="pansy-top">
    [mo-optin-form-headline tag="div" class="pansy-heading"]
    [mo-optin-form-description class="pansy-caption"]
</div>
<div class="pansy-form">
    [mo-optin-form-cta-button class="pansy-cta"]
    [mo-optin-form-fields-wrapper]
        [mo-optin-form-name-field class="pansy-input"]
        [mo-optin-form-email-field class="pansy-input"]
        [mo-optin-form-custom-fields class="pansy-input"]
        [mo-mailchimp-interests class="pansy-input"]
        [mo-optin-form-submit-button class="pansy-submit"]
        [mo-optin-form-error class="pansy-error"]
    [/mo-optin-form-fields-wrapper]
</div>
[mo-optin-form-note class="pansy-finePrint"]
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
        $header_bg_color = $this->get_customizer_value('pansy_header_bg_color', '#3f96cc');

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.pansy-container * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.pansy-container {
    background: #fff;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    overflow: hidden;
    width: 100%;
    color: #555555;
    text-align: center;
    border: 2px solid #3f96cc;
    padding-bottom: 10px;
	margin: 10px auto;
 }

html div#$optin_uuid div#$optin_css_id.pansy-container button, div#$optin_css_id.pansy-container input {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input {
    border: 0;
}
html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-top {
    padding: 30px;
    padding-top: 20px;
    background: $header_bg_color;
    color: #fff;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-top .pansy-heading {
     font-size: 30px;
     line-height: 35px;
     text-align: center;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-top .pansy-caption {
    margin-top: 5px;
    font-size: 14px;
    line-height: 24px;
    text-transform: uppercase;
    text-align: center;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-form {
    margin: 30px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container input.pansy-input,
html div#$optin_uuid div#$optin_css_id.pansy-container textarea.pansy-input {
    -webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.pansy-container select.pansy-input {
    padding: 12px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container textarea.pansy-input {
    min-height: 80px;
}


html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input {
    display: block;
    width: 100%;
    margin-top: 8px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 12px;
    font-size: 16px;
    line-height: 16px;
    color: #555555;
    outline: none;
    font-weight: 400;
    border: 1px solid #ccc;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-submit,
html div#$optin_uuid div#$optin_css_id input[type=submit] {
    display: block;
    width: 100%;
    margin-top: 8px;
    margin-bottom: 8px;
    border: 0;
    background-color: #fec32d;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 16px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    text-transform: uppercase;
    cursor: pointer;
    font-weight: 600;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input.pansy-clearfix .pansy-form {
    background-color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.pansy-container input:focus {
    background: #fff;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-clearfix:before, div#$optin_css_id.pansy-container .pansy-clearfix:after {
    display: table;
    content: " ";
}
html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-clearfix:after {
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .form-body {
    padding: 10px 10px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-finePrint {
    margin-top: 20px;
    margin-bottom: 15px;
    font-size: 12px;
    line-height: 22px;
    color: #555555;
    text-align: center;
}

html div#$optin_uuid div#$optin_css_id.pansy-container div.mo-optin-error {
    display: none;
    color: #FF0000;
    font-size: 12px;
    text-align: center;
    width: 100%;
    padding-bottom: .5em;
}

html div#$optin_uuid div#$optin_css_id.mo-has-email.bannino-container .pansy-input.pansy-clearfix .pansy-form {
    width: 100% !important;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input.pansy-clearfix input.pansy-submit {
    display: block;
    width: 100%;
    margin-top: 8px;
    -webkit-appearance: none;
    border: 2px solid #fec32d;
    background: #FEC32D;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 12px;
    font-size: 16px;
    line-height: 16px;
    color: #fff;
    outline: none;
    text-transform: uppercase;
    cursor: pointer;
    font-weight: 600;
    white-space: nowrap;
    vertical-align: middle;
}


html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input.pansy-clearfix .pansy-form,
html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-input.pansy-clearfix input.pansy-submit {
    padding: 12px;
    display: block;
    width: 100%;
    margin-bottom: 8px;
    border: 0;
    line-height: normal;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-closeBtn {
    position: absolute;
    right: 10px;
    top: 7px;
    width: 35px;
    height: 35px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container  .pansy-close-btn img {
    width: 20px;
    height: 20px;
    text-align: center;
    margin-top: 5px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .mo-mailchimp-interest-container .mo-mailchimp-interest-label {
    font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .pansy-close-btn {
    border-radius: 100px;
    border: 0;
    width: 34px;
    height: 34px;
    background: #d7dee6;
    cursor: pointer;
    position: absolute;
    right: -17px;
    top: -17px;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .radio-field label,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .checkbox-field label {
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .select-field,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .radio-field,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .checkbox-field {
    margin-top: 8px;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .select-field {
    padding: 12px 0;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .mo-mailchimp-interest-container {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice-container label {
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id.pansy-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice {
    margin-left: 0;
    margin-top: 5px;
    vertical-align: middle;
}
CSS;
    }
}