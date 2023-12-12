<?php
/**
 * LearnDash LD30 focus mode header.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php
		wp_head();
		/**
		 * Fires in the head tag in focus mode.
		 *
		 * @since 3.0.0
		 */
		do_action( 'learndash-focus-head' );
		?>
	</head>
	<body <?php body_class(); ?>>

		<div class="<?php echo esc_attr( learndash_the_wrapper_class() ); ?>">
			<?php
				/**
				 * Filter Focus Mode sidebar collpases.
				 *
				 * @since 3.0.0
				 *
				 * @param bool false Wether to collapse Focus Mode sidebar. Default false.
				 */
			?>

			<?php
			// Universal Nav
			require_once(WP_CONTENT_DIR . '/universal-assets/v2/page-elements/universal-topnav.html');
			?>
			<div id="universal-subnav" class="nav-level-2">
				<?php
					wp_nav_menu( array(
					'menu'              => 'secondary_universal_menu',
					'theme_location'    => 'secondary_universal_menu',
					'container'         => '',
					'container_class'   => '',
					'link_before'       => '<span>',
					'link_after'        => '</span>',
					'menu_class'        => 'nav navbar-nav',
					'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
					'walker'            => new wp_bootstrap_navwalker())
					);
				?>
			</div>

			<div class="ld-focus ld-focus-initial-transition <?php echo esc_attr( apply_filters( 'learndash_focus_mode_collapse_sidebar', false ) ? 'ld-focus-sidebar-collapsed ld-focus-sidebar-filtered' : '' ); ?> <?php echo esc_attr( learndash_30_get_focus_mode_sidebar_position() ); ?>">