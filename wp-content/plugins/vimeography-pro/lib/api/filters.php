<?php
namespace Vimeography\Pro\Api;

class Filters extends \WP_REST_Controller
{
  public function __construct()
  {
    add_action('rest_api_init', function () {
      $this->register_routes();
    });
  }
  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes()
  {
    $version = '1';
    $namespace = 'vimeography/v' . $version;
    $base = 'filters';
    register_rest_route($namespace, '/' . $base, array(
      array(
        'methods' => \WP_REST_Server::READABLE,
        'callback' => array($this, 'get_filters'),
        'permission_callback' => array($this, 'get_filters_permissions_check'),
        'args' => array()
      ),
      array(
        'methods' => \WP_REST_Server::CREATABLE,
        'callback' => array($this, 'create_filter'),
        'permission_callback' => array($this, 'create_filter_permissions_check'),
        'args' => $this->get_endpoint_args_for_item_schema(true)
      )
    ));
    register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
      array(
        'methods' => \WP_REST_Server::READABLE,
        'callback' => array($this, 'get_filter'),
        'permission_callback' => array($this, 'get_filter_permissions_check'),
        'args' => array(
          'context' => array(
            'default' => 'view'
          )
        )
      ),
      array(
        'methods' => \WP_REST_Server::EDITABLE,
        'callback' => array($this, 'update_filter'),
        'permission_callback' => array($this, 'update_filter_permissions_check'),
        'args' => $this->get_endpoint_args_for_item_schema(false)
      ),
      array(
        'methods' => \WP_REST_Server::DELETABLE,
        'callback' => array($this, 'delete_filter'),
        'permission_callback' => array($this, 'delete_filter_permissions_check'),
        'args' => array(
          'force' => array(
            'default' => false
          )
        )
      )
    ));

    register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)/options', array(
      array(
        'methods' => \WP_REST_Server::READABLE,
        'callback' => array($this, 'get_filter_options'),
        'permission_callback' => array($this, 'get_filter_permissions_check'),
        'args' => array(
          'context' => array(
            'default' => 'view'
          )
        )
      ),
      array(
        'methods' => \WP_REST_Server::EDITABLE,
        'callback' => array($this, 'update_filter_options'),
        'permission_callback' => array($this, 'update_filter_permissions_check'),
        'args' => $this->get_endpoint_args_for_item_schema(false)
      )
    ));

    register_rest_route($namespace, '/' . $base . '/schema', array(
      'methods' => \WP_REST_Server::READABLE,
      'callback' => array($this, 'get_public_item_schema')
    ));
  }

  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_filters($request)
  {
    $items = array(); //do a query, call another class, etc
    $data = array();

    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM " . $wpdb->vimeography_pro_filters);

    return $result;

    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  /**
   * Get one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_filter($request)
  {
    $params = $request->get_params();

    global $wpdb;
    $results = $wpdb->get_results('
      SELECT * FROM '.$wpdb->vimeography_pro_filters.' WHERE id = '. intval($params['id']) .'
      LIMIT 1;
    ');

    if ( ! $results ) {
      return new \WP_Error('cant-update', __('message', 'text-domain'), array(
        'status' => 500
      ));
    }

    $filter = $results[0];
    $options = json_decode($filter->options);
    $filter->options = $options;

    return new \WP_REST_Response($filter, 200);
  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function create_filter($request)
  {
    $item = $this->prepare_item_for_database($request);

    global $wpdb;
    $result = $wpdb->insert( $wpdb->vimeography_pro_filters, $item );

    return new \WP_REST_Response(null, 201);

    return new WP_Error('cant-create', __('message', 'text-domain'), array(
      'status' => 500
    ));
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_filter($request)
  {
    $item = $this->prepare_item_for_database($request);

    if (function_exists('slug_some_function_to_update_filter')) {
      $data = slug_some_function_to_update_filter($item);
      if (is_array($data)) {
        return new WP_REST_Response($data, 200);
      }
    }

    return new WP_Error('cant-update', __('message', 'text-domain'), array(
      'status' => 500
    ));
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_filter($request)
  {
    $item = $this->prepare_item_for_database($request);

    if (function_exists('slug_some_function_to_delete_filter')) {
      $deleted = slug_some_function_to_delete_filter($item);
      if ($deleted) {
        return new WP_REST_Response(true, 200);
      }
    }

    return new WP_Error('cant-delete', __('message', 'text-domain'), array(
      'status' => 500
    ));
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_filters_permissions_check($request)
  {
    return true;
  }

  /**
   * Check if a given request has access to get a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_filter_permissions_check($request)
  {
    return $this->get_filters_permissions_check($request);
  }

  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_filter_permissions_check($request)
  {
    return current_user_can('edit_posts'); // admin
  }

  /**
   * Check if a given request has access to update a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function update_filter_permissions_check($request)
  {
    return $this->create_filter_permissions_check($request);
  }

  /**
   * Check if a given request has access to delete a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function delete_filter_permissions_check($request)
  {
    return $this->create_filter_permissions_check($request);
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database($request)
  {
    $payload = json_decode( $request->get_body() );

    $filter = array(
      'title' => sanitize_text_field($payload->title),
      'slug'  => sanitize_title($payload->title),
      'type' => 'RADIO',
      'sort_by' => 'OPTION_POSITION',
      'options' => json_encode(array())
    );

    return $filter;
  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response($item, $request)
  {
    return array();
  }

  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function get_collection_params()
  {
    return array(
      'page' => array(
        'description' => 'Current page of the collection.',
        'type' => 'integer',
        'default' => 1,
        'sanitize_callback' => 'absint'
      ),
      'per_page' => array(
        'description' =>
          'Maximum number of items to be returned in result set.',
        'type' => 'integer',
        'default' => 10,
        'sanitize_callback' => 'absint'
      ),
      'search' => array(
        'description' => 'Limit results to those matching a string.',
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field'
      )
    );
  }

  /**
   * Get all of the options associated with a given filter ID
   */
  protected function fetch_filter_options($id) {
    global $wpdb;
    
    $results = $wpdb->get_results('
      SELECT * FROM '.$wpdb->vimeography_pro_filters.' WHERE id = '. intval($id) .'
      LIMIT 1;
    ');

    if ( ! $results ) {
      return new WP_Error('cant-update', __('message', 'text-domain'), array(
        'status' => 500
      ));
    }

    $category = $results[0];
    $options = json_decode($category->options);
    return $options;
  }

  /**
   * Get all of the options associated with a given filter ID
   */
  public function get_filter_options($request) {
    $params = $request->get_params();
    $options = $this->fetch_filter_options( $params['id'] );
    return $options;
  }

  /**
   * Update all of the options associated with a given filter ID
   */
  public function update_filter_options($request) {
    global $wpdb;
    $params = $request->get_params();

    $result = $wpdb->update(
      $wpdb->vimeography_pro_filters,
      array( 'options' => json_encode($params["options"])),
      array( 'id' => $params['id'] ),
      array('%s'),
      array('%d')
    );

    if ($result === false) {
      return new WP_Error('cant-update', __('message', 'text-domain'), array(
        'status' => 500
      ));
    }

    return $params["options"];
  }

}
