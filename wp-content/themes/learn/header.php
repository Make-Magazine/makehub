<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta http-equiv="content-type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="theme-color" content="#ffffff">

  <script type="text/javascript">
    var templateUrl = '<?= get_site_url(); ?>';
    var logoutURL = '<?php echo wp_logout_url( home_url() ); ?>';
  </script>

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-51157-36"></script>
  <script>
     window.dataLayer = window.dataLayer || [];
     function gtag(){dataLayer.push(arguments);}
     gtag('set','linker', {
         'accept_incoming': true,
         'domains': [
            'make.co',
            'makermedia.auth0.com',
            'makeco.staging.wpengine.com'
         ]
      });
     gtag('js', new Date());
     gtag('config', 'UA-51157-36', {
       'cookie_domain': 'make.co'
     });
  </script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5PRW4M2');</script>
<!-- End Google Tag Manager -->


  <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5PRW4M2" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<header class="universal-nav">

   <?php // Nav Level 1 and Hamburger
      echo file_get_contents(content_url() . '/universal-assets/v1/page-elements/universal-topnav.html');
   ?>

  <div class="nav-level-2">
    <div class="container">
        <?php
          wp_nav_menu( array(
              'menu'              => 'secondary_universal_menu',
              'theme_location'    => 'secondary_universal_menu',
              'depth'             => 1,
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
  </div><!-- .nav-level-2 -->

  <div id="nav-flyout">
   <?php
      echo file_get_contents(content_url() . '/universal-assets/v1/page-elements/universal-megamenu.html');
   ?>
  </div>

</header>
<div class="nav-flyout-underlay"></div>