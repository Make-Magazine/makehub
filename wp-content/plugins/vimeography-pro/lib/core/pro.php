<?php

namespace Vimeography\Pro;

class Core extends \Vimeography\Core {


  /**
   * Page number of videos to retrieve from Vimeo
   *
   * @var integer
   */
  private $_page = 1;


  /**
   * Number of videos to fetch for each page.
   *
   * To maximize cache efficiency, we'll fetch the highest
   * possible number of videos and then perform a range select on the response using
   * our local per_page preference.
   *
   * This can be overridden by setting the `remote_per_page` setting
   * in any request, which would return that number instead.
   *
   * @var integer
   */
  private $_per_page = 100;


  /**
   * Method to sort by: date, likes, comments, plays, alphabetical, duration
   *
   * @var string
   */
  private $_sort = 'date';


  /**
   * Descending or ascending order
   *
   * @var string
   */
  private $_direction = 'desc';


  /**
   * [$_containing_uri description]
   * @var string
   */
  private $_containing_uri = null;


  /**
   * Search term to pass to the Vimeo API.
   *
   * @var string
   */
  private $_query;


  /**
   * Instantiates the Vimeo library with the user's
   * stored access token and sets the Vimeo source type for the request.
   */
  public function __construct( $engine ) {
    parent::__construct( $engine );

    if ( ( $this->_auth = get_option('vimeography_pro_access_token') ) === false ) {
      throw new \Vimeography_Exception(
        sprintf(
          __('Visit the <a href="%1$s" title="Vimeography Pro">Vimeography Pro</a> page to finish setting up Vimeography Pro.', 'vimeography-pro'),
          network_admin_url('admin.php?page=vimeography-pro')
        )
      );
    }

    $this->_auth = apply_filters( 'vimeography-pro/edit-access-token', $this->_auth );
    $this->_vimeo = new \Vimeography\Vimeo( null, null, $this->_auth );

    add_filter( 'vimeography.request.fields', array( $this, 'add_request_fields' ) );
    add_filter( 'vimeography.pro.paginate', array( $this, 'get_video_page' ), 10, 2 );
    add_filter( 'vimeography.pro.post_process', array( $this, 'post_process' ) );

    if ( isset( $this->gallery_settings['page'] ) ) {
      $this->_page = absint( $this->gallery_settings['page'] );
    }

    // This must stay set in case the incoming request is an AJAX request for a
    // page that isn't in the current cache.
    if ( isset( $this->gallery_settings['remote_per_page'] ) ) {
      $this->_per_page = absint( $this->gallery_settings['remote_per_page'] );
    }

    if ( isset( $this->gallery_settings['sort'] ) ) {
      $this->_sort = $this->gallery_settings['sort'];
    }

    if ( isset( $this->gallery_settings['direction'] ) ) {
      $this->_direction = $this->gallery_settings['direction'];
    }

    if ( isset( $this->gallery_settings['containing_uri'] ) ) {
      $this->_containing_uri = $this->gallery_settings['containing_uri'];
    }

    if ( isset( $this->gallery_settings['query'] ) ) {
      $this->_query = $this->gallery_settings['query'];
    }

    if ( isset( $this->gallery_settings['limit'] ) ) {
      $this->_limit = absint( $this->gallery_settings['limit'] );
    }

    $this->_params = $this->_add_request_params();
  }


  /**
   * If we have the requested video range in the cache,
   * return the video range. Otherwise, go and get the
   * requested range.
   *
   * @param  object $cache     Cached Vimeo API response
   * @return object            Vimeo data
   */
  public function get_video_page( $cache, $gallery_id ) {

    if ($this->gallery_id !== $gallery_id) {
      return $cache;
    }

    $start = 1 + ( $this->_page * $this->_per_page ) - $this->_per_page;
    $end = $this->_page * $this->_per_page;

    if ( $this->_limit !== 0 && $end > $this->_limit ) {
      $end = $this->_limit;
    }

    $length = ($end - $start) + 1;
    $video_count = count( $cache->video_set );

    if (
      ($start === 1 && $video_count <= $end) ||
      $video_count >= $end
    ) {
      // Cache contains requested video range, return it from cache instead
      // of making a fresh request to Vimeo.

      // echo '<pre>';
      // print_r('Start: ' . $start);
      // echo '</pre>';
      // echo '<pre>';
      // print_r('End: ' . $end);
      // echo '</pre>';
      // echo '<pre>';
      // print_r('Page: ' . $this->_page);
      // echo '</pre>';

      // echo '<pre>';
      // print_r('Per: ' . $this->_per_page);
      // echo '</pre>';
      // echo '<pre>';
      // print_r('Limit: ' . $this->_limit);
      // echo '</pre>';
      // echo '<pre>';
      // print_r('Length: ' . $length);
      // echo '</pre>';
      // die;
      $cache->video_set = array_slice( $cache->video_set, $start - 1, $length );
      $cache->page = $this->_page;
      $cache->per_page = $this->_per_page;

      return $cache;
    } else {
      // Cache does not include range, go get it from the API.
      $response = $this->fetch();
      return $response;
    }
  }

  /**
   * Truncate the number of videos per page to the amount preferred by the gallery settings.
   *
   * @param  [type] $videos [description]
   * @return [type]         [description]
   */
  public function post_process( $videos ) {
    return array_slice( $videos, 0, $this->gallery_settings['per_page'] );
  }

  /**
   * Verifies that the resource is a valid Vimeo resource.
   *
   * @param  string $resource [description]
   * @return [type]           [description]
   */
  protected function _verify_vimeo_resource($resource) {
    return preg_match('#^/(users|channels|albums|groups|portfolios|categories|tags)/(.+)#', $resource);
  }

  /**
   * Adds the common request data to the current Vimeo request.
   *
   * @return array The params of the current request
   */
  private function _add_request_params() {
    $params = array(
      'page'      => $this->_page,
      'per_page'  => $this->_per_page,
      'sort'      => $this->_sort,
    );

    /**
     * Direction is not a valid parameter
     * if the sort order is set to default
     */
    if ( $params['sort'] !== 'default' ) {
      $params['direction'] = $this->_direction;
    }

    if ( $this->_containing_uri ) {
      $params['containing_uri'] = $this->_containing_uri;
    }

    if ( $this->_query ) {
      $params['query'] = $this->_query;
      $params['weak_search'] = 'true';
    }

    return $params;
  }

  /**
   * Request additional fields from the Vimeo API.
   *
   * @param array $fields The fields requested by Vimeography Lite.
   */
  public function add_request_fields( $fields ) {
    $fields[] = 'download';
    $fields[] = 'privacy.view';
    $fields[] = 'metadata.connections.texttracks';
    return $fields;
  }
}