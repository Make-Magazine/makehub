<?php
/**
 * The Template for displaying the project post type.
 *
 * @package Maker Camp Theme
 */

global $post;

// Get our Taxonomies
$terms = get_the_terms($post->ID, 'ld_lesson_category');
$categories = array();
$ages = array();
$times = array();
$skill_levels = array();
$materials = array();
foreach($terms as $term) {
	$parent = get_term_top_most_parent( $term, 'ld_lesson_category');
	switch ($parent->slug) {
		case "content-category":
			array_push($categories, $term);
			break;
		case "age":
			array_push($ages, $term);
			break;
		case "time":
			array_push($times, $term);
			break;
		case "skill-level":
			array_push($skill_levels, $term);
			break;
		case "materials":
			array_push($materials, $term);
			break;
	}
}
$tagTerms = array_merge($categories, $ages, $times, $skill_levels);

// Get our ACF Fields
$hero_image = get_field('hero_image');
$steps = get_field('steps');
$sponsored_by_text = get_field('sponsored_by_text');
$what_will_you_learn = get_field('what_will_you_learn');
$whats_next = get_field('whats_next');
$svg_divider = get_field('svg_divider');

$referrer_url = parse_url($_SERVER['HTTP_REFERER']);
parse_str($referrer_url['query'], $referrer_params);
$referrer_params = explode(" ", $referrer_params['_sft_ld_lesson_category']);
error_log(print_r($referrer_params, TRUE));

get_header();

