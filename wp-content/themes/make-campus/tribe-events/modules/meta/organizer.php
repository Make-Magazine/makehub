<?php
/**
 * Single Event Meta (Organizer) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta/organizer.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 */

$organizer_ids = tribe_get_organizer_ids();
$multiple = count( $organizer_ids ) > 1;

$name = tribe_get_organizer();
$phone = tribe_get_organizer_phone();
$email = tribe_get_organizer_email();
$website = tribe_get_organizer_website_link();

?>

<div class="tribe-events-meta-group tribe-events-meta-group-organizer">
	<?php // <h2 class="tribe-events-single-section-title"><?php echo tribe_get_organizer_label( ! $multiple ); end </h2> ?>
	<dl>
		<?php
		do_action( 'tribe_events_single_meta_organizer_section_start' );

		foreach ( $organizer_ids as $organizer ) {
			if ( ! $organizer ) {
				continue;
			}

			?>
			<dt style="display:none;"><?php // This element is just to make sure we have a valid HTML ?></dt>
			<dd class="tribe-organizer">
				<?php if ( has_post_thumbnail( $organizer ) ) { ?>
					<div class="tribe-organizer-photo">
						<?php echo get_the_post_thumbnail( $organizer ); ?>
					</div>
				<?php } ?>
				<?php echo $name ?>
			</dd>
			<?php
		}
		if(get_field('social_links', $organizer)) { ?>
		    <br />
		    <b>Follow them at: </b>
			<div class="social-links">
				<?php foreach(get_field('social_links', $organizer) as $link) { ?>
				<a href="<?php echo($link['social_link']); ?>">*</a>
				<?php } ?>
			</div>
		<?php } 

		if ( ! $multiple ) { // only show organizer details if there is one
			if ( ! empty( $phone ) ) {
				?>
	        	<br />
				<dt class="tribe-organizer-tel-label">
					<?php esc_html_e( 'Phone:', 'the-events-calendar' ) ?>
				</dt>
				<dd class="tribe-organizer-tel">
					<?php echo esc_html( $phone ); ?>
				</dd>
				<?php
			}//end if

			if ( ! empty( $website ) ) {
				?>
				<br />
				<dt class="tribe-organizer-url-label">
					<b><?php esc_html_e( 'Website:', 'the-events-calendar' ) ?></b>
				</dt>
				<dd class="tribe-organizer-url">
					<?php echo $website; ?>
				</dd>
				<?php
			}//end if
		}//end if

		do_action( 'tribe_events_single_meta_organizer_section_end' );
		?>
	</dl>
</div>
