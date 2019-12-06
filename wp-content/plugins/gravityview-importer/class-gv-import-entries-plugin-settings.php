<?php
namespace GV\Import_Entries;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

\GFForms::include_feed_addon_framework();

class GV_Import_Entries_Addon extends \GFAddOn {

	/**
	 * @var string Version number of the Add-On
	 */
	protected $_version = GV_IMPORT_ENTRIES_VERSION;
	/**
	 * @var string Gravity Forms minimum version requirement
	 */
	protected $_min_gravityforms_version = GV_IMPORT_ENTRIES_MIN_GF;
	/**
	 * @var string URL-friendly identifier used for form settings, add-on settings, text domain localization...
	 */
	protected $_slug = 'gravityview-importer';
	/**
	 * @var string Relative path to the plugin from the plugins folder. Example "gravityforms/gravityforms.php"
	 */
	protected $_path = 'gravityview-importer/gravityview-importer.php';
	/**
	 * @var string Full path the the plugin. Example: __FILE__
	 */
	protected $_full_path = GV_IMPORT_ENTRIES_FILE;
	/**
	 * @var string URL to the Gravity Forms website. Example: 'http://www.gravityforms.com' OR affiliate link.
	 */
	protected $_url = 'https://gravityview.co';
	/**
	 * @var string Title of the plugin to be used on the settings page, form settings and plugins page. Example: 'Gravity Forms MailChimp Add-On'
	 */
	protected $_title = 'GravityView Import Entries';
	/**
	 * @var string Short version of the plugin title to be used on menus and other places where a less verbose string is useful. Example: 'MailChimp'
	 */
	protected $_short_title = 'Import Entries';
	/**
	 * @var array Members plugin integration. List of capabilities to add to roles.
	 */
	protected $_capabilities = array( 'manage_options', 'gravityforms_import_entries' );
	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the settings page
	 */
	protected $_capabilities_settings_page = array( 'manage_options' );
	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the form settings page
	 */
	protected $_capabilities_form_settings = array( 'manage_options', 'gravityforms_import_entries' );

	/**
	 * @var string The hook suffix for the app menu
	 */
	public $app_hook_suffix = 'gv_import';

	/**
	 * @var bool
	 */
	public $show_settings = true;

	/**
	 * @var int
	 */
	protected $form_id = 0;

	/**
	 * @var GravityView_Import_License
	 */
	public $license;

	/**
	 * @var GV_Import_Entries_Addon
	 */
	private static $instance;

