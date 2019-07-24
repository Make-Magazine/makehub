<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Active Thumbnail Background Color', 'vimeography-playlister'),
    'id'         => 'active-thumbnail-background-color',
    'value'      => '#555555',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-link-active', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-playlister .vimeography-link-active + .vimeography-downloads', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Inactive Thumbnail Background Color', 'vimeography-playlister'),
    'id'         => 'inactive-thumbnail-background-color',
    'value'      => '#444444',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-link', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail-container', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-playlister .vimeography-link + .vimeography-downloads', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Hovered Thumbnail Background Color', 'vimeography-playlister'),
    'id'         => 'hovered-thumbnail-background-color',
    'value'      => '#555555',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-link:hover', 'attribute' => 'backgroundColor'),
        array('target' => '.vimeography-theme-playlister .vimeography-link:hover + .vimeography-download', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Separator Line Color', 'vimeography-playlister'),
    'id'         => 'video-separator-line-color',
    'value'      => '#333333',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail', 'attribute' => 'borderBottomColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Title Color', 'vimeography-playlister'),
    'id'         => 'video-title-color',
    'value'      => '#dddddd',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Description Color', 'vimeography-playlister'),
    'id'         => 'video-description-color',
    'value'      => '#bbbbbb',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail .vimeography-description', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Video Title Size', 'vimeography-playlister'),
    'id'         => 'video-title-size',
    'value'      => '12',
    'min'        => '10',
    'max'        => '20',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail .vimeography-title', 'attribute' => 'fontSize'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Video Description Size', 'vimeography-playlister'),
    'id'         => 'video-description-size',
    'value'      => '11',
    'min'        => '10',
    'max'        => '18',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-playlister .vimeography-thumbnail .vimeography-description', 'attribute' => 'fontSize'),
      )
  ),
);
