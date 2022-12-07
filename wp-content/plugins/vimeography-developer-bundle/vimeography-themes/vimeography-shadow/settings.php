<?php

/**
 * The `$settings` configuration variable is an array that contains all of the
 * configurable settings for this theme. Each setting is an array of its own,
 * containing each of the following key/value pairs:
 *
 * 'type'
 * The UI control to render for the current setting.
 * Possible values include 'colorpicker', 'slider', 'numeric', or 'visibility'.
 *
 * 'label'
 * The i18n-compatible label for this particular setting.
 *
 * 'id'
 * An arbitrary identifier string to associate with the UI control's form field.
 *
 * 'value'
 * The default CSS value for this setting.
 *
 * 'pro'
 * Whether or not this setting requires the Vimeography Pro plugin to
 * be installed. TRUE if `type` is 'colorpicker', otherwise FALSE.
 *
 * 'namespace'
 * Whether or not the DOM element being targeted by the CSS is a child of the
 * vimeography-gallery-{{gallery_id}} container. Usually TRUE, unless your theme
 * uses a fancybox plugin, in which case, the modal window is outside of the container
 * element, so FALSE would be appropriate.
 *
 * 'properties'
 * Defines which CSS selectors and properties that the setting will control.
 * An array of one or more arrays, with each array containing two key/value pairs:
 *
 *   - `target` defines the CSS selector that the setting will affect
 *
 *   - `attribute` defines the CSS property that this setting will
 *      control for the corresponding target selector.
 *
 *   - `transform` (optional) allows you to provide a string with a {{value}} token
 *      to define where the resulting pixel value should be injected in the generated CSS string value.
 *
 * 'expressions' - optional
 * Defines additional CSS selectors and properties that the setting will control,
 * but this time, relatively manipulating the value before associating it with the
 * selector. This is useful if you have two selectors whose values are linked and
 * change relative to one another (widescreen image ratios, margins etc.)
 *
 * An array of one or more arrays, which each array containing four key/value pairs:
 *
 *   - `target` defines the CSS selector that the setting will affect
 *
 *   - `attribute` defines the CSS property that this setting will
 *      control for the target selector.
 *
 *   - `operator` defines the symbol(s) to use for the mathmatical operation to perform
 *      on the original setting value.
 *
 *   - `value` is the input integer which acts as the addend, subtrahend, divisor, multiplier etc.
 *      to the original setting value.
 *
 * 'important' - optional
 * If set to TRUE, the CSS rule will be saved with an `!important` flag.
 *
 * 'min' - optional [required if `type` is 'slider' or 'numeric']
 * The minimum value that a CSS property can be set.
 *
 * 'max' - optional [required if `type` is 'slider' or 'numeric']
 * The maximum value that a CSS property can be set.
 *
 * 'step' - optional [required if `type` is 'slider' or 'numeric']
 * The increment/decrement value of the UI control.
 *
 * @var array
 */
  $settings = array(
    array(
      'type'       => 'colorpicker',
      'label'      => __('Spinner Color'),
      'id'         => 'spinner-color',
      'value'      => '#0077dd',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-shadow .vimeography-player:before', 'attribute' => 'borderTopColor'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Play Icon Color'),
      'id'         => 'play-icon-color',
      'value'      => '#fff',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-shadow .vimeography-thumbnail-overlay svg path', 'attribute' => 'fill'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Video Title Color'),
      'id'         => 'video-title-color',
      'value'      => '#2a282b',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-shadow .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Popup Sidebar Color'),
      'id'         => 'popup-sidebar-color',
      'value'      => '#1c1f23',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-shadow .vimeography-sidebar', 'attribute' => 'background'),
          array('target' => '.vimeography-theme-shadow .vimeography-info:after', 'attribute' => 'background', 'transform' => 'linear-gradient(rgba(255, 255, 255, 0.001), {{value}})'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Popup Background Color'),
      'id'         => 'popup-background-color',
      'value'      => '#0f0f10',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-shadow .vimeography-modal-geo', 'attribute' => 'background'),
        )
    ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Size'),
    'id'         => 'thumbnail-size',
    'value'      => '280',
    'min'        => '150',
    'max'        => '500',
    'step'       => '10',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-shadow .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
      ),
    'expressions' =>
      array(
        array(
          'target' => '.vimeography-theme-shadow .vimeography-thumbnail-container .vimeography-link',
          'attribute' => 'height',
          'operator' => '/',
          'value' => '1.777777778',
        )
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Thumbnail Column Spacing'),
    'id'         => 'thumbnail-column-spacing',
    'value'      => '10',
    'min'        => '10',
    'max'        => '80',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-shadow .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Thumbnail Row Spacing'),
    'id'         => 'thumbnail-row-spacing',
    'value'      => '50',
    'min'        => '10',
    'max'        => '80',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-shadow .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
  );