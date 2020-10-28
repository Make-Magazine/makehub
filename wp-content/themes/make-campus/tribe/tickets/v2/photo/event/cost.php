<?php
/**
 * View: Photo View - Single Event Cost
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/photo/event/cost.php
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
 */

if ( empty( $event->cost ) ) {
	return;
}
?>
<div class="tribe-events-pro-photo__event-cost">
	<?php if ( $event->tickets->exist() && $event->tickets->in_date_range() && ! $event->tickets->sold_out() ) : ?>
		<a
			href="<?php echo esc_url( $event->tickets->link->anchor ); ?>"
			class="btn universal-btn"
		>
			<?php echo esc_html( $event->tickets->link->label ); ?>
		</a>
	    <br />
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
	<?php if ( get_field("number_of_sessions") ) { ?>
		    <span class="tribe-events-series-count"> (series of <?php echo get_field("number_of_sessions"); ?>)</span>
	<?php } ?>
	<?php if ( ! empty( $event->tickets->stock->available ) && $event->tickets->in_date_range() ) : ?>
		<span class="tribe-events-c-small-cta__stock">
			<?php echo esc_html( $event->tickets->stock->available ); ?>
		</span>
	<?php endif; ?>
</div>