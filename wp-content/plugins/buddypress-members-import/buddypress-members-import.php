<?php
/*
Plugin Name: BuddyPress Members Import
Plugin URI: http://www.youngtechleads.com/buddypress-members-import/
Description: Allows the batch importation of users/members from CSV file to BuddyPress setup.
Author: Soumi Das
Author URI: http://www.youngtechleads.com
Version: 4.5
Date: 11/17/2016
Author Emailid: soumi.das1990@gmail.com/skype:soumibgb
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_init', 'change_time_out' );
//https://codex.wordpress.org/Function_Reference/is_plugin_active_for_network
function change_time_out() {
	/* max timeout to allow for mass user upload. */
	ini_set( 'max_execution_time', 0 );
	$memory_limit = ini_get( 'memory_limit' );
	$search = array('M', 'G');
	$memory_limit = str_replace( $search, '', $memory_limit );
	if ( 256 > $memory_limit ) {
		ini_set( 'memory_limit', '256M' );
	}
}

function bmi_admin_css() {
	wp_enqueue_style( 'bmi-style-css', plugin_dir_url( __FILE__ ) . '/buddypress-members-import.css' );
}

// add admin menu
add_action( 'admin_menu', 'bp_membersimport_menu' );

function bp_membersimport_menu() {
	$bmi_page_hook = add_submenu_page(
		'users.php',
		'BuddyPress Members Import',
		'BuddyPress Members Import',
		'manage_options',
		'bp-members-import',
		'bp_memberimport_page'
	);

	add_action( "admin_print_scripts-$bmi_page_hook", 'bmi_admin_css' );
}

