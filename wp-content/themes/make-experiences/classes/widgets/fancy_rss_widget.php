<?php

/* * ********************************** */

//       Fancy RSS Feed Widget       // 
/* * ********************************** */
// Like the normal RSS feed widget, but with Featured Images and good styling

class fancy_rss_widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => __('Display RSS Feed with Featured Images'),
            'customize_selective_refresh' => true,
        );
        $control_ops = array(
            'width' => 400,
            'height' => 200,
        );
        parent::__construct('rss', __('Custom RSS'), $widget_ops, $control_ops);
    }

    public function widget($args, $instance) {

        if (isset($instance['error']) && $instance['error']) {
            return;
        }
        $url = !empty($instance['url']) ? $instance['url'] : '';
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
        $title = $instance['title'];
        $desc = '';
        $link = '';

        if (!is_wp_error($rss)) {
            $desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
            if (empty($title)) {
                $title = strip_tags($rss->get_title());
            }
            $link = strip_tags($rss->get_permalink());
            while (stristr($link, 'http') != $link) {
                $link = substr($link, 1);
            }
        }

        if (empty($title)) {
            $title = !empty($desc) ? $desc : __('Unknown Feed');
        }

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $url = strip_tags($url);
        if ($title) {
            $title = '<a target="_blank" class="rsswidget" href="' . esc_url($link) . '">' . $title . '</a>';
        }

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        make_widget_rss_output($rss, $instance);
        echo $args['after_widget'];

        if (!is_wp_error($rss)) {
            $rss->__destruct();
        }
        unset($rss);
    }

    public function update($new_instance, $old_instance) {
        $testurl = ( isset($new_instance['url']) && (!isset($old_instance['url']) || ( $new_instance['url'] != $old_instance['url'] ) ) );
        return wp_widget_rss_process($new_instance, $testurl);
    }

    public function form($instance) {
        if (empty($instance)) {
            $instance = array(
                'title' => '',
                'url' => '',
                'items' => 10,
                'error' => false,
                'show_summary' => 0,
                'show_author' => 0,
                'show_date' => 0,
            );
        }
        $instance['number'] = $this->number;
        wp_widget_rss_form($instance);
    }

}
