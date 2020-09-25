<?php
/**
 * View: Filter Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/components/filter.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @var string $style        Style of filter, can be `pill` or `accordion`.
 * @var bool   $is_open      Whether the filter is open or not.
 * @var string $toggle_id    Unique ID for the toggle.
 * @var string $container_id Unique ID for the container.
 * @var string $label        Label for the filter toggle.
 * @var string $selections   Selections for the filter toggle.
 * @var array  $filters      Array of filter data.
 *
 * @version TBD
 *
 */

// Return early if style is not set or is not `pill` or `accordion`.
if ( ! isset( $style ) || ! in_array( $style, [ 'pill', 'accordion' ] ) ) {
	return;
}

$is_pill_style      = 'pill' === $style;
$is_accordion_style = 'accordion' === $style;

$classes                   = [ 'tribe-filter-bar-c-filter' ];
$toggle_wrapper_classes    = [ 'tribe-filter-bar-c-filter__toggle-wrapper' ];
$toggle_classes            = [ 'tribe-filter-bar-c-filter__toggle' ];
$toggle_label_classes      = [ 'tribe-filter-bar-c-filter__toggle-label' ];
$toggle_selections_classes = [ 'tribe-filter-bar-c-filter__toggle-selections' ];
$legend_classes            = [ 'tribe-filter-bar-c-filter__filters-legend' ];

if ( ! empty( $is_open ) ) {
	$classes[] = 'tribe-filter-bar-c-filter--open';
}

if ( $is_pill_style ) {
	$classes[]                   = 'tribe-filter-bar-c-filter--pill';
	$toggle_wrapper_classes[]    = 'tribe-filter-bar-c-pill tribe-filter-bar-c-pill--button tribe-common-b2 tribe-common-b3--min-medium';
	$toggle_classes[]            = 'tribe-filter-bar-c-pill__pill';
	$toggle_label_classes[]      = 'tribe-filter-bar-c-pill__pill-label';
	$toggle_selections_classes[] = 'tribe-filter-bar-c-pill__pill-selections';
	$legend_classes[]            = 'tribe-common-h6 tribe-common-h--alt';

	if ( ! empty( $selections ) ) {
		$classes[]                = 'tribe-filter-bar-c-filter--has-selections';
		$toggle_wrapper_classes[] = 'tribe-filter-bar-c-pill--has-selections';
	}
} elseif ( $is_accordion_style ) {
	$classes[]        = 'tribe-filter-bar-c-filter--accordion';
	$toggle_classes[] = 'tribe-common-h7 tribe-common-h--alt';
	$legend_classes[] = 'tribe-common-a11y-visual-hide';

	if ( ! empty( $selections ) ) {
		$classes[] = 'tribe-filter-bar-c-filter--has-selections';
	}
}
?>
<div <?php tribe_classes( $classes ); ?>>
	<div <?php tribe_classes( $toggle_wrapper_classes ); ?>>
		<button
			<?php tribe_classes( $toggle_classes ); ?>
			id="<?php echo esc_attr( $toggle_id ); ?>"
			aria-controls="<?php echo esc_attr( $container_id ); ?>"
			aria-expanded="<?php echo esc_attr( $is_open ? 'true' : 'false' ); ?>"
			data-js="tribe-events-accordion-trigger"
		>
			<span <?php tribe_classes( $toggle_label_classes ); ?>><?php echo esc_html( $label ); ?></span><span class="tribe-filter-bar-c-pill__pill-label-colon">:</span>
			<span <?php tribe_classes( $toggle_selections_classes ); ?>>
				<?php echo esc_html( $selections ); ?>
			</span>

			<?php if ( $is_accordion_style ) : ?>
				<span class="tribe-filter-bar-c-filter__toggle-icon tribe-filter-bar-c-filter__toggle-icon--plus">
					<?php $this->template( 'components/icons/plus', [ 'classes' => [ 'tribe-filter-bar-c-filter__toggle-plus-icon' ] ] ); ?>
					<span class="tribe-common-a11y-visual-hide">
						<?php esc_html_e( 'Open filter', 'tribe-events-filter-view' ); ?>
					</span>
				</span>

				<span class="tribe-filter-bar-c-filter__toggle-icon tribe-filter-bar-c-filter__toggle-icon--minus">
					<?php $this->template( 'components/icons/minus', [ 'classes' => [ 'tribe-filter-bar-c-filter__toggle-minus-icon' ] ] ); ?>
					<span class="tribe-common-a11y-visual-hide">
						<?php esc_html_e( 'Close filter', 'tribe-events-filter-view' ); ?>
					</span>
				</span>
			<?php endif; ?>
		</button>

		<?php if ( $is_pill_style ) : ?>
			<button class="tribe-filter-bar-c-pill__remove-button">
				<?php $this->template( 'components/icons/close-alt', [ 'classes' => [ 'tribe-filter-bar-c-pill__remove-button-icon' ] ] ); ?>
				<span class="tribe-common-a11y-visual-hide">
					<?php esc_html_e( 'Remove filters', 'tribe-events-filter-view' ); ?>
				</span>
			</button>
		<?php endif; ?>
	</div>

	<div
		class="tribe-filter-bar-c-filter__container"
		id="<?php echo esc_attr( $container_id ); ?>"
		aria-hidden="<?php echo esc_attr( $is_open ? 'false' : 'true' ); ?>"
		aria-labelledby="<?php echo esc_attr( $toggle_id ); ?>"
	>
		<fieldset class="tribe-filter-bar-c-filter__filters-fieldset">
			<legend <?php tribe_classes( $legend_classes ); ?>>
				<?php echo esc_html( $label ); ?>
			</legend>

			<button class="tribe-filter-bar-c-filter__filters-close">
				<?php $this->template( 'components/icons/close', [ 'classes' => [ 'tribe-filter-bar-c-filter__filters-close-icon' ] ] ); ?>
				<span class="tribe-common-a11y-visual-hide">
					<?php esc_html_e( 'Close filter', 'tribe-events-filter-view' ); ?>
				</span>
			</button>

			<div class="tribe-filter-bar-c-filter__filters">
				<?php
				foreach ( $filters as $filter ) {
					$this->template( 'components/filter-type', [ 'data' => $filter ] );
				}
				?>
			</div>
		</fieldset>
	</div>
</div>
