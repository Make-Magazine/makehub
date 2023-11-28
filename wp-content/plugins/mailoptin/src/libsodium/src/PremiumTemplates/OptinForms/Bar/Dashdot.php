<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Bar;

use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Dashdot extends AbstractOptinTheme
{
    public $optin_form_name = 'Dashdot';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#34495e',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#34495e',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name..", 'mailoptin'),
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email..", 'mailoptin'),
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Grab your Free Copy of SEO eBook ($9.69)", 'mailoptin'),
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Vollkorn',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#666666',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#666666',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#666666',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Raleway',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_hide_note_default',
                    'value'       => true,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 18,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 18,
                    'optin_class' => 'Dashdot',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 16,
                    'optin_class' => 'Dashdot',
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

        add_action('mo_optin_customize_preview_init', function () {
            add_action('wp_footer', [$this, 'customizer_preview_js']);
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

    public function customizer_preview_js()
    {
        if(!\MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][headline_border_color]', function (value) {
                        value.bind(function (to) {
                            $('.dashdot-dotted').css('border-color', to);
                        });
                    });
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][headline_background_color]', function (value) {
                        value.bind(function (to) {
                            $('.dashdot-dotted').css('background-color', to);
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
        $settings['headline_border_color'] = array(
            'default'   => '#ffffff',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['headline_background_color'] = array(
            'default'   => '#aa3030',
            'type'      => 'option',
            'transport' => 'postMessage',
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
    public function customizer_headline_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $controls['headline_border_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[headline_border_color]',
            apply_filters('mo_optin_form_customizer_headline_border_color_args', array(
                    'label'    => __('Border Color', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[headline_border_color]',
                    'priority' => 50
                )
            )
        );

        $controls['headline_background_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[headline_background_color]',
            apply_filters('mo_optin_form_customizer_headline_background_color_args', array(
                    'label'    => __('Background Color', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[headline_background_color]',
                    'priority' => 50
                )
            )
        );

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
        return __('Receive top education news, lesson ideas, teaching tips and more!', 'mailoptin');
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
[mo-optin-form-wrapper class="dashdot-container"]
   <div class="dashdot-close-form">[mo-close-optin class="mo-close-optin"]x[/mo-close-optin]</div>
    <div class="dashdot-form-row dashdot-text-align">
        <div class="dashdot-header-wrap">
        <div class="dashdot-header-block">
            [mo-optin-form-headline tag="div" class="dashdot-dotted"]
        </div>
        </div>
        [mo-optin-form-fields-wrapper class="dashdot-form-wrapper"]
                [mo-optin-form-name-field class="dashdot-input-field"]
                [mo-optin-form-email-field class="dashdot-input-field"]
                [mo-optin-form-custom-fields class="dashdot-input-field"]
            [mo-optin-form-submit-button class="dashdot-submit-button"]
        [/mo-optin-form-fields-wrapper]
        [mo-optin-form-cta-button class="dashdot-submit-button"]
        [mo-mailchimp-interests]
        [mo-optin-form-note class="moDashdot_note"]
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
        $optin_css_id          = $this->optin_css_id;
        $optin_uuid            = $this->optin_campaign_uuid;
        $headline_border_color = $this->get_customizer_value('headline_border_color');
        if (empty($headline_border_color)) {
            $headline_border_color = '#ffffff';
        }

        $headline_background_color = $this->get_customizer_value('headline_background_color');
        if (empty($headline_background_color)) {
            $headline_background_color = '#aa3030';
        }

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.dashdot-container {
    border: 2px solid #34495e;
    background: #34495e;
    width: 100%;
    text-align: center;
    padding: 15px 0px;
}
html div#$optin_uuid div#$optin_css_id.dashdot-container * {
      box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-form-wrapper {
    display: inline-block;
    position: relative;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-header-wrap {
    display: inline-block;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-header-block {
    display: inline-block;
    padding: 0px;
    margin: 0px;
    font-weight: 700;
    font-size: 18px;
    color: white;
	padding-right: 0;
}


html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-form-wrapper {
    padding: 0px !important;
    margin: 0px !important;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container input.dashdot-submit-button {
    color: #FFF;
    background: #1abc9c;
    font-size: 13px;
    display: inline;
    line-height: normal;
    font-weight: 700;
    padding: 10px;
	border-radius: 2px;
	border: 0;
	
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-input-field {
    font-weight: 400;
    padding: 10px 10px;
    border: none;
    font-size: 13px;
    margin: 0;
    display: inline;
    outline: 0;
    line-height: normal;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    color: #8a97a0;
    -webkit-box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
    box-shadow: 0 1px 0 rgba(0,0,0,0.03) inset;
    width: 180px;
    border-radius: 2px;
    background-color:#ffffff;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-dotted {
    padding: 5px;
    margin: 10px;
    background: $headline_background_color;
    color: #fff;
    border: 1px dashed $headline_border_color;
    border-radius: 1px;
    font-weight: 700;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container div.mo-optin-error {
         display: none;
         color: #FF0000;
         text-align: center;
         width: 100%;
         font-size: 14px;
         padding-bottom: .5em;
     }
     
html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-close-form {
    cursor: pointer;
    display: block;
    font-size: 22px;
    font-weight: 400;
    text-decoration: none !important;
    font-family: Arial, sans-serif !important;
    margin-left: 5px;
    vertical-align: text-top;
    position: absolute;
    color: #ffffff;
    top: 24px;
    margin-top: -11px;
    right: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-close-form a.mo-close-optin {
    color: #fff;
    text-decoration: none;
}

html div#$optin_uuid div#$optin_css_id.dashdot-container .moDashdot_note {
		 font-style: italic;
		 line-height: 1.5;
		 text-align: center;
		 color: #ffffff;
		 margin-top: 5px;
	 }

@media only screen and (max-width: 768px) {
	html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-header-block{
	padding-right: 0px;
	}
}
@media only screen and (max-width: 572px) {
	html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-input-field{
	width: 33%;
	}
}
@media only screen and (max-width: 370px) {
	html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-input-field{
	width: 40%;
	margin-bottom: 10px;
	}
	
	html div#$optin_uuid div#$optin_css_id.dashdot-container .dashdot-close-form {
    top: 12px;
}
	
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dashdot-container .dashdot-input-field,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dashdot-container .dashdot-submit-button {
    width: 100% !important;
    max-width: 400px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dashdot-container .dashdot-input-field{
    margin-bottom: 5px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dashdot-container .dashdot-header-wrap{
	display: block;
}

CSS;

    }
}