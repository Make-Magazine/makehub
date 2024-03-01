<?php
/**
 * BuddyPress Profile Tabs Creator Pro
 *
 * @package buddypress-profile-tabs-creator-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Get Post type for the profile tab post type.
 *
 * @return string
 */
function bpptc_get_post_type() {
	return 'bpptc_profile_tab';
}

/**
 * Get our Internal Profile Tab object corresponding to the given tab/component
 * Please avoid directly using it, you should use the bpptc_get_active_member_type_entries() for bulk fetch.
 *
 * @param string $slug profile tab slug.
 *
 * @return BPPTC_Profile_Tabs_Tab_Entry|null
 */
function bpptc_get_profile_tab_entry( $slug ) {

	$post_id = bpptc_get_post_id( $slug );

	if ( empty( $post_id ) ) {
		return null;
	}

	$meta = get_post_custom( $post_id );

	return new BPPTC_Profile_Tabs_Tab_Entry( $meta, $post_id );
}

/**
 * Get an array of Active Profile Tabs entries
 *
 * @return BPPTC_Profile_Tabs_Tab_Entry[]
 */
function bpptc_get_active_profile_tab_entries() {

	// case 1: non multisite.
	if ( ! is_multisite() ) {
		return _bpptc_get_active_profile_tab_entries();
	}

	// case 2: multisite non network active.
	// if we are here, we are on multisite.
	if ( ! bpptc_profile_tabs_pro()->is_network_active() ) {
		return _bpptc_get_active_profile_tab_entries();
	}

	// Case 3 a. Multisite network active, non multis blog mode.
	$root_blog_id = 0;
	if ( ! bp_is_multiblog_mode() ) {
		$root_blog_id = bp_get_root_blog_id();
	} else {
		// case 3.b Multisite, network active, multi blog mode.
		$root_blog_id = get_main_site_id();
	}

	if ( $root_blog_id ) {
		switch_to_blog( $root_blog_id );
	}

	$tabs = _bpptc_get_active_profile_tab_entries();

	if ( $root_blog_id ) {
		restore_current_blog();
	}

	return $tabs;
}

/**
 * Used internally to retrieve tabs.
 *
 * @see bpptc_get_active_profile_tab_entries()
 * @internal
 *
 * @return array
 */
function _bpptc_get_active_profile_tab_entries() {

	static $active_tabs;

	if ( isset( $active_tabs ) ) {
		return $active_tabs;
	}

	$active_ids = bpptc_get_active_profile_tabs_post_ids();

	if ( empty( $active_ids ) ) {
		$active_tabs = array();

		return $active_tabs;
	}

	$active_tabs = array();

	foreach ( $active_ids as $active_id ) {

		$meta = get_post_custom( $active_id );
		$obj  = new BPPTC_Profile_Tabs_Tab_Entry( $meta, $active_id );

		if ( ! $obj->slug || ! $obj->post_slug ) {
			continue;
		}

		$active_tabs[ $obj->post_slug ] = $obj;
	}

	return $active_tabs;
}

/**
 * Get an array of post ids associated with active profile tabs.
 *
 * @return array of post ids.
 */
function bpptc_get_active_profile_tabs_post_ids() {

	global $wpdb;

	$query = "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_status = %s AND post_type = %s AND ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value = %s ) ";

	$post_ids = $wpdb->get_col( $wpdb->prepare( $query,'publish', bpptc_get_post_type(), '_bpptc_tab_is_active', 'on' ) );

	_prime_post_caches( $post_ids, false, true );

	return $post_ids;
}

/**
 * Get the post ID which stores details for this profile tab
 *
 * Please avoid directly using it, you should use the bpptc_get_active_member_type_entries() for bulk fetch.
 *
 * @param string $slug tab slug.
 *
 * @return int post id.
 */
function bpptc_get_post_id( $slug ) {

	global $wpdb;
	// The reason to do select from the post table is to make sure that the post exists and not just the meta.
	$query = "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_name = %s ";

	$post_id = $wpdb->get_var( $wpdb->prepare( $query, bpptc_get_post_type(), $slug ) );

	return $post_id;
}


/**
 * Get the main blog id.
 *
 * We store the member tabs details on this blog.
 *
 * @return int|string
 */
function bpptc_get_main_blog_id() {
	$blog_id = 1;
	if ( function_exists( 'get_network' ) ) {
		$blog_id = get_network()->blog_id;
	} elseif ( defined( 'BLOG_ID_CURRENT_SITE' ) ) {
		$blog_id = BLOG_ID_CURRENT_SITE;
	}

	return apply_filters( 'bpptc_main_blog_id', $blog_id );
}

/**
 * Screen Handler for the tabs/subtabs added by this plugin.
 */
function bpptc_screen_handler() {

	// Hook content generator.
	add_action( 'bp_template_content', 'bpptc_content_generator' );
	// load plugins template.
	bp_core_load_template( array( 'members/single/plugins' ) );
}

/**
 * Tab/Sub tab content generator.
 */
