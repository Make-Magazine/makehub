<?php
    // Do Not Remove this Section - Start
    get_header();
    do_action('single_contestants_head');
    global $wpdb,$post;
    $show_cont_args = Wpvc_Shortcode_Model::wpvc_get_category_options_and_values($show_cont_args,$post->ID);
	//error_log(print_r($show_cont_args, TRUE));

	// Get all our custom fields, the hard way: by whatever hash the voting plugin decides to name each one
	$first_name = 		get_post_meta( $post->ID, '6296aa69eeefa', true );
	$last_name = 		get_post_meta( $post->ID, '6296aa7bb7d7c', true );
	$age_range = 		get_post_meta( $post->ID, '6296aaa89bf80', true );
	$address = 			get_post_meta( $post->ID, '6296aaba1fe53', true );
	$city =				get_post_meta( $post->ID, '6296aac841e1e', true );
	$state = 			get_post_meta( $post->ID, '6296aad9298ab', true );
	$country = 			get_post_meta( $post->ID, '6296aae608afb', true );
	$profile_pic =		get_post_meta( $post->ID, '6296ab09c9118', true );
	$affiliation = 		get_post_meta( $post->ID, '6296ab5342b12', true );
	if( !empty(get_post_meta( $post->ID, '629a4f2c8e5a7', true )) ) {
		$affiliation .= " - " . get_post_meta( $post->ID, '629a4f2c8e5a7', true );
	}
	$about_you = 		nl2br(get_post_meta( $post->ID, '6296abebd1db5', true ));
	$team_members = 	nl2br(get_post_meta( $post->ID, '629a44ec6e801', true ));
	$instagram = 		get_post_meta( $post->ID, '6296f3cb4740f', true );
	$facebook = 		get_post_meta( $post->ID, '6296f3df02364', true );
	$twitter = 			get_post_meta( $post->ID, '6296f3ecceb51', true );
	$youtube = 			get_post_meta( $post->ID, '6296f409b9337', true );
	$tiktok = 			get_post_meta( $post->ID, '6296f4333960e', true );
	$project_title = 	get_post_meta( $post->ID, '6296f8124ee84', true );
	$project_type = 	get_post_meta( $post->ID, '6296f8420e061', true );
	$short_desc = 		get_post_meta( $post->ID, '6296f85ac2e55', true );
	$project_url = 		get_post_meta( $post->ID, '6296f86d0e92e', true );
	$proj_photo_1 = 	get_post_meta( $post->ID, '6296f8905a6b1', true );
	$proj_photo_2 = 	get_post_meta( $post->ID, '6297e8aa5b779', true );
	$proj_photo_3 = 	get_post_meta( $post->ID, '6297e8c2debe8', true );
	$proj_photo_4 = 	get_post_meta( $post->ID, '6297e8dec3625', true );
	$proj_photo_5 = 	get_post_meta( $post->ID, '6297e8f0ba77f', true );
	$proj_photo_6 = 	get_post_meta( $post->ID, '6297e926b8bb0', true );
	$proj_photo_7 = 	get_post_meta( $post->ID, '629a31864f497', true );
	$proj_photo_8 = 	get_post_meta( $post->ID, '629a31b55f6e9', true );
	$proj_photo_9 = 	get_post_meta( $post->ID, '629a31c874571', true );
	$proj_photo_10 = 	get_post_meta( $post->ID, '629a31db752d0', true );
	$project_images =   array($proj_photo_1, $proj_photo_2, $proj_photo_3, $proj_photo_4, $proj_photo_5, $proj_photo_6, $proj_photo_7, $proj_photo_8, $proj_photo_9, $proj_photo_10);
	$project_video = 	get_post_meta( $post->ID, '6296f8b14de31', true );
	$inspired_you =		nl2br(get_post_meta( $post->ID, '6296f8c369877', true ));
	$about_project = 	nl2br(get_post_meta( $post->ID, '6296f8c43c019', true ));
	$what_you_learned = nl2br(get_post_meta( $post->ID, '6296f8f23e3c6', true ));
	$project_impact = 	nl2br(get_post_meta( $post->ID, '6296f9147b9fa', true ));

	//$title = $show_cont_args
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
                 */  ?>

				<div id="wpvc-singlecustom" class="<?php echo $class_name; ?> container">
					<div class="wpvc-title">
						<h1><?php echo $project_title; ?></h1>
						<h3>By <?php echo $first_name . " " . $last_name; ?></h3>
						<a href="/amazing-maker-awards-contestants/" class="btn contest-back-btn">Back to Entry Gallery</a>
					</div>
					<div class="wpvc-main-wrapper">
						<div class="wpvc-video flex-main">
							<?php if( str_contains($project_video, "youtube") || str_contains($project_video, "youtu.be") ) {
								echo do_shortcode("[embedyt]" . $project_video . "[/embedyt]");
							} else if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $project_video, $regs)) {
								echo('<iframe src="https://player.vimeo.com/video/' . $regs[1] . '"></iframe>');
							} else {
								echo $project_video;
							}?>
						</div>
						<?php if(is_user_logged_in() == true) { ?>
							<div id="wpvc-singlecustom-header" class="flex-main" data-shortcode="singlecontestantcustom" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>
						<?php } else { ?>
							<div class="flex-main">
								<a href="/wp-login.php?redirect_to=<?php echo get_permalink(); ?>" class="btn universal-btn">Please Log In to Vote</a>
							</div>
						<?php } ?>

						<div class="wpvc-profile flex-sidebar">
							<div class="wpvc-profile-image">
								<img src="<?php echo $profile_pic; ?>" />
							</div>
							<?php /* if (!empty($city)) { ?>
								<div class="wpvc-profile-city"><b>City:</b> <?php echo $city; ?></div>
							<?php } */ ?>
							<?php if (!empty($state)) { ?>
								<div class="wpvc-profile-state"><b>State:</b> <?php echo $state; ?></div>
							<?php } ?>
							<?php if (!empty($country)) { ?>
								<div class="wpvc-profile-country"><b>Country:</b> <?php echo $country; ?></div>
							<?php } ?>
							<?php if (!empty($affiliation)) { ?>
								<div class="wpvc-profile-affiliation"><b>Affiliation:</b> <?php echo $affiliation; ?></div>
							<?php } ?>
							<?php if (!empty($about_you)) { ?>
								<div class="wpvc-profile-about-you"><b>About them:</b> <?php echo $about_you; ?></div>
							<?php } ?>
							<?php if (!empty($team_members)) { ?>
								<div class="wpvc-profile-team-members"><b>Team Members:</b><br /> <?php echo $team_members; ?></div>
							<?php } ?>
							<?php if(!empty($instagram) || !empty($facebook) || !empty($twitter) || !empty($youtube) || !empty($tiktok)) { ?>
								<h4>Social:</h4>
								<div class="wpvc-profile-social">
									<?php if(!empty($instagram)) { ?>
										<a href="<?php echo $instagram; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
									<?php } ?>
									<?php if(!empty($facebook)) { ?>
										<a href="<?php echo $facebook; ?>" target="_blank"><i class="fab fa-facebook"></i></a>
									<?php } ?>
									<?php if(!empty($twitter)) { ?>
										<a href="<?php echo $twitter; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
									<?php } ?>
									<?php if(!empty($youtube)) { ?>
										<a href="<?php echo $youtube; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
									<?php } ?>
									<?php if(!empty($tiktok)) { ?>
										<a href="<?php echo $tiktok; ?>" target="_blank"><i class="fab fa-tiktok"></i></a>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<div class="wpvc-details flex-main">
							<p><?php echo $short_desc; ?></p>
							<?php if(!empty($project_type)) { ?>
								<p><b>Project Type: </b><?php echo $project_type; ?></p>
							<?php } ?>
							<?php if(!empty($project_url)) { ?>
								<p><a href="<?php echo $project_url; ?>" target="_blank"><?php echo $project_url; ?></a></p>
							<?php } ?>
						</div>
					</div>
					<?php if (!empty($proj_photo_1)) { ?>
			            <div class="wpvc-gallery" id="ps-gallery">
							<div class="psgal_wrap">
			                <?php
			                	foreach($project_images as $val) {
									if(!empty($val)) {
										echo("<figure><span><img src='" . $val . "' data-original-src='" . $val . "' /></span></figure>");
									}
								}
			    			?>
							</div>
			                <a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal_wrap figure:first-of-type span img').click())"><i class="fas fa-search"></i></a>
			            </div>
			        <?php } ?>

					<div class="wpvc-essay">
						<?php if(!empty($inspired_you)) { ?>
							<div class="wpvc-essay-item wpvc-inspired-you">
								<h4>What inspired you or what is the idea that got you started?</h4>
								<p><?php echo $inspired_you; ?></p>
							</div>
						<?php } ?>
						<?php if(!empty($about_project)) { ?>
							<div class="wpvc-essay-item wpvc-about-project">
								<h4>What is your project about and how does it work?</h4>
								<p><?php echo $about_project; ?></p>
							</div>
						<?php } ?>
						<?php if(!empty($what_you_learned)) { ?>
							<div class="wpvc-essay-item wpvc-what-you-learnd">
								<h4>What did you learn by doing this project?</h4>
								<p><?php echo $what_you_learned ?></p>
							</div>
						<?php } ?>
						<?php if(!empty($project_impact)) { ?>
							<div class="wpvc-essay-item wpvc-project-impact">
								<h4>What impact does your project have on others as well as yourself?</h4>
								<p><?php echo $project_impact; ?></p>
							</div>
						<?php } ?>
					</div>

				</div>

                <?php /* This entire "footer" area creates all the custom fields as a list in react. we ain't doing that
				<div id="wpvc-singlecustom-footer" class="<?php echo $class_name; ?>" data-shortcode="singlecontestantcustom" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>
				*/ ?>

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

        <?php /* <div class="ow_vote_content_comment"><?php comments_template( '', true ); ?></div> */ ?>

    </section>
<?php get_footer(); ?>
