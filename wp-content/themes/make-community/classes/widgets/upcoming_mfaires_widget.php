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
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        echo("<a href='https://makerfaire.com/map' target='_blank'><img class='mf-logo' src='/wp-content/themes/make-experiences/images/makerfaire-logo.png' alt='Maker Faire' /></a>");
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
				$return .= "<li>
								<a target='_blank' href='" . $faire['faire_url'] . "'>
									<h4>" . $faire['name'] . "</h4>
									<div class='faire-feed-date'>" . $faire['event_dt'] . "</div>
									<div class='faire-feed-location'>" . $faire['venue_address_street'] . " " . $faire['venue_address_city'] . " " . $faire['venue_address_state'] . " " . $faire['venue_address_country'] . "</div>
								</a>
							</li>";
			}
		} else {
			$return .= "<li>Having trouble getting Maker Faire data right now. Find upcoming Maker Faires <a href='https://makerfaire.com/map' target='_blank'>here!</a>";
		}
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
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number']) ) ? strip_tags($new_instance['number']) : '';
        return $instance;
    }

    /* */
    public function get_categories() {
  		return [ 'make-category' ];
  	}
}
