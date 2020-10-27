<?php
/**
 * Block: Tickets
 * Content Description
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/content-description.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since 4.9
 * @since 4.11.3 Updated the button to include a type - helps avoid submitting forms unintentionally.
 * @since 4.11.4 Added accessibility classes to screen reader text elements.
 *
 * @version 4.11.4
 */

$ticket = $this->get( 'ticket' );

if ( ! $ticket->show_description() || empty( $ticket->description ) ) {
	return false;
}

$modal  = $this->get( 'is_modal' );
$id = 'tribe__details__content' . ( true === $modal ) ?: '__modal';
$id .= '--' . $ticket->ID;
?>
<div id="<?php echo esc_attr( $id ); ?>" class="tribe-common-b2 tribe-common-b3--min-medium tribe-tickets__item__details__content">
	<?php echo $ticket->description; ?>
</div>
