<?php

namespace EasyAffiliate\Controllers;

use EasyAffiliate\Lib\CtaxCtrl;
use EasyAffiliate\Models\Campaign;

class CampaignsCtrl extends CtaxCtrl {
  public function load_hooks() {
    $this->cpts = ['esaf-creative'];

    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    add_action(Campaign::$ctax . '_edit_form', [$this, 'display_form'], 10, 2);
  }

  public function register_taxonomy() {
    $this->ctax = (object) [
      'label' => esc_html__('Campaigns', 'easy-affiliate'),
      'labels' => [
        'name' => esc_html__('Campaigns', 'easy-affiliate'),
        'singular_name' => esc_html__('Campaign', 'easy-affiliate'),
        'search_items' => esc_html__('Search Campaigns', 'easy-affiliate'),
        'popular_items' => esc_html__('Popular Campaigns', 'easy-affiliate'),
        'all_items' => esc_html__('All Campaigns', 'easy-affiliate'),
        'edit_item' => esc_html__('Edit Campaign', 'easy-affiliate'),
        'view_item' => esc_html__('View Campaign', 'easy-affiliate'),
        'update_item' => esc_html__('Update Campaign', 'easy-affiliate'),
        'add_new_item' => esc_html__('Add New Campaign', 'easy-affiliate'),
        'separate_items_with_commas' => esc_html__('Separate campaigns with commas', 'easy-affiliate'),
        'add_or_remove_items' => esc_html__('Add or remove campaigns', 'easy-affiliate'),
        'choose_from_most_used' => esc_html__('Choose from the most used campaigns', 'easy-affiliate'),
        'not_found' => esc_html__('No campaigns found', 'easy-affiliate'),
        'no_terms' => esc_html__('No campaigns', 'easy-affiliate'),
        'back_to_items' => esc_html__('&larr; Back to Campaigns', 'easy-affiliate')
      ],
      'public' => false,
      'hierarchical' => false,
      'show_ui' => true,
      'show_in_menu' => false,
      'show_in_nav_menus' => false,
      'query_var' => true,
      'rewrite' => [
        'slug' => Campaign::$ctax,
        'with_front' => true,
      ],
      'show_admin_column' => true,
      'show_in_rest' => false,
      'rest_base' => '',
      'show_in_quick_edit' => true,
    ];

    register_taxonomy(Campaign::$ctax, $this->cpts, $this->ctax);
  }

  public function enqueue_scripts() {
    // TODO
  }

  public function display_form($tag, $taxonomy) {
    if($taxonomy != Campaign::$ctax) {
      return;
    }

    // TODO: We'll use this function to add additional
    //       form fields to the taxonomy edit page.  //$campaign = new Campaign($tag->term_id);
  }

} //End class
