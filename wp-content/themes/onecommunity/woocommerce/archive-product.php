<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<section class="wrapper">

<main id="content">

<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>

	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="page-title half"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php

	?>

<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	echo '<div class="clear"></div>';

	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 *
			 * @hooked WC_Structured_Data::generate_product_data() - 10
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

?>
</main><!-- content -->

<aside id="sidebar" class="sidebar">
	<?php 
	$transient = get_transient( 'onecommunity_sidebar_shop' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidebar_shop_enable', 0 ) == 1 ) {
	ob_start();

	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-shop')) : endif;

	$sidebar = ob_get_clean();
	print_r( $sidebar );
	
	if ( get_theme_mod( 'onecommunity_transient_sidebar_shop_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_shop', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_shop_expiration', 4320 ) );
	}

	} else {
		echo '<!-- Transient onecommunity_sidebar_shop ('.get_theme_mod( 'onecommunity_transient_sidebar_shop_expiration', 4320 ).' min) -->';
		print_r( $transient );
	}
	?>
</aside><!--sidebar ends-->

<div id="sidebar-spacer"></div>

</section><!-- .wrapper -->
<?php get_footer(); ?>
