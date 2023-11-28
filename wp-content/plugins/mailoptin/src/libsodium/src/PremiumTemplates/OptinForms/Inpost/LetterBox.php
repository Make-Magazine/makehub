<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class LetterBox extends AbstractOptinTheme
{
    public $optin_form_name = 'Letter Box';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                [
                    'name' => 'mo_optin_branding_outside_form',
                    'value' => true,
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                // -- default for design sections -- //
                [
                    'name' => 'mo_optin_form_background_color_default',
                    'value' => '#9d58e2',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_border_color_default',
                    'value' => '#0e67e0',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_email_field_placeholder_default',
                    'value' => __("Enter your email here...", 'mailoptin'),
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                // -- default for headline sections -- //
                [
                    'name' => 'mo_optin_form_headline_default',
                    'value' => __("Don't Miss Our Update", 'mailoptin'),
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_headline_font_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_headline_font_default',
                    'value' => 'Raleway',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                // -- default for description sections -- //
                [
                    'name' => 'mo_optin_form_description_font_default',
                    'value' => 'Raleway',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_description_default',
                    'value' => $this->_description_content(),
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_description_font_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                // -- default for fields sections -- //
                [
                    'name' => 'mo_optin_form_name_field_color_default',
                    'value' => '#444444',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_name_field_background_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_email_field_color_default',
                    'value' => '#444444',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_email_field_background_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_background_default',
                    'value' => '#8000e2',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_font_default',
                    'value' => 'Raleway',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_name_field_font_default',
                    'value' => 'Garamond, Hoefler Text, serif',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_email_field_font_default',
                    'value' => 'Garamond, Hoefler Text, serif',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],


                // -- default for note sections -- //
                [
                    'name' => 'mo_optin_form_note_font_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_note_default',
                    'value' => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name' => 'mo_optin_form_note_font_default',
                    'value' => 'Raleway',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 28,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 24,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 20,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 21,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 16,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 14,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'LetterBox',
                    'optin_type'  => 'inpost'
                ]
            ]
        );

        // Add Raleway with 400, 700 font weight variant.
        add_filter('mo_optin_form_fonts_list', function ($webfont) {
            $webfont[] = "'Raleway:400,700'";
            return $webfont;
        });

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
        unset($settings['form_border_color']);

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
[mo-optin-form-wrapper class="letterBox_container"]
<div class="letterBox_inner">
    <div class="letterBox_copy">
        [mo-optin-form-headline class="letterBox_header" tag="div"]
        [mo-optin-form-description class="letterBox_description"]
    </div>
</div>
<div class="letterBox_form">
    [mo-optin-form-fields-wrapper]
    <div class="letterBox_field_wrapper">
        [mo-optin-form-name-field class="letterBox_form_field"]
        [mo-optin-form-email-field class="letterBox_form_field"]
        [mo-optin-form-custom-fields class="letterBox_form_field"]
        [mo-optin-form-note class="letterbox_note"]
    </div>
    [mo-mailchimp-interests]
    [mo-optin-form-error]
    [mo-optin-form-submit-button class="letterBox_submitButton"]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-cta-button class="letterBox_submitButton"]
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

html div#$optin_uuid div#$optin_css_id.letterBox_container *,
html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_submitButton,
html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_form_field
 {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
        
html div#$optin_uuid div#$optin_css_id.letterBox_container {
    background: #9d58e2;
    border-radius: 10px;
    text-align: center;
    margin: 10px auto;
    border: 0;
    width: 100%;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_copy .letterBox_header {
    color: #ffffff;
    padding: 0 20px;
    font-weight: 700;
    margin: 15px auto;
    line-height: normal;
    height: auto;
    display: block;
    border: 0;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_copy .letterBox_description {
    color: #ffffff;
    padding: 0 20px;
    font-weight: 400;
    line-height: normal;
    height: auto;
    display: block;
    border: 0;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_inner {
    padding: 10px 0 20px;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_form_field {
    width: 100%;
    max-width: 100%;
    padding: 15px;
    border: 0;
    border-radius: 5px;
    color: #444;
    font-size: 15px;
    margin: 0 0 10px;
    display: inline-block;
    line-height: normal;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_form_field:focus,
html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_submitButton:focus,
 {
   outline: 0
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_form_field:focus {
    background: #ececec;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .mo-optin-error {
     display: none; 
    color: #ff0000;
    text-align: center;
    padding: 5px;
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container input[type="submit"].letterBox_submitButton {
    width: 100%;
    border-radius: 0;
    border-bottom-right-radius: 10px;
    border-bottom-left-radius: 10px;
    border: 0;
    background: #8000e2;
    color: #ffffff;
    padding: 20px;
    font-size: 18px;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-appearance: button;
    cursor: pointer;
    font-weight: 700;
    display: inline-block;
    line-height: normal;
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_submitButton:hover{
    color: #ffffff;
    border-color: #3a3a3a;
    background-color: #3a3a3a;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_field_wrapper {
    padding: 0px 20px;
}

html div#$optin_uuid div#$optin_css_id.letterBox_container .letterbox_note {
     margin: 0 auto 10px;
     text-align: center;
     font-style: italic;
     display: block;
     border: 0;
     line-height: normal;
 }

@media only screen and (min-width: 1000px){

html div#$optin_uuid div#$optin_css_id.letterBox_container input[type="submit"].letterBox_submitButton{
    padding: 25px;
}
    
html div#$optin_uuid div#$optin_css_id.letterBox_container .letterBox_form_field {
    font-size: 20px;
}
}

html div#$optin_uuid div#$optin_css_id.letterBox_container textarea.mo-optin-form-custom-field.textarea-field {
    min-height: 80px;
}
CSS;

    }
}