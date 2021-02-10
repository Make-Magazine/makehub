<?php
/**
 * Make - Base Theme
 *
 * Onboarding config to load plugins and homepage content on theme activation.
 *
 * @package Make - Base
 * @author  Make Community
 * @license GPL-2.0-or-later
 * @link    https://make.co/
 */

return array(
	'dependencies' => array(
		'plugins' => array(
			array(
				'name'        => __( 'Atomic Blocks', 'make-base' ),
				'slug'        => 'atomic-blocks/atomicblocks.php',
			),
		),
	),
	'content' => array(
		'homepage' => array(
			'post_title'     => 'Homepage',
			'post_name'      => 'homepage-gutenberg',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/homepage.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'template-blocks.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		),
	),
);
