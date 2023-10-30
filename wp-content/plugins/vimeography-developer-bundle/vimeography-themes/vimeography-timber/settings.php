<?php

$settings = array(
  array(
    'type'       => 'colorpicker',
    'label'      => __('Title Color'),
    'id'         => 'title-color',
    'value'      => '#ffffff',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Underline Color'),
    'id'         => 'underline-color',
    'value'      => '#ffffff',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail .vimeography-title:after', 'attribute' => 'background'),
      )
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Title'),
    'id'         => 'title-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail .vimeography-title', 'attribute' => 'display'),
      )
  ),
  array(
    'type'       => 'visibility',
    'label'      => __('Show Date'),
    'id'         => 'date-visibility',
    'value'      => 'block',
    'pro'        => TRUE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail .vimeography-subtitle', 'attribute' => 'display'),
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
        array('target' => '.vimeography-theme-timber .vimeography-modal .vimeography-plays', 'attribute' => 'display'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Date Color'),
    'id'         => 'date-color',
    'value'      => '#ffffff',
    'pro'        => FALSE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail .vimeography-subtitle', 'attribute' => 'color'),
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
        array('target' => '.vimeography-theme-timber .vue-simple-spinner', 'attribute' => 'borderTopColor'),
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
        array('target' => '.vimeography-theme-timber .vimeography-paging-icon svg', 'attribute' => 'stroke'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Pop-Up Title Color'),
    'id'         => 'popup-title-color',
    'value'      => '#292d33',
    'pro'        => FALSE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-lightbox .vimeography-title', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Pop-Up Description Color'),
    'id'         => 'popup-description-color',
    'value'      => '#686d73',
    'pro'        => FALSE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-lightbox .vimeography-description', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Pop-Up Description Links Color'),
    'id'         => 'popup-description-links-color',
    'value'      => '#4cb5c2',
    'pro'        => FALSE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-lightbox a', 'attribute' => 'color'),
      )
  ),
  array(
    'type'       => 'colorpicker',
    'label'      => __('Pop-Up Background Color'),
    'id'         => 'popup-background-color',
    'value'      => '#f4f4f4',
    'pro'        => FALSE,
    'namespace'  => FALSE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-lightbox .vimeography-modal-modern-touch', 'attribute' => 'backgroundColor'),
      )
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Minimum Thumbnail Column Width'),
    'id'         => 'square-size',
    'value'      => '300',
    'min'        => '130',
    'max'        => '500',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail-container', 'attribute' => 'gridTemplateColumns', 'transform' => 'repeat(auto-fit, minmax({{value}}, 1fr))'),
      ),
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Column Spacing'),
    'id'         => 'square-column-spacing',
    'value'      => '10',
    'min'        => '0',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail-container', 'attribute' => 'gridColumnGap'),
      ),
  ),
  array(
    'type'       => 'slider',
    'label'      => __('Row Spacing'),
    'id'         => 'square-row-spacing',
    'value'      => '10',
    'min'        => '0',
    'max'        => '100',
    'step'       => '5',
    'pro'        => TRUE,
    'namespace'  => TRUE,
    'properties' =>
      array(
        array('target' => '.vimeography-theme-timber .vimeography-thumbnail-container', 'attribute' => 'gridRowGap'),
      ),
  ),
);