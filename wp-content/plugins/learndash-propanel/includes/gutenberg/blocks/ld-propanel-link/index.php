<?php
/**
 * Handles all server side logic for the ld-login Gutenberg Block. This block is functionally the same
 * as the learndash_login shortcode used within LearnDash.
 *
 * @package ProPanel
 * @since 2.2.0
 */

if ( class_exists( 'LearnDash_ProPanel_Gutenberg_Block' ) && ! class_exists( 'LearnDash_ProPanel_Gutenberg_Block_Link' ) ) {

	/**
	 * Class for handling LearnDash Login Block
	 *
	 * @since 2.2.0
	 */
	class LearnDash_ProPanel_Gutenberg_Block_Link extends LearnDash_ProPanel_Gutenberg_Block {

		/**
		 * Object constructor
		 *
		 * @since 2.2.0
		 */
		public function __construct() {
			$this->shortcode_slug   = 'ld_propanel';
			$this->shortcode_widget = 'link';
			$this->block_slug       = 'ld-propanel-link';
			$this->block_attributes = array(
				'content' => array(
					'type' => 'link',
					'default' => __( 'Show ProPanel Full Page', 'ld_propanel' )
				),
			);
			$this->self_closing = false;

			$this->init();
		}

		/**
		 * Process the block attributes before render.
		 *
		 * @param array $block_attributes Array of block attrbutes.
		 *
		 * @since  2.2.0
		 * @return array $block_attributes
		 */
		protected function process_block_attributes( $block_attributes = array() ) {
			if ( isset( $block_attributes['content'] ) ) {
				unset( $block_attributes['content'] );
			}

			return $block_attributes;
		}
	}
}
new LearnDash_ProPanel_Gutenberg_Block_Link();
