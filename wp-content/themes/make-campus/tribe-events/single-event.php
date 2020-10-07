<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = get_the_ID();

// For some reason, images are no longer associated with post to get pulled into the gallery shortcode automatically, so manually build an array
$post_image_ids = array();
array_push($post_image_ids, get_post_thumbnail_id());
for ($x = 1; $x < 6; $x++) {
	if(get_field("image_" . $x)) {
		array_push($post_image_ids, get_field("image_" . $x)["ID"]);
	}
}  
$post_image_ids = implode(', ', $post_image_ids); 
?>

<div id="tribe-events-content" class="tribe-events-single">

	<div class="tribe-events-header tribe-clearfix">
		<?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>
		<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
		<?php if ( tribe_get_cost() ) : ?>
			<span class="tribe-events-cost">&nbsp;-&nbsp;<?php echo("$" . number_format(tribe_get_cost(null, false)) ); ?></span>
		<?php endif; ?>
	</div>
 	<div class="tribe-events-image-gallery">
		<?php 

		?>
		<?php echo do_shortcode('[gallery ids="'.$post_image_ids.'" size="large" order="DESC" orderby="ID"]'); ?>
		<a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal .msnry_item:first-of-type a').click())">View All Images</a>
	</div>
	
	<!-- Notices -->
	<?php tribe_the_notices() ?>

	<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> class="container">
			<div class="row">
				<div class="col-md-8 col-sm-12 col-xs-12 event-info">
					<!-- Event content -->
					<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
					<div class="event-cat">
						<?php echo tribe_get_event_categories($event_id); ?>
					</div>
					<?php if( tribe_get_event_categories($event_id) == "In-Person" && get_field('location') )  { ?>
						<div class="tribe-events-single-skill-level tribe-events-content">
							<h3>Location:</h3> 
							<?php echo get_field('location') ?>
						</div>
					<?php } ?>
					<div class="event-author">
						<h3>About the Author:</h3> 
						<?php echo get_field('about'); ?>
					</div>
					<div class="tribe-events-single-event-description tribe-events-content">
						<h3>What You'll Do:</h3> 
						<?php the_content(); ?>
					</div>
					<?php if(get_field('promo_videos') && get_field('promo_videos')[0]['video'] != '') { ?>
						<div class="tribe-events-promo-vidos tribe-events-content">
							<h3>Promo Videos: </h3>
							    <ul>
								<?php 
									foreach(get_field('promo_videos') as $video) {
										echo '<li><a href="' . $video['video'] . '">' . $video['video'] . '</a></li>';
									}
								?>
								</ul>
						</div>
					<?php } ?>
					<?php if(get_field('basic_skills')) { ?>
						<div class="tribe-events-single-skill-level tribe-events-content">
							<h3>Skill Level for this program:</h3> 
							<?php echo get_field('basic_skills') ?>
						</div>
					<?php } ?>
					<?php if(get_field('skills_taught')) { ?>
						<div class="tribe-events-single-skills-taugh tribe-events-content">
							<h3>Skills you will learn in this program:</h3> 
							<?php echo get_field('skills_taught') ?>
						</div>
					<?php } ?>
					<?php if(get_field('materials') && get_field('materials')[0]['item'] != '') { ?>
						<div class="tribe-events-single-event-materials tribe-events-content">
							<h3>What You'll Need:</h3> 
							<div class="materials-list">
								<ul>
								<?php 
									foreach(get_field('materials') as $material) {
										echo '<li>' . $material['item'] . '</li>';
									}
								?>
								</ul>
							</div>
							<?php if(get_field('wish_list_urls') && get_field('wish_list_urls')[0]['wish_list'] != '') { ?>
								<h3>Wishlist Links: </h3>
							    <ul>
								<?php 
									foreach(get_field('wish_list_urls') as $wishlist) {
										echo '<li><a href="' . $wishlist['wish_list'] . '">' . $wishlist['wish_list'] . '</a></li>';
									}
								?>
								</ul>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if(get_field('kit_required') == "Yes") { ?>
						<div class="tribe-events-single-skill-level tribe-events-content">
							<h3>A kit is required for this program:</h3> 
	 				<?php
								if(get_field('kit_price_included') == "yes") {
									echo " and is included in the ticket price";
									echo " and will be supplied by ";
									if(get_field("kit_supplier") == "other") {
										echo get_field("other_kit_supplier");
									} else {
										echo get_field("kit_supplier");
									}
								}
								if(get_field('kit_price_included') == "no") {
									echo "<p><a class='btn btn-blue-universal' href='" . get_field("kit_url") . "'>Get Kit Here</a>";
								} ?>
						</div>
					<?php } ?>
					<!-- .tribe-events-single-event-description -->
					<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
				</div>
				<div class="col-md-4 col-sm-12 col-xs-12 event-meta">
					<div class="event-meta-sticky">
						<!-- Event meta -->
						<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
						<?php tribe_get_template_part( 'modules/meta' ); ?>
						<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
					</div>
				</div>
			</div>
		</div> <!-- #post-x -->
		<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
	<?php endwhile; ?>

	<!-- Event footer -->
	<div id="tribe-events-footer">
		<!-- Navigation -->
		<nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
			<ul class="tribe-events-sub-nav">
				<li class="tribe-events-nav-previous universal-btn"><?php echo tribe_the_prev_event_link( '<i class="fas fa-angle-double-left"></i> %title%' ) ?></li>
				<li class="tribe-events-nav-next universal-btn"><?php echo tribe_the_next_event_link( '%title% <i class="fas fa-angle-double-right"></i>' ) ?></li>
			</ul>
			<!-- .tribe-events-sub-nav -->
		</nav>
	</div>
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->

