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
			@media print {
				.elementor-column.elementor-col-50 {
					width: 50% !important;
				}
				.elementor-column.elementor-col-33 {
					width: 33.333% !important;
				}
				.elementor-social-icons-wrapper {
					display: none;
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
                window.print();
            }
        </script>
		
    </body>
</html>