<?php

namespace Vimeography\Pro;

class Init {
  public function __construct()
  {
    add_action('wp_head', array($this, 'output_single_video_meta_tags'));
  }

  /**
   * Adds open graph tags when linking to a single video in a gallery.
   *
   * @return void
   */
  public function output_single_video_meta_tags()
  {
    global $wp;

    if (!isset($_GET['vimeography_video'])) {
      return;
    }

    // for testing
    // $video_id = 489004367;
    $video_id = (int) $_GET['vimeography_video'];
    $token = get_option('vimeography_pro_access_token');

    if (!$token) {
      return;
    }

    $auth = apply_filters('vimeography-pro/edit-access-token', $token);
    $vimeo = new \Vimeography\Vimeo(null, null, $auth);

    $response = $vimeo->request(
      "/videos/" . $video_id,
      array(
        'fields' =>
          'name,link,description,duration,width,height,embed.html,created_time,pictures,tags.name,tags.canonical,stats,status,user.account'
      ),
      'GET',
      true,
      array()
    );

    if ($response['status'] !== 200) {
      return;
    }

    $video = $response['body'];

    preg_match(
      '/iframe src="([^"]+)"/',
      $video->embed->html,
      $matches,
      PREG_OFFSET_CAPTURE
    );

    if (is_array($matches) && !empty($matches[1])) {
      $player_source = $matches[1][0];
    } else {
      $player_source = "";
    }

    $thumbnail = end($video->pictures->sizes);
    $current_url = home_url(add_query_arg($_GET, $wp->request));
    $site_title = get_bloginfo('name');

    ob_start();
    ?>
      <meta property="og:title" content="<?php echo $video->name; ?>" />
      <meta property="og:description" content="<?php echo $video->description; ?>" />
      <meta property="og:type" content="video.movie" />
      <meta
        property="og:url"
        content="<?php echo esc_url($current_url); ?>"
      />
      <meta
        property="og:image"
        content="<?php echo $thumbnail->link_with_play_button; ?>"
      />
      <meta property="og:site_name" content="<?php echo $site_title; ?>" />

      <meta name="twitter:card" content="player" />
      <meta name="twitter:title" content="<?php echo $video->name; ?>" />
      <meta name="twitter:description" content="<?php echo $video->description; ?>"" />
      <meta
        name="twitter:image"
        content="<?php echo $thumbnail->link_with_play_button; ?>"
      />
      <meta
        name="twitter:player"
        content="<?php echo $player_source; ?>"
      />
      <meta name="twitter:player:width" content="<?php echo $video->width; ?>" />
      <meta name="twitter:player:height" content="<?php echo $video->height; ?>" />
    <?php echo ob_get_clean();
  }

}
