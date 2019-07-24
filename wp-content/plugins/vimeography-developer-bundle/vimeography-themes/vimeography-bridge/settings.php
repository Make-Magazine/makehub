<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Thumbnail Border Color'),
    'id'         => 'thumbnail-border-color',
    'value'      => '#dadada',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail .vimeography-link', 'attribute' => 'borderColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Title Color'), //good
    'id'         => 'video-title-color',
    'value'      => '#565656',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color'),
    'id'         => 'video-description-color',
    'value'      => '#555555',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail p.vimeography-description', 'attribute' => 'color'),
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
        array('target' => '.vimeography-theme-bridge .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-bridge .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Thumbnail Width'),
    'id'         => 'thumbnail-width',
    'value'      => '200',
    'min'        => '150',
    'max'        => '500',
    'step'       => '10',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Thumbnail Column Spacing'),
    'id'         => 'thumbnail-column-spacing',
    'value'      => '40',
    'min'        => '10',
    'max'        => '80',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      )
  ),
  array(
    'type'       => 'numeric',
    'label'      => __('Thumbnail Row Spacing'),
    'id'         => 'thumbnail-row-spacing',
    'value'      => '50',
    'min'        => '10',
    'max'        => '80',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Video Playcount'),
    'id'         => 'video-playcount-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-modal .vimeography-plays', 'attribute' => 'display'),
      )
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Tags'),
    'id'         => 'video-tags-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-bridge .vimeography-modal .vimeography-tags', 'attribute' => 'display'),
      )
  ),
);
