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
            #wpadminbar, .ldms-message-tab,a.nt-note-tab, .ldfc-favorite-button {display:none}
            .container, .elementor-section.elementor-section-boxed > .elementor-container {width:100% !important; max-width: 100% !important}
            @media print {
                .container, .elementor-section.elementor-section-boxed > .elementor-container {width:100% !important; max-width: 100% !important}
                .elementor-column.elementor-col-50 {
                    width: 50% !important;
                }
                .elementor-column.elementor-col-33 {
                    width: 33.333% !important;
                }
                .elementor-social-icons-wrapper, .elementor-social-icon, .elementor-social-icon svg {
                    display: none;
                    visibility: hidden;
                    height: 0p
                }
                * {
                    -webkit-print-color-adjust: exact !important;   /* Chrome, Safari */
                    color-adjust: exact !important;                 /*Firefox*/
                }
            }

        </style>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php
        $lesson_id = (isset($_GET['lesson'])?$_GET['lesson']:'');
        $course_id = (isset($_GET['course'])?$_GET['course']:'');;

        //is this for a lesson or a course?
        if ($course_id != '') {
            $lessons = learndash_course_get_lessons($course_id);
            foreach ($lessons as $lesson) {
                get_lesson_output($lesson->ID, $course_id);
            }
        } elseif ($lesson_id != '') {
            $lesson = get_post($lesson_id);
            $course_id = learndash_get_setting($lesson, 'course');
            get_lesson_output($lesson_id, $course_id);
        }
        ?>
     
        <div class="elementor-section elementor-section-boxed">
            <section class="elementor-container">
                <div class="elementor-widget-container">
                    <?php
                    // Author section
                    global $post;
                    $course_post = learndash_get_setting($post, 'course');
                    $course_data = get_post($course_post);
                    $author_id = $course_data->post_author;
                    learndash_get_template_part('template-course-author.php', array(
                        'user_id' => $author_id
                            ), true);
                    ?>
                    <p style="width:100%"><strong>Please Note</strong></p>
                    <p>Your safety is your own responsibility, including proper use of equipment and safety gear, and determining whether you have adequate skill and experience. Power tools, electricity, and other resources used for these projects are dangerous, unless used properly and with adequate precautions, including safety gear and adult supervision. Some illustrative photos do not depict safety precautions or equipment, in order to show the project steps more clearly. Use of the instructions and suggestions found in Maker Camp is at your own risk. Maker Media, Inc., disclaims all responsibility for any resulting damage, injury, or expense.</p>
                </div>
            </section>
        </div>

        <?php wp_footer(); ?>

        <script>
            jQuery(window).on("load", function () {
               jQuery(document).scrollTop(jQuery(document).height());
               window.print();
            });
        </script>

    </body>
</html>