<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BuddyBoss_Theme
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>

        <?php wp_body_open(); ?>

        <?php
        if (!is_singular('llms_my_certificate')):

            do_action(THEME_HOOK_PREFIX . 'before_page');

        endif;
        ?>

        <div id="page" class="site">

		<?php // Nav Level 1 and Hamburger      
			require_once(WP_CONTENT_DIR.'/universal-assets/v1/page-elements/universal-topnav.html');
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

        <?php do_action(THEME_HOOK_PREFIX . 'before_content'); ?>

            <div id="content" class="site-content">

		<?php do_action(THEME_HOOK_PREFIX . 'begin_content'); ?>

                <div class="container">
                    <div class="<?php echo apply_filters('buddyboss_site_content_grid_class', 'bb-grid site-content-grid'); ?>">