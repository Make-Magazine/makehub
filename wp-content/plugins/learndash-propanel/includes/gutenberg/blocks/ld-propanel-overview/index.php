<?php
/**
 * Handles all server side logic for the ld-login Gutenberg Block. This block is functionally the same
 * as the learndash_login shortcode used within LearnDash.
 *
 * @package ProPanel
 * @since 2.2.0
 */

if ( ( class_exists( 'LearnDash_ProPanel_Gutenberg_Block' ) ) && ( ! class_exists( 'LearnDash_ProPanel_Gutenberg_Block_Overview' ) ) ) {
	/**
	 * Class for handling LearnDash Login Block
	 */
	class LearnDash_ProPanel_Gutenberg_Block_Overview extends LearnDash_ProPanel_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug   = 'ld_propanel';
			$this->shortcode_widget = 'overview';
			$this->block_slug       = 'ld-propanel-overview';
			$this->block_attributes = array(
				'preview_show' => array(
					'type' => 'boolean',
				),
			);
			$this->self_closing = true;

			$this->init();
		}

		/** This function is documented in includes/gutenberg/lib/class-learndash-propanel-gutenberg-block.php */
		protected function process_block_attributes( $block_attributes = array() ) {
			if ( isset( $block_attributes['preview_show'] ) ) {
				unset( $block_attributes['preview_show'] );
			}

			return $block_attributes;
		}

		// End of functions.
	}
}
new LearnDash_ProPanel_Gutenberg_Block_Overview();
