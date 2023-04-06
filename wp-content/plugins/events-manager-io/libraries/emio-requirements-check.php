<?php
/* Thanks Mark Jaquith - https://markjaquith.wordpress.com/2018/02/19/handling-old-wordpress-and-php-versions-in-your-plugin/ */
class EMIO_Requirements_Check {
	private $title;
	private $php;
	private $wp;
	private $file;
	private $emio;
	private $emio_api;
	
	/**
	 * EMIO_Requirements_Check constructor.
	 * @param string $title
	 * @param string $file
	 * @param string $php
	 * @param string $wp
	 * @param string $emio      Minimum EMIO version required to run, defaults to v1.0.
	 * @param string $emio_api  Minimum EMIO format API required which protects against breaking changes for add-ons, it checks the add-on meets a minimum version support vs EMIO
	 */
	public function __construct( $title = '', $file = '', $php = '5.2.4', $wp = '4.5', $emio = '1.0', $emio_api = '1.0' ) {
		$this->title = $title;
		$this->php = $php;
		$this->wp = $wp;
		$this->file = $file;
		$this->emio = $emio;
		$this->emio_api = $emio_api;
	}
	
	/**
	 * @param bool $deactivate Whether to deactivate or not upon failure
	 * @return bool
	 */
	public function passes( $deactivate = false ) {
		$passes = $this->php_passes() && $this->wp_passes();
		if ( ! $passes ) {
			add_action( 'admin_notices', array( $this, 'deactivate' ) );
		}
		// add some soft pass checks that don't require deactivation
		$passes_soft = $this->passes_soft();
		return $passes && $passes_soft;
	}
	
	public function passes_soft(){
		$return = true;
		if( version_compare( $this->emio, EMIO_VERSION, '>' ) ){
			$return = false;
			add_action( 'admin_notices', array( $this, 'emio_version_notice' ) );
		}
		if( version_compare( $this->emio_api, EMIO_API_VERSION, '>' ) ){
			$return = false;
			add_action( 'admin_notices', array( $this, 'emio_api_version_notice' ) );
		}
		return $return;
	}

	public function deactivate() {
		if ( isset( $this->file ) ) {
			deactivate_plugins( plugin_basename( $this->file ) );
		}
	}

	private function php_passes() {
		if ( $this->__php_at_least( $this->php ) ) {
			return true;
		} else {
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			return false;
		}
	}

	private static function __php_at_least( $min_version ) {
		return version_compare( phpversion(), $min_version, '>=' );
	}

	public function php_version_notice() {
		echo '<div class="error">';
		echo "<p>The &#8220;" . esc_html( $this->title ) . "&#8221; plugin cannot run on PHP versions older than " . $this->php . '. Please contact your host and ask them to upgrade.</p>';
		echo '</div>';
	}

	private function wp_passes() {
		if ( $this->__wp_at_least( $this->wp ) ) {
			return true;
		} else {
			add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );
			return false;
		}
	}

	private static function __wp_at_least( $min_version ) {
		return version_compare( get_bloginfo( 'version' ), $min_version, '>=' );
	}

	public function wp_version_notice() {
		echo '<div class="error">';
		echo "<p>The &#8220;" . esc_html( $this->title ) . "&#8221; plugin cannot run on WordPress versions older than " . $this->wp . '. Please update WordPress.</p>';
		echo '</div>';
	}
	
	public function emio_version_notice() {
		echo '<div class="error">';
		echo "<p>The &#8220;" . esc_html( $this->title ) . "&#8221; cannot run on Events Manager I/O versions older than " . $this->emio . '. Please upgrade Events Manager I/O to continue using this format.</p>';
		echo '</div>';
	}
	
	public function emio_api_version_notice() {
		echo '<div class="error">';
		echo "<p>The &#8220;" . esc_html( $this->title ) . "&#8221; is using an outdated API (v" . $this->emio_api . ') and is not compatible with your more recent vesion of Events Manager I/O. Please update &#8220;' . esc_html( $this->title ) . '&#8221; to continue using this format.</p>';
		echo '</div>';
	}
}