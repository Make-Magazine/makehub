<?php
function onecommunity_featured_products($atts, $content = null) {
$transient = get_transient( 'onecommunity_featured_products' );
if ( false === ( $transient ) ) {
$items = '';

    $meta_query  = WC()->query->get_meta_query();
    $tax_query   = WC()->query->get_tax_query();
    $tax_query[] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
        'operator' => 'IN',
    );

	// Setup your custom query
	$args = array(
		'post_type' => 'product',
    	'meta_query' => $meta_query,
    	'tax_query' => $tax_query
	);

$loop = new WP_Query( $args );

$items .= '<ul class="shortcode-featured-products products col-3">';

while ( $loop->have_posts() ) : $loop->the_post();

$items .= '<li class="product">';

    global $product;
    $product_id = $product->get_id();

	$items .= '<a class="product-thumbnail" href="' . get_permalink($product_id) . '">';
	$items .= '<img src="'.get_the_post_thumbnail_url().'">';
	$items .= '</a>';

	$items .= '<div class="woocommerce-listing-item-content">';

	$items .= '<div class="woocommerce-listing-item-details">';

	$items .= '<span class="woocommerce-listing-item-cat">';
	$items .= wc_get_product_category_list($product_id);
	$items .= '</span>';

	ob_start();
	woocommerce_template_loop_price();
	$items .= ob_get_clean();

	$rating = $product->get_average_rating();

	if($rating > 0.99 AND $rating < 1.74 ) {
		$items .= '<span class="product-rating rating-1"></span>';
	} elseif ($rating >= 1.75 AND $rating < 2.74) {
		$items .= '<span class="product-rating rating-2"></span>';
	} elseif ($rating >= 2.75 AND $rating < 3.74) {
		$items .= '<span class="product-rating rating-3"></span>';
	} elseif ($rating >= 3.75 AND $rating < 4.49) {
		$items .= '<span class="product-rating rating-4"></span>';
	} elseif ($rating >= 4.50) {
		$items .= '<span class="product-rating rating-5"></span>';
	}

	$items .= '</div><!-- woocommerce-listing-item-details -->';

	$items .= '<h2><a href="' . get_permalink($product_id) . '">' . $product->get_title() . '</a></h2>';

	$items .= '</div><!-- woocommerce-listing-item-content -->';

$items .= '</li>';

endwhile; wp_reset_query(); // Remember to reset
$items .= '</ul>';

set_transient( 'onecommunity_featured_products', $items, DAY_IN_SECONDS * 3 );

return $items;

} else {
	return '<!-- Transient onecommunity_featured_products (3 days) -->'.$transient;
}

}

add_shortcode("onecommunity-featured-products", "onecommunity_featured_products");
