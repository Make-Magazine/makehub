<?php
/**
 * The customizer style additions for Filter Bar.
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 *
 * @since   5.0.0
 */

namespace Tribe\Events\Filterbar\Views\V2_1;

/**
 * Class Events Filterbar Views V2_1 Customizer
 *
 * @package Tribe\Events\Filterbar\Views\V2_1
 *
 * @since   5.0.0
 */
class Customizer {
	/**
	 * Adds Filter Bar styles to Customizer output.
	 *
	 * @since 5.0.0
	 *
	 * @param string $template The Customizer template.
	 *
	 * @return string $template The modified template.
	 */
	public function add_styles( $template ) {
		// This changes the Filter Icon underline color to match the global accent color.
		$template .= '
			.tribe-events .tribe-events-c-events-bar__filter-button:before {
				background-color: <%= global_elements.accent_color %>;
			}
		';

		return $template;
	}
}
