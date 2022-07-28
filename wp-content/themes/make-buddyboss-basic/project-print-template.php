<?php /* Template Name: Projects Print Page */

function get_lesson_output($lesson_id, $course_id) {
    global $post;
    do_action(THEME_HOOK_PREFIX . '_template_parts_content_top');
    $course_settings = learndash_get_setting($lesson_id);

    $lesson_materials = '';
    if (( 'on' === $course_settings['lesson_materials_enabled'] ) && (!empty($course_settings['lesson_materials']) )) {
        $lesson_materials = wp_specialchars_decode($course_settings['lesson_materials'], ENT_QUOTES);
        if (!empty($lesson_materials)) {
            $lesson_materials = do_shortcode($lesson_materials);
        }
    }
    ?>
    <div id="primary" class="content-area bb-grid-cell">
        <main id="main" class="site-main">
            <div style="text-align:center"><img src="<?php echo get_stylesheet_directory_uri() . '/images/makercamp-logo.png'; ?> " /></div>

			<div class="print-materials-wrapper">
				<div class="print-materials">
					<?php echo $lesson_materials; ?>
				</div>
            </div>

            <div class="print-body">
                <?php
                //print out lesson content
                $post = get_post($lesson_id, OBJECT); //set post info
                get_template_part('template-parts/content', 'sfwd');

                $topics = learndash_course_get_topics($course_id, $lesson_id);
                foreach ($topics as $topic) {
                    //print out topic content
                    $post = get_post($topic->ID, OBJECT); //set post info
                    get_template_part('template-parts/content', 'sfwd');
                }
                ?>
            </div>



        </main>
    </div>
    <?php
    //do_action(THEME_HOOK_PREFIX . '_single_template_part_content', 'page');
}

?>
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
			@media print {
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
                <p style="width:100%"><strong>Please Note</strong></p>
                <p>Your safety is your own responsibility, including proper use of equipment and safety gear, and determining whether you have adequate skill and experience. Power tools, electricity, and other resources used for these projects are dangerous, unless used properly and with adequate precautions, including safety gear and adult supervision. Some illustrative photos do not depict safety precautions or equipment, in order to show the project steps more clearly. Use of the instructions and suggestions found in Maker Camp is at your own risk. Maker Media, Inc., disclaims all responsibility for any resulting damage, injury, or expense.</p>
            </section>
        </div>

        <?php wp_footer(); ?>

        <script>
            jQuery(window).on("load", function() {
				 jQuery(document).scrollTop(jQuery(document).height());
				 window.print();
            });
        </script>

    </body>
</html>
