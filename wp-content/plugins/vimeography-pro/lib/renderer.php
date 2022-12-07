<?php

namespace Vimeography\Pro;

class Renderer extends \Vimeography\Renderer {
  public $version = '2.0';

  public function __construct( $engine ) {
    parent::__construct( $engine );

    add_filter('vimeography.pro.localize', array($this, 'add_pro_settings'), 10, 2 );
    add_filter('vimeography.pro.router_mode', array( $this, 'set_router_mode') );
    // add_action('wp_footer', array( $this, 'output_modified_harvestone_template') );
    // add_action('admin_footer', array( $this, 'output_modified_harvestone_template') );
  }

  /**
   * [add_pro_settings description]
   * @param [type] $settings [description]
   */
  public function add_pro_settings( $data, $gallery_settings ) {
    return array_replace_recursive( $data, array(
      'pro' => true,
      'paging' => array(
        'sort'      => isset( $gallery_settings['sort'] ) ? $gallery_settings['sort'] : 'default',
        'direction' => isset( $gallery_settings['direction'] ) ? $gallery_settings['direction'] : 'desc',
        'per_page'  => isset( $gallery_settings['per_page'] ) ? $gallery_settings['per_page'] : 25,
        'page'      => $data['page'],
        'current'   => $data['page'],
        'total'     => $data['limit'] > 0 && $data['total'] > $data['limit'] ? $data['limit'] : $data['total']
      ),
      'settings' => array(
        'player' => array(
          'transparent' => apply_filters('vimeography.player.settings.transparent', true),
          'responsive' => apply_filters('vimeography.player.settings.responsive', true),
          'speed' => apply_filters('vimeography.player.settings.speed', true),
          'playsinline' => apply_filters('vimeography.player.settings.playsinline', false),
        ),
        'downloads' => array(
          'enabled' => array_key_exists('allow_downloads', $gallery_settings) && $gallery_settings['allow_downloads'] === 1,
        ),
        'embed' => array(
          'title'  => true,
          'author' => true,
        ),
        'playlist' => array(
          'enabled' => array_key_exists('playlist', $gallery_settings) && $gallery_settings['playlist'] === 1,
        ),
        'search' => array(
          'enabled' => array_key_exists('enable_search', $gallery_settings) && $gallery_settings['enable_search'] === 1,
        ),
        'tags' => array(
          'enabled' => array_key_exists('enable_tags', $gallery_settings) && $gallery_settings['enable_tags'] === 1,
        ),
        'xhr' => array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'nonce'    => wp_create_nonce('vimeography_pro_xhr_nonce'),
        ),
      ),
      'query'  => '',
      'tags'   => $this->_aggregate_tags( $data, $gallery_settings ),
    ) );
  }


  /**
   * Consolidate all tags associated with the videos in the result set
   * and send them to the theme to be rendered.
   *
   * @param  array $videos
   * @return array $tags
   */
  private function _aggregate_tags( $data, $gallery_settings ) {
    $tags = array();

    if ( ! isset( $gallery_settings['enable_tags'] ) || $gallery_settings['enable_tags'] === 0 ) {
      return $tags;
    }

    if ( is_array( $data['video_set'] ) && ! empty( $data['video_set'] ) ) {
      foreach ( $data['video_set'] as $video ) {
        if ( ! empty( $video->tags ) ) {
          foreach ( $video->tags as $tag ) {
            $tags[] = $tag;
          }
        }
      }
    }

    $unique_tags = array_values( array_unique( $tags, SORT_REGULAR ) );
    return apply_filters('vimeography.pro.tags', $unique_tags, $gallery_settings, $data);
  }


  /**
   * [set_router_mode description]
   * @param [type] $mode [description]
   */
  public function set_router_mode( $mode ) {
    $router_mode = is_admin() ? 'abstract' : 'history';
    return $router_mode;
  }

  /**
   * [output_harvestone_template description]
   * @return [type] [description]
   */
  public function output_modified_harvestone_template() {
    if ( strtolower( $this->gallery_settings['theme'] ) !== 'harvestone' ) {
      return;
    }

    ob_start();
    ?>
    <script type="text/x-template" id="vimeography-harvestone-thumbnail">
      <figure :class="this.thumbnailClass">
        <router-link class="vimeography-link" :title="video.name" :to="this.query" exact exact-active-class="vimeography-link-active">
          <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
          <h2 class="vimeography-title">{{video.name}}</h2>
        </router-link>
      </figure>
    </script>
    <?php
    echo ob_get_clean();
    return;
  }

}
