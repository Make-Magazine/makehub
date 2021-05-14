<?php

class ldms_Private_Sessions_Widget extends WP_Widget {


  // Set up the widget name and description.
  public function __construct() {
    $widget_options = array( 'classname' => 'private-sessions-widget', 'description' => 'Private Sessions Widget' );
    parent::__construct( 'ldms_display_widget', 'Private Sessions Widget', $widget_options );
  }


  // Create the widget output.
  public function widget( $args, $instance ) {
    global $post;
    
    $title = apply_filters( 'widget_title', $instance[ 'title' ] );
    
     // Skip if the shortcode is displayed, the user is not logged in or the user can't view
    if( ( has_shortcode( $post->post_content, 'display_messages' ) || !is_user_logged_in() ) || !ldms_user_can_view() && !current_user_can('start_session') ) return;

    // Normal users who don't have messages shouldn't see the tab
    if( !ldms_is_group_leader() && !ldms_user_has_messages() && !current_user_can('start_session') ) return;

	
    ob_start();

	ldma_custom_assets();
	
	echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; 
	
    $label = '';
    $new_messages = ldms_get_user_unread_message_count(); ?>

    <div id="ldms-message-widget">
        <?php if( current_user_can('start_session')  ): ?>
            <p>
				<a class="ldms-btn" href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>#ldms-new-session">
					<span class="ldms-btn-icon">
						<img src="<?php echo esc_url( LDMA_URL . '/assets/svg/write.svg' ); ?>" alt="<?php esc_attr_e( 'New Session', 'ldmessenger' ); ?>">
					</span>
					<span class="ldms-btn-text">
						<?php esc_html_e( 'New Session', 'ldmessenger' ); ?>
					</span>
				</a>
			</p>
        <?php endif; ?>
        <p>
			<a class="ldms-btn" href="<?php echo esc_url( apply_filters( 'ldms_session_page_link' , get_the_permalink( ldms_get_option('ldma_sessions_page') ) ) ); ?>">
				<span class="ldms-btn-icon" title="<?php echo esc_attr( $new_messages ) . __( ' New Messages', 'ldmessenger' ); ?>">
					<span class="ldms-btn-msgs">
						<?php echo esc_html($new_messages); ?>
					</span>
				</span>
				<span class="ldms-btn-text">
	            	<?php esc_html_e( 'My Sessions', 'ldmessenger' ); ?>
				</span>
        	</a>
		</p>
    </div>
  
    
   <?php echo $args['after_widget'];
	   
	   echo ob_get_clean();
  }

  
  // Create the admin area widget settings form.
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
  }


  // Apply settings to the widget instance.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    return $instance;
  }

}

// Register the widget.
function ldms_register_custom_widget() { 
  register_widget( 'ldms_Private_Sessions_Widget' );
}

$position = ldms_get_option('ldms_indicator_location');

if( $position == 'widget' ) {
	add_action( 'widgets_init', 'ldms_register_custom_widget' );
}
?>