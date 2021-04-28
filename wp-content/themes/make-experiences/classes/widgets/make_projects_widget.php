<?php
/* * ***************************************** */
//           Make Projects Widget           //
/* * ***************************************** */
// Show a set number of upcoming maker faires

class make_projects_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                // Base ID of your widget
                'make_projects_widget',
                // Widget name will appear in UI
                __('Make Projects Widget', 'make_projects_widget_domain'),
                // Widget description
                array('description' => __('Display Projects from Make Projects', 'make_projects_widget_domain'),)
        );
    }

    public function widget($args, $instance) {
		// Set variables
        $number = $instance['number'];
		
		$api_url = 'https://makeprojects.com/api/projects/featured?limit=' . $number . '&offset=0&sort=recent_activity&platform=projects';
        if ($instance['category'] != 'featured') {
            $api_url = 'https://makeprojects.com/api/projects/category/' . $instance['category'] . '?limit=' . $number . '&offset=0&sort=recent_activity&platform=projects';
        }

        $headers = array('X-Partner: make');
        $projectJson = json_decode(basicCurl($api_url, $headers));
		
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title) && isset($projectJson->result->projects)) {
            echo $args['before_title'] . '<a target="_blank" href="https://makeprojects.com">'.$title . '</a>'.$args['after_title'];
        }
        
        if(isset($projectJson->result->projects)){
            $projectArray = $projectJson->result->projects;
        }else{
            error_log('error in call to make projects!!!');
            error_log(print_r($projectJson,TRUE));
            $projectArray = array();
            //send email alerting us of the error
            $to = 'webmaster@make.co';
            $subject = 'Error pulling Make: Projects content in widget';
            $body = 'Please check site '.get_site_url(). ' for the error';
            $headers = array('Content-Type: text/html; charset=UTF-8');
			
            wp_mail( $to, $subject, $body, $headers );
        }       

        $return = '<div class="make-projects-widget"><ul>';

        // Loop through products in the collection
        foreach ($projectArray as $project) {
            $return .= "<li>
			              <a href='https://makeprojects.com/project/" . $project->slug . "?utm_source=make&utm_medium=widget&utm_campaign=makezine&utm_content=link' target='_blank'>
						  	  <img src='https://makeprojects.com/images/140x100,q80/" . $project->image . "' />
							  <div class='mp-text'>
							  	<div class='mp-title'>" . $project->title . "</div>
								<cite>" . $project->user->userName . "</cite>
							  </div>
						  </a>
						</li>";
        }
        $return .= "</ul>";
		if(isset($projectJson->result->projects)) {
			$return .= '<a class="seeMore" href="https://makeprojects.com" target="_blank">See More</a>';
		}
		$return .= "</div>";
        echo __($return, 'make_projects_widget_domain');
        echo $args['after_widget'];
		
    }

    // Widget Backend 
    public function form($instance) {
        $headers = array('X-Partner: make');
        $categoriesJson = json_decode(basicCurl("https://makeprojects.com/api/projects/categories", $headers));
        $categoriesObject = (isset($categoriesJson->result)?$categoriesJson->result->categories:'');

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Make Projects', 'make_projects_widget_domain');
        }
        if (isset($instance['number'])) {
            $number = $instance['number'];
        } else {
            $number = '5';
        }
        if (isset($instance['category'])) {
            $inst_category = $instance['category'];
        } else {
            $inst_category = 'featured';
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of Projects to Show:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category:'); ?></label> 
            <select class='widefat' id="<?php echo $this->get_field_id('category'); ?>"
                    name="<?php echo $this->get_field_name('category'); ?>" type="text">
                <option <?php selected($inst_category, 'featured'); ?> value="featured">Featured</option> 
                <?php foreach ($categoriesObject as $category) { ?>
                    <option <?php selected($inst_category, $category->slug); ?> value="<?php echo $category->slug ?>"><?php echo $category->name ?></option> 
                <?php } ?>
            </select>
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number']) ) ? strip_tags($new_instance['number']) : '5';
        $instance['category'] = (!empty($new_instance['category']) ) ? strip_tags($new_instance['category']) : 'featured';
        return $instance;
    }

}
