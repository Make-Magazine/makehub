<?php
    // Do Not Remove this Section - Start
    get_header();
    do_action('single_contestants_head');
    global $wpdb,$post;
    $show_cont_args = Wpvc_Shortcode_Model::wpvc_get_category_options_and_values($show_cont_args,$post->ID);
    $options = get_option(WPVC_VOTES_SETTINGS);
    $class_name="wpvc_single_contestant_fullwidth";
    if(is_array($options)){
        $vote_sidebar = $options['common']['vote_sidebar'];
        $vote_select_sidebar = $options['common']['sidebar'];
        if($vote_sidebar!='on'){
            if($vote_select_sidebar!=''){
                $class_name="wpvc_single_contestant_partialwidth";
            }
        }
    }    
    $show_args = htmlspecialchars(json_encode($show_cont_args), ENT_QUOTES, 'UTF-8');
    // Do Not Remove this Section - End
?>
    <section class="wpvc_vote_single_section">
        <div class="wpvc_vote_single_container">
            <!--React Js div -->
            <div class="wpvc_single_contestants_page">
                <?php // Do Not Remove this DIV ?>
                <div id="wpvc-singlecontestant-page" class="<?php echo $class_name; ?>" data-shortcode="singlecontestants" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>
                
                <?php
                /******************* Split voting header and footer show your added content in center
                 * Comment above div id="wpvc-singlecontestant-page" and uncomment below code
                 * 
                 * 
                <div id="wpvc-singlecustom-header" class="<?php echo $class_name; ?>" data-shortcode="singlecontestantcustom" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>
               
                <?php the_content(); ?>

                <div id="wpvc-singlecustom-footer" class="<?php echo $class_name; ?>" data-shortcode="singlecontestantcustom" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>

                */
                ?>
                <?php apply_filters('wpvc_single_contestants_html',get_the_ID() ); ?>
            </div>

            <?php
            if($vote_sidebar!='on'){
                if($vote_select_sidebar!=''){
                    if($vote_select_sidebar=='contestants_sidebar'){
                        echo '<div class="wpvc_votes_sidebar">';
                        dynamic_sidebar('contestants_sidebar');
                        echo '</div>';
                    }else{
                        echo '<div class="wpvc_votes_sidebar">';
                        get_sidebar($vote_select_sidebar);
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>

        <div class="ow_vote_content_comment"><?php comments_template( '', true ); ?></div>
        
    </section>
<?php get_footer(); ?>


