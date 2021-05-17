<?php /* Template Name: Projects Print Page */ ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <link rel="profile" href="http://gmpg.org/xfn/11">

        <script type="text/javascript">
            var templateUrl = '<?= get_site_url(); ?>';
            var logoutURL = '<?php echo wp_logout_url(home_url()); ?>';
        </script>
        <style>
            #wpadminbar, .ldms-message-tab,a.nt-note-tab {display:none}
        </style>
        <?php
        // Tracking pixels users can turn off through the cookie law checkbox -- defaults to yes
        if (!isset($_COOKIE['cookielawinfo-checkbox-non-necessary']) || $_COOKIE['cookielawinfo-checkbox-non-necessary'] == "yes") {
            ?>
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
                'makeco.staging.wpengine.com',
                'stagemakehub.wpengine.com',
                'devmakehub.wpengine.com'
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

        <?php } // end cookie law if ?>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>

        <?php                
        $lesson_id = '';
        $course_id = '';
        
        //is this for a lesson or a course?
        if (isset($_GET['lesson'])) {
            $lesson_id = $_GET['lesson'];
        } elseif (isset($_GET['course'])) {
            $course_id = $_GET['course'];
        }

        if ($course_id != '') {
            $lessons = learndash_course_get_lessons($course_id);

            foreach ($lessons as $lesson) {
                $lesson_id = $lesson->ID;
                echo '<h2>Lesson: ' . $lesson->post_title . '</h2><br/>';
                //echo 'Lesson Content<br/>';
                echo $lesson->post_content;
                echo '<br/>';
                $topics = learndash_course_get_topics($course_id, $lesson_id);
                foreach ($topics as $topic) {
                    echo '<br/><h3>Topic: ' . $topic->post_title . '</h3><br/>';
                    //echo 'Topic Content<br/>';
                    echo $topic->post_content;
                }
                echo '<br/>';
            }
        } elseif ($lesson_id != '') {
            $lesson = get_post($lesson_id);

            $course_id = learndash_get_setting($lesson, 'course');
            do_action(THEME_HOOK_PREFIX . '_template_parts_content_top');
            ?>
            <div id="primary" class="content-area bb-grid-cell">
                <main id="main" class="site-main">
                    <div style="text-align:center"><img src="<?php echo get_stylesheet_directory_uri() . '/images/makercamp-logo.png'; ?> " /></div>
                    <?php                    
                    //print out lesson content                    
                    $post = get_post( $lesson_id, OBJECT ); //set post info                                       
                    get_template_part( 'template-parts/content', 'sfwd' );
                    
                    $topics = learndash_course_get_topics($course_id, $lesson_id);
                    foreach ($topics as $topic) {
                        //print out topic content                    
                        $post = get_post( $topic->ID, OBJECT ); //set post info     
                        get_template_part( 'template-parts/content', 'sfwd' );

                    }
                    ?>
                </main>
            </div>
            <?php
            do_action(THEME_HOOK_PREFIX . '_single_template_part_content', 'page');
        }
        ?>
        <div class="elementor-section elementor-section-boxed">
            <section class="elementor-container">
                <p style="width:100%"><strong>Please Note</strong></p>
                <p>Your safety is your own responsibility, including proper use of equipment and safety gear, and determining whether you have adequate skill and experience. Power tools, electricity, and other resources used for these projects are dangerous, unless used properly and with adequate precautions, including safety gear and adult supervision. Some illustrative photos do not depict safety precautions or equipment, in order to show the project steps more clearly. Use of the instructions and suggestions found in Maker Camp is at your own risk. Maker Media, Inc., disclaims all responsibility for any resulting damage, injury, or expense.</p>
            </section>

        </div>



        <?php wp_footer(); ?>

        <script>
            jQuery( document ).ready(function() {            
                window.print();
            }
        </script>

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-51157-25', 'auto');
            ga('require', 'displayfeatures');
            ga('send', 'pageview', {
            'page': location.pathname + location.search + location.hash
            });
        </script>
        <script>
            var _prum = [['id', '53fcea2fabe53d341d4ae0eb'],
            ['mark', 'firstbyte', (new Date()).getTime()]];
            (function() {
            var s = document.getElementsByTagName('script')[0]
            , p = document.createElement('script');
            p.async = 'async';
            p.src = '//rum-static.pingdom.net/prum.min.js';
            s.parentNode.insertBefore(p, s);
            })();
        </script>
    </body>
</html>