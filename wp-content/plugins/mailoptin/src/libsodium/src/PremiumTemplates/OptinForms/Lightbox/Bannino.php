<?php

namespace MailOptin\Libsodium\PremiumTemplates\OptinForms\Lightbox;

use MailOptin\Core\Admin\Customizer\EmailCampaign\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinTheme;

class Bannino extends AbstractOptinTheme
{
    public $optin_form_name = 'Bannino';

    public $default_form_image_partial;

    public function __construct($optin_campaign_id)
    {
        // remove default closeIcon
        add_filter('mo_optin_campaign_icon_close', function ($val, $optin_class) {
            if ($optin_class == 'Bannino') $val = false;
            return $val;
        }, 10, 2);

        $this->init_config_filters([
                // -- default for design sections -- //
                [
                    'name' => 'mo_optin_form_width_default',
                    'value' => '600',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],
                [
                    'name' => 'mo_optin_form_background_color_default',
                    'value' => '#f5d836',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                // -- default for design sections -- //
                [
                    'name' => 'mo_optin_form_border_color_default',
                    'value' => '#2c3e50',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                // -- default for headline sections -- //
                [
                    'name' => 'mo_optin_form_headline_default',
                    'value' => __("Subscribe For Latest Updates", 'mailoptin'),
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                // -- default for fields sections -- //
                [
                    'name' => 'mo_optin_form_name_field_color_default',
                    'value' => '#000',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_email_field_color_default',
                    'value' => '#000',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_color_default',
                    'value' => '#ffffff',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_background_default',
                    'value' => '#2c3e50',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_submit_button_font_default',
                    'value' => 'Open+Sans',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_name_field_font_default',
                    'value' => 'Trebuchet MS, Arial, sans-serif',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_email_field_font_default',
                    'value' => 'Trebuchet MS, Arial, sans-serif',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                // -- default for note sections -- //
                [
                    'name' => 'mo_optin_form_note_font_color_default',
                    'value' => '#000000',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_note_default',
                    'value' => __('We promise not to spam you. You can unsubscribe at any time.', 'mailoptin'),
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name' => 'mo_optin_form_note_font_default',
                    'value' => 'Titillium+Web',
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_desktop_default',
                    'value'       => 14,
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ],

                [
                    'name'        => 'mo_optin_form_note_font_size_tablet_default',
                    'value'       => 14,
                    'optin_class' => 'Bannino',
                    'optin_type' => 'lightbox'
                ]
            ]
        );

        // remove headline and description section/panel. not needed.
        add_filter('mo_optin_customizer_disable_headline_section', '__return_true');
        add_filter('mo_optin_customizer_disable_description_section', '__return_true');

        $this->default_form_image_partial = MAILOPTIN_ASSETS_URL . 'images/optin-themes/bannino/optin-image.png';

        add_filter('mo_optin_form_enable_form_image', '__return_true');
        add_filter('mo_optin_form_enable_hide_form_image', '__return_true');

        add_filter('mo_optin_form_partial_default_image', function () {
            return $this->default_form_image_partial;
        });

        add_filter('mo_optin_form_customizer_form_image_args', function ($config) {
            $config['width'] = 600;
            $config['height'] = 400;

            return $config;
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
        add_filter('mailoptin_tinymce_customizer_control_count', function ($count) {
            return $count - 2;
        });

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
        $optin_close_button = MAILOPTIN_ASSETS_URL . 'images/optin-themes/bannino/close.png';
        $optin_default_image = $this->default_form_image_partial;

        return <<<HTML
[mo-optin-form-wrapper class="bannino-container"]
[mo-close-optin class="bannino-close-btn"]
<img src="$optin_close_button" alt="Close Optin">
[/mo-close-optin]
<div class="bannino-container-inner">
	[mo-optin-form-image default="$optin_default_image" wrapper_enabled="true"]

	<div class="form-body">
      [mo-optin-form-fields-wrapper]
		<div class="bannino-input-fields bannino-clearfix">
			[mo-optin-form-name-field class="bannino-form-field"]
			[mo-optin-form-email-field class="bannino-form-field"]
			[mo-optin-form-custom-fields class="bannino-form-field"]
			[mo-optin-form-submit-button class="bannino-form-submit-button"]
		</div>
      [/mo-optin-form-fields-wrapper]
      [mo-optin-form-cta-wrapper]
	     <div class="bannino-input-fields bannino-clearfix">
            [mo-optin-form-cta-button class="bannino-form-submit-button"]
         </div>
      [/mo-optin-form-cta-wrapper]
		[mo-mailchimp-interests]
		[mo-optin-form-note class="moBannini_note"]
		[mo-optin-form-error]
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

        $form_width = $this->get_customizer_value('form_width');
        return <<<CSS
div#$optin_css_id.bannino-container * {
		 -webkit-box-sizing: border-box;
		 -moz-box-sizing: border-box;
		 box-sizing: border-box;
	 }

div#$optin_css_id.bannino-container {
		 border: 5px solid #2c3e50;
		 margin: auto;
		 width: 100%;
		 max-width: {$form_width}px;
	 }

div#$optin_css_id.bannino-container button, div#$optin_css_id.bannino-container input {
													 margin: 0;
												 }

div#$optin_css_id.bannino-container .bannino-input-fields {
		 border: 0;
	 }
	 
div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field {
	background-color: #ffffff;
}

div#$optin_css_id.bannino-container img.mo-optin-form-image{
		 display: block;
		 width: 100%;
		 height: auto;
		 max-height: 400px;
	 }

div#$optin_css_id.bannino-container input:focus {
		 background: #ece4e4;
	 }

div#$optin_css_id.bannino-container .bannino-clearfix:before, div#$optin_css_id.bannino-container .bannino-clearfix:after {
display: table;
content: " ";
}
div#$optin_css_id.bannino-container .bannino-clearfix:after{
		 clear: both;
	 }

