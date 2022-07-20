<?php
//Gravity View approve Entry
add_action('gravityview/approve_entries/approved', 'gftocpt_gv_entry_approve', 1, 3);
function gftocpt_gv_entry_approve($entry_id) {
  gv_status_change($entry_id, 'statAfterApp');
}

//gravity view - set entry to unapproved
add_action('gravityview/approve_entries/unapproved', 'gftocpt_gv_entry_unapprove', 10, 1);
function gftocpt_gv_entry_unapprove($entry_id) {
  gv_status_change($entry_id, 'statAfterRes');
}

//gravity view - set entry to disapproved/declined
add_action('gravityview/approve_entries/disapproved', 'gftocpt_gv_entry_disapprov', 10, 1);
function gftocpt_gv_entry_disapprov($entry_id) {
  gv_status_change($entry_id, 'statAfterRej');
}

//user update entry in gravity view
add_action( 'gravityview/edit_entry/after_update', 'gftocpt_gv_update_entry', 10, 2 );
function gftocpt_gv_update_entry( $form, $entry_id ) {
  gv_status_change($entry_id, 'statAfterEdit');
}

function gv_status_change($entry_id, $statusToUpdate) {
	//pull the associated entry for this entry id
	$entry = GFAPI::get_entry($entry_id);

  // Get post IDs associated with this entry id
	$created_posts = gform_get_meta( $entry_id, 'gravityformsgftocpt_post_id' );
  //if no posts are found, exit this function
  if(is_empty($created_posts)){
    return;
  }

  //pull all gf to cpt feeds for this form
	$result = GFAPI::get_feeds( null, $entry['form_id'], 'gravityformsgftocpt' );

  //if no GF to CPT feeds are found for this form, exit this function
  if(is_empty($result)){
    return;
  }

	//create an array of the status to update, keyed on feed order
	$gv_array = array();
	foreach($result as $feed){
		if($feed["is_active"]){
			if(isset($feed['meta'][$statusToUpdate]) && $feed['meta'][$statusToUpdate] != 'no-change'){
				$gv_array[$feed['feed_order']] = $feed['meta'][$statusToUpdate];
			}
		}
	}

  //sort feed by feed order
  ksort($gv_array);

	// Loop through created posts.
	foreach ( $created_posts as $post_info ) {
		// Get post ID.
		$post_id = absint( rgar( $post_info, 'post_id' ) );

		//loop through the feeds and update status of the post
		foreach($gv_array as $status){
			$data = array('ID' => $post_id,'post_status'=>$status);
			wp_update_post( $data );
		}
	}
}
