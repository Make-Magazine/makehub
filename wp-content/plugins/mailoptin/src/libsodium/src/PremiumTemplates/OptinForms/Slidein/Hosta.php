<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Slidein;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class Hosta extends AbstractOptinTheme
{
    public $optin_form_name = 'Hosta';

    public $default_form_bg_image_partial;

    public function __construct($optin_campaign_id)
    {
        $this->default_form_bg_image_partial = MAILOPTIN_ASSETS_URL . 'images/optin-themes/hosta/bg-image.png';

        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#fec32d',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'  => 'mo_optin_form_background_image_default',
                    'value' => function () {
                        return $this->default_form_bg_image_partial;
                    }
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Upcoming events in New York!", 'mailoptin'),
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],


                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Pacifico',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 28,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 26,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 24,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                // -- default for description sections -- //

                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 16,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 13,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                // -- default for fields sections -- //

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Your Name", 'mailoptin'),
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Your Email", 'mailoptin'),
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#fec32d',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                // -- default for note sections -- //

                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 12,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 12,
                    'optin_class' => 'Hosta',
                    'optin_type'  => 'slidein'
                ]
            ]
        );

        add_filter('mo_optin_form_enable_form_background_image', '__return_true');

        add_filter('mo_optin_form_partial_default_background_image', function () {
            return $this->get_form_background_image_url();
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'block';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'left';
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
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('Sign up to get email alerts about upcoming events', 'mailoptin');
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
        $settings['note_position'] = [
            'default'   => 'after_form',
            'type'      => 'option',
            'transport' => 'refresh',
        ];

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
        $controls['note_position'] = apply_filters('mo_optin_form_customizer_note_position_args', array(
                'type'        => 'select',
                'description' => __(''),
                'choices'     => [
                    'before_form' => __('Before Form', 'mailoptin'),
                    'after_form'  => __('After Form', 'mailoptin')
                ],
                'label'       => __('Position', 'mailoptin'),
                'section'     => $customizerClassInstance->note_section_id,
                'settings'    => $option_prefix . '[note_position]',
                'priority'    => 15,
            )
        );

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
        $note_position    = OptinCampaignsRepository::get_customizer_value($this->optin_campaign_id, 'note_position', 'after_form');
        $before_form_note = $after_form_note = '';

        if ($note_position == 'after_form') $after_form_note = '[mo-optin-form-note class="hosta-finePrint"]';
        if ($note_position == 'before_form') $before_form_note = '[mo-optin-form-note class="hosta-finePrint"]';

        return <<<HTML
[mo-optin-form-wrapper class="hosta-container"]
[mo-close-optin class="hosta-optin-form-close"]x[/mo-close-optin]
[mo-optin-form-headline tag="div" class="hosta-heading"]
[mo-optin-form-description class="hosta-caption"]
[mo-optin-form-cta-button class="hosta-cta"]

$before_form_note
[mo-optin-form-fields-wrapper class="hosta-form"]
<div class="hostas-subcol">
    [mo-optin-form-name-field class="hosta-input"]
    [mo-optin-form-email-field class="hosta-input"]
    [mo-optin-form-custom-fields class="hosta-input"]
    [mo-mailchimp-interests class="hosta-input"]
    [mo-optin-form-submit-button class="hosta-submit"]
</div>
[/mo-optin-form-fields-wrapper]
$after_form_note
[mo-optin-form-error class="hosta-error"]
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

        $is_background_image = '';
        if ( ! empty($this->get_form_background_image_url())) {
            $is_background_image = "background-image: url('" . $this->get_form_background_image_url() . "')";
        }

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.hosta-container * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.hosta-container {
    background-color: #000000;
    $is_background_image;
    background-size: cover;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    width: 100%;
    color: #ffffff;
    text-align: center;
    padding: 20px;
    border: 3px solid;
}

html div#$optin_uuid div#$optin_css_id.hosta-container button, div#$optin_css_id.upcoming-container input {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input {
    border: 0;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-heading {
    font-size: 30px;
    line-height: 35px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-caption {
    margin-top: 20px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-form {
    margin-left: 15px;
    margin-right: 15px;
    margin-top: 30px;
    overflow: hidden;
}

html div#$optin_uuid div#$optin_css_id.hosta-container input.hosta-input,
html div#$optin_uuid div#$optin_css_id.hosta-container textarea.hosta-input {
    -webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.hosta-container select.hosta-input {
    padding: 12px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container textarea.hosta-input {
    min-height: 80px;
}

html div#$optin_uuid.mo-optin-has-custom-field  div#$optin_css_id.hosta-container .hosta-input:not([type="radio"]):not([type="checkbox"]),
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.hosta-container .hosta-submit,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id input[type=submit] {
    width: 100%;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-cta {
    display: inline;
    margin-top: 30px;
    border: 0;
    background: #fec32d;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    padding: 14px 20px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    cursor: pointer;
    font-weight: 600;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input:not([type="radio"]):not([type="checkbox"]),
html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-submit,
html div#$optin_uuid div#$optin_css_id input[type=submit] {
    width: 100%;
    display: inline-block;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hostas-subcol {
    margin-bottom: 10px;
    text-align: left;
}

html div#$optin_uuid div#$optin_css_id.hosta-container input.hosta-input,
html div#$optin_uuid div#$optin_css_id.hosta-container select.hosta-input {
    color: #555555;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input {
    display: block;
    margin-right: 3%;
    margin-top: 8px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 12px 18px;
    font-size: 14px;
    line-height: 15px;
    outline: none;
    font-weight: 400;
    border: 1px solid #ccc;
    text-align: left;
    color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-submit,
html div#$optin_uuid div#$optin_css_id .hosta-top input[type=submit] {
    display: inline;
    margin-top: 8px;
    margin-bottom: 8px;
    border: 0;
    background: #fec32d;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    padding: 12px 18px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    cursor: pointer;
    font-weight: 600;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input.hosta-clearfix .hosta-form {
    background-color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.hosta-container input:focus {
    background: #fff;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-clearfix:before, div#$optin_css_id.hosta-container .hosta-clearfix:after {
    display: table;
    content: " ";
}
html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-clearfix:after {
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .form-body {
    padding: 10px 10px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-finePrint {
    margin-top: 15px;
    margin-bottom: 15px;
    font-size: 12px;
    line-height: 22px;
    color: #ffffff;
}

html div#$optin_uuid div#$optin_css_id.hosta-container div.mo-optin-error {
    display: none;
    color: #FF0000;
    font-size: 12px;
    text-align: center;
    width: 100%;
    padding-bottom: .5em;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-optin-form-close {
    color: #fff;
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

html div#$optin_uuid div#$optin_css_id.mo-has-email.hosta-container .hosta-input.hosta-clearfix .hosta-form {
    width: 100% !important;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input.hosta-clearfix input.hosta-submit {
    display: block;
    width: 100%;
    margin-top: 8px;
    -webkit-appearance: none;
    border: 0;
    background: #FEC32D;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 12px;
    font-size: 16px;
    line-height: 16px;
    color: #fff;
    outline: none;
    cursor: pointer;
    font-weight: 600;
    white-space: nowrap;
    vertical-align: middle;
}


html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input.hosta-clearfix .hosta-form,
html div#$optin_uuid div#$optin_css_id.hosta-container .hosta-input.hosta-clearfix input.hosta-submit {
    padding: 12px;
    display: block;
    width: 100%;
    margin-bottom: 8px;
    border: 0;
    line-height: normal;
}

html div#$optin_uuid div#$optin_css_id.hosta-container  .hosta-close-btn img {
    width: 20px;
    height: 20px;
    text-align: center;
    margin-top: 5px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .mo-mailchimp-interest-container .mo-mailchimp-interest-label {
    font-size: 16px;
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

html div#$optin_uuid div#$optin_css_id.hosta-container .mo-mailchimp-interest-container {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice-container label {
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id.hosta-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice {
    margin-left: 0;
    margin-top: 5px;
    vertical-align: middle;
}
CSS;
    }
}