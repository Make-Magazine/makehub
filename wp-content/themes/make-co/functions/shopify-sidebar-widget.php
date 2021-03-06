<?php 

// Create widget to display shopify collection

// Register and load the widget
function shopify_load_widget() {
    register_widget( 'shopify_widget' );
}
add_action( 'widgets_init', 'shopify_load_widget' );
 
// Creating the widget 
class shopify_widget extends WP_Widget {
 
	function __construct() {
		parent::__construct(

		// Base ID of your widget
		'shopify_widget', 

		// Widget name will appear in UI
		__('Shopify Widget', 'shopify_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Pull data from Makershed based on collection ID', 'shopify_widget_domain' ), ) 
		);
	}
 
	// Creating widget front-end

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Here's where we pull data from shopify
		// Set variables
		$api_url = 'https://4e27971e92304f98d3e97056a02045f1:32e156e38d7df1cd6d73298fb647be72@makershed.myshopify.com';
		$shop_url = 'http://makershed.myshopify.com';
		$collection_id = $instance['collection_id'];

		// Create the API URL for the Shopify collect
		$collects_url = $api_url . '/admin/collects.json?collection_id=' . $collection_id . '&limit=3';

		$collects_content = @file_get_contents( $collects_url );

		// Decode the JSON in the file
		$collects = json_decode( $collects_content, true );

		// Reset variant inventory count
		$variant_inventory = 0;

		$return = '<div class="shopify-product-feed">';

		// Loop through products in the collection
		for ( $prod = 0; $prod < 3; $prod++ ) {
			// Get the product ID for the current product
			$product_id = $collects['collects'][$prod]['product_id'];
			// Get the product data from the API (using the ID)
			$product_url = $api_url . '/admin/products/' . $product_id . '.json?fields=images,title,variants,handle';
			// Decode the JSON for the product data
			$product_json = json_decode( @file_get_contents( $product_url ), true );

			// Set some variables for product data
			$current_product = $product_json['product'];
			$product_handle = $current_product['handle'];
			$product_title = $current_product['title'];

			// Get the product image and modify the file name to get the large size thumbnail
			$image_src_parts = pathinfo( $current_product['images'][0]['src'] );
			$product_image_src = $image_src_parts['dirname'] . '/' . $image_src_parts['filename'] . '_large.' . $image_src_parts['extension'];

			// Get product variant information, including inventory and pricing
			$variants = $current_product['variants'];
			$variant_count = count( $variants );

			$variant_price = 0;
			$variant_prices = array();
			if ( $variant_count > 1 ) {
				for ( $v = 0; $v < $variant_count; $v++ ) {
					$variant_inventory += $variants[$v]['inventory_quantity'];
					$variant_prices[] = $variants[$v]['price'];
				}
				$price_min = min( $variant_prices );
				$price_max = max( $variant_prices );
			}else{
				$variant_price = $variants[0]['price'];
				$variant_inventory = $variants[0]['inventory_quantity'];
			}

			// Build each product
			$return .= '<div class="product-feed-item">
								<a href="'.$shop_url.'/products/'.$product_handle.'" target="_blank">
									<div class="product-image">
										<img src="'.$product_image_src.'" alt="'.$product_title.'"/>
									</div>
									<div class="product-info">
										<h3>'.$product_title.'</h3>';
										if ( $variant_inventory != 0 ) { // negative inventory denotes digital downloads
											if ( $variant_price > 0 ) {
												$return .= '<span class="price small">';
												if ( $variant_price > 0 ) {
													$return .= '$'.$variant_price;
												}else {
													$return .= 'FREE';
												}
												$return .= '</span>';
											}elseif ( ( $price_min > 0 ) && ( $price_max > 0 ) ) {
												$return .= '<span class="price small">$'.$price_min.' - $'.$price_max.'</span>';
											}
										}else{
											$return .= '<span class="sold-out">OUT OF STOCK</span>';
										} 
			$return .= '      </div>
			               </a>
							</div>';
		}

		$return .= "</div>";
		echo __( $return, 'shopify_widget_domain' );
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Maker Shed', 'shopify_widget_domain' );
		}
		if ( isset($instance['collection_id']) ) {
        $collection_id = $instance['collection_id'];
		} else {
			$collection_id = '22008761';
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
      <p>
			<label for="<?php echo $this->get_field_id( 'collection_id' ); ?>"><?php _e( 'Shopify Collection ID:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'collection_id' ); ?>" name="<?php echo $this->get_field_name( 'collection_id' ); ?>" type="text" value="<?php echo esc_attr( $collection_id ); ?>" />
		</p>
		<?php 
	}
     
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['collection_id'] = ( ! empty( $new_instance['collection_id'] ) ) ? strip_tags( $new_instance['collection_id'] ) : '';
		return $instance;
	}
} // Class ends
