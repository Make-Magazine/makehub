<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Video Background Color'),
    'id'         => 'video-background-color',
    'value'      => 'transparent',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-single .vimeography-player-container', 'attribute' => 'backgroundColor'),
      )
  ),
);
