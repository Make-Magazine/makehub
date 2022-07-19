<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Widget_Leader_Controller')){
    class Wpvc_Widget_Leader_Controller extends WP_Widget{

        public function __construct() {
			parent::__construct(
				'voting_contest_leaders', 
				__('Photo Contest Leaders', 'voting-contest'),
				array( 'description' => __( 'Highest Rated Photo List based upon the Contestants', 'voting-contest' ), ) 
			);			
		}
		
		public function widget( $args, $instance ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
	
			echo $args['before_widget'];
			if ( ! empty( $title ) )
				echo $args['before_title'] .'<span class="wpvc_photo_contest_h wpvc_vote_contest_top_bar"><span class="wpvc_vote_icons votecontestant-camera" aria-hidden="true"></span>'. $title ."</span>". $args['after_title'];
			do_action('wpvc_voting_display_widget',$instance);      
			echo $args['after_widget'];
		}
		
		public function update( $new_instance, $old_instance ) {	    
			$instance = $old_instance;
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['no_of_conts'] = ( ! empty( $new_instance['no_of_conts'] ) ) ? strip_tags( $new_instance['no_of_conts'] ) : '';
			$instance['contest_tax'] = ( ! empty( $new_instance['contest_tax'] ) ) ? strip_tags( $new_instance['contest_tax'] ) : '';
			$instance['display_category'] = ( ! empty( $new_instance['display_category'] ) ) ? '1' : '0';
			$instance['display_author'] = ( ! empty( $new_instance['display_author'] ) ) ? '1' : '0';
			$instance['display_photo'] = ( ! empty( $new_instance['display_photo'] ) ) ? '1' : '0';  
			return $instance;
		}
		
		public function form( $instance ) {
			if ( $instance) {
				$title        = $instance[ 'title' ];                 
				$no_of_photos = esc_attr($instance['no_of_photos']);
				$contest_tax  = esc_attr($instance['contest_tax']);
				$display_category = $instance[ 'display_category' ];
				$display_author = $instance[ 'display_author' ];
				$display_photo  = $instance[ 'display_photo' ];
			}       
			else {
				$title        = __( 'Photo Contest Leaders', 'voting-contest' );
				$display_category = '1';
				$display_author = '1';
				$display_photo  = '1';       
			}
			
			if ( isset($instance['no_of_conts']))
				$no_of_conts  = esc_attr($instance['no_of_conts']);       
			else
				$no_of_conts  = 5;     
				
			?>
			<p>
			  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title:' , 'voting-contest'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			
			<p>
			  <label><?php _e( 'Select Contests Category' , 'voting-contest'); ?></label> <br />
			  <?php wp_dropdown_categories( 'name='.$this->get_field_name( 'contest_tax' ).'&show_count=1&hierarchical=1&taxonomy='.WPVC_VOTES_TAXONOMY.'&selected='.$contest_tax); ?>
			</p>
	
			<p>
			  <label for="<?php echo $this->get_field_id( 'display_category' ); ?>"><?php _e( 'Display Category Title :' , 'voting-contest'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id( 'display_category' ); ?>" name="<?php echo $this->get_field_name( 'display_category' ); ?>" value="1" type="checkbox" <?php checked( esc_attr( $display_category ), 1 ); ?> />
			</p>
			
			
			<p>
			  <label for="<?php echo $this->get_field_id( 'display_author' ); ?>"><?php _e( 'Display Author :' , 'voting-contest'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id( 'display_author' ); ?>" name="<?php echo $this->get_field_name( 'display_author' ); ?>" value="1" type="checkbox" <?php checked( esc_attr( $display_author ), 1 ); ?> />
			</p>
			
			<p>
			  <label for="<?php echo $this->get_field_id( 'display_photo' ); ?>"><?php _e( 'Display Photo :' , 'voting-contest'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id( 'display_photo' ); ?>" name="<?php echo $this->get_field_name( 'display_photo' ); ?>" type="checkbox" <?php checked( esc_attr( $display_photo ), 1 ); ?> />
			</p>
			
			<p>
			  <label for="<?php echo $this->get_field_id( 'no_of_conts' ); ?>"><?php _e( 'Number of Contestants :' , 'voting-contest'); ?></label> 
			  <input class="widefat" id="<?php echo $this->get_field_id( 'no_of_conts' ); ?>" name="<?php echo $this->get_field_name( 'no_of_conts' ); ?>" type="number" min="1" value="<?php echo esc_attr( $no_of_conts ); ?>" />
			</p>
			
			
			<?php 
		}
        
      
    }
}else
die("<h2>".__('Failed to load Voting Widget Leader Controller','voting-contest')."</h2>");


return new Wpvc_Widget_Leader_Controller();
