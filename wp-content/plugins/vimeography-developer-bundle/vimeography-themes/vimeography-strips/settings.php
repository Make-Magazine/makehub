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
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color'),
    'id'         => 'video-description-color',
    'value'      => '#aaaaaa',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail .vimeography-description', 'attribute' => 'color'),
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
        array('target' => '.vimeography-theme-strips .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-strips .vimeography-paging-icon svg', 'attribute' => 'stroke'),
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
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail .vimeography-title', 'attribute' => 'fontSize'),
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Video Description Size'),
    'id'         => 'video-description-size',
    'value'      => '11',
    'min'        => '8',
    'max'        => '16',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail .vimeography-description', 'attribute' => 'fontSize'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Minimum Thumbnail Column Width'),
    'id'         => 'thumbnail-column-width',
    'value'      => '200',
    'min'        => '150',
    'max'        => '500',
    'step'       => '10',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
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
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
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
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Description'),
    'id'         => 'video-description-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-strips .vimeography-thumbnail .vimeography-description', 'attribute' => 'display'),
      )
  ),
);