function bpptc_content_generator() {

	$tab         = bpptc_get_current_tab();
	$subtab      = bpptc_get_current_sub_tab();
	//$active_tabs = ();

	if ( ! $tab || ! $subtab ) {
		return;
	}

	//$tab_object = $active_tabs[ $tab ];
	//$subtab_obj     = isset( $tab_object->sub_navs[ $subtab ] ) ? $tab_object->sub_navs[ $subtab ] : null;
	// upto BuddyPress 2.9.2, BuddyPress does not support a tab without a default sub tab,
	// See ticket https://buddypress.trac.wordpress.org/ticket/7628
	// for the details.
	if ( ! $subtab ) {
		return;
	}

	$content = $subtab->content;

	// allow admins to use displayed_user_id and logged_user_id in their shortcodes.
	$content = str_replace(
		array(
			'#site_url#',
			'#displayed_user_link#',
			'#displayed_user_url#',
			'#displayed_user_id#',
			'#logged_user_link#',
			'#logged_user_url#',
			'#logged_user_id#',
		),
		array(
			untrailingslashit( site_url( '/' ) ),
			bp_core_get_userlink( bp_displayed_user_id() ),
			untrailingslashit( bp_displayed_user_domain() ),
			bp_displayed_user_id(),
			bp_core_get_userlink( bp_loggedin_user_id() ),
			bp_loggedin_user_domain(),
			bp_loggedin_user_id(),
		),
		$content
	);


	setup_postdata( get_post( $tab->post_id ) );
	$class = 'bpptc-content-tab-' . esc_attr( $tab->slug ) . ' bpptc-content-sub-tab-' . esc_attr( $subtab->slug );
	$class .= ' bpptc-content-view-' . esc_attr( $tab->slug ) . '-' . esc_attr( $subtab->slug );

	echo "<div class='bpptc-item-content bpptc-member-content $class' >";
	// applying the_content gives us all the benefits of the default the_content hooks.
	echo apply_filters( 'the_content', $content );
	echo '</div>';
	wp_reset_postdata();
}

/**
 * Parse url and generate dynamic url if needed.
 *
 * @param string $url absolute or dynamic url.
 *
 * @return string
 */
function bpptc_parse_profile_tab_url( $url ) {

	$search = array(
		'[displayed-user-profile]',
		'[logged-user-profile]',
		'[site-url]',
		'[blog-url]',
		'[displayed-username]',
		'[displayed-user-id]',
	);

	$replace = array(
		bp_displayed_user_domain(),
		bp_loggedin_user_domain(),
		network_home_url( '/' ),
		site_url( '/' ),
		bp_get_displayed_user_username(),
		bp_displayed_user_id(),
	);

	return apply_filters( 'bpptc_parsed_profile_url', str_replace( $search, $replace, $url ), $url );
}

/**
 * Is the tab for the displayed user is visible to the given user.
 *
 * @param int                          $user_id logged user id.
 * @param BPPTC_Profile_Tabs_Tab_Entry $tab tab object.
 *
 * @return bool
 */
function bpptc_is_tab_visible_for( $user_id, $tab ) {

	if ( empty( $tab->visible_roles ) ) {
		return false;
	}

	$roles_visible = (array) $tab->visible_roles;

	// check for not self before anything else.
	if ( in_array( 'not_self', $roles_visible, true ) && bp_is_my_profile() ) {
		return false;
	}

	if ( in_array( 'none', $roles_visible, true ) ) {
		return false;
	} elseif ( in_array( 'all', $roles_visible, true ) ) {
		return true;
	} elseif ( in_array( 'do_not_modify', $roles_visible, true ) ) {
		return true;// do not modify the visibility for existing tabs.
	} elseif ( ! is_user_logged_in() ) {
		return false; // in all other cases, user must be logged in.
	}


	// we will reach here only if the user is logged in, so yeh, let's allow.
	if ( is_super_admin() ) {
		return true;
	} elseif ( in_array( 'logged_in', $roles_visible, true ) ) {
		return true;
	} elseif ( in_array( 'self', $roles_visible, true ) && bp_is_my_profile() ) {
		return true;
	}

	$displayed_user_id = bp_displayed_user_id();

	// if self, and is my profile.
	if ( $displayed_user_id && in_array( 'friends', $roles_visible, true ) && function_exists( 'friends_check_friendship' ) && friends_check_friendship( $user_id, $displayed_user_id ) ) {
		return true;
	} elseif ( $displayed_user_id && function_exists( 'bp_follow_get_followers' ) ) {

		if ( in_array( 'followers', $roles_visible, true ) && bp_follow_is_following( array(
				'leader_id'   => $displayed_user_id,
				'follower_id' => $user_id,
			) ) ) {
			return true;
		} elseif ( in_array( 'following', $roles_visible, true ) && bp_follow_is_following( array(
				'leader_id'   => $user_id, // Logged in user is being followed by our displayed user.
				'follower_id' => $displayed_user_id,
			) ) ) {

			return true;
		}
	}
	// if we are here, we need to check again for self.
	if ( in_array( 'self', $roles_visible, true ) && ! bp_is_user() && is_user_logged_in() ) {
		return true;
	}

	// if we are here, it is some role selection.
	$user = wp_get_current_user();

	if ( empty( $user ) ) {
		return false;
	}

	$user_roles = $user->roles;

	if ( empty( $user_roles ) ) {
		return false;
	}

	// check if the displayed user has contactable roles?
	if ( array_intersect( $roles_visible, $user_roles ) ) {
		return true;
	}

	return false;
}

