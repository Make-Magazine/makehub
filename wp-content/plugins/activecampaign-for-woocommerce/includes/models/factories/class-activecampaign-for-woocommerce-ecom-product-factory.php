<?php

/**
 * The Ecom Product Factory file.
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/
 */

use Activecampaign_For_Woocommerce_Logger as Logger;
use Activecampaign_For_Woocommerce_Utilities as AC_Utilities;

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
		try {
			return array_map( [ $this, 'product_from_cart_content' ], $cart_contents );
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->error(
				'Product factory could not create products from cart contents.',
				[
					'message'  => $t->getMessage(),
					'function' => 'create_products_from_cart_contents',
					'trace'    => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}
	}

	/**
	 * Given a cart content, create a product
	 *
	 * @param array $content The key-value array of a cart content.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	public function product_from_cart_content( $content ) {
		try {
			if (
				AC_Utilities::validate_object( $content['data'], 'get_id' ) ||
				$content['data'] instanceof WC_Product ||
				$content['data'] instanceof WC_Product_Factory
			) {
				$ecom_product = $this->convert_wc_product_to_ecom_product( $content['data'] );
			} else {
				$ecom_product = $this->convert_item_data_to_generic_product( $content );
			}

			if ( isset( $ecom_product ) ) {
				$ecom_product->set_quantity( $content['quantity'] );
				return $ecom_product;
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->error(
				'Product factory could not create products from cart contents.',
				[
					'message'  => $t->getMessage(),
					'function' => 'product_from_cart_content',
					'trace'    => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		return null;
	}

	/**
	 * Given a WC Product, create an Ecom Product.
	 *
	 * @param WC_Product $product The WC Product.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product|null
	 */
	private function convert_wc_product_to_ecom_product( $product ) {
		try {
			if ( AC_Utilities::validate_object( $product, 'get_id' ) && ! empty( $product->get_id() ) ) {
				$ecom_product = new Activecampaign_For_Woocommerce_Ecom_Product();

				$ecom_product->set_externalid( $product->get_id() );
				$ecom_product->set_name( $product->get_name() );
				$ecom_product->set_price( $product->get_price() > 0 ? $product->get_price() * 100 : 0 );
				$ecom_product->set_category( $this->get_product_all_categories( $product ) );
				$ecom_product->set_image_url( $this->get_product_image_url( $product ) );
				$ecom_product->set_product_url( $this->get_product_url( $product ) );
				$ecom_product->set_sku( $this->get_sku( $product ) );

				if ( ! empty( $product->get_short_description() ) ) {
					$description = $product->get_short_description();
				} else {
					$description = $product->get_description();
				}

				$ecom_product->set_description( $this->clean_description( $description ) );

				return $ecom_product;
			}
		} catch ( Throwable $t ) {
			$logger = new Logger();
			$logger->error(
				'Product factory could not convert a WC_Product to an ecom_product.',
				[
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}

		return null;
	}

	/**
	 * Convert a pre_product object to a generic product.
	 *
	 * @param array $pre_product The product item.
	 *
	 * @return Activecampaign_For_Woocommerce_Ecom_Product
	 */
	private function convert_item_data_to_generic_product( $pre_product ) {
		$logger = new Logger();
		try {
			$ecom_product = new Activecampaign_For_Woocommerce_Ecom_Product();

			$ecom_product->set_externalid( $pre_product['product_id'] );
			$ecom_product->set_name( $pre_product['name'] );
			$ecom_product->set_price( $pre_product['total'] > 0 ? $pre_product['total'] * 100 : 0 );
			$ecom_product->set_category( $this->get_product_all_categories( $pre_product['item'] ) );
			$ecom_product->set_image_url( $this->get_product_image_url( $pre_product['item'] ) );
			$ecom_product->set_product_url( $this->get_product_url( $pre_product['item'] ) );
			$ecom_product->set_sku( $this->get_sku( $pre_product['item'] ) );
			$ecom_product->set_description( '' );

			return $ecom_product;
		} catch ( Throwable $t ) {

			$logger->error(
				'Product factory could not convert the product to a generic ecom_product.',
				[
					'message' => $t->getMessage(),
					'trace'   => $logger->clean_trace( $t->getTrace() ),
				]
			);
		}
	}

	/**
	 * Cleans a description field by removing tags and shortening the number of words to a max amount.
	 *
	 * @param string $description The description.
	 *
	 * @return string
	 */
	public function clean_description( $description ) {
		$logger = new Logger();

		try {
			$plain_description = str_replace( array( "\r", "\n", '&nbsp;' ), ' ', $description );
			$plain_description = trim( wp_strip_all_tags( $plain_description, false ) );
			$plain_description = preg_replace( '/\s+/', ' ', $plain_description );
			$wrap_description  = wordwrap( $plain_description, 300 );
			$description_arr   = explode( "\n", $wrap_description );
			if ( isset( $description_arr[0] ) ) {
				$fin_description = $description_arr[0] . '...';
			}
		} catch ( Throwable $t ) {
			$logger->warning(
				'There was an issue cleaning the description field.',
				[
					'message'     => $t->getMessage(),
					'description' => $description,
				]
			);
		}

		if ( ! empty( $fin_description ) ) {
			return $fin_description;
		}

		if ( ! empty( $plain_description ) ) {
			return $plain_description;
		}

		return $description;
	}

	/**
	 * Parse the results of the all of a product's categories and return all as separated list
	 *
	 * @param WC_Product $product The WC Product.
	 *
	 * @return string|null
	 */
	private function get_product_all_categories( $product ) {
		$logger = new Logger();
		if ( AC_Utilities::validate_object( $product, 'get_id' ) && ! empty( $product->get_id() ) ) {
			$terms    = get_the_terms( $product->get_id(), 'product_cat' );
			$cat_list = [];
			try {
				// go through the categories and make a named list
				if ( ! empty( $terms ) && is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$product_cat_id   = $term->term_id;
						$product_cat_name = $term->name;
						if ( $product_cat_id >= 0 && ! empty( $product_cat_name ) ) {
							$cat_list[] = $product_cat_name;
						} else {
							$logger->warning(
								'A product category attached to this product does not have a valid category and/or name.',
								[
									'product_id' => $product->get_id(),
									'term_id'    => $term->term_id,
									'term_name'  => $term->name,
								]
							);
						}
					}
				}
			} catch ( Throwable $t ) {
				$logger->warning(
					'There was an error getting all product categories.',
					[
						'terms'          => $terms,
						'product_id'     => $product->get_id(),
						'trace'          => $logger->clean_trace( $t->getTrace() ),
						'thrown_message' => $t->getMessage(),
					]
				);
			}

			if ( ! empty( $cat_list ) ) {
				// Convert to a comma separated string
				return implode( ', ', $cat_list );
			}
		}

		return null;
	}

	/**
	 * Get the image URL for a given WC Product.
	 *
	 * @param WC_Product $product The WC Product.
	 * @return string|null
	 */
	private function get_product_image_url( $product ) {

		if ( AC_Utilities::validate_object( $product, 'get_id' ) && ! empty( $product->get_id() ) ) {
			try {
				$post         = get_post( $product->get_id() );
				$thumbnail_id = get_post_thumbnail_id( $post );
				$image_src    = wp_get_attachment_image_src( $thumbnail_id, 'woocommerce_single' );
			} catch ( Throwable $t ) {
				$logger = new Logger();

				$logger->warning(
					'There was an error getting product image url.',
					[
						'thrown_message' => $t->getMessage(),
						'post'           => isset( $post ) ? $post : null,
						'thumbnail_id'   => isset( $thumbnail_id ) ? $thumbnail_id : null,
						'image_src'      => isset( $image_src ) ? $image_src : null,
						'product_id'     => AC_Utilities::validate_object( $product, 'get_id' ) ? $product->get_id() : null,
						'trace'          => $logger->clean_trace( $t->getTrace() ),
					]
				);
			}

			if ( ! is_array( $image_src ) ) {
				// Right now this is null by default, we could go looking for a fallback.
				return '';
			}

			// The first element is the actual URL
			return $image_src[0];
		}

		return '';
	}


	/**
	 * Get the product url for the product
	 *
	 * @param  WC_Product $product The WC Product.
	 * @return false|string|null
	 */
	private function get_product_url( $product ) {
		if ( AC_Utilities::validate_object( $product, 'get_id' ) && ! empty( $product->get_id() ) ) {
			try {
				$product_id = get_post( $product->get_id() );
				$url        = get_permalink( $product_id );

				if ( is_null( $url ) || empty( $url ) ) {
					return '';
				}

				return $url;
			} catch ( Throwable $t ) {
				$logger = new Logger();
				$logger->warning(
					'There was an error getting product URL.',
					[
						'product_id'     => AC_Utilities::validate_object( $product, 'get_id' ) ? $product->get_id() : null,
						'thrown_message' => $t->getMessage(),
						'trace'          => $logger->clean_trace( $t->getTrace() ),
					]
				);
			}
		}

		return '';
	}

	/**
	 * Get the sku for the product
	 *
	 * @param WC_Product $product The WC Product.
	 * @return string|null
	 */
	private function get_sku( $product ) {
		if ( AC_Utilities::validate_object( $product, 'get_sku' ) && ! empty( $product->get_sku() ) ) {
			try {
				$sku = $product->get_sku();

				if ( is_null( $sku ) || empty( $sku ) ) {
					return '';
				}

				return $sku;
			} catch ( Throwable $t ) {
				$logger = new Logger();
				$logger->warning(
					'There was an error getting product sku.',
					[
						'product_id'     => AC_Utilities::validate_object( $product, 'get_id' ) ? $product->get_id() : null,
						'thrown_message' => $t->getMessage(),
						'trace'          => $logger->clean_trace( $t->getTrace() ),
					]
				);
			}
		}
		return '';
	}
}