// show import form
function bp_memberimport_page() {
	global $wpdb, $blog_id;

	if ( ! get_option( 'email-template' ) ) {
		$email_template = 'Hello {USERNAME}

Username: {USERNAME}
Password: {PASSWORD}
Login url: {LOGIN_URL}

Thanks
{SITE_ADMIN}';
		update_option( 'email-template', $email_template );
	}
	$bp_status  = is_plugin_active( 'buddypress/bp-loader.php' );
	$wpm_status = is_plugin_active( 'membership/membership.php' );
	$pmpro_status = is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' );
	if ( $bp_status ) {
		// Check whether the avatars directory present or not. If not then create.
		$bp_plugin_details = get_plugin_data( ABSPATH .'wp-content/plugins/buddypress/bp-loader.php' );
		$bp_plugin_version = $bp_plugin_details['Version'];

		if ( $bp_plugin_version < 1.8 ) {
			define( 'AVATARS', ABSPATH . 'assets/avatars' );
		} else {
			define( 'AVATARS', ABSPATH . 'wp-content/uploads/avatars' );
		}
	}
	// User data fields list used to differentiate with user meta
	$wp_userdata_fields = array(
		'user_login', 'user_pass',
		'user_email', 'user_url', 'user_nicename',
		'display_name', 'user_registered', 'first_name',
		'last_name', 'nickname', 'description',
		'rich_editing', 'comment_shortcuts', 'admin_color',
		'use_ssl', 'show_admin_bar_front', 'show_admin_bar_admin',
		'role',
	);

	$html_message = $error_message = $not_imported_usernames = '';

	//Check whether the curent user have the access or not
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

	// if the form is submitted
	$valid_nonce = array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'bmi_by_ytl' );
	if ( ( $valid_nonce ) && array_key_exists( 'mode', $_POST ) && ( 'submit' == $_POST['mode'] ) ) {
		if ( $bp_status ) {
			//Get the BP extra fields id name name
			$bp_xprofile_fields = $bp_xprofile_fields_with_default_value = array();

			$bp_extra_fields = $wpdb->get_results( 'SELECT id, type, name FROM ' . $wpdb->prefix . 'bp_xprofile_fields' );

			$bpxfwdv_sql = 'SELECT name
							FROM ' . $wpdb->prefix . 'bp_xprofile_fields
							WHERE type
								IN ("checkbox", "multiselectbox", "selectbox", "radio")
								AND parent_id=0';
			$bp_xprofile_fields_with_default_value = $wpdb->get_col( $bpxfwdv_sql );

			// Get xprofile field visibility
			$xprofile_visibility_sql = 'SELECT object_id, meta_value FROM ' . $wpdb->prefix . 'bp_xprofile_meta WHERE meta_key = "default_visibility"';
			$bp_fields_visibility    = $wpdb->get_results( $xprofile_visibility_sql );
			$xprofile_fields_visibility = array( 1 => 'public' );

			foreach ( (array) $bp_fields_visibility as $bp_field_visibility ) {
				$xprofile_fields_visibility[$bp_field_visibility->object_id] = $bp_field_visibility->meta_value;
			}

			//Create an array of BP fields
			foreach ( $bp_extra_fields as $value ) {
				$bp_xprofile_fields[$value->id] = $value->name;
				$bp_fields_type[$value->id] = $value->type;
			}
		}

		if ( false === strpos( $_FILES['csv_file']['name'], '.csv' )  ) {
			$html_message = '<div class="updated">';
			$html_message .= 'Upload only csv file!!';
			$html_message .= '</div>';
		} else {
			$avatar = isset( $_POST['avatar'] ) ? $_POST['avatar'] : false;

			// Check whether the admin wants to upload members avatar or not. If yes then
			// Check whether the avatars directory present or not. If not then create.
			if ( $avatar ) if ( ! file_exists( AVATARS ) ) mkdir( AVATARS, 0777 );

			ini_set( 'auto_detect_line_endings', TRUE );
			$handle = fopen( $_FILES['csv_file']['tmp_name'], 'r' );
			$first = true;
			$not_imported = '';
			$flag = 0;
			$user_import = 0;
			$not_import_message = '';
			$total_rows = $new_user_imported = $old_user_updated = $user_not_imported = 0;
			$future_friend_mapping = array();
			$friend_count = array(0);
			while ( ( $row = fgetcsv( $handle, filesize( $_FILES['csv_file']['tmp_name'] ), ',' ) ) !== false ) {
				$row = array_map( 'trim', $row );
				if ( 1 === count( $row ) ) continue;

				// If we are on the first line, get the columns name in headers array
				if ( $first ) {
					$headers = $row;
					$first = false;
					continue;
				}
				$total_rows++;

				// Separate user data from meta
				$userdata = $usermeta = $bpmeta = $bp_provided_fields = array();

				foreach ( $row as $ckey => $cvalue ) {
					if ( empty( $cvalue ) ) continue;

					$column_name = $headers[$ckey];

					if ( array_key_exists( 'utf8_encoding', $_POST ) ) {
						$cvalue = utf8_encode( $cvalue );
					}

					if ( strpos( $cvalue, '::' ) ) {
						$cvalue = explode( '::', $cvalue );
						$cvalue = array_filter( $cvalue, function( $item ) { return !empty( $item[0] ); } );
					}

					if ( in_array( $column_name, $wp_userdata_fields ) ) $userdata[$column_name] = $cvalue;
					else if ( $bp_status && array_search( $column_name, $bp_xprofile_fields ) ) {
							$bp_provided_fields[] = $column_name;
							$bpmeta[array_search( $column_name, $bp_xprofile_fields )] = $cvalue;
						}
					else $usermeta[$column_name] = $cvalue;

					if ( array_key_exists( 'pmpro_level', $usermeta ) ) {
						$pmpro_level = $usermeta['pmpro_level'];
					}
				}
				
				if ( !isset( $_POST['update_user'] ) && $bp_status ) {
					$bp_left_fields = array_diff( $bp_xprofile_fields_with_default_value, $bp_provided_fields );

					if ( count( $bp_left_fields ) ) {
						foreach ( $bp_left_fields as $bp_left_field ) {
							$bpf_sql = 'SELECT id, type
										FROM ' . $wpdb->prefix . 'bp_xprofile_fields
										WHERE name="' . $bp_left_field . '"
											AND parent_id=0';
							$bp_fields = $wpdb->get_results( $bpf_sql );

							$bpfo_sql = 'SELECT name
										 FROM ' . $wpdb->prefix . 'bp_xprofile_fields
										 WHERE parent_id=' . $bp_fields[0]->id . '
											AND is_default_option=1';
							$bp_field_options = $wpdb->get_results( $bpfo_sql );
							$field_options = array();

							if ( $bp_fields[0]->type == 'selectbox' || $bp_fields[0]->type == 'radio' ) {
								$bpmeta[$bp_fields[0]->id] = $bp_field_options[0]->name;
							} else {
								foreach ( $bp_field_options as $bp_field_option ) {
									$field_options[] = $bp_field_option->name;
								}
								$bpmeta[$bp_fields[0]->id] = maybe_unserialize( $field_options );
							}
						}
					}
				}
				// If no user data, comeout!
				if ( empty( $userdata ) ) continue;

				// If creating a new user and no password was set, let auto-generate one!
				if ( empty( $userdata['user_pass'] ) )
					$userdata['user_pass'] = wp_generate_password( 12, false );

				$userdata['user_login'] = strtolower( $userdata['user_login'] );

				$space_remove = array( 'user_login', 'user_nicename' );
				foreach ( $space_remove as $key ) {
					if ( isset( $userdata[$key] ) ) {
						$userdata[$key] = sanitize_user( strtolower( $userdata[$key] ) );
						$userdata[$key] = str_replace( ' ', '', $userdata[$key] );
					}
				}

				if ( ( $userdata['user_login'] == '' ) && ( $userdata['user_email'] == '' ) ) {
					$error_message .= '<br />user_login or/and user_email needed to import members for row ' . $total_rows;
					$user_not_imported++;
					continue;
				} else if ( $userdata['user_login'] == '' ) {
					$userdata['user_login'] = $userdata['user_email'];
				} else if ( $userdata['user_email'] == '' ) {
					$userdata['user_email'] = $userdata['user_login'];
				}
				$user_login = $userdata['user_login'];

				if ( isset( $_POST['update_user'] ) ) {
					//Check whether the user already exist or not
					$user_details = get_user_by( 'email', $userdata['user_email'] );

					//If user already exists then assign ID and update the account.
					if ( $user_details ) {
						$userdata['ID'] = $user_details->data->ID;

						if ( !array_key_exists( 'update_password', $_POST ) ) {
							unset( $userdata['user_pass'] );
						}
						$user_id = wp_update_user( $userdata );
						$custom_send_email_flag = 0;
					} else {
						$user_id = wp_insert_user( $userdata );
						$custom_send_email_flag = 1;	
					}
				} else {
					$user_id = wp_insert_user( $userdata );
					$custom_send_email_flag = 1;
				}

				// If CSV file has encrypted password
				if ( array_key_exists( 'encrypted_password', $_POST ) && array_key_exists( 'user_pass', $userdata ) ) {
					$sql = 'UPDATE ' . $wpdb->base_prefix . 'users SET user_pass = "' . $userdata['user_pass'] . '" WHERE ID = ' . $user_id;
					$wpdb->query( $sql );
				}

				// Is there an error?
				if ( is_wp_error( $user_id ) ) {
					$flag = 1;
					$user_not_imported++;
					$not_imported_usernames  .= '<b>' . $userdata['user_login'] . '</b> ' . $user_id->errors['existing_user_login'][0] . '<br />';
				} else {
					//Upload user avatar if permission granted.
					if ( $bp_status && $avatar ) {
						$image_dir = AVATARS . '/'  . $user_id;
						mkdir( $image_dir, 0777 );
						$current_time = time();
						$destination_bpfull = $image_dir . '/' . $current_time . '-bpfull.jpg';
						$destination_bpthumb = $image_dir . '/' . $current_time . '-bpthumb.jpg';

						if ( array_key_exists( 'avatar', $usermeta ) ) {
							$usermeta['avatar'] = str_replace( ' ', '%20', $usermeta['avatar'] );
							$bpfull = $bpthumb = wp_get_image_editor( $usermeta['avatar'] );

							// Handle 404 avatar url
							if ( !is_wp_error( $bpfull ) ) {
								$bpfull->resize( 150, 150, true );
								$bpfull->save( $destination_bpfull );
								$bpthumb->resize( 50, 50, true );
								$bpthumb->save( $destination_bpthumb );
							}
						}
					}

					//User count
					if ( array_key_exists( 'ID', $userdata ) ) {
						$old_user_updated++;
					} else {
						$new_user_imported++;
					}

					$user_import = 1;

					if ( $bp_status ) {
						// Insert xprofile field visibility state for user level.
						update_user_meta( $user_id, 'bp_xprofile_visibility_levels', $xprofile_fields_visibility );

						if ( isset( $bpmeta ) ) {
							//Added an entry in user_meta table for current user meta key is last_activity
							bp_update_user_last_activity( $user_id, date( 'Y-m-d H:i:s' ) );

							foreach ( $bpmeta as $bpmetakeyid => $bpmetavalue ) {
								$current_field_type = $bp_fields_type[$bpmetakeyid];
								if ( 'image' === $current_field_type || $current_field_type == 'file') {
									$sql = 'SELECT id FROM ' . $wpdb->prefix . 'bp_xprofile_data WHERE field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
									$result = $wpdb->get_var( $sql );
									$date = date('Y-m-d G:i:s');
									if ( '' == $result ) {
										$sql = 'INSERT INTO ' . $wpdb->prefix . "bp_xprofile_data (`field_id`,`user_id`,`value`, `last_updated`) VALUES($bpmetakeyid, $user_id, '$bpmetavalue', '$date')";
									} else {
										$sql = 'UPDATE ' . $wpdb->prefix . 'bp_xprofile_data SET value = "' . $bpmetavalue . '", last_updated = "' . $date . '" WHERE id = ' . $result . ' AND field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
									}
									$wpdb->query( $sql );
								} else {
									xprofile_set_field_data( $bpmetakeyid, $user_id, $bpmetavalue );
								}
							}
						}
					}

					// If no error, let's update the user meta too!
					if ( $usermeta ) {
						if ( $bp_status && array_key_exists( 'bp_member_types', $usermeta ) ) {
							$bp_member_types = $usermeta['bp_member_types'];
							unset( $usermeta['bp_member_types'] );
							$bp_member_types_arr = explode( '::', $bp_member_types );
							foreach ( $bp_member_types_arr as $type ) {
								// Set the member type of user $user_id to $type.
								$member_type = bp_set_member_type( $user_id, $type );
							}
						}
						if ( $bp_status && array_key_exists( 'member_friends_login', $usermeta ) ) {
							$member_friends_login = $usermeta['member_friends_login'];
							unset( $usermeta['member_friends_login'] );

							$friend_add_time = date( 'Y-m-d H:i:s' );
							$inset_values = array();

							foreach ( (array) $member_friends_login as $login ) {
								$frd_arr = explode( ':', $login );
								if ( is_array( $frd_arr ) && ( count( $frd_arr ) ) ) {
									$sql = 'SELECT ID FROM ' . $wpdb->base_prefix . 'users WHERE user_login = "' . $frd_arr[0] . '"';
									$bp_friend_id = $wpdb->get_var( $sql );
									if ( '' != $bp_friend_id ) {
										$inset_values[] = "($user_id, $bp_friend_id, $frd_arr[1], 0, '$friend_add_time' )";
									} else {
										$future_friend_mapping[$frd_arr[0]][] = $user_id;
										if ( isset( $frd_arr[1] ) ) {
											$friend_status[$frd_arr[0] . '-' . $user_id ] = $frd_arr[1];
										}
									}
								}
							}
						}

						if ( ( array_key_exists( $user_login, $future_friend_mapping ) ) && 0 < count( $future_friend_mapping[$user_login] ) ) {
							foreach ( $future_friend_mapping[$user_login] as $requester_id ) {
								$status_flag = $friend_status[$user_login . '-' . $requester_id ];
								$inset_values[] = "($requester_id, $user_id, $status_flag, 0, '$friend_add_time' )";
								@$friend_count[$frd_arr[0]]++;
								@$friend_count[$user_id]++;
							}
							$inset_values = array_unique( $inset_values );
							unset( $future_friend_mapping[$user_login] );
						}

						if ( isset( $inset_values ) && count( $inset_values ) ) {
							$insert_query = 'INSERT INTO `' . $wpdb->prefix . 'bp_friends` (`initiator_user_id`, `friend_user_id`, `is_confirmed`, `is_limited`, `date_created`) VALUES ' . implode( ',', $inset_values);
							$inset_values = array();
							$wpdb->query( $insert_query );
							$insert_query = '';
						}

						// Map member to group provided group IDs
						if ( $bp_status && array_key_exists( 'member_group_ids', $usermeta ) ) {
							$member_group_ids = $usermeta['member_group_ids'];
							unset( $usermeta['member_group_ids'] );

							if ( is_array( $member_group_ids ) ) {
								//Attached members with BuddyPress groups
								foreach ( $member_group_ids as $member_group_id ) {
									groups_join_group( $member_group_id, $user_id );
								}
							} else {
								groups_join_group( $member_group_ids, $user_id );
							}
						}

						// Map member to group provided group Name
						if ( $bp_status && array_key_exists( 'member_groups_name', $usermeta ) ) {
							$member_groups_name = $usermeta['member_groups_name'];
							unset( $usermeta['member_groups_name'] );

							if ( is_array( $member_groups_name ) ) {
								//Attached members with BuddyPress groups
								foreach ( $member_groups_name as $member_group_name ) {
									$member_group_id = create_a_group( $member_group_name );
									groups_join_group( $member_group_id, $user_id );
								}
							} else {
								$member_group_id = create_a_group( $member_groups_name );
								groups_join_group( $member_group_id, $user_id );
							}
						}

						if ( $pmpro_status ) {
							if ( isset( $pmpro_level ) && ( '' != trim( $pmpro_level ) ) ) {
								pmpro_changeMembershipLevel( $pmpro_level, $user_id );
							}
						}

						if ( is_plugin_active( 'membership/membership.php' ) ) {
							if ( array_key_exists( 'wpm_subs_id', $usermeta ) ) {
								$wpm_subs_id = $usermeta['wpm_subs_id'];
								unset( $usermeta['wpm_subs_id'] );
							}
							if ( array_key_exists( 'wpm_level_id', $usermeta ) ) {
								$wpm_level_id = $usermeta['wpm_level_id'];
								unset( $usermeta['wpm_level_id'] );
							}
							if ( array_key_exists( 'wpm_level_order', $usermeta ) ) {
								$wpm_level_order = $usermeta['wpm_level_order'];
								unset( $usermeta['wpm_level_order'] );
							}

							$gateway = isset( $usermeta['wpm_payment_gateway'] ) ? $usermeta['wpm_payment_gateway'] : 'admin';
							unset( $usermeta['wpm_payment_gateway'] );
							$member = new M_Membership( $user_id );
							$member->add_subscription( $wpm_subs_id, $wpm_level_id, $wpm_level_order, $gateway );
						}

						foreach ( $usermeta as $metakey => $metavalue ) {
							$metavalue = maybe_unserialize( $metavalue );
							update_user_meta( $user_id, $metakey, $metavalue );
						}
					}

					if ( isset( $_POST['new_member_notification'] ) ) {
						if ( 1 === $custom_send_email_flag ) {
							if ( isset( $_POST['custom_notification'] ) ) {
								send_notifiction_to_new_user( $user_id, $userdata['user_pass'] );
							} else {
								wp_new_user_notification( $user_id, $userdata['user_pass'] );
							}
						}
					}
				}

				if ( 0 === $user_import && 0 === $user_import )
					$not_import_message = 'No users imported.<br />';
				$not_import_message .= $not_imported_usernames;

				if ( 1 === $flag && 1 === $user_import ) {
					$not_import_message = 'Following user(s) are not imported as they are already registered in your website:<br />';
					$not_import_message .= $not_imported_usernames;
				}
			}

			// Set users friends count in usermeta table
			foreach ( $friend_count as $key => $count ) {
				$total_friend_count = get_user_meta( $key, 'total_friend_count', true );
				$total_friend_count += $count;
				$total_friend_count = update_user_meta( $key, 'total_friend_count', $total_friend_count );
			}
			

			$html_message = '<div class="updated">';
			$html_message .= $not_import_message;
			$html_message .= '<p style="color: #ff0000;">' . $error_message . '</p>';
			$html_message .= '<p>Total users in CSV: ' . $total_rows . '</p>';
			$html_message .= '<p>Total new users imported: '. $new_user_imported . '</p>';
			$html_message .= '<p>Total old users updated: '. $old_user_updated . '</p>';
			$html_message .= '<p style="color: #ff0000;">Total users not imported: ' . $user_not_imported . '</p>';
			$html_message .= '</div>';
		}
	}  // end of 'if mode is submit'

	if ( array_key_exists( 'save-email-template', $_POST ) ) {
		$_POST['email-template'] = stripslashes( $_POST['email-template'] );
		update_option( 'email-template', $_POST['email-template'] );
	}

	// Get the members import form
	get_form( $html_message );
} // end of 'function bp_memberimport_page()'

