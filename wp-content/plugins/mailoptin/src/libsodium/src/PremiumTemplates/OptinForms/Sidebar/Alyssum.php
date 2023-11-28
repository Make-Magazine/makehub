<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Sidebar;

use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Alyssum extends AbstractOptinTheme
{
    public $optin_form_name = 'Alyssum';

    public $default_form_image_partial;

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_width_default',
                    'value'       => '450',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#e0e0e5',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Full Name", 'mailoptin'),
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("E-mail Address", 'mailoptin'),
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("<strong>Keep an eye</strong> on what we are doing", 'mailoptin'),
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 27,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 21,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 21,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],


                // -- default for fields sections -- //

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#6355b9',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_hide_note_default',
                    'value'       => true,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#6d7680',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#6d7680',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#0073b7',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],


                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],


                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Open+Sans',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],


                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'sidebar'
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
            return '#444444';
        });

        $this->default_form_image_partial = MAILOPTIN_ASSETS_URL . 'images/optin-themes/alyssum/intro-icon.png';


        add_filter('mo_optin_form_enable_hide_form_image', '__return_true');
        add_filter('mo_optin_form_enable_form_image', '__return_true');

        add_filter('mo_optin_form_partial_default_image', function () {
            return $this->default_form_image_partial;
        });

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 54;
            $config['height'] = 50;

            return $config;
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
     * @param Customizer $customizerClassInstance
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
     * @param Customizer $customizerClassInstance
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
     * @param Customizer $customizerClassInstance
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
     * @param Customizer $customizerClassInstance
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
        $fields_settings['hide_name_field']['transport'] = 'refresh';

        return $fields_settings;
    }

    /**
     * @param array $fields_controls
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
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
     * @param Customizer $customizerClassInstance
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
     * @param Customizer $customizerClassInstance
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
        $optin_default_image = $this->default_form_image_partial;

        return <<<HTML
[mo-optin-form-wrapper class="alyssum"]
    <div class="alyssum_inner">
        <div class="alyssum-intro-section">
            [mo-optin-form-image default="$optin_default_image" wrapper_enabled="true" wrapper_tag="span" wrapper_class="alyssum-img-icon"]
            [mo-optin-form-headline tag="div" class="alyssum-headline"]
            [mo-optin-form-description]
            [mo-optin-form-cta-button class="alyssum_subscibe_btn"]
        </div>
        [mo-optin-form-fields-wrapper class="alyssum-input"]     
            [mo-optin-form-name-field class="alyssum_input_field"]
            [mo-optin-form-email-field class="alyssum_input_field"]
            [mo-optin-form-custom-fields class="alyssum_input_field"]                     
            [mo-mailchimp-interests] 
            [mo-optin-form-submit-button class="alyssum_subscibe_btn"]
        [/mo-optin-form-fields-wrapper]
        [mo-optin-form-note class="alyssum_note"]
        [mo-optin-form-error]
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
html div#$optin_uuid div#$optin_css_id.alyssum * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.alyssum {
	background-color: #fff;
	border-radius: 10px;
	margin: 10px auto;
	padding: 20px 20px 40px 20px;
	text-align: center;
	position: relative;
	border: 2px solid #fff;
	width: 100%;
}
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum-img-icon {
	padding-right: 8px;
}
	
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum-headline {
	color: #444;
	vertical-align: top;
	padding-top: 10px;
	font-weight: 400;
	display: inline-block;
}
	
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_subscibe_btn {
	padding: 20px 10px;
	border-radius: 30px;
	font-size: 15px;
	max-width: 100%;
    width: 100%;
	border: 0;
	background: #6355b9;
	color: #fff;
}

html div#$optin_uuid div#$optin_css_id.alyssum .mo-optin-error {
    padding: 7px;
    font-size: 14px;
    color: #e74c3c;
    margin-top: 10px;
    display: none;
}

html div#$optin_uuid div#$optin_css_id.alyssum .mo-optin-form-description{
    margin-top: 20px;
    margin-bottom: 20px;
}

html div#$optin_uuid div#$optin_css_id.alyssum .mo-optin-form-cta-button{
    padding: 16px 40px;
    border-radius: 30px;
    border: 0;
    font-weight: 700;
    margin: 20px;
}
	
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_input_field {
	width: 100%;
	border-radius: 30px;
    margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 10px;
    font-size: 17px;
	background: #fff;
	text-align: left;
	font-weight: 700;
	border: 1px solid #e0e0e5;
    padding: 20px 20px 20px 30px;
}

html div#$optin_uuid div#$optin_css_id.alyssum .alyssum-input {
	margin: 20px 20px 0;
}

html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_input_field:focus {
	background: #ecf0f1;
	border: 1px solid #ecf0f1;
	outline: 0;
	box-shadow: none;
	transition: linear.2s;
}

html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_subscibe_btn {
	text-transform: uppercase;
	font-weight: 700;
}
	
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_subscibe_btn {
	cursor: pointer;
}

html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_note,
html div#$optin_uuid div#$optin_css_id.alyssum .alyssum_note * {
                     margin-top: 10px;
                     text-align: center;
                     font-style: italic;
                     border: 0;
                     line-height: normal;
                 }

    
    html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id .alyssum_input_field,
    html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id .alyssum_subscibe_btn,
    html div#$optin_uuid div#$optin_css_id.mo-has-name-email .alyssum_input_field,
    html div#$optin_uuid div#$optin_css_id.mo-has-name-email .alyssum_subscibe_btn {
        max-width: 100%;
    }

    
    html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id .alyssum_input_field,
    html div#$optin_uuid div#$optin_css_id.mo-has-name-email .alyssum_input_field {
        display: block;
        margin-bottom: 10px;
    }

CSS;

    }
}