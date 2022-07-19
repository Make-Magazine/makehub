<?php

/* * ********************************** */

//       Fancy RSS Feed Widget       //
/* * ********************************** */
// Like the normal RSS feed widget, but with Featured Images and good styling

class fancy_rss_widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'description' => __('Display RSS Feed with Featured Images'),
			'classname' => 'widget_fancy_rss',
            'customize_selective_refresh' => true,
        );
        $control_ops = array(
            'width' => 400,
            'height' => 200,
        );
        parent::__construct('fancy_rss', __('Custom RSS'), $widget_ops, $control_ops);

    }


    public function widget($args, $instance) {

		$detect = new Mobile_Detect;
		// no need to run at all if we have this set to hide on mobile
		if(isset($instance['hide_mobile']) && $instance['hide_mobile'] == 1 && $detect->isMobile()) {
			return;
		}

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
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $desc = '';
        $link = !empty( $instance['link'] ) ? $instance['link'] : '';

        if (!is_wp_error($rss)) {
            $desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
            if (empty($title)) {
                $title = strip_tags($rss->get_title());
            }
			if(empty($link)) {
				$link = strip_tags($rss->get_permalink());
				while (stristr($link, 'http') != $link) {
					$link = substr($link, 1);
				}
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

    public function form($instance) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $url   = isset( $instance['url'] ) ? $instance['url'] : '';
		$link  = isset( $instance['link'] ) ? $instance['link'] : '';
		$items = isset( $instance['items'] ) ? (int) $instance['items'] : 0;

		if ( $items < 1 || 20 < $items ) {
			$items = 10;
		}

		$show_summary = isset( $instance['show_summary'] ) ? (int) $instance['show_summary'] : 0;
		$show_author  = isset( $instance['show_author'] ) ? (int) $instance['show_author'] : 0;
		$show_date    = isset( $instance['show_date'] ) ? (int) $instance['show_date'] : 0;

		$hide_mobile = isset( $instance['hide_mobile'] ) ? (int) $instance['hide_mobile'] : 0;

        // Widget admin form
        ?>
		<p><label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Enter the RSS feed URL here:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Give the feed a title (optional):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e( 'Provide a link (defaults to site):' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('items'); ?>"><?php _e( 'How many items would you like to display?' ); ?></label>
		<select id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>">
			<?php
			for ( $i = 1; $i <= 12; ++$i ) {
				echo "<option value='$i' " . selected( $items, $i, false ) . ">$i</option>";
			}
			?>
		</select></p>

		<p>
			<input id="<?php echo $this->get_field_id('show_summary'); ?>" name="<?php echo $this->get_field_name('show_summary'); ?>" type="checkbox" <?php checked( $show_summary ); ?> />
			<label for="<?php echo $this->get_field_id('show_summary'); ?>"><?php _e( 'Display item content?' ); ?></label><br />

			<input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked( $show_author ); ?> />
			<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e( 'Display item author if available?' ); ?></label><br />

			<input id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" <?php checked( $show_date ); ?>/>
			<label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e( 'Display item date?' ); ?></label><br />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('hide_mobile'); ?>" name="<?php echo $this->get_field_name('hide_mobile'); ?>" type="checkbox" <?php checked( $hide_mobile ); ?>/>
			<label for="<?php echo $this->get_field_id('hide_mobile'); ?>"><?php _e( 'Hide for Mobile' ); ?></label><br />
		</p>
	<?php
    }

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['url'] = (!empty($new_instance['url']) ) ? strip_tags($new_instance['url']) : '';
		$instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
		$instance['link'] = (!empty($new_instance['link']) ) ? strip_tags($new_instance['link']) : '';
		$instance['items'] = (!empty($new_instance['items']) ) ? strip_tags($new_instance['items']) : '';

		$instance['show_summary'] = (!empty($new_instance['show_summary'])) ? 1 : 0;
		$instance['show_author'] = (!empty($new_instance['show_author'])) ? 1 : 0;
		$instance['show_date'] = (!empty($new_instance['show_date'])) ? 1 : 0;
		$instance['hide_mobile'] = (!empty($new_instance['hide_mobile'])) ? 1 : 0;
        return $instance;
	}

}
