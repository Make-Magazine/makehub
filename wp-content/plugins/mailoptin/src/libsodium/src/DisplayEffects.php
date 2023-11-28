<?php

namespace MailOptin\Libsodium;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Single_Select_With_Group_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\OptinForms\AbstractOptinForm;

class DisplayEffects
{
    public function __construct()
    {
        add_action('mo_optin_customizer_settings', [$this, 'customizer_settings'], 10, 3);
        add_action('mo_optin_after_customizer_controls', [$this, 'customizer_controls'], 10, 4);

        add_filter('mo_optin_js_config', [$this, 'optin_effect_js_config'], 10, 3);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param \MailOptin\Core\Admin\Customizer\OptinForm\Customizer $customizerClassInstance
     */
    public function customizer_settings($wp_customize, $option_prefix, $customizerClassInstance)
    {
        $wp_customize->add_setting($option_prefix . '[modal_effects]', array(
                'default'   => (new AbstractCustomizer($customizerClassInstance->optin_campaign_id))->customizer_defaults['modal_effects'],
                'type'      => 'option',
                'transport' => 'postMessage',
                'autoload' => false
            )
        );
    }

    /**
     * @param mixed $instance
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function customizer_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        if (in_array($customizerClassInstance->optin_campaign_type, ['sidebar', 'inpost'])) return;

        $wp_customize->add_control(
            new WP_Customize_Single_Select_With_Group_Control(
                $wp_customize,
                $option_prefix . '[modal_effects]',
                apply_filters('mo_optin_form_customizer_modal_effects_args', array(
                        'label'       => __('Display Effect', 'mailoptin'),
                        'section'     => $customizerClassInstance->design_section_id,
                        'settings'    => $option_prefix . '[modal_effects]',
                        'description' => '',
                        'choices'     => $this->animated_effects(),
                        'priority'    => 8
                    )
                )
            )
        );
    }

    public function animated_effects()
    {
        return [
            __('Attention Seekers', 'mailoptin')  => [
                'MObounce'     => __('Bounce', 'mailoptin'),
                'MOflash'      => __('Flash', 'mailoptin'),
                'MOpulse'      => __('Pulse', 'mailoptin'),
                'MOrubberBand' => __('RubberBand', 'mailoptin'),
                'MOshake'      => __('Shake', 'mailoptin'),
                'MOswing'      => __('Swing', 'mailoptin'),
                'MOtada'       => __('Tada', 'mailoptin'),
                'MOwobble'     => __('Wobble', 'mailoptin'),
                'MOjello'      => __('Jello', 'mailoptin'),
            ],
            __('Bouncing Entrances', 'mailoptin') => [
                'MObounceIn'      => __('Bounce In', 'mailoptin'),
                'MObounceInDown'  => __('Bounce In (Down)', 'mailoptin'),
                'MObounceInLeft'  => __('Bounce In (Left)', 'mailoptin'),
                'MObounceInRight' => __('Bounce In (Right)', 'mailoptin'),
                'MObounceInUp'    => __('Bounce In (Up)', 'mailoptin'),
            ],
            __('Fading Entrances', 'mailoptin')   => [
                'MOfadeIn'        => __('Fade In', 'mailoptin'),
                'MOfadeInDown'    => __('Fade In (Down)', 'mailoptin'),
                'MOfadeInDownBig' => __('Fade In (Down Big)', 'mailoptin'),
                'MOfadeInLeft'    => __('Fade In (Left)', 'mailoptin'),
                'MOfadeInLeftBig' => __('Fade In (Left Big)', 'mailoptin'),
            ],
            __('Zoom Entrances', 'mailoptin')     => [
                'MOzoomIn'      => __('Zoom In', 'mailoptin'),
                'MOzoomInDown'  => __('Zoom In (Down)', 'mailoptin'),
                'MOzoomInLeft'  => __('Zoom In (Left)', 'mailoptin'),
                'MOzoomInRight' => __('Zoom In (Right)', 'mailoptin'),
                'MOzoomInUp'    => __('Zoom In (Up)', 'mailoptin'),
            ],
            __('Flippers', 'mailoptin')           => [
                'MOflip'    => __('Flip', 'mailoptin'),
                'MOflipInX' => __('Flip In (X)', 'mailoptin'),
                'MOflipInY' => __('Flip In (Y)', 'mailoptin'),
            ],
            __('Rotating Entrances', 'mailoptin') => [
                'MOrotateIn'          => __('Rotate In', 'mailoptin'),
                'MOrotateInDownLeft'  => __('Rotate In (Down Left)', 'mailoptin'),
                'MOrotateInDownRight' => __('Rotate In (Down Right)', 'mailoptin'),
            ],
            __('Sliding Entrances', 'mailoptin')  => [
                'MOslideInUp'    => __('Slide In (Up)', 'mailoptin'),
                'MOslideInDown'  => __('Slide In (Down)', 'mailoptin'),
                'MOslideInLeft'  => __('Slide In (Left)', 'mailoptin'),
                'MOslideInRight' => __('Slide In (Right)', 'mailoptin'),
            ],
            __('Specials', 'mailoptin')           => [
                'MOhinge'        => __('Hinge', 'mailoptin'),
                'MOrollIn'       => __('Roll In', 'mailoptin'),
                'MOjackInTheBox' => __('jack In The Box', 'mailoptin'),
            ],
            __('Lightspeed', 'mailoptin')         => [
                'MOlightSpeedIn' => __('Light Speed In', 'mailoptin'),
            ]
        ];
    }

    /**
     * @param array $js_config
     * @param AbstractOptinForm $abstractOptinForm
     *
     * @return array
     */
    public function optin_effect_js_config($js_config, $abstractOptinForm)
    {
        $js_config['effects'] = $abstractOptinForm->get_customizer_value('modal_effects');

        return $js_config;
    }

    /**
     * Singleton poop
     *
     * @return DisplayEffects|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}