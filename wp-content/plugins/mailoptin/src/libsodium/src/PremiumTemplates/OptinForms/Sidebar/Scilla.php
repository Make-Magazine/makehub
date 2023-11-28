<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Sidebar;


use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Scilla extends AbstractOptinTheme
{
    public $optin_form_name = 'Scilla';

    public $default_form_image_partial;

    public function __construct($optin_campaign_id)
    {

        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_width_default',
                    'value'       => '500',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#246bb5',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Stay Informed", 'mailoptin'),
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#ededed',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 18,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 18,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 18,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#424242',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#424242',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#e37c5d',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 24,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 24,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 20,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 16,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 16,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 14,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Scilla',
                    'optin_type'  => 'sidebar'
                ],
            ]
        );

        $this->default_form_image_partial = MAILOPTIN_ASSETS_URL . 'images/optin-themes/scilla/style-2.png';


        add_filter('mo_optin_form_enable_hide_form_image', '__return_true');
        add_filter('mo_optin_form_enable_form_image', '__return_true');

        add_filter('mo_optin_form_partial_default_image', function () {
            return $this->default_form_image_partial;
        });

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 960;
            $config['height'] = 440;

            return $config;
        });

        add_action('mo_optin_customize_preview_init', function () {
            add_action('wp_footer', [$this, 'customizer_preview_js']);
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

        add_filter('mailoptin_optin_customizer_form_background_color_args', function ($config) {
            $config['label'] = __('Background Color I', 'mailoptin');

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
        $settings['form_background_color2'] = array(
            'default'           => '#424242',
            'type'              => 'option',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        );

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
        $controls ['form_background_color2'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[form_background_color2]',
            array(
                'label'    => __('Background Color II', 'mailoptin'),
                'section'  => $customizerClassInstance->design_section_id,
                'settings' => $option_prefix . '[form_background_color2]',
                'priority' => 22,
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
        return __('Subscribe to my newsletter to receive the latest wordpress news and blog updates.', 'mailoptin');
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

    public function customizer_preview_js()
    {
        if(!\MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (value) {
                        value.bind(function (to) {
                            $('.mo-optin-form-name-field').toggle(!to);
                        });
                    });

                });
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
        $optin_default_image = $this->default_form_image_partial;

        return <<<HTML
[mo-optin-form-wrapper class="scilla-container"]
	<div class="scilla-inner-wrap">
		<div class="scilla-img-is-responsive mo-optin-form-image-wrapper">
            [mo-optin-form-image default="$optin_default_image"]
            [mo-optin-form-headline class="scilla-headline" tag="div"]
            [mo-optin-form-description class="scilla-description"]
            [mo-optin-form-cta-button class="scilla-field scilla-submit-btn"]
		</div>
    </div>
    [mo-optin-form-fields-wrapper class="scilla-inner-wrap_fields"]
        [mo-optin-form-name-field class="scilla-field"]
        [mo-optin-form-email-field class="scilla-field"]
        [mo-optin-form-custom-fields class="scilla-field"]                     
        [mo-mailchimp-interests] 
        [mo-optin-form-submit-button class="scilla-field scilla-submit-btn"]
        [mo-optin-form-note class="scilla_note"]
        [mo-optin-form-error]
    [/mo-optin-form-fields-wrapper]
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

        $background_color_2 = $this->get_customizer_value('form_background_color2', '#424242');

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.scilla-container * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.scilla-container {
	width: 100%;
	margin: 0 auto;
	text-align: center;
}

html div#$optin_uuid div#$optin_css_id .scilla-img-is-responsive img {
	width: 100%;
	height: auto;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-error  {
	margin-top: 20px;
    margin-bottom: 20px;
    display: none;
    color: #e74c3c;
	font-style: italic;
}

html div#$optin_uuid div#$optin_css_id .scilla-inner-wrap {
	text-align: center;
	padding: 20px;
}

html div#$optin_uuid div#$optin_css_id .scilla-img-is-responsive .scilla-description {
	padding: 10px 0;
	font-size: 18px;
	color: #ededed;
	line-height: 1.4;
} 
	
html div#$optin_uuid div#$optin_css_id .scilla-headline {
	text-transform: capitalize;
	font-weight: 700;
	font-size: 21px;
	color: #fff;
	margin: 5px 0;
}
	
html div#$optin_uuid div#$optin_css_id .scilla-field {
	display: block;
	width: 100%;
	padding: 15px;
	margin-bottom: 10px;
	border: 0px;
	font-size: 15px;
	border-radius: 5px;
	font-weight: 700;
}
	
html div#$optin_uuid div#$optin_css_id .scilla-inner-wrap_fields {
	background: $background_color_2;
	padding: 20px;
}
	
	
html div#$optin_uuid div#$optin_css_id .scilla-field.scilla-submit-btn {
	background-color: #e37c5d;
	color: #fff;
}
	
html div#$optin_uuid div#$optin_css_id.scilla-container .scilla-field.scilla-submit-btn {
	cursor: pointer;
}

html div#$optin_uuid div#$optin_css_id.scilla-container .scilla_note,
html div#$optin_uuid div#$optin_css_id.scilla-container .scilla_note * {
                     margin-top: 5px;
                     text-align: center;
                     font-style: italic;
                     border: 0;
                     line-height: normal;
                 }
CSS;

    }
}