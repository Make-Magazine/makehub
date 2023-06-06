<?php
function add_head_pixels() {   
    ?>
    <script type="text/javascript">
        var templateUrl = '<?= get_site_url(); ?>';
        var logoutURL   = '<?php echo wp_logout_url(home_url()); ?>';
    </script>
    <?php
    // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
    if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
        $pageUniq = trim(strtok($_SERVER["REQUEST_URI"], '?'), '/');
        if(is_front_page()) {
            $pageUniq = "home";
        }
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
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5PRW4M2');</script>
        <!-- End Google Tag Manager -->
        
        <!--  Pinterest  -->
            <script type="text/javascript">
                !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(
                    Array.prototype.slice.call(arguments))};var
                    n=window.pintrk;n.queue=[],n.version="3.0";var
                    t=document.createElement("script");t.async=!0,t.src=e;var
                    r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}
                    ("https://s.pinimg.com/ct/core.js"); pintrk('load', '2613138638003'); pintrk('page');
            </script>
            <noscript>
              <img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?tid=2613138638003&noscript=1" />
            </noscript>
            <script>pintrk('track', 'pagevisit', {event_id: '<?php echo $pageUniq; ?>'});</script>
            <noscript>
                <img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/v3/?tid=2613138638003&event=pagevisit&noscript=1" />
            </noscript>
            <!--  end Pinterest -->
    <?php } // end cookie law if  
}
add_action ('wp_head','add_head_pixels');

function add_body_pixels() { 
    if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") { ?>
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5PRW4M2"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        <?php 
    }     
}    
add_action ('wp_body_open','add_body_pixels');

function add_foot_pixels(){
    ?>
    <a href="mailto:feedback@make.co" class="btn feedback-btn">FEEDBACK</a>

    <?php
    // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
    if(!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes" ) {
        ?>
        <!-- Start Active Campaign Pixel -->
        <script type="text/javascript">
            (function(e,t,o,n,p,r,i){e.visitorGlobalObjectAlias=n;e[e.visitorGlobalObjectAlias]=e[e.visitorGlobalObjectAlias]||function(){(e[e.visitorGlobalObjectAlias].q=e[e.visitorGlobalObjectAlias].q||[]).push(arguments)};e[e.visitorGlobalObjectAlias].l=(new Date).getTime();r=t.createElement("script");r.src=o;r.async=true;i=t.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)})(window,document,"https://diffuser-cdn.app-us1.com/diffuser/diffuser.js","vgo");
            vgo('setAccount', '1000801328');
            vgo('setTrackByDefault', true);
            vgo('process');
        </script>
        <!-- Start Active Campaign Pixel -->
<?php } 
}
add_action('wp_footer', 'add_foot_pixels');