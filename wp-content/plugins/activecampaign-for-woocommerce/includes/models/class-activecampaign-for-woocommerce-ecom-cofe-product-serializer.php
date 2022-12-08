<?php

use Activecampaign_For_Woocommerce_Ecom_Enum_Type as Enumish;
use Activecampaign_For_Woocommerce_Utilities as AC_Utilities;

/**
 * The file for the EcomProduct Cofe Model
 *
 * @link       https://www.activecampaign.com/
 * @since      1.0.0
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/models
 */

use Activecampaign_For_Woocommerce_Has_Id as Has_Id;
use Brick\Math\BigDecimal;
use Activecampaign_For_Woocommerce_Logger as Logger;

/**
 * The model class for the EcomProduct
 *
 * @since      1.0.0
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/models
 * @author     acteamintegrations <team-integrations@activecampaign.com>
 */
class Activecampaign_For_Woocommerce_Ecom_Cofe_Product_Serializer {

	/**
	 * @param WC_Product | WC_Product_Variation $product
	 * @param ?int                              $connection_id
	 * @param WC_Product?                       $parent
	 * @return array
	 */
	public static function product_array_for_cofe( $product, ?int $connection_id, $parent = null ): ?array {
		$logger       = new Logger();
		$cofe_product = self::base_product_fields( ! is_null( $parent ) ? $parent : $product );

		if ( $cofe_product ) {
			$cofe_product['legacyConnectionId'] = $connection_id;
			try {
				$tags = get_the_terms( $product->get_id(), 'product_tag' );

				if ( $tags ) {
					$tags = array_map(
						function ( $it ) {
							if ( $it->slug ) {
								return $it->slug;
							} elseif ( $it->name ) {
								return $it->name;
							} else {
								return null;
							}
						},
						$tags
					);
				}

				$cofe_product['tags'] = $tags ? $tags : null;

				$categories = get_the_terms( $product->get_id(), 'product_cat' );

				if ( $categories ) {
					$categories = array_map(
						function ( $it ) {
							if ( $it->term_id ) {
								return (string) $it->term_id;
							} else {
								return null;
							}
						},
						$categories
					);
				}

				if ( false === $categories ) {
					$categories = null;
				}

				$cofe_product['categories']     = $categories;
				$cofe_product['dimensionsUnit'] = get_option( 'woocommerce_dimension_unit' ) === 'cm' ? new Enumish( 'METRIC' ) : new Enumish( 'IMPERIAL' );

				$cofe_product['storePrimaryId']           = self::int_as_string( $product->get_id() );
				$cofe_product['variantSku']               = $product->get_sku();
				$cofe_product['variantName']              = $product->get_name();
				$cofe_product['variantDescription']       = AC_Utilities::clean_description( $product->get_description(), 4096 );
				$cofe_product['variantPriceCurrency']     = get_woocommerce_currency();
				$cofe_product['variantPriceAmount']       = self::big_decimal_price( $product->get_price() );
				$cofe_product['variantStoreCreatedDate']  = self::date_format( $product->get_date_created() );
				$cofe_product['variantStoreModifiedDate'] = self::date_format( $product->get_date_modified() );
				$cofe_product['variantImages']            = self::images( $product );
				$cofe_product['variantUrl']               = $product->get_permalink();
				$cofe_product['variantUrlSlug']           = $product->get_slug();
				$cofe_product['variantWeight']            = self::big_decimal_weight( $product->get_weight() );
				$dimensions                               = self::dimensions( $product );
				if ( ! empty( $dimensions ) ) {
					$cofe_product['variantDimensions'] = $dimensions;
				}

				$cofe_product['type']                = $product->get_type();
				$cofe_product['status']              = $product->get_status();
				$cofe_product['inventoryQuantity']   = $product->get_stock_quantity();
				$cofe_product['numberOfSales']       = self::int_field( $product->get_total_sales() );
				$cofe_product['isVirtual']           = $product->get_virtual();
				$cofe_product['isDownloadable']      = $product->get_downloadable();
				$cofe_product['isVisible']           = $product->is_visible();
				$cofe_product['isOnSale']            = $product->is_on_sale();
				$cofe_product['isBackordersAllowed'] = $product->backorders_allowed();
				if ( $product->is_in_stock() !== null ) {
					if ( $product->is_in_stock() ) {
						$cofe_product['stockStatus'] = new Enumish( 'IN_STOCK' );
					} else {
						$cofe_product['stockStatus'] = new Enumish( 'OUT_OF_STOCK' );
					}

					if ( $product->is_on_backorder() ) {
						$cofe_product['stockStatus'] = new Enumish( 'BACKORDER' );
					}
				}
				$cofe_product['averageRatings'] = self::int_as_string( $product->get_average_rating() );
				$cofe_product['ratingCount']    = self::int_field( $product->get_rating_count() );
				$cofe_product['attributes']     = self::map_field( $product->get_attributes() );

				return $cofe_product;
			} catch ( Throwable $t ) {
				$logger->error(
					'Failed to build cofe product',
					[
						'message'      => $t->getMessage(),
						'cofe_product' => $cofe_product,
						'trace'        => $t->getTrace(),
					]
				);
			}
		}

		return null;
	}

