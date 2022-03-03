<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Custom RSS feed
 *
 * Elementor widget that allows you to pull in an RSS feed and customize the look and feel
 *
 * @since 1.0.0
 */
class Elementor_makeCustomRss_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makecustomrss';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Custom RSS feed', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'custom', 'rss'];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    $this->add_control(
    			'title',
    			[
    				'label' => esc_html__( 'Title (optional)', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);
		$this->add_control(
		    			'link',
		    			[
		    				'label' => esc_html__( 'Link (defaults to site)', 'elementor-make-widget' ),
		    				'type' => \Elementor\Controls_Manager::TEXT,
		    				'placeholder' => esc_html__( 'Provide a link', 'elementor-make-widget' ),
		    			]
		);
		$this->add_control(
		    			'rss_url',
		    			[
		    				'label' => esc_html__( 'Enter the full RSS feed URL', 'elementor-make-widget' ),
		    				'type' => \Elementor\Controls_Manager::TEXT,
		    				'placeholder' => esc_html__( 'https://', 'elementor-make-widget' ),
		    			]
		);
				$this->add_control(
							'num_display',
							[
								'label' => esc_html__( 'Items to display', 'plugin-name' ),
								'type' => \Elementor\Controls_Manager::SELECT,
								'default' => 'solid',
								'options' => [
									'1'  => esc_html__( '1', 'elementor-make-widget' ),
									'2' => esc_html__( '2', 'elementor-make-widget' ),
									'3' => esc_html__( '3', 'elementor-make-widget' ),
									'4' => esc_html__( '4', 'elementor-make-widget' ),
									'5' => esc_html__( '5', 'elementor-make-widget' ),
									'6' => esc_html__( '6', 'elementor-make-widget' ),
									'7' => esc_html__( '7', 'elementor-make-widget' ),
									'8' => esc_html__( '8', 'elementor-make-widget' ),
									'9' => esc_html__( '9', 'elementor-make-widget' ),
									'10' => esc_html__( '10', 'elementor-make-widget' ),
								],
							]
						);
						$this->add_control(
								'disp_order',
								[
									'label' => esc_html__( 'Display Order', 'elementor-make-widget' ),
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => [
										'random' => [
											'title' => esc_html__( 'Random', 'elementor-make-widget' ),
											'icon' => 'eicon-text-align-left',
										],
										'center' => [
											'title' => esc_html__( 'Date', 'elementor-make-widget' ),
											'icon' => 'eicon-date',
										],
										'right' => [
											'title' => esc_html__( 'Author', 'elementor-make-widget' ),
											'icon' => 'eicon-person',
										],
									],
									'default' => 'center',
									'toggle' => true,
								]
							);
		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
    $settings = $this->get_settings_for_display();
		$num_display = $settings['num_display'];
		$disp_order = $settings['disp_order'];
		$title = $settings['title'];
		echo '<h4>'.$settings['title'].'</h4>';

		$url = !empty($settings['rss_url']) ? $settings['rss_url'] : '';
		while (stristr($url, 'http') != $url) {
				$url = substr($url, 1);
		}
		if (empty($url)) {
				return;
		}

		// self-url destruction sequence
		if (in_array(untrailingslashit($url), array(site_url(), home_url()))) {
				return;
		}

		$rss = fetch_feed($url);
		$desc = '';

		if (!is_wp_error($rss)) {
				$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
				if (empty($title)) {
						$title = strip_tags($rss->get_title());
				}
				if (empty($link)) {
						$link = strip_tags($rss->get_permalink());
						while (stristr($link, 'http') != $link) {
								$link = substr($link, 1);
						}
				}
		}

		if (empty($title)) {
				$title = !empty($desc) ? $desc : __('Unknown Feed');
		}

		$url = strip_tags($url);
		if ($title) {
				$title = '<a target="_blank" class="rsswidget" href="' . esc_url($link) . '">' . $title . '</a>';
		}

		make_widget_rss_output($rss, $instance);

		if (!is_wp_error($rss)) {
				$rss->__destruct();
		}
		unset($rss);

	} //end render public function

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
}
