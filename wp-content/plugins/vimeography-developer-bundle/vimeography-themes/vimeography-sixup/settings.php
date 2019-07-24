<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Inactive Thumbnail Border Color'),
    'id'         => 'inactive-thumbnail-border-color',
    'value'      => '#DDDDDD',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-sixup .vimeography-link', 'attribute' => 'borderColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Active Thumbnail Border Color'),
    'id'         => 'active-thumbnail-border-color',
    'value'      => '#0088CC',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-sixup .vimeography-link-active', 'attribute' => 'borderColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Title Color'),
    'id'         => 'video-title-color',
    'value'      => '#000000',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-sixup .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color'),
    'id'         => 'video-description-color',
    'value'      => '#000000',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-sixup .vimeography-description', 'attribute' => 'color'),
      )
  ),
);