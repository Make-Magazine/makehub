<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Slidein;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Mimosa extends AbstractOptinTheme
{
    public $optin_form_name = 'Mimosa';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                [
                    'name'        => 'mo_optin_form_width_default',
                    'value'       => '350',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_background_color_default', // 1
                    'value'       => '#ffffff',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default', // 2
                    'value'       => '#dddddd',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default', // 3
                    'value'       => __("Enter your email here...", 'mailoptin'),
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name here...", 'mailoptin'),
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_headline_default', // 4
                    'value'       => __("Don't Miss Our Update", 'mailoptin'),
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_headline_font_color_default', // 5
                    'value'       => '#000000',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_description_default', // 6
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_color_default', // 6.5
                    'value'       => '#000000',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_name_field_color_default', // 7
                    'value'       => '#555555',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_email_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#1b1bea',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Raleway',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_modal_effects_default',
                    'value'       => 'MOslideInUp',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_slidein_position_default',
                    'value'       => 'bottom_right',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],
                [
                    // Add Raleway with 400, 700 font weight variant.
                    'name'  => 'mo_optin_form_fonts_list',
                    'value' => function ($webfont) {
                        $webfont[] = "'Raleway:400,700'";

                        return $webfont;
                    }
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#ea0c1a',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Palatino Linotype, Book Antiqua, serif',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'optin_form_slidein_position_default',
                    'value'       => 'bottom_right',
                    'optin_class' => 'Mimosa',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'  => 'mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style',
                    'value' => function () {
                        return 'inline';
                    }
                ],

                [
                    'name'  => 'mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment',
                    'value' => function () {
                        return 'center';
                    }
                ],

                [
                    'name'  => 'mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color',
                    'value' => function () {
                        return '#000000';
                    }
                ],

                [
                    'name'       => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'      => 15,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'      => 15,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'      => 15,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_description_font_size_desktop_default',
                    'value'      => 12,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_description_font_size_tablet_default',
                    'value'      => 12,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_description_font_size_mobile_default',
                    'value'      => 12,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_note_font_size_desktop_default',
                    'value'      => 10.5,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_note_font_size_tablet_default',
                    'value'      => 10.5,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'       => 'mo_optin_form_note_font_size_mobile_default',
                    'value'      => 10.5,
                    'optin_type' => 'slidein'
                ]
            ]
        );

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
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('Be the first to get exclusive content straight to your email.', 'mailoptin');
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
[mo-optin-form-wrapper class="mimosa-container"]
[mo-close-optin class="mimosa-optin-form-close"]x[/mo-close-optin]
<div class="mimosa-inner-wrapper">
    [mo-optin-form-headline class="mimosa-heading" tag="div"]
    [mo-optin-form-description class="mimosa-caption"]
    <div class="mimosa-form">
    [mo-optin-form-error]
    [mo-optin-form-fields-wrapper]
    [mo-optin-form-name-field class="mimosa-input"]
    [mo-optin-form-email-field class="mimosa-input"]
    [mo-optin-form-custom-fields class="mimosa-input"]
    [mo-mailchimp-interests]
    [mo-optin-form-submit-button class="mimosa-submit"]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-cta-button class="mimosa-submit"]
    </div>
    [mo-optin-form-note class="mimosa-finePrint"]
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
html div#$optin_uuid div#$optin_css_id.mimosa-container * {
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container {
  border: 3px solid #dddddd;
  background: #fff;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
  width: auto;
  padding: 0.5em;
  font-size: 15px;
  color: #000;
  text-align: center;
  -webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-optin-form-close {
    color: #000;
    display: inline;
    cursor: pointer;
    font-size: 1.5em;
    font-weight: 400;
    font-family: 'Open Sans', arial, sans-serif;
    float: right;
    text-decoration: none !important;
    vertical-align: text-top;
    margin-top: -0.5em;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-inner-wrapper {
  padding: 0.5em;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-heading {
  font-weight: 700;
  font-size: 1em;
  line-height: 1.5em;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-caption {
  margin: 1em 0;
  font-size: 0.8em
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-form {
  max-width: 100%;
  margin-left: 0.5em;
  margin-right: 0.5em;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mo-mailchimp-interest-label {
  font-size: 0.83em;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container span.mo-mailchimp-choice-label {
  font-size: 0.8em;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mo-optin-error {
display: none;
color: #c94c4c;
text-align: center;
padding: .2em;
margin: 0 0 -0.6em;
width: 100%;
font-size: 0.8em;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box; 
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-input {
  -webkit-box-sizing: border-box;
   -moz-box-sizing: border-box;
  box-sizing: border-box;
  display: block;
  width: 100%;
  margin-top: 0.5em;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  padding: 0.7em;
  font-size: 0.9em;
  line-height: 0.9em;
  text-align: left;
  color: #555;
  border: 1px solid #ccc;
  outline: none;
  background: 0;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-input:not(select) {
  -webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container input[type="submit"].mimosa-submit {
  display: block;
  width: 100%;
  margin-top: 0.5em;
  -webkit-appearance: none;
  border: 0;
  background: #EA0C1A;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  padding: 0.9em;
  font-size: 0.9em;
  line-height: 0.9em;
  text-align: center;
  color: #fff;
  outline: none;
  text-transform: uppercase;
  cursor: pointer;
  font-weight: 700;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container .mimosa-finePrint {
  margin-top: 1em;
  font-size: 0.7em;
  line-height: 1.5em;
  font-style: italic;
}

html div#$optin_uuid div#$optin_css_id.mimosa-container textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}
CSS;

    }
}