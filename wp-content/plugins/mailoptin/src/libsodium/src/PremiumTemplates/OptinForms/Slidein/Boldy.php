<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Slidein;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

class Boldy extends AbstractOptinTheme
{
    public $optin_form_name = 'Boldy';

    public $default_form_image_partial;

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([

                // -- default for design sections -- //
                [
                    'name' => 'mo_optin_form_background_color_default',
                    'value' => '#2d3144',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_border_color_default',
                    'value' => '#2d3144',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                // -- default for headline sections -- //
                [
                    'name' => 'mo_optin_form_headline_default',
                    'value' => __("Sport News Around The World", 'mailoptin'),
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_headline_font_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_headline_font_default',
                    'value' => 'Lato',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                // -- default for description sections -- //
                [
                    'name' => 'mo_optin_form_description_font_default',
                    'value' => 'Lato',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_description_default',
                    'value' => __('Subscribe To Our Newsletter'),
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_description_font_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                // -- default for fields sections -- //
                [
                    'name' => 'mo_optin_form_name_field_color_default',
                    'value' => '#181818',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_name_field_placeholder_default',
                    'value' => __("Enter your name...", 'mailoptin'),
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_name_field_background_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],
                [
                    'name' => 'mo_optin_form_email_field_color_default',
                    'value' => '#181818',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_email_field_background_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_email_field_placeholder_default',
                    'value' => __("Enter your email...", 'mailoptin'),
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_background_default',
                    'value' => '#13aff0',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_font_default',
                    'value' => 'Lato',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_name_field_font_default',
                    'value' => 'Franklin Gothic Medium, sans-serif',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_email_field_font_default',
                    'value' => 'Franklin Gothic Medium, sans-serif',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                // -- default for note sections -- //
                [
                    'name' => 'mo_optin_form_note_font_color_default',
                    'value' => '#8f8888',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_note_default',
                    'value' => $this->_note_content(),
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_note_font_default',
                    'value' => 'Lato',
                    'optin_class' => 'Boldy',
                    'optin_type' => 'slidein'
                ],

                [
                    'name' => 'mo_optin_form_modal_effects_default',
                    'value' => 'MOslideInUp',
                    'optin_class' => 'LetterBox',
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 20,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 20,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 16,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 16,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 16,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 14,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_type' => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_type' => 'slidein'
                ]
            ]
        );

        add_filter('mo_optin_form_enable_hide_form_image', '__return_true');
        add_filter('mo_optin_form_enable_form_image', '__return_true');

        $this->default_form_image_partial = MAILOPTIN_PREMIUMTEMPLATES_ASSETS_URL . 'optin/boldy-img.png';

        add_filter('mo_optin_form_partial_default_image', function () {
            return $this->default_form_image_partial;
        });

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width'] = 700;
            $config['height'] = 190;

            return $config;
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
        $settings['note_position'] = [
            'default' => 'before_form',
            'type' => 'option',
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
                'type' => 'select',
                'description' => __(''),
                'choices' => [
                    'before_form' => __('Before Form', 'mailoptin'),
                    'after_form' => __('After Form', 'mailoptin')
                ],
                'label' => __('Position', 'mailoptin'),
                'section' => $customizerClassInstance->note_section_id,
                'settings' => $option_prefix . '[note_position]',
                'priority' => 15,
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
    private function _note_content()
    {
        return __('Join our subscribers who get content directly to their inbox.', 'mailoptin');
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
        $note_position = OptinCampaignsRepository::get_customizer_value($this->optin_campaign_id, 'note_position', 'before_form');
        $before_form_note = $after_form_note = '';

        if ($note_position == 'after_form') $after_form_note = '[mo-optin-form-note class="boldy_note"]';
        if ($note_position == 'before_form') $before_form_note = '[mo-optin-form-note class="boldy_note"]';

        return <<<HTML
        [mo-optin-form-wrapper class="boldy_container"]
            <div class="boldy_inner">
            [mo-close-optin class="boldy_closeIcon"]x[/mo-close-optin]
                [mo-optin-form-image default="$optin_default_image" wrapper_enabled="true" wrapper_class="boldy_featureImage boldy_isresponsive"]
                <div class="boldy_main">
                    [mo-optin-form-headline tag="div" class="boldy_header"]
                    [mo-optin-form-description class="boldy_description"]
                    $before_form_note
                        <div class="boldy_main-form">
                        [mo-optin-form-fields-wrapper]
                            [mo-optin-form-name-field class="boldy_input"]
                            [mo-optin-form-email-field class="boldy_input"]
                            [mo-optin-form-custom-fields class="boldy_input"]
                            [mo-mailchimp-interests]
                            [mo-optin-form-submit-button class="boldy_submitButton"]
                            [/mo-optin-form-fields-wrapper]
                        [mo-optin-form-cta-button class="boldy_submitButton"]
                        </div>
                        $after_form_note
		            [mo-optin-form-error class="boldy_optin_error"]
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
        $optin_uuid = $this->optin_campaign_uuid;

        return <<<CSS
        /* close button is pretty big. adjust space to left to accommodate it.*/
      html div#$optin_uuid {
        margin-right: 10px !important;
      }

      html div#$optin_uuid div#$optin_css_id.boldy_container {
            margin: 0 auto;
            font-size: 16px;
            background: #2d3144;
            border: 2px solid #2d3144;
            width: 100%;
        }

      html div#$optin_uuid div#$optin_css_id.boldy_container * {
            font-size: 16px;
        }

       html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_isresponsive img {
            display: block;
            width: 100%;
            height: auto;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_inner {
            margin: 0;
            position: relative;
        }

       html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_header,
       html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_description,
       html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_note{
            color: #fff;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_header {
            font-weight: 700;
            display: block;
            border: 0;
            line-height: normal;
        }
        
     html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_closeIcon {
    position: absolute;
    width: 40px;
    height: 40px;
    top: -18px;
    right: -18px;
    text-align: center;
    font-size: 30px;
    line-height: .45;
    font-weight: 700;
    text-decoration: none!important;
    font-family: Helvetica,"Helvetic Neue",Arial,sans-serif;
    z-index: 1050;
    padding: 11px 12px;
    border-radius: 100px;
    background-color:#2d3144;
    color:#ffffff;
}

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main {
            text-align: center;
            padding: 10px;
            background: inherit;
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 5px;
            margin: 0;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_description {
            padding-bottom: 10px;
            padding-top: 10px;
            font-weight: 400;
            display: block;
            border: 0;
            line-height: normal;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main .boldy_note {
            color: #8f8888;
            display: block;
            border: 0;
            line-height: normal;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            border: 0px;
            outline: none;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_input:focus,
         html div#$optin_uuid div#$optin_css_id.boldy_container input.boldy_submitButton:focus {
            outline: 0
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_main-form {
            padding: 20px;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container input.boldy_submitButton {
            font-size: 15px;
            text-transform: uppercase;
            padding: 10px;
            border-radius: 4px;
            border: 0;
            font-weight: 700;
            background: #13aff0;
            color: #fff;
            cursor: pointer;
            outline: none;
            letter-spacing: 1.5px;
            width: 100%;
        }

        html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_optin_error {
            display:none;
            padding: 5px;
            font-size: 14px;
            color: #ff5151 !important;
            font-style: italic;
        }

       html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_featureImage.boldy_isresponsive img {
            border-top-right-radius: 5px;
            border-top-left-radius: 5px;
        }

@media screen and (max-width: 480px) {
    html div#$optin_uuid div#$optin_css_id.boldy_container .boldy_closeIcon {
    position: absolute;
    width: 35px;
    height: 35px;
    top: -18px;
    right: -18px;
    text-align: center;
    font-size: 25px;
    line-height: .45;
    margin: 10px;
    font-weight: 700;
    text-decoration: none!important;
    font-family: Helvetica,"Helvetic Neue",Arial,sans-serif;
    z-index: 1050;
    padding: 10px 10px;
    border-radius: 100px;
    background-color: #2d3144;
    color: #ffffff;
    }
}
        
 html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.boldy_container textarea.mo-optin-form-custom-field.textarea-field {
       min-height: 80px;
}
CSS;

    }
}