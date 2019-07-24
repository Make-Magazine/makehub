<?php
  $settings = array(
    array(
      'type'       => 'colorpicker',
      'label'      => __('Thumbnail Background Color'),
      'id'         => 'thumbnail-background-color',
      'value'      => '#4c4c4c',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-container .vimeography-link', 'attribute' => 'backgroundColor'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Video Title Color'),
      'id'         => 'video-title-color',
      'value'      => '#f4f4f4',
      'pro'        => false,
      'namespace'  => true,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-container .vimeography-title', 'attribute' => 'color'),
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
          array('target' => '.vimeography-theme-aloha .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
          array('target' => '.vimeography-theme-aloha .vimeography-paging-icon svg', 'attribute' => 'stroke'),
        )
    ),
    array(
      'type'       => 'slider',
      'label'      => __('Thumbnail Column Width'),
      'id'         => 'thumbnail-column-width',
      'value'      => '130',
      'min'        => '100',
      'max'        => '530',
      'step'       => '10',
      'pro'        => TRUE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, {{value}})'),
        ),
      'expressions' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-img', 'attribute' => 'maxWidth', 'operator' => '*', 'value' => '1.77'),
        )
    ),
    array(
      'type'       => 'slider',
      'label'      => __('Thumbnail Column Spacing'),
      'id'         => 'thumbnail-column-spacing',
      'value'      => '10',
      'min'        => '0',
      'max'        => '80',
      'step'       => '2',
      'pro'        => TRUE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
        ),
    ),
    array(
      'type'       => 'slider',
      'label'      => __('Thumbnail Row Spacing'),
      'id'         => 'thumbnail-row-spacing',
      'value'      => '10',
      'min'        => '0',
      'max'        => '80',
      'step'       => '2',
      'pro'        => TRUE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-aloha .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
        ),
    ),
  );