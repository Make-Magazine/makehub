<?php

function register_custom_widgets() {
    register_widget('shopify_widget');
    register_widget('fancy_rss_widget');
    register_widget('upcoming_mfaires_widget');
    register_widget('make_projects_widget');
}

add_action('widgets_init', 'register_custom_widgets', 99);

function my_add_force_rss($feed,$url){
   $feed->force_feed(true);
   $feed->enable_order_by_date(false);
}
add_action('wp_feed_options', 'my_add_force_rss', 10,2);

function make_widget_rss_output($rss, $args = array()) {
    if (is_string($rss)) {
        $rss = fetch_feed($rss);
    } elseif (is_array($rss) && isset($rss['url'])) {
        $args = $rss;
        $rss = fetch_feed($rss['url']);
    } elseif (!is_object($rss)) {
        return;
    }
    
    if (is_wp_error($rss)) {
        if (is_admin() || current_user_can('manage_options')) {
            echo '<p><strong>' . __('RSS Error:') . '</strong> ' . $rss->get_error_message() . '</p>';
        }
        return;
    }
    
    $default_args = array(
        'show_author' => 0,
        'show_date' => 0,
        'show_summary' => 0,
        'items' => 0,
    );
    $args = wp_parse_args($args, $default_args);
    
    $items = (int) $args['items']; // this is the number of items we show
    if ($items < 1 || 20 < $items) {
        $items = 10;
    }
    $show_summary = (int) $args['show_summary'];
    $show_author = (int) $args['show_author'];
    $show_date = (int) $args['show_date'];
    
    if (!$rss->get_item_quantity()) {
        echo '<ul><li>' . __('An error has occurred, which probably means the feed is down. Try again later.') . '</li></ul>';
        $rss->__destruct();
        unset($rss);
        return;
    }
    
    $dateNow = new DateTime('now');
    
    $sortedFeed = array();
    $feedItems = $rss->get_items();
    $i = 0;
    foreach ($feedItems as $item) {
        //exclude events that have already occurred
        
        $date = '';
        if(is_array($item->get_item_tags('', 'event_date'))){
            if($item->get_item_tags('', 'event_date')[0]['data']) {
                $dateString = new DateTime($item->get_item_tags('', 'event_date')[0]['data']);
                if(date_timestamp_get($dateNow) > 	date_timestamp_get($dateString)){
                    continue; //(skip this record);
                }
                // if it isn't a youtube feed, exclude feed items with no date
            } else if(strpos($args['url'], 'youtube.com/feeds') == false ) {
                if(!$item->get_item_tags('', 'pubDate')[0]['data']){
                    continue; //(skip this record);
                }
                $dateString = new DateTime($item->get_item_tags('', 'pubDate')[0]['data']);
            }
            if ($show_date) {
                $date = $dateString->format('M j, Y');
            }
        }
        
        //get the link
        $link = $item->get_link();
        while (stristr($link, 'http') != $link) {
            $link = substr($link, 1);
        }
        $link = esc_url(strip_tags($link));
        
        //set the title
        $title = esc_html(trim(strip_tags($item->get_title())));
        if (empty($title)) {
            $title = __('Untitled');
        }
        $title = '<div class="rssTitle">' . $title . "</div>";
        
        //set image
        if (strpos($args['url'], 'youtube.com/feeds') !== false && $enclosure = $item->get_enclosure()) {
            $image = '<img src="' . get_resized_remote_image_url($enclosure->get_thumbnail(), 140, 100) . '" width="140" height="100" />';
        } else {
            $image = '<img src="' . get_resized_remote_image_url(get_first_image_url($item->get_content()), 140, 100) . '" width="140" height="100" />';
        }
        
        //set description
        $desc = html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'));
        $desc = esc_attr(wp_trim_words($desc, 55, ' [&hellip;]'));
        
        //summary
        $summary = '';
        if ($show_summary) {
            $summary = $desc;
            
            // Change existing [...] to [&hellip;].
            if ('[...]' == substr($summary, -5)) {
                $summary = substr($summary, 0, -5) . '[&hellip;]';
            }
            
            $summary = '<div class="rssSummary">' . esc_html($summary) . '</div>';
        }
        
        //author
        $author = '';
        if ($show_author) {
            $author = $item->get_author();
            if (is_object($author)) {
                $author = $author->get_name();
                $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';
            }
        }
        
        $sortedFeed[] = array('date' => $date, 'show_date' => $show_date, 'link' => $link, 'title' => $title, 'image' => $image, 'desc' => $desc, 'summary' => $summary, 'author' => $author);
        //sort this by date, newest first, if it's an event
        if(is_array($item->get_item_tags('', 'event_date'))){
            if($item->get_item_tags('', 'event_date')[0]['data']) {
                usort($sortedFeed, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
            }
        }
        // limit by items
        if (++$i == $items) break;
    }
    
    echo '<ul class="custom-rss">';
    foreach ($sortedFeed as $item) {
        $link       = $item['link'];
        $title      = $item['title'];
        $image      = $item['image'];
        $desc       = $item['desc'];
        $summary    = $item['summary'];
        $author     = $item['author'];
        
        if ($item['show_date']) {
            $date = '<date>' . $item['date'] . '</date>';
        }
        
        if ($link == '') {
            echo "<li>{$image}<div class='rss-widget-text'>$title{$date}{$summary}{$author}</div></li>";
        } elseif ($show_summary) {
            echo "<li><a class='rss-image-link' href='$link' target='_blank'>{$image}</a><div class='rss-widget-text'><a class='rsswidget' href='$link' target='_blank'>$title{$date}{$summary}{$author}</a></div></li>";
        } else {
            echo "<li><a class='rss-image-link' href='$link' target='_blank'>{$image}</a><div class='rss-widget-text'><a class='rsswidget' href='$link' target='_blank'>$title{$date}{$author}</a></div></li>";
        }
    }
    echo '</ul>';
    $rss->__destruct();
    unset($rss);
}

function html_widget_title($title) {
    //HTML tag opening/closing brackets
    $title = str_replace('[', '<', $title);
    $title = str_replace('[/', '</', $title);
    // bold — changed from 's' to 'strong' because of strikethrough code
    $title = str_replace('strong]', 'strong>', $title);
    $title = str_replace('b]', 'b>', $title);
    // italic
    $title = str_replace('em]', 'em>', $title);
    $title = str_replace('i]', 'i>', $title);
    return $title;
}

add_filter('widget_title', 'html_widget_title');
