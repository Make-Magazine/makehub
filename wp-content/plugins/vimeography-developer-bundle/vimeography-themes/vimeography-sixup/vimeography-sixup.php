<?php
/*
Plugin Name: Vimeography Theme: Sixup
Plugin URI: https://vimeography.com/themes
Theme Name: Sixup
Theme URI:  https://vimeography.com/themes/sixup
Version: 2.0.6
Description: Sixup displays the 6 latest video thumbnails from your Vimeo collection, which can be clicked for navigating the slider.
Author: Dave Kiss
Author URI: https://vimeography.com
Copyright: 2018 Dave Kiss
*/

if ( ! class_exists('Vimeography_Themes_Sixup') ) {

  class Vimeography_Themes_Sixup {

    /**
     * The current version of this Vimeography theme.
     *
     * Make sure to specify it here as well as above
     * in the header metadata and in the readme.txt
     * file, located in the root of the plugin directory.
     *
     * @var string
     */
    public $version = '2.0.6';


    /**
     * The constructor is used to load the plugin
     * when the WordPress `plugins_loaded` hook is fired.
     *
     * This includes this theme in the Vimeography theme loader.
     */
    public function __construct() {
      add_action('plugins_loaded', array( $this, 'load_theme' ) );
      add_action('plugins_loaded', array( $this, 'update_db_records') );
    }


    /**
     * Has to be public so the wp actions can reach it.
     * @return [type] [description]
     */
    public function load_theme() {
      do_action('vimeography/load-addon-plugin', __FILE__);
    }


    /**
     * Change the theme_name in the Vimeography database table
     * from "6up" to "Sixup"
     *
     * This method is unique to the sixup theme.
     *
     * @return void
     */
    public function update_db_records() {
      $routine_performed = get_site_option('vimeography_sixup_2.0_routine_performed');

      if ( ! $routine_performed ) {
        global $wpdb;
        $result = $wpdb->query("UPDATE $wpdb->vimeography_gallery_meta SET theme_name = 'Sixup' WHERE theme_name = '6up';");

        update_site_option('vimeography_sixup_2.0_routine_performed', true);
      }
    }

  }

  new Vimeography_Themes_Sixup;
}