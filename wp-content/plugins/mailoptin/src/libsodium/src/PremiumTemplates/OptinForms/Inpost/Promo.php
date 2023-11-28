<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Promo extends AbstractOptinTheme
{
    public $optin_form_name = 'Promo';
    
    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#dddddd',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_dotted_border_color_default',
                    'value'       => '#2c303d',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Flat 20% Off Today!", 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                
                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#2c303d',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 40,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 37,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 32,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                // -- default for description sections -- //
                
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_style_default',
                    'value'       => 'italic',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 18,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 15,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 13,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                // -- default for fields sections -- //
                
                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name", 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email", 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_submit_button_default',
                    'value'       => __('Avail Now', 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#54c3a5',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#555555',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('Hurry up! Offer valid till stocks last.', 'mailoptin'),
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Montserrat',
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 12,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ],
                
                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 12,
                    'optin_class' => 'Promo',
                    'optin_type'  => 'inpost'
                ]
            ]
        );
        
        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 540;
            $config['height'] = 351;
            
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
        
        add_filter('mo_optin_form_customizer_defaults', [$this, 'add_dotted_border_line_attributes'], 10, 3);
        
        add_filter('mo_optin_form_customizer_design_controls', [$this, 'add_form_dotted_border_color_control'], 10, 4);
        
        add_filter('mo_optin_form_customizer_design_settings', [$this, 'add_form_dotted_border_color_settings'], 10, 2);
        
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
        return __('Dramatically maintain clicks-and-mortar solutions without functional errors.', 'mailoptin');
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
     * @param $defaults
     * @param $optin_campaign_type
     * @param $optin_campaign_class
     * @return mixed
     */
    public function add_dotted_border_line_attributes($defaults, $optin_campaign_type, $optin_campaign_class)
    {
        $defaults['form_dotted_border_color']     = apply_filters('mo_optin_form_promo_dotted_border_color_default', '#2c303d', $defaults, $optin_campaign_type, $optin_campaign_class);
        
        return $defaults;
    }
    
    public function add_form_dotted_border_color_control($page_control_args, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        if (apply_filters('mo_optin_form_enable_dotted_border_color', true)) {
            
            $page_control_args['form_dotted_border_color'] = new \WP_Customize_Color_Control(
                $wp_customize,
                $option_prefix . '[form_dotted_border_color]',
                apply_filters('mo_optin_form_customizer_form_dotted_border_args', array(
                        'label'    => __('Dotted Border Color', 'mailoptin'),
                        'section'  => $customizerClassInstance->design_section_id,
                        'settings' => $option_prefix . '[form_dotted_border_color]',
                        'priority' => 40,
                    )
                )
            );
        }
        
        return $page_control_args;
    }
    
    public function add_form_dotted_border_color_settings($design_settings_args, $_this)
    {
        $design_settings_args['form_dotted_border_color'] = array(
            'default'           => $_this->customizer_defaults['form_dotted_border_color'],
            'type'              => 'option',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        );
        
        return $design_settings_args;
    }
    
    /**
     * Template body.
     *
     * @return string
     */
    public function optin_form()
    {
        return <<<HTML
[mo-optin-form-wrapper class="promo-container"]
<div class="promo-border">
    [mo-optin-form-headline tag="div" class="promo-heading"]
    [mo-optin-form-description class="promo-caption"]
    [mo-optin-form-cta-button class="promo-cta"]
    
    [mo-optin-form-fields-wrapper class="promo-form"]
        [mo-optin-form-name-field class="promo-input"]
        [mo-optin-form-email-field class="promo-input"]
        [mo-optin-form-custom-fields class="promo-input"]
        [mo-mailchimp-interests class="promo-input"]
        [mo-optin-form-submit-button class="promo-submit"]
        [mo-optin-form-error class="promo-error"]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-note class="promo-finePrint"]
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
        $form_border_color = $this->get_customizer_value('form_dotted_border_color');
        
        return <<<CSS
html div#$optin_uuid div#$optin_css_id.promo-container * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.promo-container {
    background: #fff;
    width: 100%;
    padding: 20px;
    color: #555555;
    text-align: center;
    border: 1px solid #dddddd;
    margin: 10px auto;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-border {
    border-style: dashed;
    border-color: $form_border_color;
    border-width: 3px;
    padding: 30px;
}