	/**
	 * @return GV_Import_Entries_Addon
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Before running anything else, require files
	 */
	function pre_init() {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && ! DOING_AJAX ) ) {
			return;
		}

		/** @define "$file_path" "./" */
		$file_path = trailingslashit( $this->get_base_path() );

		$this->set_license_handler();
	}

	/**
	 * Set the license handler
	 */
	function set_license_handler() {

		// If importing or license handler is already set, get outta here
		if ( ! empty( $this->license ) ) {
			return;
		}

		if ( ! class_exists( '\GV\Import_Entries\GravityView_Import_License' ) ) {

			/** @define "$file_path" "./" */
			$file_path = trailingslashit( $this->get_base_path() );

			require_once $file_path . 'class-gv-import-entries-license.php';
		}

		$this->license = GravityView_Import_License::get_instance( $this );
	}

	/**
	 * Replace the gear icon with a Floaty head
	 *
	 * @return string
	 */
	function plugin_settings_icon() {
		return '<a class="gvi-astronaut-head-icon" href="https://gravityview.co/extensions/gravity-forms-entry-importer/">GravityView</a>';
	}

	/**
	 * Get the current version #
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->_version;
	}

	/**
	 * @return string
	 */
	public function get_full_path() {
		return $this->_full_path;
	}

	/**
	 * Render plugin settings field
	 *
	 * @return array
	 */
	function plugin_settings_fields() {

		$this->set_license_handler();

		return $this->license->plugin_settings_fields();
	}

	/**
	 * Update a single setting
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return boolean Whether the settings were updated or not
	 */
	public function update_plugin_setting( $key, $value ) {

		if ( ! is_string( $key ) ) {
			return false;
		}

		$settings         = parent::get_plugin_settings();
		$existing_setting = isset( $settings[ $key ] ) ? $settings[ $key ] : false;

		if ( $existing_setting !== $value ) {
			$settings[ $key ] = $value;
			parent::update_plugin_settings( $settings );

			return true;
		}

		return false;
	}

	/**
	 * Show the settings screen
	 *
	 * @param $sections
	 */
	function render_settings( $sections ) {

		/**
		 * @deprecated Renamed to `gravityview/import/settings/before'
		 */
		do_action( 'gravityview-import/before-settings' );

		do_action( 'gravityview/import/settings/before' );

		/**
		 * @deprecated Use `gravityview/import/settings/enabled`
		 */
		$show = apply_filters( 'gravityview-import/show-settings', $this->show_settings );

		/**
		 * @filter `gravityview/import/settings/enabled` Hide the settings for reasons unknown.
		 * @param boolean $show Show or not. Default: true.
		 */
		if ( apply_filters( 'gravityview/import/settings/enabled', $show ) ) {
			parent::render_settings( $sections );
		}

		/**
		 * @deprecated Renamed to `gravityview/import/settings/after'
		 */
		do_action( 'gravityview-import/after-settings' );

		do_action( 'gravityview/import/settings/after' );
	}

	/**
	 * Register the settings field for the EDD License field type
	 *
	 * @param array $field
	 * @param bool $echo Whether to echo the
	 *
	 * @return string
	 */
	protected function settings_edd_license( $field, $echo = true ) {

		$text = self::settings_text( $field, false );

		$activation = $this->license->settings_edd_license_activation( $field, false );

		$return = $text . $activation;

		if ( $echo ) {
			echo $return;
		}

		return $return;
	}

	/***
	 * Renders the save button for settings pages.
	 *
	 * Same as GFAddOn::settings_save(), but allows for overriding the button class.
	 *
	 * @inheritDoc
	 */
	public function settings_save( $field, $echo = true ) {

		$button = parent::settings_save( $field, false );

		// Replace the class
		if ( ! empty( $field['class'] ) ) {
			$button = str_replace( 'button-primary gfbutton', esc_attr( $field['class'] ), $button );
		}

		$button .= wp_nonce_field( sprintf( 'import_form_%d', $this->form_id ), '_wpnonce', true, false );

		if ( $echo ) {
			echo $button;
		}

		return $button;
	}
	
	/***
	 * Renders the save button for settings pages
	 *
	 * @param array $field - Field array containing the configuration options of this field
	 * @param bool $echo = true - true to echo the output to the screen, false to simply return the contents as a string
	 *
	 * @return string The HTML
	 */
	public function settings_submit( $field, $echo = true ) {

		$field['type'] = ( isset( $field['type'] ) && in_array( $field['type'], array(
				'submit',
				'reset'
			) ) ) ? $field['type'] : 'submit';

		$attributes    = $this->get_field_attributes( $field );
		$default_value = rgar( $field, 'value' ) ? rgar( $field, 'value' ) : rgar( $field, 'default_value' );
		$value         = $this->get_setting( $field['name'], $default_value );


		$attributes['class'] = isset( $field['class'] ) ? esc_attr( $field['class'] ) : $attributes['class'];
		$tooltip             = isset( $choice['tooltip'] ) ? gform_tooltip( $choice['tooltip'], rgar( $choice, 'tooltip_class' ), true ) : '';

		$html       = isset( $field['html_before'] ) ? $field['html_before'] : '';
		$html_after = isset( $field['html_after'] ) ? $field['html_after'] : '';

		if ( ! rgar( $field, 'value' ) ) {
			$field['value'] = esc_html__( 'Update Settings', 'gravityview-importer' );
		}

		$attributes = $this->get_field_attributes( $field );

		unset( $attributes['html_before'], $attributes['html_after'], $attributes['tooltip'] );

		$html .= '<input
                    type="' . $field['type'] . '"
                    name="' . esc_attr( $field['name'] ) . '"
                    value="' . $value . '" ' .
		         implode( ' ', $attributes ) .
		         ' />';

		$html .= $tooltip;
		$html .= $html_after;
		$html .= wp_nonce_field( sprintf( 'import_form_%d', $this->form_id ), '_wpnonce', true, false );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Register scripts
	 *
	 * @return array
	 */
	public function scripts() {

		$scripts = array();

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$scripts[] = array(
			"handle"  => 'gv_importer',
			"src"     => $this->get_base_url() . "/assets/js/admin{$script_debug}.js",
			"version" => $this->_version,
			'enqueue' => array(
				array(
					'admin_page' => array(
						'form_settings',
						'plugin_settings',
						'plugin_page',
						'app_settings',
					),
					'query' => 'subview=gravityview-importer',
				),
			),
			'strings' => array(
				'nonce'               => wp_create_nonce( 'gv-import-ajax' ),
				'complete'            => esc_html__( 'Complete', 'gravityview-importer' ),
				'cancel'              => esc_html__( 'Cancel', 'gravityview-importer' ),
				'updated'             => esc_html__( 'Updated', 'gravityview-importer' ),
				'column_header'       => esc_html__( '&hellip;will be added to this form field', 'gravityview-importer' ),
				'hide_console'        => esc_html__( 'Hide Console', 'gravityview-importer' ),
				'show_console'        => esc_html__( 'Show Console', 'gravityview-importer' ),
				'wrapping_up'         => esc_html__( 'Wrapping up&hellip;', 'gravityview-importer' ),
				'already_mapped'      => esc_html__( 'This field has already been mapped.', 'gravityview-importer' ),
				'overwrite_posts'     => esc_html__( 'Warning: Existing post content will be overwritten by the imported data. Proceed?', 'gravityview-importer' ),
				'overwrite_entry'     => esc_html__( 'Warning: Existing entry values will be overwritten by the imported data. Proceed?', 'gravityview-importer' ),
				'field_mapping_empty' => esc_html__( 'No fields have been mapped. Please configure the field mapping before starting the import.', 'gravityview-importer' ),
				'error_message'       => sprintf( esc_html__( 'There was an error on row %s.', 'gravityview-importer' ), '{row}' ),
				'success_message'     => sprintf( esc_html__( 'Created %s from Row %s', 'gravityview-importer' ), sprintf( esc_html__( 'Entry #%s', 'gravityview-importer' ), '{entry_id}' ), '{row}' ),
			)
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Register styles used by the plugin
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array();

		$styles[] = array(
			"handle"  => $this->_slug . '-admin',
			"src"     => $this->get_base_url() . "/assets/css/admin.css",
			"version" => $this->_version,
			'enqueue' => array(
				array(
					'admin_page' => array(
						'form_settings',
						'plugin_settings',
						'plugin_page',
						'app_settings',
					),
					'query' => 'subview=gravityview-importer',
				),
			),
		);

		/**
		 * Also enqueue on the Gravity Forms Import/Export page.
		 * Need to do this here because there's no `gf_export` check in Gravity Forms for the Import/Export page
		 * @see \GFAddon::_page_condition_matches
		 */
		$styles[] = array(
			"handle"  => $this->_slug . '-admin',
			"src"     => $this->get_base_url() . "/assets/css/admin.css",
			"version" => $this->_version,
			'enqueue' => array(
				array(
					'query' => 'page=gf_export&view=import_entries'
				),
			)
		);

		$styles[] = array(
			"handle"  => $this->_slug . '-admin-settings',
			"src"     => $this->get_base_url() . "/assets/css/admin-settings.css",
			"version" => $this->_version,
			'enqueue' => array(
				array(
					'admin_page' => array(
						'plugin_settings',
						'app_settings',
					),
				),
			)
		);

		return array_merge( parent::styles(), $styles );
	}

}

GV_Import_Entries_Addon::get_instance();