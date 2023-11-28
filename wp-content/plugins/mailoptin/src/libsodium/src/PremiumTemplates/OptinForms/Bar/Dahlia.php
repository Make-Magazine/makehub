<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Bar;

use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Dahlia extends AbstractOptinTheme
{
    public $optin_form_name = 'Dahlia';

    public function __construct($optin_campaign_id)
    {
        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name'        => 'mo_optin_form_background_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_border_color_default',
                    'value'       => '#00ceff',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_placeholder_default',
                    'value'       => __("Enter your name...", 'mailoptin'),
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_placeholder_default',
                    'value'       => __("Enter your email...", 'mailoptin'),
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                // -- default for headline sections -- //
                [
                    'name'        => 'mo_optin_form_headline_default',
                    'value'       => __("Subscribe for Free Marketing Tips", 'mailoptin'),
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_color_default',
                    'value'       => '#000000',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                // -- default for fields sections -- //
                [
                    'name'        => 'mo_optin_form_name_field_color_default',
                    'value'       => '#2e2e2e',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_color_default',
                    'value'       => '#2e2e2e',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_color_default',
                    'value'       => '#ffffff',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_background_default',
                    'value'       => '#00c1f3',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_submit_button_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_name_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_email_field_font_default',
                    'value'       => 'Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_hide_note_default',
                    'value'       => true,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],
                [
                    'name'        => 'mo_optin_form_note_font_color_default',
                    'value'       => '#515151',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_default',
                    'value'       => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_default',
                    'value'       => 'Helvetica',
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_mobile_default',
                    'value'       => 12,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_desktop_default',
                    'value'       => 18,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_tablet_default',
                    'value'       => 18,
                    'optin_class' => 'Dahlia',
                    'optin_type'  => 'bar'
                ],

                [
                    'name'        => 'mo_optin_form_headline_font_size_mobile_default',
                    'value'       => 18,
                    'optin_class' => 'Dahlia',
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
            return '#000000';
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

        $fields_settings['dahlia_icons_color'] = array(
            'default'   => '#00ceff',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

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
        $fields_controls['dahlia_icons_color'] = new \WP_Customize_Color_Control(
            $wp_customize,
            $option_prefix . '[dahlia_icons_color]',
            apply_filters('mo_optin_form_customizer_dahlia_icons_color_args', array(
                    'label'    => __('Icons Color', 'mailoptin'),
                    'section'  => $customizerClassInstance->fields_section_id,
                    'settings' => $option_prefix . '[dahlia_icons_color]',
                    'priority' => 5,
                )
            )
        );

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

    public function customizer_preview_js()
    {
        if ( ! \MailOptin\Core\is_mailoptin_customizer_preview()) return;
        ?>
        <script type="text/javascript">
            (function ($) {
                $(function () {
                    wp.customize(mailoptin_optin_option_prefix + '[' + mailoptin_optin_campaign_id + '][dahlia_icons_color]', function (value) {
                        value.bind(function (to) {
                            $('.dahlia-icons').css('color', to);
                        });
                    });
                })
            })(jQuery)
        </script>
        <?php
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
        $tag_start = '<div class="dahlia-form-group">';
        $tag_end   = '<span class="dahlia-icons-wrap">';
        $tag_end   .= apply_filters('mo_optin_form_customizer_dahlia_custom_fields_fa_icon', '<i class="fa fa-comment fa-lg dahlia-icons" aria-hidden="true"></i>');
        $tag_end   .= '</span>';
        $tag_end   .= '</div>';

        return <<<HTML
[mo-optin-form-wrapper class="dahlia-container"]
<div class="dahlia-form-row dahlia-text-align">
    <div class="dahlia-title-wrap">
        [mo-optin-form-headline tag="div" class="dahlia-headline"]
        <div class="dahlia-close-form">[mo-close-optin class="mo-close-optin"]x[/mo-close-optin]</div>
    </div>
    
    [mo-optin-form-fields-wrapper]
    <div class="dahlia-form-group dahlia-name-field" id="mo-optin-form-name-field">
        [mo-optin-form-name-field class="dahlia-form-control"]
        <span class="dahlia-icons-wrap">
          <i class="fa fa-user fa-lg dahlia-icons" aria-hidden="true"></i>
		</span>
    </div>
    <div class="dahlia-form-group dahlia-email-field">
        [mo-optin-form-email-field class="dahlia-form-control"]
        <span class="dahlia-icons-wrap">
            <i class="fa fa-envelope fa-lg dahlia-icons" aria-hidden="true"></i>
		</span>
    </div>
    
    [mo-optin-form-custom-fields class="dahlia-form-control" tag_start='$tag_start' tag_end='$tag_end']
    
    <div class="dahlia-form-group dahlia-button-group">
        [mo-optin-form-submit-button class="dahlia-button"]
    </div>
    [/mo-optin-form-fields-wrapper]
    
    
  [mo-optin-form-cta-wrapper]
    <div class="dahlia-form-group dahlia-button-group">
    [mo-optin-form-cta-button class="dahlia-button"]
    </div>
  [/mo-optin-form-cta-wrapper]
    [mo-mailchimp-interests]
	[mo-optin-form-note class="moDahlia_note"]
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
        $optin_css_id       = $this->optin_css_id;
        $optin_uuid         = $this->optin_campaign_uuid;
        $dahlia_icons_color = $this->get_customizer_value('dahlia_icons_color');
        $dahlia_icons_color = empty($dahlia_icons_color) ? '#00ceff' : $dahlia_icons_color;

        return <<<CSS
        @import "https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css";
html div#$optin_uuid div#$optin_css_id.dahlia-container * {
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container {
    background-color: #fff;
    color:#fff;
    padding-left: 15px;
    padding-right: 35px;
    padding-bottom: 10px;
    width: 100%;
    font-size: 16px;
    border: 2px solid #00ceff;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-close-form {
    display: block;
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

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-close-form a {
    color: #000;
    cursor: pointer;
    font-size: 22px;
    font-weight: 400;
    text-decoration: none !important;
    font-family: Arial, sans-serif !important;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-close-form:hover {
    text-shadow: 0 0 2px #fff
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .moDahlia_note {
		 font-style: italic;
		 line-height: 1.5;
		 text-align: center;
		 color: #515151;
	 }

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-title-wrap{
    padding: 15px;
    width:100%;
    text-align: center;
    font-size: 18px;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-group {
    margin-bottom: 15px;
    padding-left: 15px;
    padding-right: 15px;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-control{
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
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-row{
    margin-right: -15px;
    margin-left: -15px;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-row:after{
    clear: both;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container input.dahlia-button{
    outline: none !important;
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 16px;
    line-height: 1.42857143;
    text-align: center;
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

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-email-field,
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-name-field,
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-button-group{
    width: 100%;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container div.mo-optin-error {
         display: none;
         color: #FF0000;
         text-align: center;
         width: 100%;
         font-size: 14px;
         padding-bottom: .5em;
     }

/* Media queries for responsiveness */
/* Tablet */
@media (min-width: 768px) {
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-email-field, 
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-name-field{
        width:28.19%;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-button-group{
        width: auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-close-form{
        top: 50%;
    }
}

/* Medium device */
@media (min-width: 992px) {
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-text-align{
        text-align: center;
    }
}


/* Large screens & TVs */
@media (min-width: 1200px) {
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-title-wrap{
        width: auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-email-field,
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-name-field,
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-button-group{
        width:auto;
        display: inline-block;
    }
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-close-form{
        top: 24px;
    }
    html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-group{
        margin-bottom: -5px;
    }
    
    html div#$optin_uuid div#$optin_css_id.dahlia-container .moDahlia_note {
		 margin-top: 10px;
	 }
	 
	 html div#$optin_uuid div#$optin_css_id.dahlia-container div.mo-optin-error {
	    margin-top: 5px;
	 }
}
html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-headline{
    color: #000;
    display: block;
    border: none;
    line-height: normal;
    height: auto;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-control{
    color: #2e2e2e;
    border-bottom: 2px solid #D6D6D6;
    padding-left: 45px;
    background-color: transparent;
    border-radius: 0;
    height: 36px;
    display:block;
    margin: 0;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-control:hover{
    outline: 0;
    border-bottom-color: $dahlia_icons_color;
    -webkit-transition: border-bottom-color 0.15s;
    -moz-transition: border-bottom-color 0.15s;
    -ms-transition: border-bottom-color 0.15s;
    -o-transition: border-bottom-color 0.15s;
    transition: border-bottom-color 0.15s;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-form-group{
    position: relative;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container input.dahlia-button{
    background: #00CCFF;
    color:#fff;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container input.dahlia-button:hover{
    background: #00c1f3;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-icons-wrap{
    position:absolute;
    bottom:-5px;
    top:0;
    left:15px;
    background:transparent;
    border-bottom-left-radius:5px;
    height: 36px;
    width: 34px;
    border-top-left-radius: 5px;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .mo-optin-form-custom-field.list_subscription-field + .dahlia-icons-wrap, 
html div#$optin_uuid div#$optin_css_id.dahlia-container .mo-optin-form-custom-field.checkbox-field + .dahlia-icons-wrap, 
html div#$optin_uuid div#$optin_css_id.dahlia-container .mo-optin-form-custom-field.radio-field + .dahlia-icons-wrap {
    display:none;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container .dahlia-icons{
    width:30px;
    height:30px;
    padding: 5px;
    color: $dahlia_icons_color;
}

html div#$optin_uuid div#$optin_css_id.dahlia-container ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
    color: rgba(46,46,46,0.70);
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container ::-moz-placeholder { /* Firefox 19+ */
    color: rgba(46,46,46,0.70);
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container :-ms-input-placeholder { /* IE 10+ */
    color: rgba(46,46,46,0.70);
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}
html div#$optin_uuid div#$optin_css_id.dahlia-container :-moz-placeholder { /* Firefox 18- */
    color: rgba(46,46,46,0.70);
    font-family: Palatino,Helvetica, Arial, sans-serif; !important;
    font-size: 15px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dahlia-container .dahlia-form-group {
     width: 100%;
    display:block;
    max-width: 400px;
    margin:  auto;
    margin-bottom:10px;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.dahlia-container textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
}

CSS;

    }
}