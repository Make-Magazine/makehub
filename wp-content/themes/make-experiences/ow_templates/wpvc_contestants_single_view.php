<?php
    // Do Not Remove this Section - Start
    get_header();
    do_action('single_contestants_head');
    global $wpdb,$post;
    $show_cont_args = Wpvc_Shortcode_Model::wpvc_get_category_options_and_values($show_cont_args,$post->ID);
	//error_log(print_r($show_cont_args, TRUE));

	// Get all our custom fields, the hard way: by whatever hash the voting plugin decides to name each one
	$first_name = 		get_field( 'user_first_name', $post->ID, true );
	$last_name = 		get_field( 'user_last_name', $post->ID, true );
	$age_range = 		get_field( 'age_group', $post->ID, true );
	$address = 			get_field( 'user_address', $post->ID, true );
	$city =				get_field( 'user_city', $post->ID, true );
	$state = 			get_field( 'user_state', $post->ID, true );
	$country = 			get_field( 'user_country', $post->ID, true );
	$profile_pic =		get_field( 'upload_profile_photo', $post->ID, true );
	$affiliation = 		get_field( 'affiliation', $post->ID, true );
	if( !empty(get_field( 'affiliation_text', $post->ID, true )) ) {
		$affiliation .= " - " . get_field( 'affiliation_text', $post->ID, true );
	}
	$about_you = 		nl2br(get_field( 'user_about',  $post->ID, true ));
	$team_members = 	nl2br(get_field( 'team_members',  $post->ID, true ));
	$social_media =		get_field( 'social_media', $post->ID, true );
	$project_title = 	get_field( 'project_title', $post->ID, true );
	$project_type = 	implode(', ', get_field( 'project_type', $post->ID, true ));
	$short_desc = 		get_field( 'project_short_description', $post->ID, true );
	$project_url = 		get_field( 'project_url', $post->ID, true );
	$project_images =   get_field( 'project_photos', $post->ID, true );
	$project_video = 	get_field( 'project_video_link', $post->ID, true );
	$inspired_you =		nl2br(get_field( 'inspiration', $post->ID, true ));
	$about_project = 	nl2br(get_field( 'what_is_project_about', $post->ID, true ));
	$what_you_learned = nl2br(get_field( 'what_did_you_learn', $post->ID, true ));
	$project_impact = 	nl2br(get_field( 'project_impact', $post->ID, true ));

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
				<a href="/amazing-maker-awards/" class="btn contest-back-btn"><< Project Gallery</a>
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
					</div>
					<div class="wpvc-main-wrapper">
						<div class="flex-main">
							<div class="wpvc-video">
								<?php if( str_contains($project_video, "youtube") || str_contains($project_video, "youtu.be") ) {
									echo do_shortcode("[embedyt]" . $project_video . "[/embedyt]");
								} else if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $project_video, $regs)) {
									echo('<iframe src="https://player.vimeo.com/video/' . $regs[1] . '"></iframe>');
								} else {
									echo $project_video;
								}?>
							</div>
							<div class="wpvc-vote">
								<div id="wpvc-singlecustom-header" data-shortcode="singlecontestantcustom" data-url="<?php echo site_url();?>" data-args='<?php echo $show_args; ?>' data-postid="<?php echo $post->ID; ?>" ></div>
								<a href="/wp-login.php?redirect_to=<?php echo get_permalink(); ?>" class="btn universal-btn lgn-btn">Please Log In to Vote</a>
							</div>
							<div class="wpvc-details">
								<p><?php echo $short_desc; ?></p>
								<?php if(!empty($project_type)) { ?>
									<p><b>Type: </b><?php echo $project_type; ?></p>
								<?php } ?>
								<?php if(!empty($project_url)) { ?>
									<p><b>Website: </b><a href="<?php echo $project_url; ?>" target="_blank"><?php echo $project_url; ?></a></p>
								<?php } ?>
							</div>
						</div>
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
								<div class="wpvc-profile-about-you">
									<p class="collapse" id="collapseIt" aria-expanded="false"><b>About them:</b> <?php echo $about_you; ?></p>
									<a role="button" class="collapsed" data-toggle="collapse" href="#collapseIt" aria-controls="collapseIt"></a>
								</div>
							<?php } ?>
							<?php if (!empty($team_members)) { ?>
								<div class="wpvc-profile-team-members"><b>Team Members:</b><br /> <?php echo $team_members; ?></div>
							<?php } ?>
							<?php if(!empty($social_media)) { ?>
								<h4>Social:</h4>
								<div class="wpvc-profile-social">
									<?php
									foreach($social_media as $social) {
										if(strtolower($social['platform']) == 'instagram') { ?>
											<a href="<?php echo $social['social_url'] ?>" target="_blank"><i class="fab fa-instagram"></i></a>
										<?php } ?>
										<?php if(strtolower($social['platform']) == 'facebook') { ?>
											<a href="<?php echo $social['social_url'] ?>" target="_blank"><i class="fab fa-facebook"></i></a>
										<?php } ?>
										<?php if(strtolower($social['platform']) == 'twitter') { ?>
											<a href="<?php echo $social['social_url'] ?>" target="_blank"><i class="fab fa-twitter"></i></a>
										<?php } ?>
										<?php if(strtolower($social['platform']) == 'youtube') { ?>
											<a href="<?php echo $social['social_url'] ?>" target="_blank"><i class="fab fa-youtube"></i></a>
										<?php } ?>
										<?php if(strtolower($social['platform']) == 'tiktok') { ?>
											<a href="<?php echo $social['social_url'] ?>" target="_blank"><i class="fab fa-tiktok"></i></a>
										<?php }
									}
									?>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php if ($project_images) { ?>
			            <div class="wpvc-gallery" id="ps-gallery">
							<div class="psgal_wrap">
			                <?php
			                	foreach($project_images as $project_image) {
									if(!empty($project_image['photo_url'])) {
										echo("<figure><span><img src='" . $project_image['photo_url'] . "' data-original-src='" . $project_image['photo_url'] . "' /></span></figure>");
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
