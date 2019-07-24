<?php

$settings = array(
  array(
    'type'       => 'visibility',
    'label'      => __('Show Title'),
    'id'         => 'show-title',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-ballistic .vimeography-title', 'attribute' => 'display'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Title Font Size'),
    'id'         => 'title-font-size',
    'value'      => '12',
    'min'        => '10',
    'max'        => '30',
    'step'       => '1',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-ballistic .vimeography-title', 'attribute' => 'fontSize'),
      ),
  ),
);
