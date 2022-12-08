<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

GFForms::include_addon_framework();

class GV_Entry_Revisions_Settings extends GFAddOn {

	/**
	 * @var string Not used.
	 */
	protected $_min_gravityforms_version = '2.3.3.9';

	/**
	 * @var string Not used.
	 */
	protected $_slug = 'gk-gravityrevisions';

	/**
	 * @var string Not used.
	 */
	protected $_path = 'gk-gravityrevisions/gk-gravityrevisions.php';

	/**
	 * @var string Not used.
	 */
	protected $_full_path = __FILE__;

	/**
	 * @var string Not used.
	 */
	protected $_title = 'GravityRevisions';

	/**
	 * @var string Not used.
	 */
	protected $_short_title = 'GravityRevisions';

	private static $_instance = null;

	/** @var string The setting name for default Inline Edit support. */
	const INLINE_EDIT_GLOBAL_SETTING = 'inline_edit_create_revisions';

	/** @var string The setting name for per-form Inline Edit support. */
	const INLINE_EDIT_FORM_SETTING = 'inline_edit_create_revisions_per_form';

	public function __construct() {

		if ( self::$_instance ) {
			return self::$_instance;
		}

		$this->_short_title = esc_html__( 'Entry Revisions', 'gk-gravityrevisions' );

		add_filter( 'gform_form_settings_fields', array( $this, 'add_form_settings_fields' ), 20 );

		parent::__construct();
	}

	/**
	 * Adds a per-form option to enable or disable Entry Revisions for Inline Edit changes.
	 *
	 * @since 1.2
	 *
	 * @param array $fields Form settings fields.
	 *
	 * @return array
	 */
	public function add_form_settings_fields( $fields ) {

		$setting = $this->get_plugin_setting(self::INLINE_EDIT_GLOBAL_SETTING );

		// If global settings haven't yet been configured, set to enabled.
		if ( is_null( $setting ) ) {
			$setting = '1';
		}

		// If Inline Edit isn't active, don't remove the form setting if it has been already set.
		if ( ! $this->is_gravityedit_activated() && ! is_null( $setting ) ) {

			$fields['form_options']['fields'][] = array(
				'name'          => self::INLINE_EDIT_FORM_SETTING,
				'type'          => 'hidden',
				'value' => $setting,
			);

			return $fields;
		}

		$fields['form_options']['fields'][] = array(
			'name'          => self::INLINE_EDIT_FORM_SETTING,
			'type'          => 'radio',
			'label'         => esc_html__( 'Inline Edit Behavior', 'gk-gravityrevisions' ),
			'description'   => '<p class="clear">' . esc_html__( 'Should edits made using the Inline Edit plugin create revisions?', 'gk-gravityrevisions' ) . '</p>',
			'default_value' => $setting,
			'dependency'    => array(
				'live'   => true,
				'fields' => array(
					array(
						'field'  => 'gv_inline_edit_enable',
						'values' => array( '1', true ),
					),
				),
			),
			'choices'       => array(
				array( 'label' => esc_html__( 'Add revisions for edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '1' ),
				array( 'label' => esc_html__( 'Ignore edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '0' ),
			),
		);

		return $fields;
	}

	/**
	 * Returns TRUE if the settings "Save" button was pressed
	 *
	 * @since 1.0.3 Fixes conflict with Import Entries plugin
	 *
	 * @return bool True: Settings form is being saved and the Entry Revisions setting is in the posted values (form settings)
	 */
	public function is_save_postback() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return ! rgempty( 'gform-settings-save' ) && ( isset( $_POST['gform_settings_save_nonce'] ) || isset( $_POST['_gravityview-entry-revisions_save_settings_nonce'] ) );
	}

	/**
	 * Get the one instance of the object
	 *
	 * @since 1.0
	 *
	 * @return GV_Entry_Revisions_Settings
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();

			GFAddOn::register( __CLASS__ );
		}

		return self::$_instance;
	}

	/**
	 * Define the plugin addon settings
	 *
	 * @since 1.0
	 *
	 * @return array Array that contains plugin settings
	 */
	public function plugin_settings_fields() {

		$fields = array();

		if ( $this->is_gravityedit_activated() ) {
			$fields[] = array(
				'name'          => self::INLINE_EDIT_GLOBAL_SETTING,
				'label'         => esc_html__( 'Default Inline Edit Behavior', 'gk-gravityrevisions' ),
				'description'   => '<p class="clear">' . esc_html__( 'Should edits made using the Inline Edit plugin create revisions?', 'gk-gravityrevisions' ) . ' ' . esc_html__( 'Note: This is the global default. You may override this setting in a form&rsquo;s Settings page.', 'gk-gravityrevisions' ) . '</p>',
				'type'          => 'radio',
				'default_value' => '1',
				'choices'       => array(
					array( 'label' => esc_html__( 'Add revisions for edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '1' ),
					array( 'label' => esc_html__( 'Ignore edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '0' ),
				),
			);
		}

		return array(
			array(
				'title'  => '',
				'fields' => $fields,
			)
		);
	}

	/**
	 * Returns whether the GravityEdit plugin is activated
	 *
	 * @since 1.2
	 *
	 * @return bool
	 */
	private function is_gravityedit_activated() {
		return defined( 'GRAVITYVIEW_INLINE_VERSION' ) || defined( 'GRAVITYEDIT_VERSION' );
	}

	/**
	 * Don't show the uninstall form
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function render_uninstall() {}

}
