<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Thumbnail Title Color'),
    'id'         => 'video-thumbnail-title-color',
    'value'      => '#f4f4f4',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-greyscale .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Thumbnail Title Background Color'),
    'id'         => 'video-thumbnail-title-background-color',
    'value'      => '#fafafa',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-greyscale .vimeography-thumbnail .vimeography-title', 'attribute' => 'backgroundColor'),
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
        array('target' => '.vimeography-theme-greyscale .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-greyscale .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Column Width'),
    'id'         => 'thumbnail-width',
    'value'      => '200',
    'min'        => '150',
    'max'        => '500',
    'step'       => '10',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-greyscale .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Column Spacing'),
    'id'         => 'vertical-thumbnail-spacing',
    'value'      => '5',
    'min'        => '5',
    'max'        => '40',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-greyscale .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Row Spacing'),
    'id'         => 'row-thumbnail-spacing',
    'value'      => '5',
    'min'        => '5',
    'max'        => '40',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-greyscale .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
);
