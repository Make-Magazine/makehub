<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Inpost;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Solidago extends AbstractOptinTheme
{
    public $optin_form_name = 'Solidago';

    public function __construct($optin_campaign_id)
    {

        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#171a1f',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#171a1f',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter email address", 'mailoptin'),
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter full name", 'mailoptin'),
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Subscribe For Latest Updates", 'mailoptin'),
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 40,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 27,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 21,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                // -- default for description sections -- //
                [
                    'name'        => 'mo_optin_form_description_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_default',
                    'value'       => $this->_description_content(),
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_color_default',
                    'value'       => '#6b6f76',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_desktop_default',
                    'value'       => 23,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_tablet_default',
                    'value'       => 18,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_description_font_size_mobile_default',
                    'value'       => 15,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_background_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Alyssum',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#171a1f',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#fff',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],


                // -- default for note sections -- //

                [
                    'name'        => 'mo_optin_form_hide_note_default',
                    'value'       => true,
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#fafafa',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Work+Sans',
                    'optin_class' => 'Solidago',
                    'optin_type'  => 'inpost'
                ]
            ]
        );

        add_filter('mo_optin_hide_name_field_background_control', '__return_true');
        add_filter('mo_optin_hide_email_field_background_control', '__return_true');

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
        return __('See For your self how brilliant it is.', 'mailoptin');
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
        return <<<HTML
[mo-optin-form-wrapper class="solidago-container"]
	<div class="solidago-container_inner">
        [mo-optin-form-headline tag="div" class="solidago-headline"]
        [mo-optin-form-description class="solidago-description"]
        [mo-optin-form-cta-button class="solidago-cta"]
	</div>
    [mo-optin-form-fields-wrapper class="solidago-container_field"]
        [mo-optin-form-name-field class="solidago-field"]
        [mo-optin-form-email-field class="solidago-field"]
        [mo-optin-form-custom-fields class="solidago-field"]
        [mo-mailchimp-interests]
        [mo-optin-form-submit-button class="solidago-submit-btn"]
    [/mo-optin-form-fields-wrapper]
    [mo-optin-form-note class="solidago-note"]
    [mo-optin-form-error class="solidago-error"]
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
html div#$optin_uuid div#$optin_css_id.solidago-container * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
html div#$optin_uuid div#$optin_css_id.solidago-container {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	
	background: #171a1f;
	color: #fff;
	text-align: center;
	width: 100%;
	max-width: 100%;
	padding: 40px 30px;
	margin: 20px auto;
}

html div#$optin_uuid div#$optin_css_id .solidago-cta {
    border: 2px solid #171a1f;
    cursor: pointer;
    margin-top: 30px;
    padding: 20px;
    border-radius: 5px;
    font-size: 20px;
    font-weight: 700;
    text-transform: capitalize;
}

html div#$optin_uuid div#$optin_css_id .solidago-field {
	display: block;
	width: 100%;
	margin-bottom: 20px;
	padding: 20px;
	background: transparent;
	color: #fff;
	font-weight: 700;
	font-size: 14px;
	border-left: 0;
	border-right: 0;
	border-top: 0;
}
		
html div#$optin_uuid div#$optin_css_id .solidago-field:focus {
	transition: 1s ease-in;
	border-bottom: 2px solid #02d2d8;
}

html div#$optin_uuid div#$optin_css_id .solidago-submit-btn {
	background: #fff;
	border: 0;
	padding: 20px;
	border-radius: 5px;
	font-size: 16px;
	margin-top: 30px;
	text-transform: capitalize;
    cursor: pointer;
}
		
html div#$optin_uuid div#$optin_css_id .solidago-error {
	margin-top: 10px;
    display: none;
    color: #e74c3c;
	font-style: italic;
}

html div#$optin_uuid div#$optin_css_id .solidago-note {
	margin-top: 20px;
	font-style: italic;
}
		
html div#$optin_uuid div#$optin_css_id .solidago-headline {
	font-size: 21px;
	margin-bottom: 10px;
	font-weight: 700;
}
		
html div#$optin_uuid div#$optin_css_id .solidago-description {
	padding-top: 10px;
	color: #6b6f76;
}
		
html div#$optin_uuid div#$optin_css_id .solidago-container_field {
	padding: 20px 0px;
}

CSS;

    }
}