div#$optin_css_id.bannino-container .form-body {
		 padding: 10px 10px;
	 }

div#$optin_css_id.bannino-container .moBannini_note {
		 font-style: italic;
		 font-size: 14px;
		 line-height: 1.5;
		 text-align: center;
		 color: #000;
	 }

div#$optin_css_id.bannino-container div.mo-optin-error {
		 display: none;
		 color: #FF0000;
		 font-size: 14px;
		 text-align: center;
		 width: 100%;
		 padding-bottom: .5em;
	 }

div#$optin_css_id.mo-has-email.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field {
		 width: 100% !important;
	 }

div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
		 background: #2c3e50;
		 border: 0;
		 margin-top: 5px;
		 color: white;
		 font-weight: 700;
		 padding: 5px 10px;
		 font-size: 14px;
		 text-align: center;
		 white-space: nowrap;
		 vertical-align: middle;
		 -webkit-appearance: button;
		 cursor: pointer;
	 }


div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field,
	 div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button{
			  padding: 7px !important;
			  display: block;
			  width: 100%;
			  margin-bottom: 10px;
			  border: 0;
			  line-height: normal;
		  }

div#$optin_css_id.bannino-container  .bannino-close-btn img {
		 width: 20px;
		 height: 20px;
		 text-align: center;
		 margin-top: 5px;
	 }

div#$optin_css_id.bannino-container .bannino-close-btn {
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

@media only screen and (min-width: 414px){
	div#$optin_css_id.bannino-container  .bannino-input-fields.bannino-clearfix {
			 display: flex;
		 }

	div#$optin_css_id.bannino-container input.bannino-form-submit-button {
			 margin-right: 0 !important;
		 }

	div#$optin_css_id.bannino-container  .bannino-input-fields.bannino-clearfix .bannino-form-field,
		 div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
				  width: 33%;
				  float: left;
				  margin-right: 10px;
				  margin-bottom: 10px;
				  height: 40px !important;
				  padding: 0 20px !important;

			  }

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
			 background: #2c3e50;
			 border: 0;
			 margin-top: 0 !important;
			 padding: 0 20px;
			 height: 40px !important;
		 }
}
@media only screen and (min-width: 500px){

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field,
		 div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
				  width: 33%;
				  float: left;
				  font-size: 14px;
				  margin-right: 10px;
				  margin-bottom: 10px;
				  height: 40px !important;
				  padding: 0 20px !important;

			  }

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
			 background: #2c3e50;
			 border: 0;
			 margin-top: 0 !important;
			 padding: 0 20px;
			 height: 40px !important;
		 }
}

@media only screen and (min-width: 1200px){

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field,
		 div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
				  width: 33%;
				  height: 40px !important;
			  }

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
			 margin-top: 0 !important;
			 padding: 8px 20px !important;
			 height: 40px !important;
			 margin-bottom: 10px !important;
		 }
	div#$optin_css_id.bannino-container .bannino-input-fields {
			 padding: 15px 30px 0 !important;
		 }

}

@media only screen and (min-width: 768px){

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix .bannino-form-field,
		 div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button   {
				  width: 33%;
				  float: left;
				  margin-right: 10px;
				  height: 40px !important;
				  padding: 0 20px !important;
			  }

	div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix input.bannino-form-submit-button {
			 margin-top: 0 !important;
			 padding: 0 20px !important;
			 height: 40px !important;
		 }
}

div#$optin_uuid.mo-cta-button-display input.mo-optin-form-cta-button {
    width: 100% !important;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container input:not([type="radio"]):not([type="checkbox"]),
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container .mo-optin-form-custom-field,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container textarea {
    width: 100% !important;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container textarea.mo-optin-form-custom-field.textarea-field {
min-height: 80px;
padding-top: 15px !important;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container .mo-optin-form-custom-field.radio-field,
html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container .mo-optin-form-custom-field.checkbox-field {
    height: auto !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}

html div#$optin_uuid.mo-optin-has-custom-field div#$optin_css_id.bannino-container .bannino-input-fields.bannino-clearfix {
	display: block !important;
}

html div#$optin_uuid div#$optin_css_id.bannino-container .mo-optin-fields-wrapper .list_subscription-field:not(select) {
    display: inline-table;
}
CSS;

    }
}