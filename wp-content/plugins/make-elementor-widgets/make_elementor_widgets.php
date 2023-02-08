<?php
/**
 * Plugin Name: Make: Elementor Widgets
 * Description: This plugin adds some common Make: dashboard widgets
 * Version:     1.2.0
 * Author:      Make: developers
 * Text Domain: elementor-make-widget
 *
 * Elementor tested up to: 3.5.0
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// AJAX
require_once(plugin_dir_path(__FILE__) . 'classes/ajax_omeda.php');

/**
 * @package Make_Elementor_Widgets
 * @author Make: developers
 * @version 1.0.0
 * @see https://developers.elementor.com/creating-an-extension-for-elementor/
 */
final class Make_Elementor_Widgets
{

    const VERSION = '1.2.1';
    const MINIMUM_ELEMENTOR_VERSION = '3.5.0';
    const MINIMUM_PHP_VERSION = '7.0';

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);

        // # load at frontend
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles'], 11);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function init() {
        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        // Add Plugin actions
        add_action('elementor/widgets/register', [$this, 'init_widgets']);
    }

    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'make-elementor-widgets'),
            '<strong>' . esc_html__('Elementor', 'make-elementor-widgets') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'make-elementor-widgets'),
            '<strong>' . esc_html__('Elementor', 'make-elementor-widgets') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'make-elementor-widgets'),
            '<strong>' . esc_html__('PHP 7.0', 'make-elementor-widgets') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
    * We will register our widgets within init_widgets function
	* deprecated widgets:
	*		facilitator_event_listings-widget.php
	*		my_campus_tickets-widget.php
    */
    public function init_widgets() {
        // ----------------------
        // # Maker Shed purchases Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/shed-purchases-widget.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_mShedPurch_Widget()); // Register widget

		// ----------------------
        // # My Makerspaces Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/my-makerspaces-widget.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_myMspaces_Widget()); // Register widget

		// ----------------------
        // # My Makerspaces Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/my-makerCamp-widget.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_MyMakerCamp_Widget()); // Register widget

		// ----------------------
        // # Make: Custom RSS Feed Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/make-custom-rss-feed.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_makeCustomRss_Widget()); // Register widget

		// ----------------------
        // # Make: upcoming MakerFaire Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/upcoming-makerfaires-widget.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_upcomingMakerFaires_Widget()); // Register widget

		// ----------------------
        // # Make: initiativies Widget
        // ----------------------
        require_once(__DIR__ . '/widgets/make-initiatives.php'); // Include Widget files
        \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_makeInitatives_Widget()); // Register widget

		// ----------------------
        // # Subscription information from Omeda Widget
        // ----------------------
		if(bp_is_active('xprofile')){
        	require_once(__DIR__ . '/widgets/my-subscription-widget.php'); // Include Widget files
        	\Elementor\Plugin::instance()->widgets_manager->register(new \Elementor_mySubscription_Widget()); // Register widget
		}

		// ----------------------
        // # Make: customized MZ feed based on member interestes
        // ----------------------
        // require_once(__DIR__ . '/widgets/make-interests-rss-widget.php'); // Include Widget files
        // \Elementor\Plugin::instance()->widgets_manager->register_widget(new \Elementor_makeInterestsRss_Widget()); // Register widget

    }


    /**
    * we will add stylesheet for our plugin in style.css
    */
    public function enqueue_styles() {
		//widget styles
		wp_register_style("make-elementor-style", plugins_url('/css/style.css', __FILE__), array(), self::VERSION );
		wp_enqueue_style('make-elementor-style');
    }


   /**
    * register javascript files
    */
    public function enqueue_scripts() {
		//widget scripts
		wp_enqueue_script('make-elementor-script', plugins_url( '/js/scripts.js', __FILE__ ), array(), self::VERSION  );
    }
}

Make_Elementor_Widgets::instance();

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

//common functions
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
