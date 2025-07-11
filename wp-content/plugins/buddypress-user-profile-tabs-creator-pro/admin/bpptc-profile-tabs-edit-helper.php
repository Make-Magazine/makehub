<?php
/**
 * Plugin admin file add new meta boxes to our post type
 *
 * @package buddypress-user-profile-tabs-creator-pro
 */

/**
 * If file access directly if will exit
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BPPTC_Profile_Tabs_Pro_Admin
 */
class BPPTC_Profile_Tabs_Admin {
	/**
	 * BPPTC_Profile_Tabs_Pro_Admin constructor.
	 */
	public function __construct() {
		$this->add_meta_box();
		add_action( 'bpptc_post_type_admin_enqueue_scripts', array( $this, 'load_assets' ) );
		// GD fixes.
		add_action( 'media_buttons', array( $this, 'fix_media_buttons_for_ayecode' ), 1 );
		add_action( 'bpptc_post_type_admin_enqueue_scripts', array( $this, 'media_button_fixes' ) );
		add_action( 'admin_footer', array( $this, 'fix_uploader_for_wp6_3' ), 20 );
	}

	/**
	 * Add meta boxes
	 */
	public function add_meta_box() {
		add_action( 'cmb2_admin_init', array( $this, 'render_meta_box' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_details' ) );
	}

	/**
	 * Render format meta box
	 */
	public function render_meta_box() {

		$prefix = '_bpptc_';

		$roles = wp_roles()->roles;

		$user_roles         = array();
		$user_roles['all']  = __( 'All Members', 'buddypress-user-profile-tabs-creator-pro' );
		$user_roles['none'] = __( 'None(Will remove it for everyone. Only applies for existing tabs)', 'buddypress-user-profile-tabs-creator-pro' );

		foreach ( $roles as $role => $detail ) {
			$user_roles[ $role ] = $detail['name'];
		}

		$who_can_view_roles = $user_roles;
		unset( $who_can_view_roles['all'] );


		$extra_visible_options = array(
			'do_not_modify' => __( 'Do Not Modify(only applies when modifying existing tab or subnav)', 'buddypress-user-profile-tabs-creator-pro' ),
			'all'           => __( 'Anyone', 'buddypress-user-profile-tabs-creator-pro' ),
			'none'          => __( 'None(Will remove it for everyone. Only applies for existing tabs)', 'buddypress-user-profile-tabs-creator-pro' ),
			'logged_in'     => __( 'Logged In Members', 'buddypress-user-profile-tabs-creator-pro' ),
			'self'          => __( 'Profile Owner', 'buddypress-user-profile-tabs-creator-pro' ),
		);

		if ( bp_is_active( 'friends' ) ) {
			$extra_visible_options['friends'] = __( 'Friends', 'buddypress-user-profile-tabs-creator-pro' );
		}

		if ( function_exists( 'bp_follow_get_followers' ) ) {
			$extra_visible_options['followers'] = __( 'Followers', 'buddypress-user-profile-tabs-creator-pro' );
			$extra_visible_options['following'] = __( 'Leaders(whom the user is following)', 'buddypress-user-profile-tabs-creator-pro' );
		}

		$who_can_view_roles             = array_merge( $extra_visible_options, $who_can_view_roles );
		$who_can_view_roles['not_self'] = __( 'Not Profile Owner', 'buddypress-user-profile-tabs-creator-pro' );

		$subnav_access_roles = array_merge( $extra_visible_options, $who_can_view_roles );

		// Tabs Meta.
		$cmb_tabs = new_cmb2_box( array(
			'id'           => $prefix . 'tabs_meta',
			'title'        => __( 'Tab Settings', 'buddypress-user-profile-tabs-creator-pro' ),
			'object_types' => array( bpptc_get_post_type() ),
			'context'      => 'advanced',
			'priority'     => 'high',
			'show_names'   => true,
		) );

		$cmb_tabs->add_field( array(
			'name'  => __( 'Need help?', 'buddypress-user-profile-tabs-creator-pro' ),
			'desc'  => sprintf( __( 'Get started with the <a href="%s">new tab options</a>', 'buddypress-user-profile-tabs-creator-pro' ),  'https://buddydev.com/docs/guides/plugins/buddypress-plugins/buddypress-user-profile-tabs-creator-pro/buddypress-user-profile-tab-options/' ),
			'type'  => 'title',
			'id'    => 'this_is_un_wanted_id_and_will_not_be_used_1001',
		) );

		$cmb_tabs->add_field( array(
			'name'    => __( 'Is enabled?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'tab_is_active',
			'type'    => 'checkbox',
			'default' => 0,
			'desc'    => __( 'Only enabled tabs will be visible.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field(
			array(
				'name'    => __( 'Tab Label', 'buddypress-user-profile-tabs-creator-pro' ),
				'id'      => $prefix . 'tab_label',
				'type'    => 'text',
				'default' => '',
				'desc'    => __( 'Tab label. If Tab label is not provided, the entry title will be used as label.', 'buddypress-user-profile-tabs-creator-pro' ),
			)
		);

		$cmb_tabs->add_field(
			array(
				'name'    => __( 'Tab Slug', 'buddypress-user-profile-tabs-creator-pro' ),
				'id'      => $prefix . 'tab_slug',
				'type'    => 'text',
				'default' => '',
				'desc'    => __( 'Preferably unique tab slug. If tab slug is not provided, the entry slug will be used as label.', 'buddypress-user-profile-tabs-creator-pro' ),
			)
		);

		$cmb_tabs->add_field( array(
			'name'    => __( 'Set it as default component?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'is_default_component',
			'type'    => 'checkbox',
			'desc'    => __( 'Set it as default component?', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name'    => __( 'Are we modifying predefined tab?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'tab_is_existing',
			'type'    => 'checkbox',
			'desc'    => __( 'Are we modifying a tab added by BuddyPress or some other plugin?', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		// replace with tab visibility.
		$cmb_tabs->add_field( array(
			'name'    => __( 'Update label?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'tab_update_label',
			'type'    => 'checkbox',
			'default' => 0,
			'classes' => 'bpptc-existing-tab-show',
			//'show_on_cb' => array( $this, 'show_for_new' ),
			'desc'    => __( 'Should we update the tab label with new label?', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		// replace with tab visibility.
		$cmb_tabs->add_field( array(
			'name'    => __( 'Site admin only?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'site_admin_only',
			'type'    => 'checkbox',
			'default' => 0,
			'desc'    => __( 'Is this tab visible to site admin only?', 'buddypress-user-profile-tabs-creator-pro' ),
		) );


		$cmb_tabs->add_field( array(
			'name'    => __( 'Add this tab for?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'tab_enabled_roles',
			'type'    => 'multicheck',
			'default' => 'all',
			'options' => $user_roles,
			'desc'    => __( 'Tab will be available to these users on their profile', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name'    => __( 'Tab Visibility', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => $prefix . 'tab_visible_roles',
			'type'    => 'multicheck',
			'default' => 'all',
			'options' => $who_can_view_roles,
			'desc'    => __( 'Tab will be visible to these users when they visit the profile of other user(tab enabled user)', 'buddypress-user-profile-tabs-creator-pro' ),
		) );


		$cmb_tabs->add_field( array(
			'name' => __( 'Position', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'tab_position',
			'type' => 'text',
			'desc' => __( 'Number, Required. Determines position of tab.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name' => __( 'Tab Link', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'tab_link',
			'type' => 'text',
			'desc' => __( 'Optional. Only use if you need dynamic url or link to absolute urls. See our <a href="https://buddydev.com/docs/guides/plugins/buddypress-plugins/buddypress-user-profile-tabs-creator-pro/buddypress-user-profile-tab-link-configuration/">documentation</a> about this.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name' => __( 'Default Subnav Slug', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'tab_default_subnav_slug',
			'type' => 'text',
			//'classes' => 'bpptc-existing-tab-hide',
			'desc' => __( 'Optional. Specify the slug of the subnav to be used. If not specified, first sub nav will be default.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name' => __( 'Item CSS ID', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'tab_item_css_id',
			'type' => 'text',
			'desc' => __( 'Optional. CSS id for the nav item.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name' => __( 'Add to admin bar?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'tab_add_to_adminbar',
			'type'    => 'checkbox',
			'default' => 0,
			'classes' => 'bpptc-existing-tab-hide',
			'desc' => __( 'Optional. Add this tab to adminbar account menu for user.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb_tabs->add_field( array(
			'name' => __( 'Adminbar Item ID', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => $prefix . 'item_adminbar_slug',
			'type' => 'text',
			//'classes' => 'bpptc-existing-tab-show',
			'desc' => __( 'Optional. Admin bar nav item id. Required if you want to remove item from adminbar account menu(or add sub menu).', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		// sub navs.
		$cmb = new_cmb2_box( array(
			'id'           => $prefix . 'sub_tabs_meta',
			'title'        => __( 'Subnav', 'buddypress-user-profile-tabs-creator-pro' ),
			'object_types' => array( bpptc_get_post_type() ),
			'context'      => 'advanced',
			'priority'     => 'high',
			'show_names'   => true,
		) );

		$cmb->add_field( array(
			'name'  => __( 'Need help?', 'buddypress-user-profile-tabs-creator-pro' ),
			'desc'  => sprintf( __( 'Get started with the <a href="%s">sub nav options</a>', 'buddypress-user-profile-tabs-creator-pro' ),  'https://buddydev.com/docs/guides/plugins/buddypress-plugins/buddypress-user-profile-tabs-creator-pro/adding-buddypress-user-profile-tab-sub-navs/' ),
			'type'  => 'title',
			'id'    => 'this_is_un_wanted_id_and_will_not_be_used_1002',
		) );

		$group_field_id = $cmb->add_field( array(
			'id'      => $prefix . 'subnav_items',
			'type'    => 'group',
			'options' => array(
				'group_title'   => __( 'Subnav {#}', 'buddypress-user-profile-tabs-creator-pro' ),
				'add_button'    => __( 'New', 'buddypress-user-profile-tabs-creator-pro' ),
				'remove_button' => __( 'Remove', 'buddypress-user-profile-tabs-creator-pro' ),
				'sortable'      => true,
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Is enabled?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'is_active',
			'type'    => 'checkbox',
			'default' => 0,
			'desc'    => __( 'Only enabled sub navs are visible.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Is Existing?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'is_existing',
			'type'    => 'checkbox',
			'classes' => 'bpptc-existing-subnav bpptc-existing-tab-show',
			'desc'    => __( 'Are we modifying an existing sub nav?', 'buddypress-user-profile-tabs-creator-pro' ),
			//'show_on_cb' => array( $this, 'show_for_existing' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'Label', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'label',
			'type' => 'text',
			'desc' => __( 'Required. Label for the sub nav item.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'Slug', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'slug',
			'type' => 'text',
			'desc' => __( 'Required. Unique slug for this sub nav.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'Position', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'position',
			'type' => 'text',
			'classes' => 'bpptc-existing-tab-show',
			'desc' => __( 'Required. Please specify a position for the sub nav.', 'buddypress-user-profile-tabs-creator-pro' ),
			//'show_on_cb' => array( $this, 'show_for_existing' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Site Admin Only', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'site_admin_only',
			'type'    => 'checkbox',
			'default' => 0,
			'classes' => 'bpptc-existing-subnav-hide',
			'desc'    => __( 'Is this sub nav visible to site admin only?', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Add this tab for?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'enabled_roles',
			'type'    => 'multicheck',
			'default' => 'all',
			'options' => $user_roles,
			'classes' => 'bpptc-subnav-available-roles',
			'desc'    => __( 'Tab will be available to these users on their profile', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Visible for?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'visible_roles',
			'type'    => 'multicheck',
			'default' => 'all',
			'options' => $subnav_access_roles,
			'classes' => 'bpptc-subnav-visibile-roles',// bpptc-existing-subnav-hide.
			'desc'    => __( 'Tab will be available to these users on their profile.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'Url', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'url',
			'type' => 'text',
			'desc' => __( '(Optional), if you want to link to some other page.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => __( 'CSS ID', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'item_css_id',
			'type' => 'text',
			'desc' => __( 'Optional, CSS id for the sub nav item.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Add to admin bar?', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'add_to_adminbar',
			'type'    => 'checkbox',
			'default' => 0,
			'classes' => 'bpptc-existing-subnav-hide',
			'desc'    => __( 'Optional, Add this tab to adminbar account menu for user.', 'buddypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field($group_field_id, array(
			'name' => __( 'Adminbar Item ID', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'   => 'item_adminbar_slug',
			'type' => 'text',
			'desc' => __( 'Optional. Admin bar nav item id. Required if you want to remove item from adminbar account menu.', 'dypress-user-profile-tabs-creator-pro' ),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Content', 'buddypress-user-profile-tabs-creator-pro' ),
			'id'      => 'content',
			'type'    => 'wysiwyg',
			'classes' => 'bpptc-existing-subnav-hide',
			'desc'    => sprintf( __( 'Any type of content( shortcodes too ) will work. Please see <a href="%s">documentation</a> for more details.', 'buddypress-user-profile-tabs-creator-pro' ), 'https://buddydev.com/docs/guides/plugins/buddypress-plugins/buddypress-user-profile-tabs-creator-pro/profile-tab-subnav-options/#bbptc-tab-content' ),
			'options' => array(
				'textarea_rows' => 5,
			),
			'sanitization_cb' => false,
		) );
	}

	/**
	 * Register the metabox for tab scope by users.
	 */
	public function register_metabox() {
		add_meta_box( '_bpptc_associate_users', __( 'Tab Scope', 'buddypress-user-profile-tabs-creator-pro' ), array(
			$this,
			'render_metabox',
		), bpptc_get_post_type(), 'side' );
	}

	/**
	 * Render metabox.
	 *
	 * @param WP_Post $post currently editing tab.
	 */
	public function render_metabox( $post ) {
		$meta           = get_post_custom( $post->ID );
		$selected_users = isset( $meta['_bpptc_tab_users'] ) ? $meta['_bpptc_tab_users'][0] : array();
		$selected_users = maybe_unserialize( $selected_users );

		if ( ! empty( $selected_users ) ) {
			$users = get_users(
				array(
					'include' => $selected_users,
					'fields'  => array( 'ID', 'display_name' ),
					'number'  => - 1,
				)
			);
		} else {
			$users = array();
		}

		?>
		<p class="bpptc-notice">
			<?php _e( 'You can limit the scope of this tab/modification to selected users only.', 'buddypress-user-profile-tabs-creator-pro' ); ?>
		</p>
		<p>
			<?php _e( 'If you select some users, the tab settings will only apply to the users satisfying these criteria.', 'buddypress-user-profile-tabs-creator-pro' ); ?>
		</p>

		<h4><?php _e( 'Associated Users:', 'buddypress-user-profile-tabs-creator-pro' );?></h4>
		<ul id="bpptc-selected-users-list">
			<?php foreach ( $users as $user ): ?>
				<li class="bpptc-user-entry" id="bpptc-user-<?php echo esc_attr( $user->ID );?>">
					<input type="hidden" value="<?php echo esc_attr( $user->ID );?>" name="_bpptc_tab_users[]" />
					<a class="bpptc-remove-user" href="#">X</a>
					<a href="<?php echo esc_url( bpptc_get_user_url( $user->ID ) ); ?>"><?php echo esc_html( $user->display_name );?> </a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<input type="text" placeholder="<?php _e( 'Type user name.', 'buddypress-user-profile-tabs-creator-pro' );?>" id="bpptc-user-selector" />
		</p>
		<p>
			<?php _e( 'You can select one or more user by typing the user name.', 'buddypress-user-profile-tabs-creator-pro' ); ?>
		</p>
		<style type="text/css">
            .bpptc-remove-user {
                padding-right: 5px;
                color: red;
            }
            #bpptc-user-selector {
                width: 100%;
            }
            #bpptc-selected-users-list a {
                text-decoration: none;
            }
		</style>

		<?php
	}

	/**
	 * Save the associated users
	 *
	 * @param int $post_id numeric post id of the post containing tab details.
	 */
	public function save_details( $post_id ) {

		if ( bpptc_get_post_type() != get_post_type( $post_id ) ) {
			return;
		}

		//$post = wp_unslash( $_POST );

		$associated_users = isset( $_POST['_bpptc_tab_users'] ) ? array_map( 'absint', (array) $_POST['_bpptc_tab_users'] ) : array();

		if ( $associated_users ) {
			$associated_users = array_unique( $associated_users );
			// should we validate the groups?
			// Let us trust site admins.
			update_post_meta( $post_id, '_bpptc_tab_users', $associated_users );
		} else {
			delete_post_meta( $post_id, '_bpptc_tab_users' );
		}
	}

	/**
	 * Load assets on the add/edit tab page.
	 */
	public function load_assets() {
		wp_enqueue_media(); // we put it here to fix a bug with CMB2 where add media won't work for repeatable fields.
		wp_enqueue_script( 'bpptc-edit-js', bpptc_profile_tabs_pro()->get_url() . 'admin/assets/bpptc-edit.js', array( 'jquery' ) );
		wp_enqueue_style( 'bpptc-admin-style',bpptc_profile_tabs_pro()->get_url() . 'admin/assets/bpptc-admin-style.css' );

		wp_enqueue_script(
			'bpptc_admin_users_helper_js',
			bpptc_profile_tabs_pro()->get_url() . 'admin/assets/bpptc-admin-users-helper.js',
			array( 'jquery', 'jquery-ui-autocomplete' )
		);
	}

	/**
	 * Callback for showing the metabox for existing tabs only.
	 *
	 * @param  object $cmb CMB2 object.
	 *
	 * @return bool True/false whether to show the metabox
	 */
	public function show_for_existing( $cmb ) {

		$status = get_post_meta( $cmb->object_id(), '_bpptc_tab_is_existing', 1 );

		return $status == 'on';
	}

	/**
	 * Callback for showing the metabox for new tabs only.
	 *
	 * @param  object $cmb CMB2 object.
	 *
	 * @return bool True/false whether to show the metabox
	 */
	public function show_for_new( $cmb ) {
		return ! $this->show_for_existing( $cmb );
	}

	/**
	 * Disable custom media buttons.
	 */
	public function media_button_fixes() {
		// if needed, media buttons can be removed via hook.
		if ( apply_filters( 'bpptc_remove_classic_editor_media_buttons', false ) ) {
			remove_all_actions( 'media_buttons' );
		}
	}

	/**
	 * Fix for Geodirectory breaking tinymce editor.
	 *
	 * @param string $editor_id editor id.
	 */
	public function fix_media_buttons_for_ayecode( $editor_id = 'content' ) {
		global $post, $shortcode_insert_button_once;

		if ( ! empty( $post ) && ! empty( $post->post_type ) && $post->post_type == bpptc_get_post_type() ) {
			$shortcode_insert_button_once = true;
		}
	}

	/**
	 * Fixes media uploader for wp 6.3+
	 */
	public function fix_uploader_for_wp6_3() {

		// no need to worry if already loaded.
		if ( did_action( 'print_media_templates' ) ) {
			return;
		}

		if (
			! function_exists( 'get_current_screen' )
			|| 'bpptc_profile_tab' !== get_current_screen()->id
		) {
			return;
		}

		if ( ! function_exists( 'wp_print_media_templates' ) ) {
			return;
		}
		// we only have issue if it is less than or equal to 10.
		if ( has_action( 'admin_footer', 'wp_print_media_templates' ) <= 10 ) {
			add_action( 'admin_footer', 'wp_print_media_templates', 30 );
		}
	}

}

new BPPTC_Profile_Tabs_Admin();
