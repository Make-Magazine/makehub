<?php

class Vimeography_Pro_Admin_Scripts
{
  public function __construct()
  {
    add_action('admin_enqueue_scripts', array($this, 'add_scripts'), 20, 1);
  }

  /**
   * Load the common admin scripts across the Vimeography plugin.
   *
   * @param string $hook  slug of the current admin page
   */
  public function add_scripts($hook)
  {
    if (
      strpos($hook, 'vimeography') !== false &&
      strpos($hook, 'vimeography-stats') == false
    ) {

      switch ($hook) {
        case 'vimeography_page_vimeography-new-gallery':
          break;
        case "toplevel_page_vimeography-edit-galleries":
          if (defined('VIMEOGRAPHY_DEV') && VIMEOGRAPHY_DEV) {
            
            wp_enqueue_script(
              'vimeography_pro_admin',
              'https://localhost:8025/remoteEntry.js'
            );

          } else {
            $manifest = VIMEOGRAPHY_PRO_PATH . 'lib/admin/app/dist/manifest.json';
            $manifest = file_get_contents($manifest);
            $manifest = (array) json_decode($manifest);

            $script_url = VIMEOGRAPHY_PRO_URL . 'lib/admin/app/dist/' . $manifest['vimeography_pro.js'];
            wp_enqueue_script(
              'vimeography_pro_admin',
              $script_url,
              array(),
              "1.0",
              true
            );
          }

          wp_add_inline_script('vimeography_pro_admin', 'var vimeographyExportGalleriesUrl = "' . add_query_arg( 'vimeography-action', 'export_galleries', menu_page_url('vimeography-edit-galleries', false) ) . '";');
          wp_add_inline_script('vimeography_pro_admin', 'var vimeographyImportGalleriesUrl = "' . add_query_arg( 'vimeography-action', 'import_galleries', menu_page_url('vimeography-edit-galleries', false) ) . '";');
          break;
        default:
          break;
      }
    }
  }
}
