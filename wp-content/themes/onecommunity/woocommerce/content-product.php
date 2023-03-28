<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class(); ?>>
<?php

	$product_id = $product->get_id();

	echo '<a class="product-thumbnail" href="' . get_permalink($product_id) . '">' . woocommerce_get_product_thumbnail() . '</a>';

	echo '<div class="woocommerce-listing-item-content">';

	echo '<div class="woocommerce-listing-item-details">';

	echo '<span class="woocommerce-listing-item-cat">';
	echo wc_get_product_category_list($product_id);
	echo '</span>';

	woocommerce_template_loop_price();

	$rating = $product->get_average_rating();

	if($rating > 0.99 AND $rating < 1.74 ) {
		echo '<span class="product-rating rating-1"></span>';
	} elseif ($rating >= 1.75 AND $rating < 2.74) {
		echo '<span class="product-rating rating-2"></span>';
	} elseif ($rating >= 2.75 AND $rating < 3.74) {
		echo '<span class="product-rating rating-3"></span>';
	} elseif ($rating >= 3.75 AND $rating < 4.49) {
		echo '<span class="product-rating rating-4"></span>';
	} elseif ($rating >= 4.50) {
		echo '<span class="product-rating rating-5"></span>';
	}

	echo '</div><!-- woocommerce-listing-item-details -->';	

	echo '<h2><a href="' . get_permalink($product_id) . '">' . $product->get_title() . '</a></h2>';


	do_action( 'woocommerce_custom_loop_bottom' );
	?>

	</div><!-- woocommerce-listing-item-content -->
	
</li>
