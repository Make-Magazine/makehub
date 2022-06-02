<?php
    //Get current theme
    function wpvc_check_active_theme( $theme ) {
        return is_array( $theme ) ? in_array( get_template(), $theme, true ) : get_template() === $theme;
    }
                
	add_action('wp_head', 'wpvc_sharing_contestant_function');
    function wpvc_sharing_contestant_function(){	

        if(is_singular(WPVC_VOTES_TYPE)){
            global $wpdb,$post;
            $post_id = $post->ID;
            //for sharing
            $permalink1 = get_permalink( get_the_ID());
            $image_path = wpvc_vote_get_contestant_image($post_id,'large');
            $ow_image_src = $image_path['ow_image_src'];
            $content_desc = html_entity_decode(strip_tags($post->post_content));
            ?>
                <!-- for Google -->
                <meta name="description" content="<?php echo $content_desc; ?>" />
                <meta name="keywords" content="" />

                <meta name="author" content="" />
                <meta name="copyright" content="" />
                <meta name="application-name" content="" />

                <!-- for Facebook -->
                <meta property="fb:app_id" content="966242223397117" />
                <meta property="og:title" content="<?php echo get_the_title(); ?>" />
                <meta property="og:type" content="article" />
                <meta property="og:url" content="<?php echo $permalink1;?>" />
                <meta property="og:image" content="<?php echo $ow_image_src; ?>" />
         
                <meta property="og:image:width" content="450" />
                <meta property="og:image:height" content="250" />
                <meta property="og:description" content='<?php //echo $content_desc; ?>' />
                <meta name="og:author" content="Voting"/>

                <!-- for Twitter -->
                <meta name="twitter:card" content="summary" />
                <meta name="twitter:title" content="<?php echo htmlentities(get_the_title(),ENT_QUOTES); ?>" />
                <meta name="twitter:description" content="<?php echo $content_desc; ?>" />
                <meta name="twitter:image" content="<?php echo $ow_image_src; ?>" />

                <link rel="image_src" href="<?php echo $ow_image_src; ?>"/>
            <?php
        }
    }

    function wpvc_vote_get_contestant_image($post_id,$short_cont_image){
        if (has_post_thumbnail($post_id)) {
            $ow_image_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $short_cont_image);
            $data['ow_original_img'] = wp_get_attachment_url(get_post_thumbnail_id($post_id)).'?'.uniqid();
            $data['ow_image_src'] = $ow_image_arr[0];

            if(realpath($data['ow_image_src'])!='')
                $get_img_size=getimagesize(realpath($data['ow_image_src']));
            else
                $get_img_size=getimagesize($data['ow_image_src']);
            
        }else{
            $data['ow_image_src'] = WPVC_NO_IMAGE_CONTEST;
            $data['ow_original_img'] = WPVC_NO_IMAGE_CONTEST;
        }
        return $data;
    }

?>
