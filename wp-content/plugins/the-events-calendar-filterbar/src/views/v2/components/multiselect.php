<?php
/**
 * View: Multiselect Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2/components/multiselect.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @var string  $value   Value for the multiselect.
 * @var string  $id      ID of the multiselect.
 * @var string  $name    Name attribute for the multiselect.
 * @var array   $options Options for the multiselect.
 *
 * @version TBD
 *
 */
?>
<div class="tribe-filter-bar-c-multiselect">
	<input
		class="tribe-filter-bar-c-multiselect__input"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="hidden"
		value="<?php echo esc_attr( $value ); ?>"
		data-allow-html
		data-dropdown-css-width="false"
		data-options="<?php echo esc_attr( wp_json_encode( $options ) ); ?>"
		data-attach-container
		multiple
		placeholder="<?php esc_attr_e( 'Search', 'tribe-events-filter-view' ); ?>"
		style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
	/>
</div>

<?php
/**
 * @todo: @paulmskim remove when properly implementing JS for multiselect.
 */
?>
<script>
	jQuery( document ).ready( function( $ ) {
		var $dropdowns = $( '.tribe-filter-bar-c-multiselect__input' );
		window.tribe_dropdowns.dropdown( $dropdowns, {
			templateSelection: function( el ) {
				var $newEl = $( '<span class="select2-selection__choice__text"></span>' );
				$newEl.text( el.text );
				return $newEl;
			},
		} );
	} );
</script>
