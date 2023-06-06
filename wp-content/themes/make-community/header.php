<?php
/**
 * The header for our theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php endif; ?>
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>

    <?php if ( get_theme_mod( 'onecommunity_preloader_enable', false ) == true ) {
        echo '<div id="loader-wrapper"></div>';
    }
    if( function_exists( 'wd_asp' ) ) { ?>
		<div class="ajax-site-search" style="display:none;"><?php echo do_shortcode('[wd_asp id=1]'); ?></div>
	<?php } ?>

        <div class="container">

            <?php
            // Universal Nav
            require_once(WP_CONTENT_DIR . '/universal-assets/v2/page-elements/universal-topnav.html');
            // Universal Subnav
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