function create_a_group( $group_name ) {
	global $wpdb;

	$sql = 'SELECT id FROM ' . $wpdb->prefix . 'bp_groups WHERE name = "' . $group_name . '"';
	$group_id = $wpdb->get_var( $sql );
	if ( '' == $group_id ) {
		$new_group = new BP_Groups_Group;
		$new_group->creator_id = 1;
		$new_group->name = $group_name;
		$new_group->slug = str_replace( ' ', '-', $group_name );
		$new_group->description = $group_name;
		$new_group->news = $group_name;
		$new_group->status = 'public';
		$new_group->is_invitation_only = 1;
		$new_group->enable_wire = 1;
		$new_group->enable_forum = 0;
		$new_group->enable_photos = 1;
		$new_group->photos_admin_only = 1;
		$new_group->date_created = current_time('mysql');
		$new_group->total_member_count = 1;
		$new_group->avatar_thumb = '';
		$new_group->avatar_full = '';

		$new_group -> save();
		$group_id = $new_group->id;
	}

	return $group_id;

}

function get_form( $html_message ) {
	?>
	<div class="wrap">
		<?php echo $html_message; ?>
		<div id="icon-users" class="icon32"><br /></div>
		<div class="bmi_meta">
			<h2><span class="bmi_tag-green">BuddyPress</span> <span class="bmi_tag-blue1">Members</span> <span class="bmi_tag-green">Import</span></h2>
		</div>
		<div class="bmi_meta">
			<span class="bmi_tag-grey">v4.5</span>
			<a target="_blank" class="bmi_tag-blue" href="http://www.youngtechleads.com/buddypress-members-import-support/">Plugin Support</a>
		</div>

		<div class="samplecsv">Sample CSV files present in folder <strong>/wp-content/plugins/buddypress-members-import/</strong></div>
		<p><strong>Select the CSV file you want to import.</strong></p>
		<form action="users.php?page=bp-members-import" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'bmi_by_ytl' ); ?>
			<input type="hidden" name="mode" value="submit" />
			<input type="file" name="csv_file" />
			<input type="submit" value="Upload Members" />
			<br/><br/>
		<p style="color: red">Make sure that you have back up your database before proceed!</p>
			<table class="bmi-options">
				<tr valign="top">
					<th scope="row">Update existing users: </th>
					<td>
						<label for="update_user">
							<input id="update_user" name="update_user" type="checkbox" value="1" />
							By checking this checkbox existing users data will be updated.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Update existing users password: </th>
					<td>
						<label for="update_password">
							<input id="update_password" name="update_password" type="checkbox" value="1" />
							By checking this checkbox existing users passowrd will be updated. Otherwise remain unchanged.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Password type: </th>
					<td>
						<label for="encrypted_password">
							<input id="encrypted_password" name="encrypted_password" type="checkbox" value="1" />
							CSV file has encrypted password.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Enable UTF8 Encoding: </th>
					<td>
						<label for="utf8_encoding">
							<input id="utf8_encoding" name="utf8_encoding" type="checkbox" value="1" />
							If you are facing language issue try this.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Notification: </th>
					<td>
						<label for="new_member_notification">
							<input id="new_member_notification" name="new_member_notification" type="checkbox" value="1" />
							Send username and password to new users.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Custom Notification: </th>
					<td>
						<label for="custom_notification">
							<input id="custom_notification" name="custom_notification" type="checkbox" value="1" />
							Send custom notification message to users.
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Upload Avatar: </th>
					<td>
						<label for="avatar">
							<input id="avatar" name="avatar" type="checkbox" value="1" />
							Upload user avatar from CSV file. You have to provide full path of the image.
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">Notice: </th>
					<td>The CSV file should be in the following format:</td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td>
					#: Fields name should be at the top line in CSV file separated by comma(,) and delimited by double quote(").<br />
					#: For multivalued field value should be separate by :: in csv file
					#: CSV file should be save in UTF8 format.
					#: You do not have any blank row in CSV file.
					</td>
				</tr>
			</table>
		</form>
		<div class="section2">
			<div class="email-template-form">
				<form action="users.php?page=bp-members-import" method="post" enctype="multipart/form-data">
					<table>
						<tr>
							<th scope="row">Email Template</th>
							<td>
								<?php
			$args = array(
				'media_buttons' => false,
				'textarea_name ' => 'email-template1',
			);
			wp_editor( get_option( 'email-template' ), 'email-template', $args );
			?>
								<br />
								<strong>
									Do not change {FIRSTNAME}, {LASTNAME}, {USERNAME}, {PASSWORD},  {LOGIN_URL}, {SITE_ADMIN}
								</strong>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<input type="submit" class="save-email-template" name="save-email-template" value="Save email template"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="otherlinks">
				<?php quick_links(); ?>
				<?php other_plugins(); ?>
			</div>
		</div>

		<div>
			<hr />
			<p><strong>WordPress default fields are as follows</strong></p>
			'user_login', 'user_pass',
			'user_email', 'user_url', 'user_nicename',
			'display_name', 'user_registered', 'first_name',
			'last_name', 'nickname', 'description',
			'rich_editing', 'comment_shortcuts', 'admin_color',
			'use_ssl', 'show_admin_bar_front', 'show_admin_bar_admin',
			'role'
			<?php if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) { ?>
			<hr />
			<p><strong>BuddyPress profile fields are as follows</strong></p>
			<?php
				global $wpdb;
				$bp_extra_fields = $wpdb->get_col( 'SELECT name FROM ' . $wpdb->prefix . 'bp_xprofile_fields' );
				echo implode( ', ', $bp_extra_fields );
			} ?>
			<?php if ( is_plugin_active( 'membership/membership.php' ) ) { ?>
			<hr />
			<p><strong>Membership plugin fields are as follows</strong></p>
			wpm_subs_id, wpm_level_id, wpm_level_order, wpm_payment_gateway, wp_membership_active
			<br />
			wp_membership_active field value can be <i>"yes"</i> or <i>"no"</i>
			<?php } ?>
			<hr />
		</div>
	</div>
	<?php
}

