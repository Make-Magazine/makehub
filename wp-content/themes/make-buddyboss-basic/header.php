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

        <script type="text/javascript">
            var templateUrl = '<?= get_site_url(); ?>';
            var logoutURL = '<?php echo wp_logout_url(home_url()); ?>';
        </script>

        <?php
        // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
        if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
            ?>
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=UA-51157-36"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('set', 'linker', {
                    'accept_incoming': true,
                    'domains': [
                        'make.co',
                        'makermedia.auth0.com',
                        'stagemakehub.wpengine.com',
                        'devmakehub.wpengine.com'
                    ]
                });
                gtag('js', new Date());
                gtag('config', 'UA-51157-36');
            </script>

            <!-- Google Tag Manager -->
            <script>(function (w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({'gtm.start':
                                new Date().getTime(), event: 'gtm.js'});
                    var f = d.getElementsByTagName(s)[0],
                            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', 'GTM-5PRW4M2');</script>
            <!-- End Google Tag Manager -->

        <?php } // end cookie law if  ?>

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

            <?php do_action(THEME_HOOK_PREFIX . 'before_header'); ?>

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

            <?php do_action(THEME_HOOK_PREFIX . 'after_header'); ?>

            <?php do_action(THEME_HOOK_PREFIX . 'before_content'); ?>

            <div id="content" class="site-content">

                <?php do_action(THEME_HOOK_PREFIX . 'begin_content'); ?>

                <div class="container">
                    <div class="<?php echo apply_filters('buddyboss_site_content_grid_class', 'bb-grid site-content-grid'); ?>">
