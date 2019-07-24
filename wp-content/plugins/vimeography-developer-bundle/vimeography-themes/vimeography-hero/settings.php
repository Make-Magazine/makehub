<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Title Color'),
    'id'         => 'video-title-color',
    'value'      => '#222222',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-hero .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
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
        array('target' => '.vimeography-theme-hero .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-hero .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Video Title Size'),
    'id'         => 'video-title-size',
    'value'      => '13',
    'min'        => '8',
    'max'        => '24',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-hero .vimeography-thumbnail .vimeography-title', 'attribute' => 'fontSize'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Minimum Thumbnail Column Width'),
    'id'         => 'thumbnail-width',
    'value'      => '300',
    'min'        => '80',
    'max'        => '500',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-hero .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Column Spacing'),
    'id'         => 'thumbnail-column-spacing',
    'value'      => '0',
    'min'        => '0',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-hero .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Row Spacing'),
    'id'         => 'thumbnail-row-spacing',
    'value'      => '20',
    'min'        => '10',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-hero .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
);
