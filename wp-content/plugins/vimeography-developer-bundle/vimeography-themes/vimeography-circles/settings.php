<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Thumbnail Overlay Color'),
    'id'         => 'thumbnail-overlay-color',
    'value'      => '#232323',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-overlay', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Caption Title Color'),
    'id'         => 'caption-title-color',
    'value'      => '#e4e4e4',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-overlay .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Play Arrow Color'),
    'id'         => 'play-arrow-color',
    'value'      => '#e4e4e4',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-overlay .vimeography-play-icon', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Spinner Color'),
    'id'         => 'loader-color',
    'value'      => '#2196f3',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'important'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-circles .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Circle Size'),
    'id'         => 'circle-size',
    'value'      => '280',
    'min'        => '120',
    'max'        => '350',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, {{value}})'),
      ),
    'expressions' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-thumbnail-img', 'attribute' => 'maxWidth', 'operator' => '*', 'value' => '1.77777778'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Circle Column Spacing'),
    'id'         => 'circle-column-spacing',
    'value'      => '15',
    'min'        => '5',
    'max'        => '50',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Circle Row Spacing'),
    'id'         => 'circle-row-spacing',
    'value'      => '15',
    'min'        => '5',
    'max'        => '50',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Play Arrow Size'),
    'id'         => 'play-arrow-size',
    'value'      => '12',
    'min'        => '4',
    'max'        => '25',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-overlay .vimeography-play-icon', 'attribute' => 'borderTopWidth'),
        array('target' => '.vimeography-theme-circles .vimeography-overlay .vimeography-play-icon', 'attribute' => 'borderBottomWidth'),
      ),
    'expressions' =>
      array(
        array('target' => '.vimeography-theme-circles .vimeography-overlay .vimeography-play-icon', 'attribute' => 'borderLeftWidth', 'operator' => '*', 'value' => '1.666666667')
      ),
  ),
);