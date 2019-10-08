<?php 
/* Commenting this out for use later
// Pull shopify data

// Set variables
$api_url = 'https://4e27971e92304f98d3e97056a02045f1:32e156e38d7df1cd6d73298fb647be72@makershed.myshopify.com';
$shop_url = 'http://makershed.myshopify.com';
$collection_id = '22008761';

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
	$product_url = $api_url . '/admin/products/' . $product_id . '.json?fields=images,title,variants,handle&published_status=published';
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
						<a href="'.$shop_url.'/products/'.$product_handle.'">
							<img src="'.$product_image_src.'" alt="'.$product_title.'"/>
							<h3>'.$product_title.'</h3>';
							if ( $variant_inventory > 0 ) {
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
	$return .= '   </a>
					</div>';
}

$return .= "</div>";
echo $return;
*/