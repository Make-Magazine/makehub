<?php

$settings        = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Overlay Color'),
    'id'         => 'overlay-color',
    'value'      => '#ffffff',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail figcaption', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail figcaption:after', 'attribute' => 'background'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Title Color'),
    'id'         => 'video-title-color',
    'value'      => '#333333',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color'),
    'id'         => 'video-description-color',
    'value'      => '#222222',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail .vimeography-description', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Paging Loader Color'),
    'id'         => 'loader-color',
    'value'      => '#2196f3',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'important'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vue-simple-spinner', 'attribute' => 'borderTopColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Paging Controls Color'),
    'id'         => 'paging-title-color',
    'value'      => '#000000',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'important'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Minimum Thumbnail Column Width'),
    'id'         => 'square-size',
    'value'      => '235',
    'min'        => '130',
    'max'        => '350',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, {{value}})'),
      ),
    'expressions' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail-container', 'attribute' => 'gridAutoRows', 'operator' => '*', 'value' => '1.0'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Column Spacing'),
    'id'         => 'square-column-spacing',
    'value'      => '0',
    'min'        => '0',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      ),
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Row Spacing'),
    'id'         => 'square-row-spacing',
    'value'      => '0',
    'min'        => '0',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      ),
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Description'),
    'id'         => 'description-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-squares .vimeography-thumbnail .vimeography-description', 'attribute' => 'display'),
      )
  ),
);