<?php
/*
Plugin Name: Vimeography Theme: Shadow
Plugin URI: https://vimeography.com/themes
Theme Name: Shadow
Theme URI:  https://vimeography.com/themes/shadow
Version: 2.1.1
Description: helps to showcase your videos fullscreen as a site takeover.
Author: Dave Kiss
Author URI: https://vimeography.com
Copyright: 2020 Dave Kiss
*/

if ( ! class_exists('Vimeography_Themes_Shadow') ) {

  class Vimeography_Themes_Shadow {

    /**
     * The current version of this Vimeography theme.
     *
     * Make sure to specify it here as well as above
     * in the header metadata and in the readme.txt
     * file, located in the root of the plugin directory.
     *
     * @var string
     */
    public $version = '2.1.1';


    /**
     * The constructor is used to load the plugin
     * when the WordPress `plugins_loaded` hook is fired.
     *
     * This includes this theme in the Vimeography theme loader.
     */
    public function __construct() {
      add_action('plugins_loaded', array( $this, 'load_theme' ) );
    }


    /**
     * Has to be public so the wp actions can reach it.
     * @return [type] [description]
     */
    public function load_theme() {
      do_action('vimeography/load-addon-plugin', __FILE__);
    }

  }

  new Vimeography_Themes_Shadow;
}