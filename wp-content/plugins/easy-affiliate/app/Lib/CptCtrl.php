<?php

namespace EasyAffiliate\Lib;

abstract class CptCtrl extends BaseCtrl {
  public $cpt, $ctaxes;

  public function __construct() {
    add_action('init', [$this, 'register_post_type'], 1);
    add_filter('post_updated_messages', [$this, 'post_updated_messages']);
    add_filter('bulk_post_updated_messages', [$this, 'bulk_post_updated_messages'], 10, 2);
    parent::__construct();
  }

  abstract public function register_post_type();

  /** Used to ensure we don't see any references to 'post' or a link when. */
  public function post_updated_messages($messages) {
    global $post, $post_ID;

    if(!isset($this->cpt) || !isset($this->cpt->config)) {
      return $messages;
    }

    $singular_name = $this->cpt->config['labels']['singular_name'];
    $slug = $this->cpt->slug;
    $public = $this->cpt->config['public'];

    $messages[$slug] = [];
    $messages[$slug][0] = '';

    if($public) {
      $messages[$slug][1] = sprintf(
        // translators: %1$s: post type name, %2$s: open link tag, %3$s: close link tag
        esc_html__( '%1$s updated. %2$sView %1$s%3$s', 'easy-affiliate' ),
        esc_html( $singular_name ),
        sprintf( '<a href="%s">', esc_url( get_permalink($post_ID) ) ),
        '</a>'
      );
    }
    else {
      $messages[$slug][1] = esc_html( sprintf( __('%1$s updated.', 'easy-affiliate'), $singular_name ) );
    }

    $messages[$slug][2] = esc_html__('Custom field updated.', 'easy-affiliate');
    $messages[$slug][3] = esc_html__('Custom field deleted.', 'easy-affiliate');
    $messages[$slug][4] = esc_html( sprintf( __('%s updated.', 'easy-affiliate'), $singular_name ) );
    $messages[$slug][5] = isset($_GET['revision']) ? esc_html( sprintf( __('%1$s restored to revision from %2$s', 'easy-affiliate'), $singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) ) : false;

    if($public) {
      $messages[$slug][6] = sprintf(
        // translators: %1$s: post type name, %2$s: open link tag, %3$s: close link tag
        esc_html__( '%1$s published. %2$sView %1$s%3$s', 'easy-affiliate' ),
        esc_html( $singular_name ),
        sprintf( '<a href="%s">', esc_url( get_permalink($post_ID) ) ),
        '</a>'
      );
    }
    else {
      $messages[$slug][6] = esc_html( sprintf( __('%1$s published.', 'easy-affiliate'), $singular_name ) );
    }

    $messages[$slug][7] = esc_html( sprintf( __('%s saved.', 'easy-affiliate'), $singular_name ) );

    if($public) {
      $messages[$slug][8] = sprintf(
        // translators: %1$s: post type name, %2$s: open link tag, %3$s: close link tag
        esc_html__( '%1$s submitted. %2$sPreview %1$s%3$s', 'easy-affiliate' ),
        esc_html( $singular_name ),
        sprintf( '<a target="_blank" href="%s">', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        '</a>'
      );

      $messages[$slug][9] = sprintf(
        // translators: %1$s: post type name, %2$s: scheduled date, %3$s: open link tag, %4$s: close link tag
        esc_html__( '%1$s scheduled for: %2$s. %3$sPreview %1$s%4$s', 'easy-affiliate' ),
        esc_html( $singular_name ),
        sprintf( '<strong>%s</strong>', esc_html( date_i18n('M j, Y @ G:i', strtotime($post->post_date), true) ) ),
        sprintf( '<a target="_blank" href="%s">', esc_url( get_permalink($post_ID) ) ),
        '</a>'
      );

      $messages[$slug][10] = sprintf(
        // translators: %1$s: post type name, %2$s: open link tag, %3$s: close link tag
        esc_html__( '%1$s draft updated. %2$sPreview %1$s%3$s', 'easy-affiliate' ),
        esc_html( $singular_name ),
        sprintf( '<a target="_blank" href="%s">', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        '</a>'
      );
    }
    else {
      $messages[$slug][8] = esc_html( sprintf( __('%s submitted.', 'easy-affiliate'), $singular_name ) );
      $messages[$slug][9] = sprintf(
        // translators: %1$s: post type name, %2$s: scheduled date
        esc_html__('%1$s scheduled for: %2$s.', 'easy-affiliate'),
        esc_html( $singular_name ),
        sprintf('<strong>%s</strong>', esc_html( date_i18n('M j, Y @ G:i', strtotime($post->post_date), true) ) )
      );
      $messages[$slug][10] = esc_html( sprintf( __('%s draft updated.', 'easy-affiliate'), $singular_name ) );
    }

    return $messages;
  }

  /* Modify the bulk update messages for the cpt associated with this controller */
  public function bulk_post_updated_messages($messages, $counts) {
    if(!isset($this->cpt) || !isset($this->cpt->config)) {
      return $messages;
    }

    $singular_name = strtolower( $this->cpt->config['labels']['singular_name'] );
    $plural_name = strtolower( $this->cpt->config['labels']['name'] );
    $slug = $this->cpt->slug;

    $messages[$slug] = array(
      'updated'   => esc_html( _n( sprintf('%1$d %2$s updated.', $counts['updated'], $singular_name),
                         sprintf('%1$d %2$s updated.', $counts['updated'], $plural_name),
                         $counts['updated'] , 'easy-affiliate') ),
      'locked'    => esc_html( _n( sprintf('%1$d %2$s not updated, somebody is editing it.', $counts['locked'], $singular_name),
                         sprintf('%1$d %2$s not updated, somebody is editing them.', $counts['locked'], $plural_name),
                         $counts['locked'] , 'easy-affiliate') ),
      'deleted'   => esc_html( _n( sprintf('%1$d %2$s permanently deleted.', $counts['deleted'], $singular_name),
                         sprintf('%1$d %2$s permanently deleted.', $counts['deleted'], $plural_name),
                         $counts['deleted'] , 'easy-affiliate') ),
      'trashed'   => esc_html( _n( sprintf('%1$s %2$s moved to the Trash.', $counts['trashed'], $singular_name),
                         sprintf('%1$s %2$s moved to the Trash.', $counts['trashed'], $plural_name),
                         $counts['trashed'] , 'easy-affiliate') ),
      'untrashed' => esc_html( _n( sprintf('%1$s %2$s restored from the Trash.', $counts['untrashed'], $singular_name),
                         sprintf('%1$s %2$s restored from the Trash.', $counts['untrashed'], $plural_name),
                         $counts['untrashed'] , 'easy-affiliate') )
    );

    return $messages;
  }
}
