<?php
/**
 * Adds a link back to the event, at the start of the order item meta section (which
 * displays in various locations).
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/community-tickets/modules/email-item-event-details.php
 *
 * @version 4.6.3
 */
?>
<div class="event-title" style="color:#999;font-size:90%;">
	<a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>"><?php echo esc_html( $title ); ?></a>
</div>
