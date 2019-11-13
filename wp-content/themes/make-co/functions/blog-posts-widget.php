<?php 
// Widget for display most recent blog posts
class Blog_Posts_Widget extends WP_Widget{
	function __construct() {
		parent::__construct(
			'blog_posts_widget', // Base ID
			'Blog Posts Widget', // Name
			array('description' => __( 'Displays the most recent blog posts'))
			);
	}

	public function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$numberOfBlogPosts = $instance['numberOfBlogPosts'];
		echo $args['before_widget'];
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		global $post;
		$posts = new WP_Query();
		$posts->query('post_type=blog_posts&posts_per_page=' . $numberOfBlogPosts );
		$return = '<div class="inner-widget">';
		if($posts->found_posts > 0) {
			foreach($posts->posts as $post) {
      		$image_url = photon_image_url(get_the_post_thumbnail_url($post->ID), 50, 50, 'resize');
				$return .= '<div class="widget-item">
				              <a href="'.get_post_permalink($post->ID).'">
								    <div class="widget-image">
									   <img src="'.$image_url.'" />
									 </div>
									 <div class="widget-info">'.$post->post_title.' <p>by '.get_the_author_meta('display_name', $post->post_author).'</p></div>
								  </a>';
				$return .= '</div>';
			}
		}
		$return .= '</div>';
		echo $return;
		echo $args['after_widget'];
	}
	
	public function form($instance) {
		if( $instance) {
			$title = esc_attr($instance['title']);
			$numberOfBlogPosts = esc_attr($instance['numberOfBlogPosts']);
		} else {
			$title = 'Recent Blog Posts';
			$numberOfBlogPosts = 5;
		} 
		?>
      <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
      <p>
			<label for="<?php echo $this->get_field_id( 'numberOfBlogPosts' ); ?>"><?php _e( 'Number of Blog Posts to Display:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'numberOfBlogPosts' ); ?>" name="<?php echo $this->get_field_name( 'numberOfBlogPosts' ); ?>" type="text" value="<?php echo esc_attr( $numberOfBlogPosts ); ?>" />
		</p>
      <?php
	}
	
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['numberOfBlogPosts'] = strip_tags($new_instance['numberOfBlogPosts']);
		return $instance;
	}
	

} //end class
register_widget('Blog_Posts_Widget');