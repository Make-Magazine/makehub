<?php
include '../../../../wp-load.php';
/*
 * This script is used to make existing users members if they don't have another member type
 */
 
 function members_membertypes() {
	$members =  get_users( 'blog_id=1&fields=ID' );
	$query_args = array(
		  'meta_key' => 'registryoptout', 
		  'meta_value' => 'a:1:{i:0;s:3:"Yes";}',
		  'fields' => 'ID'
	  );
	$excluded = get_users($query_args);
	$countedMembers = array_diff($members, $excluded);
	foreach ( $countedMembers as $user_id ) {
		bp_set_member_type( $user_id, '' );
		$user_meta = get_user_meta($user_id);
		// Later might want to add these two to make sure that our count doesn't include people with no level && isset($user_meta['ihc_user_levels'][0])
		if ( !bp_get_member_type($user_id) ) {
			bp_set_member_type( $user_id, 'member' );
		}
	}
	error_log(print_r($countedMembers, TRUE));
}

if($_GET['activate'] == "true") {
	echo("Running the script to turn all non-assigned users to membertype=Member");
	members_membertypes();
}