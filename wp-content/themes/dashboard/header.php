<?php

/**
 * The header for our theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if (is_singular() && pings_open(get_queried_object())) : ?>
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php endif; ?>
    <!--[if lt IE 9]>
    <script src="<?php echo esc_url(get_template_directory_uri()); ?>/js/html5.js"></script>
    <![endif]-->
    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

    <div class="container">
        <?php
        // Universal Nav
        require_once(WP_CONTENT_DIR . '/universal-assets/v2/page-elements/universal-topnav.html');

        // Universal Subnav
        ?>
        <div id="universal-subnav" class="nav-level-2">
            <?php
            wp_nav_menu(
                array(
                    'menu'              => 'secondary_universal_menu',
                    'theme_location'    => 'secondary_universal_menu',
                    'container'         => '',
                    'container_class'   => '',
                    'link_before'       => '<span>',
                    'link_after'        => '</span>',
                    'menu_class'        => 'nav navbar-nav',
                    'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                    'walker'            => new wp_bootstrap_navwalker()
                )
            );
            ?>
        </div>