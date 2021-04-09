<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package BuddyBoss_Theme
 */

global $post;
$person = EEH_People_View::get_person();

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="host-wrapper">
				<div class="host-photo">
					<?php get_the_post_thumbnail(); ?>	
				</div>
				
				<div class="host-meta">
					<h1 class="host-title"><?php echo get_the_title(); ?></h1>

					<div class="host-email">
						<i class="fas fa-link"></i>
						<a href="<?php echo get_field('website'); ?>" target="_blank"><?php echo get_field('website'); ?></a>
					</div>
					<?php 
						$social_links = get_field('social_links', $person->ID());
						if($social_links) { ?>
							<span class="social-links">
							<b>See more of <?php echo get_the_title(); ?> at:</b>
							<?php foreach ($social_links as $link) {
								if ($link['social_link'] != '') {
									echo '<a href="' . $link['social_link'] . ' target="_blank">*</a>';
								}
							} ?>
							</span>
						<?php }
					?>

					<div class="host-bio">
						<?php
							$content = get_field('facilitator_info');
							$postwithbreaks = wpautop( $content, true );
							echo $postwithbreaks;
						?>	
					</div>
				</div>

			</div>

			<div class='host-events'>
				<?php
					$events = EEH_People_View::get_events_for_person();
					?>
					<div class="eea-people-addon-person-events-container">
						<?php if ( $events ) : ?>
						<h3>Events <?php echo get_the_title(); ?> is involved with:</h3>
						<?php foreach ( $events as $type => $event ) : ?>
							<div class="eea-people-addon-people-type-container">
								<h4 class="eea-people-addon-people-type-label"><?php echo $type; ?></h4>
								<ul class="eea-people-addon-event-list-ul">
									<?php foreach ( $event as $evt ) : ?>
										<li>
											<a class="eea-people-addon-link-to-event" href="<?php echo get_permalink( $evt->ID() ); ?>" title="<?php printf( __('Click here to view more info about %s', 'event_espresso' ), $evt->name() ); ?>"><span class="eea-people-addon-event-name"><?php echo $evt->name(); ?></span></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->


<?php
get_footer();
