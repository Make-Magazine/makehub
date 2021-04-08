<?php

/**
 * The Ecom Product Factory file.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/
 */

/**
 * The Ecom Product Factory class.
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Ecom_Product_Factory {

	/**
	 * Given an array of cart contents, create an array of products
	 *
	 * @param array $cart_contents The cart contents.
	 *
	 * @return array
	 */
	public function create_products_from_cart_contents( $cart_contents ) {
		return array_map( [ $this, 'product_from_cart_content' ], $cart_contents );
	}

	/**
	 * Given a cart content, create a product
	 *
	 * @param array $content The key-value array of a cart content.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	public function product_from_cart_content( $content ) {
		$ecom_product = $this->convert_wc_product_to_ecom_product( $content['data'] );
		$ecom_product->set_quantity( $content['quantity'] );
		return $ecom_product;
	}

	/**
	 * Given a WC Product, create an Ecom Product.
	 *
	 * @param WC_Product $product The WC Product.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	private function convert_wc_product_to_ecom_product( WC_Product $product ) {
		/**
		 * An instance of the Ecom Product class.
		 *
		 * @var Activecampaign_For_Woocommerce_Ecom_Product $ecom_product
		 */
		$ecom_product = new Activecampaign_For_Woocommerce_Ecom_Product();

		$ecom_product->set_externalid( $product->get_id() );
		$ecom_product->set_name( $product->get_name() );
		$ecom_product->set_price( $product->get_price() * 100 );
		$ecom_product->set_description( $product->get_description() );
		$ecom_product->set_category( $this->get_product_category( $product ) );
		$ecom_product->set_image_url( $this->get_product_image_url( $product ) );
		$ecom_product->set_product_url( $this->get_product_url( $product ) );
		$ecom_product->set_sku( $this->get_sku( $product ) );

		return $ecom_product;
	}

	/**
	 * Parse the results of the all of a product's categories and return the first one
	 *
	 * @param WC_Product $product The WC Product.
	 *
	 * @return string|null
	 */
	private function get_product_category( WC_Product $product ) {
		$terms = get_the_terms( $product->get_id(), 'product_cat' );

		if ( ! is_array( $terms ) ) {
			return null;
		}

		$term = array_pop( $terms );
		if ( $term instanceof WP_Term ) {
			return $term->name;
		}
		return null;
	}

	/**
	 * Get the image URL for a given WC Product.
	 *
	 * @param WC_Product $product The WC Product.
	 * @return string|null
	 */
	private function get_product_image_url( WC_Product $product ) {
		// TODO: Verify these still work with latest WC
		$post         = get_post( $product->get_id() );
		$thumbnail_id = get_post_thumbnail_id( $post );
		$image_src    = wp_get_attachment_image_src( $thumbnail_id, 'woocommerce_single' );

		if ( ! is_array( $image_src ) ) {
			// TODO: Add fallback for if thumbnail cannot be found
			return null;
		}

		// The first element is the actual URL
		return $image_src[0];
	}


	/**
	 * Get the product url for the product
	 *
	 * @param  WC_Product $product The WC Product.
	 * @return false|string|null
	 */
	private function get_product_url( WC_Product $product ) {
		$product_id = get_post( $product->get_id() );
		$url        = get_permalink( $product_id );

		if ( is_null( $url ) || empty( $url ) ) {
			return null;
		}

		return $url;
	}

	/**
	 * Get the sku for the product
	 *
	 * @param WC_Product $product The WC Product.
	 * @return string|null
	 */
	private function get_sku( WC_Product $product ) {
		$sku = $product->get_sku();

		if ( is_null( $sku ) || empty( $sku ) ) {
			return null;
		}

		return $sku;
	}
}
