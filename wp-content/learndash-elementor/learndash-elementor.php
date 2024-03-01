<?php
/**
 * Plugin Name: LearnDash LMS - Elementor
 * Plugin URI:  http://www.learndash.com
 * Description: LearnDash LMS official add-on to integrate LearnDash LMS with Elementor widgets and templates.
 * Version: 1.0.6
 * Author:      LearnDash
 * Author URI:  http://www.learndash.com
 * Text Domain: learndash-elementor
 * Domain Path: /languages/
 *
 * @package LearnDash\Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LEARNDASH_ELEMENTOR_VERSION', '1.0.6' );
define( 'LEARNDASH_ELEMENTOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEARNDASH_ELEMENTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LEARNDASH_ELEMENTOR_VIEWS_DIR', plugin_dir_path( __FILE__ ) . 'src/views/' );
define( 'LEARNDASH_ELEMENTOR_VIEWS_URL', plugin_dir_url( __FILE__ ) . 'src/views/' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

use LearnDash\Core\Autoloader;
use LearnDash\Elementor\Container;
use LearnDash\Elementor\App;
use LearnDash\Elementor\Provider;
use LearnDash\Elementor\Utilities\Dependency_Checker;

Dependency_Checker::get_instance()->set_dependencies(
	[
		'sfwd-lms/sfwd_lms.php'           => [
			'label'       => '<a href="https://learndash.com">LearnDash LMS</a>',
			'class'       => 'SFWD_LMS',
			'min_version' => '4.7.0',
		],
		'elementor/elementor.php'         => [
			'label'       => '<a href="https://elementor.com">Elementor</a>',
			'min_version' => '3.15.0',
		],
		'elementor-pro/elementor-pro.php' => [
			'label'       => '<a href="https://elementor.com">Elementor Pro</a>',
			'min_version' => '3.15.0',
		],
	]
);

Dependency_Checker::get_instance()->set_message(
	esc_html__( 'LearnDash LMS - Elementor add-on requires the following plugin(s) be active:', 'learndash-elementor' )
);

add_action(
	'plugins_loaded',
	function() {
		if (
			! Dependency_Checker::get_instance()->check_dependency_results()
			|| ! learndash_is_active_theme( 'ld30' )
		) {
			return;
		}

		learndash_elementor_extra_autoloading();

		App::set_container( new Container() );
		App::register( Provider::class );
	}
);

/**
 * Setup the autoloader for extra classes, which are not in the src/Elementor directory.
 *
 * @since 1.0.5
 *
 * @return void
 */
function learndash_elementor_extra_autoloading(): void {
	$autoloader = Autoloader::instance();

	foreach ( (array) glob( LEARNDASH_ELEMENTOR_PLUGIN_DIR . 'src/deprecated/*.php' ) as $file ) {
		$autoloader->register_class( basename( (string) $file, '.php' ), (string) $file );
	}

	$autoloader->register_autoloader();
}
