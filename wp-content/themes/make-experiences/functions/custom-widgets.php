<?php

function register_custom_widgets() {
    register_widget('shopify_widget');
    register_widget('fancy_rss_widget');
    register_widget('upcoming_mfaires_widget');
    register_widget('make_projects_widget');
}

add_action('widgets_init', 'register_custom_widgets', 99);

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

    $items = (int) $args['items'];
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

    echo '<ul class="custom-rss">';
	if(strpos($args['url'],'campus.make.co') !== false) {
		$feedItems = $rss->get_items(0);
		$count = count($feedItems);
		array_splice($feedItems, 0, $count - $items);
		$feedItems = array_reverse($feedItems);
	} else {
		$feedItems = $rss->get_items(0, $items);
	}
	
    foreach ($feedItems as $item) {

        $link = $item->get_link();
        while (stristr($link, 'http') != $link) {
            $link = substr($link, 1);
        }
        $link = esc_url(strip_tags($link));

        $title = esc_html(trim(strip_tags($item->get_title())));
        if (empty($title)) {
            $title = __('Untitled');
        }
        $title = '<div class="rssTitle">' . $title . "</div>";
		
		if (strpos($args['url'],'youtube.com/feeds') !== false && $enclosure = $item->get_enclosure()) {
			$image = '<img src="' . get_resized_remote_image_url( $enclosure->get_thumbnail(), 140, 100 )  . '" width="140" height="100" />';
		} else {
        	$image = '<img src="' . get_resized_remote_image_url( get_first_image_url($item->get_content()), 140, 100 ) . '" width="140" height="100" />';
		}

        $desc = html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'));
        $desc = esc_attr(wp_trim_words($desc, 55, ' [&hellip;]'));

        $summary = '';
        if ($show_summary) {
            $summary = $desc;

            // Change existing [...] to [&hellip;].
            if ('[...]' == substr($summary, -5)) {
                $summary = substr($summary, 0, -5) . '[&hellip;]';
            }

            $summary = '<div class="rssSummary">' . esc_html($summary) . '</div>';
        }

        $author = '';
        if ($show_author) {
            $author = $item->get_author();
            if (is_object($author)) {
                $author = $author->get_name();
                $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';
            }
        }

        $date = '';
        if ($show_date) {
            $date = '<date>' . $item->get_date('M j, Y', true) . '</date>';
        }

        if ($link == '') {
            echo "<li>{$image}<div class='rss-widget-text'>$title{$date}{$summary}{$author}</div></li>";
        } elseif ($show_summary) {
            echo "<li><a class='rss-image-link' href='$link'>{$image}</a><div class='rss-widget-text'><a class='rsswidget' href='$link'>$title{$date}{$summary}{$author}</a></div></li>";
        } else {
            echo "<li><a class='rss-image-link' href='$link'>{$image}</a><div class='rss-widget-text'><a class='rsswidget' href='$link'>$title{$date}{$author}</a></div></li>";
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