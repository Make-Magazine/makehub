<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Bar;

use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Muscari extends AbstractOptinTheme
{
    public $optin_form_name = 'Muscari';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name...", 'mailoptin'),
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email...", 'mailoptin'),
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Grab your Free Copy of SEO eBook ($9.69)", 'mailoptin'),
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#dddddd',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#dddddd',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#3abaab',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],


                [
                    'name'        => 'mo_optin_form_hide_note_default',
                    'value'       => true,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 15,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 15,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 15,
                    'optin_class' => 'Muscari',
                    'optin_type'  => 'bar'
                ]
            ]
        );

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'inline';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'center';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#ffffff';
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
        $tag_start = '<div class="muscari-form-group">';
        $tag_end = '</div>';

        return <<<HTML
[mo-optin-form-wrapper class="muscari-container"]
<div role="form">
    <div class="muscari-form-row muscari-text-align">
        <div class="muscari-form-title-wrap">
            [mo-optin-form-headline tag="div" class="muscari-title-wrap-content"]
        <div class="muscari-close-form">[mo-close-optin class="mo-close-optin"]x[/mo-close-optin]</div>
        </div>
        [mo-optin-form-fields-wrapper tag="span"]
        <div class="muscari-form-group muscari-name-field mo-optin-form-name-field">
           [mo-optin-form-name-field class="muscari-form-control"]
        </div>
        <div class="muscari-form-group muscari-email-field mo-optin-form-email-field">
            [mo-optin-form-email-field class="muscari-form-control"]
        </div>
        
        [mo-optin-form-custom-fields class="muscari-form-control" tag_start='$tag_start' tag_end='$tag_end']
    
        <div class="muscari-form-group muscari-button-group">
            [mo-optin-form-submit-button class="muscari-button"]
        </div>
        [mo-mailchimp-interests]
        [/mo-optin-form-fields-wrapper]
        <div class="muscari-form-group muscari-button-group" style="padding-bottom: 5px;">
        [mo-optin-form-cta-button class="muscari-button"]
        </div>
        [mo-optin-form-note class="moMuscari_note"]
        [mo-optin-form-error]
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
html div#$optin_uuid div#$optin_css_id.muscari-container,
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-group,
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-control {
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.muscari-container{
    background-color: #000000;
    border: 3px solid #000000;
    color:#fff;
    padding-left: 15px;
    padding-right: 35px;
    padding-bottom: 5px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale;
    width: 100%;
    font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form{
    cursor: pointer;
    display: block;
    font-size: 22px;
    font-weight: 400;
    text-decoration: none !important;
    font-family: Arial, sans-serif !important;
    margin-left: 5px;
    vertical-align: text-top;
    position: absolute;
    top: 24px;
    margin-top: -11px;
    right: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form,
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form a.mo-close-optin {
    color: #fff;
    text-decoration: none;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form:hover {
    text-shadow: 0 0 2px #fff
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-title-wrap{
    padding: 15px;
    width:100%;
    text-align: center;
    font-size: 15px;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-group {
    margin-bottom: 15px;
    padding-left: 15px;
    padding-right: 15px;
}
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-control{
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    background-image: none;
    outline: none !important;
    border:none;
}
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-row{
    margin-right: auto;
    margin-left: auto;
}
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-row:after{
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button{
    outline: none !important;
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 16px;
    line-height: 1.42857143;
    text-align: center;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    font-weight: normal;
    width: 100%;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-email-field,.muscari-name-field,
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button-group {
    width: 100%;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.muscari-container div.mo-optin-error {
         display: none;
         color: #FF0000;
         text-align: center;
         width: 100%;
         padding-bottom: .5em;
         font-size: 14px;
     }
     
     html div#$optin_uuid div#$optin_css_id.muscari-container .moMuscari_note {
		 font-style: italic;
		 font-size: 14px;
		 text-align: center;
		 color: #fff;
	 }

/* Media queries for responsiveness */
/* Tablet */
@media (min-width: 768px) {
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-email-field,
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-name-field{
        width:28.19%;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button-group{
        width: auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form{
        top: 50%;
    }
}

/* Tablet onwards with only email field */
@media (min-width: 992px) {
    html div#$optin_uuid div#$optin_css_id.mo-has-email.muscari-container .muscari-form-title-wrap{
        width: auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.mo-has-email.muscari-container .muscari-email-field,
    html div#$optin_uuid div#$optin_css_id.mo-has-email.muscari-container .muscari-name-field,
    html div#$optin_uuid div#$optin_css_id.mo-has-email.muscari-container .muscari-button-group{
        width:auto;
        display: inline-block;
    }
}

/* Medium device */
@media (min-width: 992px) {
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-text-align{
        text-align: center;
    }
}

/* Large screens & TVs */
@media (min-width: 1200px) {
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-title-wrap{
        width: auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-email-field,
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-name-field,
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button-group{
        width:auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-close-form{
        top: 24px;
    }
    html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-group{
        margin-bottom: -5px;
    }
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-control{
    background: transparent;
    color: #dddddd;
    border-radius: 0;
    border-bottom: 2px solid #cccccc;
}

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-form-control:hover{
    outline: 0;
    border-bottom-color: #3abaab;
    -webkit-transition: border-bottom-color 0.15s;
    -moz-transition: border-bottom-color 0.15s;
    -ms-transition: border-bottom-color 0.15s;
    -o-transition: border-bottom-color 0.15s;
    transition: border-bottom-color 0.15s;
}
html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button{
    background: #3abaab;
    color:#fff;
}

html div#$optin_uuid.mo-cta-button-display .muscari-button-group,
html div#$optin_uuid.mo-cta-button-display .muscari-form-title-wrap {
    display: inline-block;
    width: auto;
    margin: auto;
}

html div#$optin_uuid.mo-cta-button-display .muscari-text-align{
        text-align: center;
    }

html div#$optin_uuid div#$optin_css_id.muscari-container .muscari-button:hover{
    background: #2fa699;
}

html div#$optin_uuid div#$optin_css_id.muscari-container ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
    color: #cccccc;
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.muscari-container ::-moz-placeholder { /* Firefox 19+ */
    color: #cccccc;
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.muscari-container :-ms-input-placeholder { /* IE 10+ */
    color: #cccccc;
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.muscari-container :-moz-placeholder { /* Firefox 18- */
    color: #cccccc;
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.muscari-container .muscari-form-group {
     width: 100%;
    display:block;
    max-width: 400px;
    margin:  auto;
    margin-bottom:10px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.muscari-container textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}
CSS;

    }
}