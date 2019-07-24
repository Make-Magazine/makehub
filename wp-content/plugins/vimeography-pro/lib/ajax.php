<?php

namespace Vimeography\Pro;

/**
 * NOTES:
 *
 * 1.) If the gallery ID is POSTed along with the pagination request, we should load
 * up that gallery's cache file to check if it contains the range of videos that
 * we are looking for. Otherwise, we'll need to fetch from the remote source.
 *
 * 2.) Core should be smart enough to fetch the correct page if the class includes
 * a page number property
 *
 * - gallery id to pull the cache
 * - page to pull correct page
 * - per_page to include correct video amount on the page.
 * - limit to ensure we don't exceed that amount of total videos.
 *
 * - If no gallery id is set, then we won't pull from cache.
 * - If page is set and gallery id is set, determine if we have page in cache.
 * - If only page, then just send that along with the request to Vimeo.
 * 
 */
class Ajax {

  public function __construct() {
    add_action( 'wp_ajax_vimeography_pro_request', array($this, 'handle_request') );
    add_action( 'wp_ajax_nopriv_vimeography_pro_request', array($this, 'handle_request') );
  }

  /**
   * Handles all AJAX requests for Vimeography themes > 2.0
   *
   * @return json
   */
  public function handle_request() {

    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'vimeography_pro_xhr_nonce') ) {
      die;
    }

    $whitelist = array(
      'gallery_id',
      'page',
      'per_page',
      'source',
      'sort',
      'limit',
      'direction',
      'query',
    );

    $params = json_decode( stripslashes( $_REQUEST['payload'] ), true );

    foreach( $params as $key => $v ) {
      if ( ! in_array( $key, $whitelist ) ) {
        unset( $params[$key] );
      }
    }

    if ( isset( $params['per_page' ] ) ) {
      $params['remote_per_page'] = $params['per_page'];
    }

    if ( isset( $params['query' ] ) ) {
      $params['weak_search'] = 'true';
    }

    $engine = new \Vimeography\Engine;

    if ( isset( $params['gallery_id'] ) ) {
      $engine->set_gallery_id( $params['gallery_id'] );
    }

    try {
      $result = $engine
                ->set_gallery_settings( $params )
                ->set_theme( array('version' => '2.0') )
                ->load()
                ->fetch()
                ->post_process()
                ->to_json();

      echo $result;
      die;
    } catch (\Vimeography_Exception $e) {

      $shape = array(
        'status' => 500,
        'error' => $e->getMessage()
      );

      echo json_encode( $shape );
      die;
    }
  }
}
