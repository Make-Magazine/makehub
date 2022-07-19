<?php
function featuredtoRSS($content) {
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<div>' . get_the_post_thumbnail($post->ID, 'medium', array('style' => 'margin-bottom: 15px;')) . '</div>' . $content;
    }
    return $content;
}
add_filter('the_excerpt_rss', 'featuredtoRSS', 20, 1);
add_filter('the_content_feed', 'featuredtoRSS', 20, 1);

function add_event_date_to_rss() {
    global $post;
    if (get_post_type() == 'espresso_events') {
        //determine start date
        $event = EEM_Event::instance()->get_one_by_ID($post->ID);
        $date = $event->first_datetime();
        $start_date = date('m/d/Y', strtotime($date->start_date()));
        ?>
        <event_date><?php echo $start_date ?></event_date>
        <?php
    }
}
add_action('rss2_item', 'add_event_date_to_rss', 30, 1);

// Exclude espresso_events from rss feed if marked for supression
function filter_posts_from_rss($where, $query = NULL) {
    global $wpdb;

    if (isset($query->query['post_type']) && !$query->is_admin && $query->is_feed && $query->query['post_type']) {
  		if($query->query['post_type'] == 'espresso_events') {
  			$dbSQL = "SELECT post_id FROM `wp_postmeta` WHERE `meta_key` LIKE 'suppress_from_rss_widget' and meta_value = 1";
  			$results = $wpdb->get_results($dbSQL);
  			$suppression_IDs = array();

  			foreach($results as $result){
  				$suppression_IDs[] = $result->post_id;
  			}

  			$exclude = implode(",", $suppression_IDs);

  			if (!empty($exclude)) {
  				$where .= ' AND wp_posts.ID NOT IN (' . $exclude . ')';
  			}
  		}
    }
    return $where;
}
add_filter( 'posts_where', 'filter_posts_from_rss', 1, 4 );
