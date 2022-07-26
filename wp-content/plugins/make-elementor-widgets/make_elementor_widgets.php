<?php
/**
 * Plugin Name: Make: Elementor Widgets
 * Description: This plugin adds some common Make: dashboard widgets including: Makershed purchases, My Makerspace listings, Facilitator Event listings, Maker Campus tickets, Maker camp projects
 * Version:     1.2.0
 * Author:      Alicia Williams
 * Text Domain: elementor-make-widget
 *
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_make_widgets( $widgets_manager ) {
	// Include all function files in the makerfaire/functions directory:
	foreach (glob(__DIR__ . '/widgets/*.php') as $file) {
		require_once($file);
	}

	$widgets_manager->register( new \Elementor_mShedPurch_Widget() );
	$widgets_manager->register( new \Elementor_myMspaces_Widget() );
	$widgets_manager->register( new \Elementor_MakeFacilitatorEvents_Widget() );
	$widgets_manager->register( new \Elementor_MyCampusTickets_Widget() );
	$widgets_manager->register( new \Elementor_MyMakerCamp_Widget() );
	$widgets_manager->register( new \Elementor_makeCustomRss_Widget() );
	$widgets_manager->register( new \Elementor_upcomingMakerFaires_Widget() );
	$widgets_manager->register( new \Elementor_makeInitatives_Widget() );

}
add_action( 'elementor/widgets/register', 'register_make_widgets' );

/* Add new Make: category for our widgets */
function add_elementor_widget_categories( $elements_manager ) {
	$elements_manager->add_category(
		'make-category',
		[
			'title' => esc_html__( 'Make:', 'elementor-make-widget' ),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );

function build_ee_ticket_section($event, $user_email) {
    // if the first date of event has passed and it's a multiday event with one ticket, skip this item in the loop
    $firstExpiredDate = EEM_Datetime::instance()->get_oldest_datetime_for_event($event->ID(), true, false, 1)->start();
    $now = new DateTime("now", new DateTimeZone('America/Los_Angeles'));
    $now = $now->format('Y-m-d H:i:s');
    $past_event = (date('Y-m-d H:i:s', $firstExpiredDate) < $now ? TRUE : FALSE);
    $registrations = $event->get_many_related('Registration', array(array('Attendee.ATT_email' => $user_email)));
    $time_range = espresso_event_date_range('', '', '', '', $event->ID(), FALSE);
    //get group link
    $group_id = get_field('group_id', $event->ID());
    $group = groups_get_group(array('group_id' => $group_id));
    $group_link = bp_get_group_link($group);

    //build the inner rows
    $return = '<tr class="ee-my-events-event-section-summary-row">
                    <td>' . $group_link . '</td>
                    <td>' . $time_range . '</td>
                    <td>' . count($registrations) . ' </td>
                    <td>';
    foreach ($registrations as $registration) {
        if (!$registration instanceof EE_Registration) {
            continue;
        }
        $actions = array();
        $link_to_edit_registration_text = esc_html__('Link to edit registration.', 'event_espresso');
        $link_to_make_payment_text = esc_html__('Link to make payment', 'event_espresso');
        $link_to_view_receipt_text = esc_html__('Link to view receipt', 'event_espresso');
        $link_to_view_invoice_text = esc_html__('Link to view invoice', 'event_espresso');

        //attendee name
        $attendee = $registration->attendee();
        $return .= $attendee->full_name() . '<br/>';

        if (!$past_event) {
            // only show the edit registration link IF the registration has question groups.
            $actions['edit_registration'] = $registration->count_question_groups() ? '<a aria-label="' . $link_to_edit_registration_text
                    . '" title="' . $link_to_edit_registration_text
                    . '" href="' . $registration->edit_attendee_information_url() . '">'
                    . '<span class="ee-icon ee-icon-user-edit ee-icon-size-16"></span></a>' : '';

            // resend confirmation email.
            $resend_registration_link = add_query_arg(
                    array('token' => $registration->reg_url_link(), 'resend' => true),
                    null
            );
        }

        // make payment?
        if ($registration->is_primary_registrant() && $registration->transaction() instanceof EE_Transaction && $registration->transaction()->remaining()) {
            $actions['make_payment'] = '<a aria-label="' . $link_to_make_payment_text
                    . '" title="' . $link_to_make_payment_text
                    . '" href="' . $registration->payment_overview_url() . '">'
                    . '<span class="dashicons dashicons-cart"></span></a>';
        }

        // receipt link?
        if ($registration->is_primary_registrant() && $registration->receipt_url()) {
            $actions['receipt'] = '<a aria-label="' . $link_to_view_receipt_text
                    . '" title="' . $link_to_view_receipt_text
                    . '" href="' . $registration->receipt_url() . '">'
                    . '<span class="dashicons dashicons-media-default ee-icon-size-18"></span></a>';
        }

        // ...and echo the actions!
        if (!empty($actions))
            $return .= implode('&nbsp;', $actions) . '<br/>';
    }

    $return .= '    </td>
                </tr>';

    return $return;
}
function makewidget_rss_output($rss, $settings) {
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
    $args = $default_args;
    $items = (int) $settings['num_display']; // this is the number of items we show

    $show_summary = $settings['show_summary'];
    $show_author = $settings['show_author'];
    $show_date = $settings['show_date'];
	$feed_link = $settings['link'];
	$title_position = $settings['title_position'];
	$horizontal = $settings['horizontal_display'];
	$carousel = $settings['carousel'];
	$read_more = $settings['read_more'];

    if (!$rss->get_item_quantity()) {
        echo '<ul><li>' . __('An error has occurred, which probably means the feed is down. Try again later.') . '</li></ul>';
        $rss->__destruct();
        unset($rss);
        return;
    }

    $dateNow = new DateTime('now');

    $sortedFeed = array();
    $feedItems = $rss->get_items();

    //sort based on disp_order
    if($settings['disp_order']=='random'){
        shuffle($feedItems);
    }

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
            }
        } else if(strpos($settings['rss_url'], 'youtube.com/feeds') == false ) {
			if($item->get_item_tags('', 'pubDate')[0]['data']){
				$dateString = new DateTime($item->get_item_tags('', 'pubDate')[0]['data']);
			}
		}
		if ($show_date == 'yes' && $dateString) {
			$date = $dateString->format('M j, Y');
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
        if (strpos($settings['rss_url'], 'youtube.com/feeds') !== false && $enclosure = $item->get_enclosure()) {
            $image = '<img src="' . get_resized_remote_image_url($enclosure->get_thumbnail(), 600, 400) . '"  />';
        } else {
            $image = '<img src="' . get_resized_remote_image_url(get_first_image_url($item->get_content()), 600, 400) . '"  />';
        }

        //set description
		if (strpos($settings['rss_url'], 'www.makershed.com')) {
			//$desc = "<p><b>" . $item->get_item_tags("http://jadedpixel.com/-/spec/shopify", 'variant')[0]['child']["http://jadedpixel.com/-/spec/shopify"]['price'][0]['data'] . "</b></p>";
			$desc = "<a href='" . $link . "' class='universal-btn btn'>Buy Now</a>";
		} else {
	        $desc = html_entity_decode($item->get_description(), ENT_QUOTES, get_option('blog_charset'));
	        $desc = esc_html(esc_attr(wp_trim_words($desc, 55, ' [&hellip;]')));
		}

        //summary
        $summary = '';
        if ($show_summary == 'yes') {
            $summary = $desc;
            // Change existing [...] to [&hellip;].
            if ('[...]' == substr($summary, -5)) {
                $summary = substr($summary, 0, -5) . '[&hellip;]';
            }
            $summary = '<div class="rssSummary">' . $summary . '</div>';
        }

        //author
        $author = '';
        if ($show_author == 'yes') {
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

	$wrapper_classes = "";
	if ($horizontal == 'yes') {
		$wrapper_classes .= " horizontal";
	}
	if ($carousel == 'yes') {
		$wrapper_classes .= " carousel";
	}
	if ($summary != '') {
		$wrapper_classes .= " summary";
	}
	if (strpos($settings['rss_url'], 'www.makershed.com')) {
		$wrapper_classes .= " makershed";
	}
    echo '<ul class="custom-rss-elementor' . $wrapper_classes . '">';
    foreach ($sortedFeed as $item) {
        $link       = $item['link'];
        $title      = $item['title'];
        $image      = $item['image'];
        $desc       = $item['desc'];
        $summary    = $item['summary'];
        $author     = $item['author'];

		echo "<li style='list-style:none;'>";
        if ($link != '') {
            echo "<a class='rss-link' href='$link' target='_blank'>";
		}
		if ($title_position == "top") {
            echo "{$title}";
        }
		if ($horizontal == 'yes') {
			echo "<div class='rss-content-wrapper'>";
		}
		echo 	"<div class='rss-image-wrapper'>{$image}</div>";
		if ($title_position == "bottom") {
            echo "{$title}";
        }
		if ($horizontal == 'yes') {
			echo "<div class='rss-text-wrapper'>";
			echo "{$title}";
		}
		if ($show_summary == "yes") {
            echo "{$summary}";
        }
		if ($horizontal == 'yes') {
			echo "</div>";
			echo "</div>";
		}
		if ($item['show_date'] == 'yes') {
            echo '<date>' . $item['date'] . '</date>';
        }
		if ($show_author == "yes") {
            echo "{$author}";
        }
		if ($link != '') {
            echo "</a>";
		}
		echo "</li>";
    }
	if ($carousel == 'yes') {
		echo "<li class='rss-carousel-read-more'><a href='". $feed_link ."' target='_blank'>" . $read_more . "</a></li>";
	}
    echo '</ul>';
    $rss->__destruct();
    unset($rss);
}


add_action( 'wp_enqueue_scripts', 'make_elementor_enqueue_scripts');
function make_elementor_enqueue_scripts() {
	$myVersion = '2.4';
	wp_enqueue_script('make-elementor-script', plugins_url( '/js/scripts.js', __FILE__ ), array(), $myVersion );
	wp_enqueue_style('make-elementor-style', plugins_url( '/css/style.css', __FILE__ ), array(),$myVersion );
}