html div#$optin_uuid div#$optin_css_id.promo-container button, div#$optin_css_id.promo-container input {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-input {
    border: 0;
    outline: none;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-border .promo-heading {
    font-size: 30px !important;
    line-height: 35px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-border .promo-caption {
    margin-top: 12px;
    font-size: 14px;
    line-height: 28px;
    font-style: italic;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-form {
    margin-left: 15px;
    margin-right: 15px;
    margin-top: 15px;
}

html div#$optin_uuid div#$optin_css_id.promo-container input.promo-input,
html div#$optin_uuid div#$optin_css_id.promo-container textarea.promo-input {
    -webkit-appearance: none;
}

html div#$optin_uuid div#$optin_css_id.promo-container select.promo-input {
    padding: 12px;
}

html div#$optin_uuid div#$optin_css_id.promo-container textarea.promo-input {
    min-height: 80px;
}


html div#$optin_uuid div#$optin_css_id.promo-container .promo-input {
    display: block;
    width: 100%;
    margin-top: 8px;
    padding: 11px 17px;
    font-size: 16px;
    line-height: 16px;
    color: #555555;
    outline: none;
    font-weight: 400;
    border: 2px solid #ccc;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-submit,
html div#$optin_uuid div#$optin_css_id .promo-top input[type=submit] {
    display: block;
    width: 100%;
    margin-top: 8px;
    margin-bottom: 8px;
    border: 0;
    background: #54c3a5;
    padding: 14px 20px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    cursor: pointer;
    font-weight: 600;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
}

html div#$optin_uuid.mo-optin-has-custom-field  div#$optin_css_id.promo-container .promo-input:not([type="radio"]):not([type="checkbox"]),
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.promo-container .promo-submit,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id input[type=submit],
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.promo-container.mo-has-email .promo-input:not([type="radio"]):not([type="checkbox"]),
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.promo-container.mo-has-email .promo-submit,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.promo-container.mo-has-email input[type=submit] {
    width: 100%;
}

html div#$optin_uuid div#$optin_css_id.promo-container.mo-has-email .promo-input:not([type="radio"]):not([type="checkbox"]) {
    width: 63.333%;
    display: inline-block;
    margin-right: 10px;
}

html div#$optin_uuid div#$optin_css_id.promo-container.mo-has-email .promo-submit,
html div#$optin_uuid div#$optin_css_id.promo-container.mo-has-email input[type=submit] {
    width: 30.333%;
    display: inline-block;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-cta {
    display: block;
    width: 100% !important;
    margin-top: 30px;
    margin-bottom: 8px;
    border: 2px solid #54c3a5;
    background: #54c3a5;
    padding: 12px 18px;
    font-size: 16px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    outline: none;
    cursor: pointer;
    font-weight: 600;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
}


html div#$optin_uuid div#$optin_css_id.promo-container .promo-clearfix:before, div#$optin_css_id.promo-container .promo-clearfix:after {
    display: table;
    content: " ";
}
html div#$optin_uuid div#$optin_css_id.promo-container .promo-clearfix:after {
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.promo-container .form-body {
    overflow: hidden;
    margin-top: 30px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-finePrint {
    margin-top: 15px;
    line-height: 22px;
}

html div#$optin_uuid div#$optin_css_id.promo-container div.mo-optin-error {
    display: none;
    color: #FF0000;
    font-size: 12px;
    text-align: center;
    width: 100%;
    padding-bottom: .5em;
}

html div#$optin_uuid div#$optin_css_id.mo-has-email.bannino-container .promo-input.promo-clearfix .promo-form {
    width: 100% !important;
}


html div#$optin_uuid div#$optin_css_id.promo-container  .promo-close-btn img {
    width: 20px;
    height: 20px;
    text-align: center;
    margin-top: 5px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .mo-mailchimp-interest-container .mo-mailchimp-interest-label {
    font-size: 16px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .promo-close-btn {
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

html div#$optin_uuid div#$optin_css_id.promo-container .mo-mailchimp-interest-container {
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.promo-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice-container label {
    font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id.promo-container .mo-mailchimp-interest-container .mo-mailchimp-interest-choice {
    margin-left: 0;
    margin-top: 5px;
    vertical-align: middle;
}

@media screen and (max-width: 966px) {
    html div#$optin_uuid div#$optin_css_id.promo-container .promo-input:not([type="radio"]):not([type="checkbox"]),
    html div#$optin_uuid div#$optin_css_id.promo-container .promo-submit,
    html div#$optin_uuid div#$optin_css_id input[type=submit] {
        width: 100% !important;
        display: block;
    }
    
    html div#$optin_uuid div#$optin_css_id.promo-container.mo-has-email .promo-input:not([type="radio"]):not([type="checkbox"]) {
        width: 100%;
        display: block;
    }
}

CSS;
    
    }
}