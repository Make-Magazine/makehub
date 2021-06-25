<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

            <div style="float:left;width:70%">
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
            <div style="float:left;width:30%">
                <?php echo $lesson_materials; ?>    
            </div>                        


        </main>
    </div>
    <?php
    do_action(THEME_HOOK_PREFIX . '_single_template_part_content', 'page');
}
