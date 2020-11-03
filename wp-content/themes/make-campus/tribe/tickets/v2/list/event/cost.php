<?php
/**
 * View: List Single Event Cost
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/list/event/cost.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since   4.10.9
 * @version 4.12.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 */

if ( empty( $event->cost ) ) {
	return;
}
?>
<div class="tribe-events-c-small-cta tribe-common-b3 tribe-events-calendar-list__event-cost">
	<?php if ( $event->tickets->exist() && $event->tickets->in_date_range() && ! $event->tickets->sold_out() ) : ?>
		<a
			href="<?php echo esc_url( $event->tickets->link->anchor ); ?>"
			class="tribe-events-c-small-cta__link tribe-common-cta tribe-common-cta--thin-alt btn universal-btn"
		>
			<?php echo esc_html( $event->tickets->link->label ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $event->tickets->sold_out() ) : ?>
		<span class="tribe-events-c-small-cta__sold-out tribe-common-b3--bold">
			<?php echo esc_html( $event->tickets->stock->sold_out ); ?>
		</span>
	<?php endif; ?>
	<?php if ( !$event->tickets->sold_out() ) : ?>
		<span class="tribe-events-c-small-cta__price">
			<?php echo esc_html( $event->cost ); ?>
		</span>
	<?php endif; ?>
	<?php if ( ! empty( $event->tickets->stock->available ) && $event->tickets->in_date_range() ) : ?>
		<span class="tribe-events-c-small-cta__stock">
			<?php echo esc_html( $event->tickets->stock->available ); ?>
		</span>
	<?php endif; ?>
</div>

<?php 
// Recurrence Info
$start_date = tribe_get_start_date(null, false);
$num_sessions = get_field("number_of_sessions", $event->ID);
$recurrence_info = get_field("recurrence_type", $event->ID);
$exclusion_txt = get_field("exclusion_text", $event->ID);
if ($num_sessions)) { ?>
	<div class="tribe-events-c-small-cta tribe-common-b3 tribe-events-calendar-list__event-cost">
		<?php echo $num_sessions; ?> <?php echo $recurrence_info; ?> sessions starting on <?php echo esc_html($start_date); ?>
		<?php if ( $exclusion_txt && $exclusion_txt != '' ) ) { echo " " . $exclusion_txt; } ?>								  
	</div>	
<?php } ?>