?>
<div id="learndash-content">
    <div class="bb-grid grid">
		<?php /* Sidebar emulating the LearnDash sidebar
		 <div class="lms-topic-sidebar-wrapper ">
			<div class="lms-topic-sidebar-data" style="max-height: calc(-76px + 100vh); top: 76px;">
				<div class="bb-elementor-header-items">
					<a href="#" id="bb-toggle-theme">
					<span class="sfwd-dark-mode" data-balloon-pos="down" data-balloon="Dark Mode"><i class="bb-icon-moon-circle"></i></span>
					<span class="sfwd-light-mode" data-balloon-pos="down" data-balloon="Light Mode"><i class="bb-icon-sun"></i></span>
					</a>
					<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="down" data-balloon="Maximize"><i class="bb-icon-maximize"></i></a>
					<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="down" data-balloon="Minimize"><i class="bb-icon-minimize"></i></a>
				</div>
				<div class="lms-topic-sidebar-course-navigation">
					<div class="ld-course-navigation">
						<a title="Arts &amp; Crafts" href="https://makercamp.make.co/adventures/arts-crafts/" class="course-entry-link">
							<span><i class="bb-icons bb-icon-chevron-left"></i>Back to Category </span>
						</a>
						<h2 class="course-entry-title">This should be the User Project Category</h2>
					</div>
				</div>
				<div class="lms-topic-sidebar-progress">
					<div class="course-progress-wrap"></div>
				</div>
				<div class="lms-lessions-list">
					<ol class="bb-lessons-list">
						<li class="lms-lesson-item current  lms-not-locked bb-lesson-item-no-topics">
							<a href="https://makercamp.make.co/projects/flea-circus-coin-magic/" title="Flea Circus Coin Magic" class="bb-lesson-head flex">
								<div class="flex-1 push-left bb-not-completed-item">
									<div class="bb-lesson-title">This should be a repeater of User Projects in the same Category</div>
								</div>
								<div class="flex align-items-center bb-check-not-completed">
									<span class="bb-progress-left"><span class="bb-progress-circle"></span></span>
									<span class="bb-progress-right"><span class="bb-progress-circle"></span></span>
								</div>
							</a>
						</li>
					</ol>
				</div>
				<div class="ld-sidebar-widgets">
					<ul>
						<li id="boss-follow-us-3" class="widget widget_follow_us"><h2 class="widgettitle">Share what you make with #MakerCamp</h2>
						<div class="bb-follow-links"><a href="https://www.facebook.com/familymakercamp" target="_blank"><i class="bb-icon-rounded-facebook"></i></a><a href="https://twitter.com/makercamp?lang=en" target="_blank"><i class="bb-icon-rounded-twitter"></i></a><a href="https://www.youtube.com/channel/UCDdMryhO2umklbFW8GZA3-w" target="_blank"><i class="bb-icon-youtube-logo"></i></a><a href="https://www.instagram.com/maker_camp/?hl=en" target="_blank"><i class="bb-icon-rounded-instagram"></i></a></div></li>
					</ul>
				</div>
			</div>
		</div> */ ?>
		<?php // Page Content emulating the LearnDash body ?>
        <div id="learndash-page-content" class="lesson-page">
            <div class="learndash-content-body">
				<div class="learndash-wrapper lds-focus-mode-content-widgets lds-columns-3 lds-template-grid-banner">
					<div class="project-breadcrumbs">
						<?php foreach($referrer_params as $param) {
								$breadCrumb = get_term_by('slug', $param, 'ld_lesson_category'); ?>
								<a href="/projects-search/?_sft_ld_lesson_category=<?php echo $breadCrumb->slug; ?>" class="project-tag"><?php echo $breadCrumb->name; ?></a>
						<?php } ?>
					</div>
					<div class="learndash_content_wrap">
						<div class="ld-tabs ld-tab-count-2">
							<div class="ld-tabs-navigation">
								<div class="ld-tab ld-active" data-ld-tab="ld-tab-content">
									<span class="ld-icon ld-icon-content"></span>
									<span class="ld-text">Project</span>
								</div>
								<div class="ld-tab " data-ld-tab="ld-tab-materials">
									<span class="ld-icon ld-icon-materials"></span>
									<span class="ld-text">Materials</span>
								</div>
							</div>
							<div class="ld-tabs-content">

								<?php // The First Tab 'Project' ?>

								<div class="ld-tab-content ld-visible" id="ld-tab-content">
									<img src="<?php echo get_resized_remote_image_url($hero_image['url'], 1900, 814); ?>" />
									<h1><?php the_title(); ?></h1>

									<?php if($sponsored_by_text) { ?>
										<div class="proj-sponsor-text">
											<?php echo $sponsored_by_text; ?>
										</div>
									<?php } ?>

									<div class="proj-divider">
										<?php echo $svg_divider; ?>
									</div>
									<div class="proj-taxonomy-filters">
										<a href="/projects-search/?_sft_ld_lesson_category=<?php echo $times[0]->slug; ?>" class="tax-time"><?php echo $times[0]->name; ?></a>
										<a href="/projects-search/?_sft_ld_lesson_category=<?php echo $skill_levels[0]->slug; ?>" class="tax-skill-level"><?php echo $skill_levels[0]->name; ?></a>
										<a href="/projects-search/?_sft_ld_lesson_category=<?php echo $ages[0]->slug; ?>" class="tax-age"><?php echo $ages[0]->name; ?></a>
									</div>


							        <section class="up-intro text-center">
							            <h2>WHAT WILL YOU LEARN?</h2>
							            <p><?php echo $what_will_you_learn; ?></p>
							        </section>

							        <section class="up-steps container">
							            <?php
							            if ($steps) {
							                $step_number = 1;
							                foreach ($steps as $step) {
							                    $image_1 = $step['image_1'];
							                    $image_2 = $step['image_2'];
							                    $title = $step['title'];
							                    $description = $step['description'];
							                    ?>
							                    <div class="row">
							                        <div class="col-xs-12 col-sm-6">

							                            <?php if (!empty($image_1)) { ?>
							                                <a class="up-step-img" href="<?php echo get_fitted_remote_image_url($image_1['url'], 1000, 1000); ?>">
							                                    <div style="background-image: url(<?php echo get_resized_remote_image_url($image_1['url'], 500, 500); ?>);"></div>
							                                </a>
							            				<?php } ?>

							                            <?php if (!empty($image_2)) { ?>
							                                <a class="up-step-img" href="<?php echo get_fitted_remote_image_url($image_2['url'], 1000, 1000); ?>">
							                                    <div style="background-image: url(<?php echo get_resized_remote_image_url($image_2['url'], 500, 500); ?>);"></div>
							                                </a>
							            				<?php } ?>

							                        </div>

							                        <div class="col-xs-12 col-sm-6">
							                            <h4>STEP <?php echo $step_number; ?></h4>
							                            <?php
							                            if (!empty($title)) {
							                                echo '<h5>' . $title . '</h5>';
							                            }
							                            ?>
							                            <?php
							                            if (!empty($description)) {
							                                echo '<div class="sp-step-desc">' . $description . '</div>';
							                            }
							                            ?>
							                        </div>
							                    </div>

							                    <?php
							                    $step_number++;
							                }
							            }
							            ?>
							        </section>

							        <?php
							        if ($whats_next) { ?>
							            <section class="up-whats-next">
											<h2>WHAT'S NEXT?</h2>
							                <p><?php echo $whats_next; ?></p>
							            </section>
							        <?php } ?>

								</div>

								<div class="ld-tab-content" id="ld-tab-materials">
									<p>Materials:</p>
									<ul>
										<?php foreach($materials as $material) { ?>
											<li><?php echo $material->name; ?>
										<?php } ?>
									</ul>
								</div>

							</div> <?php // end tabs content ?>

							<section class="tags">
								<?php foreach($tagTerms as $tag) { ?>
										<a href="/projects-search/?_sft_ld_lesson_category=<?php echo $tag->slug; ?>" class="project-tag"><?php echo $tag->name; ?></a>
								<?php } ?>
							</section>

							<section class="up-author">
								<?php
								// Author section
								$author_id=$post->post_author;
								learndash_get_template_part('template-course-author.php', array(
									'user_id' => $author_id
										), true);
								?>
							</section>

							<section class="up-print">
								<a class="universal-btn btn" href="/print-project/?project=<?php echo $post->ID; ?>" target="_blank">PRINT THESE INSTRUCTIONS</a>
							</section>

							<section class="up-buttons text-center">
								<a class="mc-blue-arrow-btn" href="/projects"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>BROWSE MORE MAKER CAMP PROJECTS</a>
								<a class="mc-blue-arrow-btn" href="http://makezine.com/projects/" target="_blank"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>FIND EVEN MORE PROJECTS AT Make:</a>
							</section>

							<section class="up-disclaimer">
								<p><strong>Please Note</strong></p>
								<p>Your safety is your own responsibility, including proper use of equipment and safety gear, and determining whether you have adequate skill and experience. Power tools, electricity, and other resources used for these projects are dangerous, unless used properly and with adequate precautions, including safety gear and adult supervision. Some illustrative photos do not depict safety precautions or equipment, in order to show the project steps more clearly. Use of the instructions and suggestions found in Maker Camp is at your own risk. Make Community, LLC, disclaims all responsibility for any resulting damage, injury, or expense.</p>
							</section>

							<section class="up-colab-share">
								<h3>ALL DONE? SHARE IT!</h3>
								<p>Share pictures and videos of your cool build! Be sure to use #maketogether or #makercamp</p>
								<a class="mc-blue-arrow-btn" href="/"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i>POST YOUR PROJECTS</a>
							</section>

							<script type="text/javascript">
								jQuery(document).ready(function () {
									jQuery(".up-step-img").fancybox();
								});
							</script>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
?>
