<?php
/**
 * View: Pill Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/components/pill.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @var string $label      Label for the pill.
 * @var string $selections Selections of the filter labeled by pill.
 *
 * @version TBD
 *
 */
$classes = [ 'tribe-filter-bar-c-pill', 'tribe-common-b2', 'tribe-common-b3--min-medium' ];

if ( ! empty( $selections ) ) {
	$classes[] = 'tribe-filter-bar-c-pill--has-selections';
}
?>
<div <?php tribe_classes( $classes ); ?>>
	<div class="tribe-filter-bar-c-pill__pill">
		<span class="tribe-filter-bar-c-pill__pill-label"><?php echo esc_html( $label ); ?></span><span class="tribe-filter-bar-c-pill__pill-label-colon">:</span>
		<span class="tribe-filter-bar-c-pill__pill-selections">
			<?php echo esc_html( $selections ); ?>
		</span>
	</div>
	<button class="tribe-filter-bar-c-pill__remove-button">
		<?php $this->template( 'components/icons/close-alt', [ 'classes' => [ 'tribe-filter-bar-c-pill__remove-button-icon' ] ] ); ?>
		<span class="tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Remove filters', 'tribe-events-filter-view' ); ?>
		</span>
	</button>
</div>
