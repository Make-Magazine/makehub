<?php

  $settings = array(
    array(
      'type'       => 'colorpicker',
      'label'      => __('Caption Background Color'),
      'id'         => 'caption-background-color',
      'value'      => '#eeeeee',
      'pro'        => FALSE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-covers .vimeography-thumbnail-container .vimeography-title', 'attribute' => 'backgroundColor'),
        )
    ),
    array(
      'type'       => 'colorpicker',
      'label'      => __('Caption Title Color'),
      'id'         => 'caption-title-color',
      'value'      => '#999999',
      'pro'        => FALSE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-covers .vimeography-thumbnail-container .vimeography-title', 'attribute' => 'color'),
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
          array('target' => '.vimeography-theme-covers .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
          array('target' => '.vimeography-theme-covers .vimeography-paging-icon svg', 'attribute' => 'stroke'),
        )
    ),
    array(
      'type'       => 'slider',
      'label'      => __('Cover Size'),
      'id'         => 'cover-size',
      'value'      => '200',
      'min'        => '80',
      'max'        => '400',
      'step'       => '5',
      'pro'        => TRUE,
      'namespace'  => TRUE,
      'properties' =>
        array(
          array('target' => '.vimeography-theme-covers .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, {{value}})'),
        ),
    ),
  );
