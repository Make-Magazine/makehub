<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Slidein;


use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Tinymce_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Liatris extends AbstractOptinTheme
{
    public $optin_form_name = 'Liatris';

    public $default_form_image_partial;

    public function __construct($optin_campaign_id)
    {

        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_modal_effects_default',
                    'value'       => 'MOslideInUp',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name", 'mailoptin'),
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email", 'mailoptin'),
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Alicia Bakery", 'mailoptin'),
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 18,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 18,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 16,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 16,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 16,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 14,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_hide_name_field_default',
                    'value'       => true,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#444444',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#5d6d7b',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                // -- default for note sections -- //
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#90a1af',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('Your privacy is guranteed.', 'mailoptin'),
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Roboto',
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 12,
                    'optin_class' => 'Liatris',
                    'optin_type'  => 'slidein'
                ]
            ]
        );


        $this->default_form_image_partial = MAILOPTIN_ASSETS_URL . 'images/optin-themes/liatris/profile.jpg';

        add_filter('mo_optin_form_enable_form_image', '__return_true');

        add_filter('mo_optin_form_partial_default_image', function () {
            return $this->default_form_image_partial;
        });

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width']  = 400;
            $config['height'] = 400;

            return $config;
        });

        add_action('mo_optin_customize_preview_init', function () {
            add_action('wp_footer', [$this, 'customizer_preview_js']);
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', function () {
            return 'inline';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', function () {
            return 'center';
        });

        add_filter('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', function () {
            return '#000000';
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
     * Default description content.
     *
     * @return string
     */
    private function _description_content()
    {
        return __('This company is awesome. I love receiving emails every month that can help my business grow and prosper. If you want to keep track of business news, subscribing to the newsletter is a must!', 'mailoptin');
    }

    /**
     * @param mixed $settings
     * @param CustomizerSettings $CustomizerSettingsInstance
     *
     * @return mixed
     */
    public function customizer_headline_settings($settings, $CustomizerSettingsInstance)
    {
        $settings['mini_headline'] = array(
            'default'           => __("CEO - A really good hub", 'mailoptin'),
            'type'              => 'option',
            'transport'         => 'postMessage',
            'sanitize_callback' => array($CustomizerSettingsInstance, '_remove_paragraph_from_headline'),
        );

        $settings['hide_mini_headline'] = array(
            'default'   => false,
            'type'      => 'option',
            'transport' => 'postMessage'
        );

        $settings['mini_headline_font_color'] = array(
            'default'   => '#4fa9db',
            'type'      => 'option',
            'transport' => 'postMessage'
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
        add_filter('mailoptin_tinymce_customizer_control_count', function ($count) {
            return ++$count;
        });

        $controls['mini_headline'] = new WP_Customize_Tinymce_Control(
            $wp_customize,
            $option_prefix . '[mini_headline]',
            apply_filters('mo_optin_form_customizer_mini_headline_args', array(
                    'label'         => __('Mini Headline', 'mailoptin'),
                    'section'       => $customizerClassInstance->headline_section_id,
                    'settings'      => $option_prefix . '[mini_headline]',
                    'editor_id'     => 'mini_headline',
                    'quicktags'     => true,
                    'editor_height' => 50,
                    'priority'      => 4
                )
            )
        );

        $controls['hide_mini_headline'] = new WP_Customize_Toggle_Control(
            $wp_customize,
            $option_prefix . '[hide_mini_headline]',
            apply_filters('mo_optin_form_customizer_hide_mini_headline_args', array(
                    'label'    => __('Hide Mini Headline', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[hide_mini_headline]',
                    'type'     => 'light',
                    'priority' => 2,
                )
            )
        );

        $controls['mini_headline_font_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[mini_headline_font_color]',
            apply_filters('mo_optin_form_customizer_headline_mini_headline_font_color_args', array(
                    'label'    => __('Mini Headline Color', 'mailoptin'),
                    'section'  => $customizerClassInstance->headline_section_id,
                    'settings' => $option_prefix . '[mini_headline_font_color]',
                    'priority' => 3
                )
            )
        );

        return $controls;
    }

    public function customizer_preview_js()
    {
        if ( ! \MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][mini_headline_font_color]', function (value) {
                        value.bind(function (to) {
                            $('.liatris_mini_headline').css('color', to);
                        });
                    });

                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_mini_headline]', function (value) {
                        value.bind(function (to) {
                            $('.liatris_mini_headline').toggle(!to);
                        });
                    });

                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][hide_name_field]', function (value) {
                        value.bind(function (to) {
                            $('.mo-optin-form-name-field').toggle(!to);
                        });
                    });

                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][mini_headline]', function (value) {
                        value.bind(function (to) {
                            $('.liatris_mini_headline').html(to);
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

        $mini_header = $this->get_customizer_value('mini_headline', __("CMO - A really good agency", 'mailoptin'));

        return <<<HTML
    [mo-optin-form-wrapper class="liatris-container"]
		<div class="liatris_inner">
			<div class="liatris_top-section">
                 [mo-optin-form-image default="$optin_default_image" wrapper_enabled="true" wrapper_class="liatris_attendant_image liatris-img-is-responsive"]
                [mo-close-optin class="liatris-optin-form-close"]x[/mo-close-optin]
				<div class="liatris_top-section_inner">
                    [mo-optin-form-headline tag="div" class="liatris_headline"]
                    <div class="liatris_mini_headline">$mini_header</div>
				</div>
			</div>
			<div class="liatris_middle-section">
                [mo-optin-form-description class="liatris_description"]
                [mo-optin-form-cta-button class="liatris_cta"]
			</div>
			<div class="liatris_-bottom">
                [mo-optin-form-fields-wrapper]
                    [mo-optin-form-error]                     
				    <div class="liatris_fields_wrap">
                        [mo-optin-form-name-field class="liatris_fields liatris_field"]
                        [mo-optin-form-email-field class="liatris_fields liatris_field"]
                        [mo-optin-form-custom-fields class="liatris_fields liatris_field"]
                        [mo-optin-form-submit-button class="liatris_fields liatris_sub_field"]
                    </div>                    
                    [mo-mailchimp-interests]
                [/mo-optin-form-fields-wrapper]
                [mo-optin-form-note class="liatris_note"]
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

        $is_mini_headline_display = '';
        if ($this->get_customizer_value('hide_mini_headline', false)) {
            $is_mini_headline_display = 'display:none;';
        }

        // mini headline must share same font as headline.
        $mini_headline_font_family = $this->_construct_font_family($this->get_customizer_value('headline_font'));

        return <<<CSS
html div#$optin_uuid div#$optin_css_id.liatris-container * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.liatris-container {
	width: 250px;
	margin: 0;
	background: #fff;
	padding: 20px;
	border-radius: 3px;
}

html div#$optin_uuid div#$optin_css_id.liatris-container .mo-mailchimp-interest-container {
	padding: 0px 30px;
}

