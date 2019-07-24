<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Thumbnail Box Background Color'),
    'id'         => 'thumbnail-container-background-color',
    'value'      => '#232323',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-thumbnail-container', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Info Box Background Color'),
    'id'         => 'info-container-background-color',
    'value'      => '#232323',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-info', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-journey .vimeography-info:after', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Info Box Video Title Color'),
    'id'         => 'info-container-title-color',
    'value'      => '#cccccc',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-info .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Duration Color'),
    'id'         => 'info-container-duration-color',
    'value'      => '#cccccc',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-info .vimeography-duration', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color'),
    'id'         => 'info-container-description-color',
    'value'      => '#cccccc',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-info .vimeography-description', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Width'),
    'id'         => 'thumbnail-width',
    'value'      => '95',
    'min'        => '80',
    'max'        => '250',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-thumbnail', 'attribute' => 'width'),
      ),
    'expressions' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-thumbnail img', 'attribute' => 'maxWidth', 'operator' => '*', 'value' => '1.77777778'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Height'),
    'id'         => 'thumbnail-height',
    'value'      => '95',
    'min'        => '45',
    'max'        => '140',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-journey .vimeography-thumbnail', 'attribute' => 'height'),
        array('target' => '.vimeography-theme-journey .vimeography-thumbnail img', 'attribute' => 'height'),
      ),
  ),
);