	/**
	 * @param ?WC_DateTime $wc_date_field
	 * @return string|null
	 */
	private static function date_format( $wc_date_field ): ?string {
		return null !== $wc_date_field ? $wc_date_field->__toString() : null;
	}

	/**
	 * @param ?int $int_field
	 * @return string
	 */
	private static function int_as_string( ?int $int_field ): ?string {
		if ( null === $int_field ) {
			return null;
		} else {
			return strval( $int_field );
		}
	}

	/**
	 * @param WC_Product $product
	 * @return array
	 */
	public static function dimensions( $product ): ?array {
		if ( ! $product instanceof WC_Product ) {
			return null;
		}
		$dimensions = array();
		if ( is_numeric( $product->get_length() ) ) {
			$dimensions['length'] = BigDecimal::of( $product->get_length() );
		}
		if ( is_numeric( $product->get_width() ) ) {
			$dimensions['width'] = BigDecimal::of( $product->get_width() );
		}
		if ( is_numeric( $product->get_height() ) ) {
			$dimensions['height'] = BigDecimal::of( $product->get_height() );
		}
		return $dimensions;
	}

	/**
	 * @param string $weight
	 * @return BigDecimal|null
	 */
	public static function big_decimal_weight( string $weight ): ?BigDecimal {
		if ( is_numeric( $weight ) ) {
			return BigDecimal::of( $weight );
		} else {
			return null;
		}
	}

	/**
	 * @param string|int $field
	 * @return BigDecimal|null
	 */
	public static function big_decimal_price( $field ): ?BigDecimal {
		if ( is_numeric( $field ) ) {
			return BigDecimal::of( $field );
		} else {
			return BigDecimal::of( 0 );
		}
	}

	/**
	 * @param WC_Product $product
	 * @return array
	 */
	public static function images( $product ): array {
		$images = $product->get_gallery_image_ids();
		if ( $product->get_image_id() ) {
			$images[] = $product->get_image_id();
		}

		$result = array();
		foreach ( $images as $image_id ) {
			$image_data = wp_get_attachment_image_src( $image_id, 'full' );
			if ( $image_data ) {
				$result[] = array(
					'url'    => $image_data[0],
					'width'  => $image_data[1] ? $image_data[1] : null,
					'height' => $image_data[2] ? $image_data[2] : null,
				);
			}
		}

		return $result;
	}


	/**
	 * @param ?WC_Product $base_product
	 * @return array
	 */
	public static function base_product_fields( $base_product ): array {
		$logger = new Logger();
		if ( ! $base_product ) {
			$logger->debug( 'No base product provided.' );
			return array();
		}
		try {
			$cofe_product                                 = array();
			$cofe_product['storeBaseProductId']           = self::int_as_string( $base_product->get_parent_id() ? $base_product->get_parent_id() : $base_product->get_id() );
			$cofe_product['baseProductName']              = $base_product->get_name();
			$cofe_product['baseProductDescription']       = AC_Utilities::clean_description( $base_product->get_description(), 4096 );
			$cofe_product['baseProductStoreCreatedDate']  = self::date_format( $base_product->get_date_created() );
			$cofe_product['baseProductStoreModifiedDate'] = self::date_format( $base_product->get_date_modified() );

			$cofe_product['baseProductImages']  = self::images( $base_product );
			$cofe_product['baseProductUrl']     = $base_product->get_permalink();
			$cofe_product['baseProductUrlSlug'] = $base_product->get_slug();

			$cofe_product['baseProductWeight'] = self::big_decimal_weight( $base_product->get_weight() );
			$dimensions                        = self::dimensions( $base_product );
			if ( ! empty( $dimensions ) ) {
				$cofe_product['baseProductDimensions'] = $dimensions;
			}

			return $cofe_product;
		} catch ( Throwable $t ) {
			$logger->error(
				'Failed for build base product fields for cofe product',
				[
					'message'      => $t->getMessage(),
					'cofe_product' => $cofe_product,
					'found_in'     => 'base_product_fields',
				]
			);

			return array();
		}
	}

	private static function int_field( $stringy ) {
		$int_val = intval( $stringy );
		if ( 0 !== $int_val ) {
			return $int_val;
		} elseif ( '0' === $stringy || 0.0 === $stringy ) {
			return 0;
		} else {
			return null;
		}
	}

	/**
	 * Needed to normalize arrays so that they are not nested.
	 *
	 * @param ?array $field
	 * @return array
	 */
	private static function map_field( ?array $field ) : ?array {
		$result = array();
		foreach ( $field as $k => $v ) {
			// GraphQL cannot process any other characters as keys, so replace them
			$k = preg_replace( '/[^A-Za-z0-9_]+/', '__', $k );

			if ( is_array( $v ) ) {
				if ( ! empty( $v ) ) {
					$result[ $k ] = wp_json_encode( $v );
				}
			} elseif ( $v instanceof WC_Product_Attribute ) {
				// For some reason, $v was WC_Product_Attribute, it rendered in a weird way. Not setting anything when $v is WC_Product_Attribute
				$result[ $k ] = null;
			} else {
				$result[ $k ] = $v;
			}
		}
		if ( empty( $result ) ) {
			return null;
		} else {
			return $result;
		}
	}


}