html div#$optin_uuid div#$optin_css_id .liatris-optin-form-close {
    right: .5em;
    top: .5em;
    padding: 0;
    margin: 0;
    text-align: center;
    position: absolute;
    cursor: pointer;
    z-index: 2;
    line-height: .5;
    font-size: 1.5em;
    text-decoration: none !important;
    color: #444444;
    font-family: Roboto, "Lato", sans-serif;
}

html div#$optin_uuid div#$optin_css_id .liatris-optin-form-close:hover {
    transform: scale(1.2);
    position: absolute;
    right: .5em;
    top: .5em;
    padding: 0;
    margin: 0;
    transition: all .2s ease-out 0s;
}

html div#$optin_uuid div#$optin_css_id .liatris-img-is-responsive img {
	width: 100%;
	height: auto;
	max-width: 130px;
    max-height: 130px;
}

html div#$optin_uuid div#$optin_css_id .liatris_middle-section {
	text-align: center;
	padding: 10px 0;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_cta {
	margin-top: 30px;
	margin-bottom: 10px;
    background: #5d6d7b;
	border: 0;
	font-weight: 700;
	font-size: 17px;
	color: #fff;
	border-radius: 3px;
    padding: 10px;
    width: 100%;
    display: block;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_top-section {
	text-align: center;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_note {
	text-align: center;
	margin-top: 20px;
	color: #90a1af;
	font-size: 12px;
	font-weight: 700;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_field {
	width: 100%;
	margin-right: 10px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_sub_field {
	width: 100%;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_fields_wrap {
	display: block;
	padding: 0px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_description,
html div#$optin_uuid div#$optin_css_id .liatris_cta {
	font-weight: 700;
	line-height: 1.7;
	font-size: 15px;
	color: #444;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_mini_headline {
	font-weight: 400;
	font-size: 12px;
	margin-top: 5px;
    letter-spacing: .2px;
    color: #4fa9db;
    font-family: $mini_headline_font_family;
    $is_mini_headline_display
}
		
html div#$optin_uuid div#$optin_css_id .liatris_headline {
	font-weight: 700;
	color: #444;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_fields {
	padding: 15px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_fields.liatris_field {
	background: #f0f4f7;
	border-radius: 3px;
	font-size: 15px;
	color: #444;
	border: 1px solid #f0f4f7;
    margin-top: 20px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_fields.liatris_field:focus {
	background: #fff;
	border: 1px solid #f0f4f7;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_fields.liatris_sub_field {
	background: #5d6d7b;
	border: 0;
	font-weight: 700;
	font-size: 16px;
	color: #fff;
	border-radius: 3px;
	margin-top: 20px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_attendant_image.liatris-img-is-responsive img {
	border: 5px solid #fff;
	border-radius: 100%;
	box-shadow: 2px 4px 15px rgba(0, 0, 0, 0.3);
}
		
html div#$optin_uuid div#$optin_css_id .liatris_attendant_image.liatris-img-is-responsive {
	width: 94px;
	margin: 0 auto;
	position: absolute;
	top: -48px;
	right: 80px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_top-section_inner {
	margin-top: 40px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_mini_headline {
	font-size: 15px;
}
		
html div#$optin_uuid div#$optin_css_id .liatris_mini_headline {
	font-size: 14px;
}

html div#$optin_uuid div#$optin_css_id .mo-optin-error  {
    display: none;
    background: #F44336;
    color: #ffffff;
    text-align: center;
    padding: .2em;
    font-size: 14px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
		
@media only screen and (min-width: 380px) {
	html div#$optin_uuid div#$optin_css_id.liatris-container {
		width: 350px;
		background: #fff;
		padding: 20px;
		border-radius: 3px;
	}
	html div#$optin_uuid div#$optin_css_id .liatris_attendant_image.liatris-img-is-responsive {
		right: 132px;
	}
}
		
@media only screen and (min-width: 480px) {
	html div#$optin_uuid div#$optin_css_id .liatris_attendant_image.liatris-img-is-responsive {
		right: 155px;
	}
	html div#$optin_uuid div#$optin_css_id.liatris-container {
		width: 100%;
		max-width: 400px;
	}
}
CSS;

    }
}