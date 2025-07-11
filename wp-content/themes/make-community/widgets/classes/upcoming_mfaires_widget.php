<?php
/* * ***************************************** */

//          Upcoming Maker Faires           //
/* * ***************************************** */
// Show a set number of upcoming maker faires

class upcoming_mfaires_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                // Base ID of your widget
                'upcoming_mfaires_widget',
                // Widget name will appear in UI
                __('Make: Upcoming Maker Faires Widget', 'upcoming_mfaires_widget_domain'),
                // Widget description
                array('description' => __('Display Upcoming Maker Faires', 'upcoming_mfaires_widget_domain'),)
        );
    }

    public function widget($args, $instance) {
        $title = (isset($instance['title'])?apply_filters('widget_title', $instance['title']):'');
        $show_logo = isset( $instance['show_logo'] ) ? (int) $instance['show_logo'] : 0;
        $show_only_city  = isset( $instance['show_only_city'] ) ? (int) $instance['show_only_city'] : 0;
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if($show_logo == 1) {
          echo("<a href='https://makerfaire.com/map' target='_blank'><img class='mf-logo' src='/wp-content/themes/make-experiences/images/makerfaire-logo.png' alt='Maker Faire' /></a>");
        }
        if (!empty($title)) {
            echo $args['before_title'] . "<a href='https://makerfaire.com/map' target='_blank'>" . $title . "</a>" . $args['after_title'];
        }
        // Set variables
        $number = (isset($instance['number'])?$instance['number']:5);
        $api_url = 'https://makerfaire.com/query/?type=map&upcoming=true&number=' . $number . '&categories=mini,featured,flagship';

        $faire_content = basicCurl($api_url);

        // Decode the JSON in the file
        $faires = json_decode($faire_content, true);

        $return = '<div class="upcoming-makerfaires-feed"><ul>';

        // Loop through products in the collection
		if(isset($faires['Locations'])) {
			foreach ($faires['Locations'] as $faire) {
                $location = ($show_only_city) ? $faire['venue_address_city'] : $faire['venue_address_street'] . " " . $faire['venue_address_city'] . " " . $faire['venue_address_state'] . " " . $faire['venue_address_country'];
				$return .= "<li>
								<a target='_blank' href='" . $faire['faire_url'] . "'>
									<h4>" . $faire['name'] . "</h4>
									<div class='faire-feed-date'>" . $faire['event_dt'] . "</div>
									<div class='faire-feed-location'>" . $location . "</div>
								</a>
							</li>";
			}
		} else {
			$return .= "<li>Having trouble getting Maker Faire data right now. Find upcoming Maker Faires <a href='https://makerfaire.com/map' target='_blank'>here!</a>";
		}
        $return .= "<a href='https://makerfaire.com/map'>>> All Faires</a>";
        $return .= "</ul></div>";
        echo __($return, 'upcoming_mfaires_widget_domain');
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Upcoming Faires', 'upcoming_mfaires_widget_domain');
        }
        if (isset($instance['number'])) {
            $number = $instance['number'];
        } else {
            $number = '5';
        }
        $show_logo = isset( $instance['show_logo'] ) ? (int) $instance['show_logo'] : 0;
        $show_only_city = isset( $instance['show_only_city'] ) ? (int) $instance['show_only_city'] : 0;
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Faires to Show:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
        </p>
        <p>
			<input id="<?php echo $this->get_field_id('show_logo'); ?>" name="<?php echo $this->get_field_name('show_logo'); ?>" type="checkbox" <?php checked( $show_logo ); ?>/>
			<label for="<?php echo $this->get_field_id('show_logo'); ?>"><?php _e( 'Show Logo' ); ?></label><br />
		</p>
        <p>
			<input id="<?php echo $this->get_field_id('show_only_city'); ?>" name="<?php echo $this->get_field_name('show_only_city'); ?>" type="checkbox" <?php checked( $show_only_city ); ?>/>
			<label for="<?php echo $this->get_field_id('show_only_city'); ?>"><?php _e( 'Show Only City' ); ?></label><br />
		</p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number']) ) ? strip_tags($new_instance['number']) : '';
        $instance['show_logo'] = (!empty($new_instance['show_logo'])) ? 1 : 0;
        $instance['show_only_city'] = (!empty($new_instance['show_only_city'])) ? 1 : 0;
        return $instance;
    }

    /* */
    public function get_categories() {
  		return [ 'make-category' ];
  	}
}
