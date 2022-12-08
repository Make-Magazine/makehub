<?php
/*
Plugin Name: Vimeography Developer Bundle
Plugin URI: http://www.vimeography.com/themes
Description: A collection of every Vimeography gallery theme in one plugin. Use along with the Vimeography video gallery plugin and start making awesome video galleries for your Vimeo videos.
Version: 2.1.1
Author: Dave Kiss
Author URI: http://www.davekiss.com/
Copyright: Dave Kiss
*/

define( 'VIMEOGRAPHY_DEVELOPER_BUNDLE_PATH', plugin_dir_path(__FILE__) );

class Vimeography_Developer_Bundle {

  /**
   * The current version of this bundle
   *
   * @var string
   */
  public $version = '2.1.1';


  /**
   * Include all themes in the Vimeography theme loader.
   */
  public function __construct() {
    add_action('plugins_loaded', array($this, 'load_vimeography_themes'), 1);
    add_action('plugins_loaded', array($this, 'load_addon_plugin') );
  }


  /**
   * Include all Vimeography theme files by looping through the
   * vimeography-themes subfolder.
   *
   * @return void
   */
  public function load_vimeography_themes() {
    foreach(glob(VIMEOGRAPHY_DEVELOPER_BUNDLE_PATH . 'vimeography-themes/*/vimeography?*.php') as $filename) {
      include $filename;
    }
  }


  /**
   * Send the addons meta headers to the Vimeography updater
   * class as a registered addon.
   *
   * @return void
   */
  public function load_addon_plugin() {
    do_action('vimeography/load-addon-plugin', __FILE__);
  }
}

function vimeography_developer_bundle() {
  if ( ! class_exists( 'Vimeography', false ) ) {
    return;
  }

  new Vimeography_Developer_Bundle;
}

// Get Vimeography Pro Running
add_action( 'plugins_loaded', 'vimeography_developer_bundle', 0 );