/**
 * Is tab scoping enabled?
 *
 * @param BPPTC_Profile_Tabs_Tab_Entry $tab tab object.
 *
 * @return bool
 */
function bpptc_is_scoping_enabled( $tab ) {
	return ! empty( $tab->associated_user_ids );
}

/**
 * Is user associated with the tab?
 *
 * @param int                          $user_id User id.
 * @param BPPTC_Profile_Tabs_Tab_Entry $tab tab object.
 *
 * @return bool
 */
function bpptc_is_scoped_user( $user_id, $tab ) {
	return ! empty( $tab->associated_user_ids ) && in_array( $user_id, $tab->associated_user_ids );
}

/**
 * Is the tab enabled on the profile of the given user?
 *
 * @param int                          $user_id user id.
 * @param BPPTC_Profile_Tabs_Tab_Entry $tab tab object.
 *
 * @return bool
 */
function bpptc_is_tab_enabled_for( $user_id, $tab ) {

	$is_existing   = isset( $tab->is_existing ) ? $tab->is_existing : false;
	$roles_enabled = isset( $tab->enabled_roles ) ? (array) $tab->enabled_roles : array();

	// this is a scoped tab.
	if ( ! empty( $tab->associated_user_ids ) ) {
		$is_scope_match = in_array( $user_id, (array) $tab->associated_user_ids );

		if ( in_array( 'none', $roles_enabled, true ) ) {
			return $is_existing ? ! $is_scope_match : false;
		} else {
			return $is_scope_match;
		}
	}

	// for non scoped user, the old settings continue.
	if ( empty( $roles_enabled ) || in_array( 'none', $roles_enabled, true ) ) {
		return false;
	}

	if ( in_array( 'all', $roles_enabled, true ) ) {
		return true;
	}

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return false;
	}

	$user_roles = $user->roles;

	if ( empty( $user_roles ) ) {
		return false;
	}

	// check if the displayed user has contactable roles?
	if ( ! array_intersect( $roles_enabled, $user_roles ) ) {
		return false;
	}

	return true;
}

/**
 * Get the total count from label.
 *
 * @param string $text string to extract from.
 *
 * @return bool|int false on failure otherwise count.
 */
function bpptc_parse_count_from_text( $text ) {

	$matches = array();
	$regex   = '#<\s*?span\b[^>]*>(.*?)</span\b[^>]*>#s';

	if ( ! preg_match( $regex, $text, $matches ) ) {
		return false;
	}

	return $matches[1];
}


/**
 * Get current tab object.
 *
 * @return null|\BPPTC_Profile_Tabs_Tab_Entry
 */
function bpptc_get_current_tab() {
	$tab_slug = bp_current_component();
	if ( ! $tab_slug ) {
		return null;
	}

	$active_tabs = bpptc_get_active_profile_tab_entries();

	$current_tab = null;

	$user_id = bp_displayed_user_id();
	foreach ( $active_tabs as $active_tab ) {
		if ( isset( $active_tab->slug ) && $tab_slug === $active_tab->slug && bpptc_is_tab_enabled_for( $user_id, $active_tab ) ) {
			$current_tab = $active_tab;
			break;
		}
	}

	return $current_tab; // isset( $active_tabs[ $tab_slug ] ) ? $active_tabs[ $tab_slug ] : null;
}

/**
 * Get current subtab object.
 *
 * @return null|\BPPTC_Profile_Tabs_Subnav_Entry
 */
function bpptc_get_current_sub_tab() {


	$tab = bpptc_get_current_tab();

	if ( ! $tab ) {
		return null;
	}

	$sub_tab_slug = bp_current_action();

	if ( $sub_tab_slug && isset( $tab->sub_navs[ $sub_tab_slug ] ) ) {
		$sub_tab = $tab->sub_navs[ $sub_tab_slug ];
	} else {
		$sub_tab = current( $tab->sub_navs );
	}

	// upto BuddyPress 2.9.2, BuddyPress does not support a tab without a default sub tab,
	// See ticket https://buddypress.trac.wordpress.org/ticket/7628
	// for the details.
	if ( ! $sub_tab ) {
		return null;
	}

	return $sub_tab;
}

/**
 * Retrieves user url
 *
 * @param int    $user_id       User id.
 * @param string $user_nicename User nicename.
 * @param string $user_login    User login.
 *
 * @return string
 */
function bpptc_get_user_url( $user_id, $user_nicename = '', $user_login = '' ) {

	if ( function_exists( 'bp_members_get_user_url' ) ) {
		return bp_members_get_user_url( $user_id );
	}

	return bp_core_get_user_domain( $user_id, $user_nicename, $user_login );
}