function other_plugins() {
	?>
	<div class="quicklinks">
		<h3>Other Useful Plugin</h3>
		<p><a href="http://www.youngtechleads.com/buddypress-members-export/" target="_blank"> BuddyPress Members Export</a></p>
		<p><a href="http://www.youngtechleads.com/wordpress-database-cleaner/" target="_blank"> WordPress Database Cleaner</a></p>
		<p><a href="http://www.youngtechleads.com/ajaxify-wordpress-site-pro/" target="_blank"> Ajaxify WordPress Site Pro</a></p>
	</div>
	<?php
}
function quick_links() {
	?>
	<div class="quicklinks other_plugins">
		<marquee direction="right" behavior="alternate"><h3>Other useful plug-in</h3></marquee>
		<p>If you do not want to send login details to your members at the time of import then you must have <a target="_blank" href="http://www.youngtechleads.com/wordpress-bulk-password-reset/">WordPress Bulk Password Reset</a> plugin which will allow you to send the login details to your members whenever you want. It also has custom email template.</p>
	</div>
	<?php
}

function send_notifiction_to_new_user( $user_id, $user_pass ) {
	wp_new_user_notification( $user_id, $user_pass, true );
}

if ( ! function_exists( 'wp_new_user_notification' ) ) {
	function wp_new_user_notification( $user_id, $plaintext_pass = '', $custom = false ) {
		global $custom_message;

		$user = new WP_User( $user_id );

		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

		$message  = sprintf( __( 'New user registration on %s:' ), get_option( 'blogname' ) ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'E-mail: %s' ), $user_email ) . "\r\n";

		if ( empty( $plaintext_pass ) )
			return;

		if ( $custom ) {
			$replace_terms = array( 
								'{FIRSTNAME}', 
								'{LASTNAME}', 
								'{USERNAME}', 
								'{PASSWORD}', 
								'{SITE_ADMIN}', 
								'{LOGIN_URL}', 
								);
			$current_data = array(
								get_user_meta( $user->ID, 'first_name', true ),
								get_user_meta( $user->ID, 'last_name', true ), 
								$user_login, 
								$plaintext_pass, 
								get_option( 'blogname' ), 
								wp_login_url(), 
							);
			$custom_message = get_option( 'email-template' );
			$message  = str_replace( $replace_terms, $current_data, $custom_message ) . "\r\n\r\n";
		} else {
			$message  = __( 'Hi there,' ) . "\r\n\r\n";
			$message .= sprintf( __( "Welcome to %s! Here's how to log in:" ), get_option( 'blogname' ) ) . "\r\n\r\n";
			$message .= wp_login_url() . "\r\n";
			$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n";
			$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n\r\n";
			$message .= sprintf( __( 'If you have any problems, please contact me at %s.' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
			$message .= __( 'Adios!' );
		}

		wp_mail(
			$user_email,
			sprintf( __( '[%s] Your login details' ), get_option( 'blogname' ) ),
			$message
		);
	}
}


add_filter( 'wp_mail_content_type', 'bmi_set_content_type', 100 );
function bmi_set_content_type( $content_type ) {
	return 'text/html';
}
