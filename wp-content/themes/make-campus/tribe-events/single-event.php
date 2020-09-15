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
$featured_image = tribe_event_featured_image( $event_id, 'full', false, false );
?>

<div id="tribe-events-content" class="tribe-events-single">

	<div class="tribe-events-header tribe-clearfix">
		<?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>
		<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
		<?php if ( tribe_get_cost() ) : ?>
			<span class="tribe-events-cost">&nbsp;-&nbsp;<?php echo tribe_get_cost( null, true ) ?></span>
		<?php endif; ?>
		<p class="tribe-events-back">
			<a href="<?php echo esc_url( tribe_get_events_link() ); ?>" class="btn universal-btn-reversed"> <?php printf( '&laquo; ' . esc_html_x( 'All %s', '%s Events plural label', 'the-events-calendar' ), $events_label_plural ); ?></a>
		</p>
	</div>

 	<div class="tribe-events-image-gallery">
		<?php echo do_shortcode('[gallery id="'.$event_id.'"]'); ?>
		<a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal .msnry_item:first-of-type a').click())">Show All Images</a>
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
					<div class="event-author">
						<h3>About the Author:</h3> 
						<?php echo get_field('about'); ?>
					</div>
					<div class="tribe-events-single-event-description tribe-events-content">
						<h3>What You'll Do:</h3> 
						<?php the_content(); ?>
					</div>
					<div class="tribe-events-single-event-description tribe-events-content">
						<h3>What You'll Need:</h3> 
						<?php echo get_field('materials'); ?>
					</div>

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

