<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Sidebar;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Ursinia extends AbstractOptinTheme
{
    public $optin_form_name = 'Ursinia';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_width_default',
                    'value'       => '500',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#fff',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#ea0c1a',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Enjoy The Good News", 'mailoptin'),
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],


                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 25,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 22,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 19,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for description sections -- //

                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 15,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 14,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for fields sections -- //

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name", 'mailoptin'),
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email", 'mailoptin'),
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],
                [
                    'name'        => 'mo_optin_form_submit_button_default',
                    'value'       => __('Subscribe Now', 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#ea0c1a',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('Give it a try, you can unsubscribe anytime.', 'mailoptin'),
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 12,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 12,
                    'optin_class' => 'Ursinia',
                    'optin_type'  => 'sidebar'
                ]
            ]
        );

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 410;
            $config['height'] = 448;

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
        return __('Join our email list to get the latest blog posts straight to your inbox', 'mailoptin');
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
[mo-optin-form-wrapper class="ursinia-container"]
[mo-optin-form-headline tag="div" class="ursinia-heading"]
[mo-optin-form-description class="ursinia-caption"]
[mo-optin-form-cta-button class="ursinia-cta"]
[mo-optin-form-fields-wrapper class="ursinia-form"]
    [mo-optin-form-name-field class="ursinia-input"]
    [mo-optin-form-email-field class="ursinia-input"]
    [mo-optin-form-custom-fields class="ursinia-input"]
    [mo-mailchimp-interests class="ursinia-input"]
    [mo-optin-form-submit-button class="ursinia-submit"]
    [mo-optin-form-error class="ursinia-error"]
[/mo-optin-form-fields-wrapper]
[mo-optin-form-note class="ursinia-finePrint"]
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
html div#$optin_uuid div#$optin_css_id.ursinia-container * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container {
    background: #fff;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    width: 100%;
    padding: 20px;
    color: #555555;
    text-align: center;
    border: 3px solid;
}

@media screen and (max-width: 746px) {
    html div#$optin_uuid div#$optin_css_id.ursinia-container {
        padding: 20px;
    }
    
    html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-heading {
        line-height: 30px;
    }
}

html div#$optin_uuid div#$optin_css_id.ursinia-container button, div#$optin_css_id.ursinia-container input {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input {
    border: 0;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-caption {
    margin-top: 12px;
    font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-form {
    margin-left: auto;
    margin-right: auto;
    margin-top: 30px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container input.ursinia-input,
html div#$optin_uuid div#$optin_css_id.ursinia-container textarea.ursinia-input {
    -webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container select.ursinia-input {
    padding: 12px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container textarea.ursinia-input {
    min-height: 80px;
}


html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input {
    display: block;
    width: 100%;
    margin-top: 8px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 12px;
    font-size: 16px;
    text-align: left;
    line-height: 16px;
    color: #555555;
    outline: none;
    font-weight: 400;
    border: 1px solid #ccc;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-submit,
html div#$optin_uuid div#$optin_css_id input[type=submit] {
    display: block;
    width: 100%;
    margin-top: 8px;
    margin-bottom: 8px;
    border: 0;
    background: #ea0c1a;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 14px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    text-transform: uppercase;
    cursor: pointer;
    font-weight: 600;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input.ursinia-clearfix .ursinia-form {
    background-color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container input:focus {
    background: #fff;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-clearfix:before, div#$optin_css_id.ursinia-container .ursinia-clearfix:after {
    display: table;
    content: " ";
}
html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-clearfix:after {
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .form-body {
    padding: 10px 10px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-finePrint {
    margin-top: 20px;
    font-size: 12px;
    line-height: 22px;
    color: #555555;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container div.mo-optin-error {
    display: none;
    color: #FF0000;
    font-size: 12px;
    text-align: center;
    width: 100%;
    padding-bottom: .5em;
}

html div#$optin_uuid div#$optin_css_id.mo-has-email.bannino-container .ursinia-input.ursinia-clearfix .ursinia-form {
    width: 100% !important;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input.ursinia-clearfix input.ursinia-submit {
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


html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input.ursinia-clearfix .ursinia-form,
html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-input.ursinia-clearfix input.ursinia-submit {
    padding: 12px;
    display: block;
    width: 100%;
    margin-bottom: 8px;
    border: 0;
    line-height: normal;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container  .ursinia-close-btn img {
    width: 20px;
    height: 20px;
    text-align: center;
    margin-top: 5px;
 }

html div#$optin_uuid div#$optin_css_id.ursinia-container .mo-mailchimp-interest-container .mo-mailchimp-interest-label {
    font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .ursinia-close-btn {
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
    text-align: left;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .select-field,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .radio-field,
html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .checkbox-field {
    margin-top: 8px;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-fields-wrapper .select-field {
    padding: 12px 0;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .mo-mailchimp-interest-container {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice-container label {
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id.ursinia-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice {
    margin-left: 0;
    margin-top: 5px;
    vertical-align: middle;
}

CSS;

    }
}