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

add_action('gform_after_update_entry', 'gftocpt_update_post', 10, 2); //via the entry detail page.
function gftocpt_update_post( $form, $entry_id ) {
  gv_status_change($entry_id, 'adminEditNoChange');
}

function gv_status_change($entry_id, $statusToUpdate) {
	//pull the associated entry for this entry id
	$entry = GFAPI::get_entry($entry_id);

  // Get post IDs associated with this entry id
	$created_posts = gform_get_meta( $entry_id, 'gravityformsgftocpt_post_id' );
  //if no posts are found, exit this function
  if(empty($created_posts)){
    return;
  }

  //pull all gf to cpt feeds for this form
	$result = GFAPI::get_feeds( null, $entry['form_id'], 'gravityformsgftocpt' );

  //if no GF to CPT feeds are found for this form, exit this function
  if(empty($result)){
    return;
  }

	//create an array of the status to update, keyed on feed order
	foreach($result as $feed){
		if($feed["is_active"]){
      // Loop through created posts.
      foreach ( $created_posts as $post_info ) {
        $post_id = absint( rgar( $post_info, 'post_id' ) ); // Get post ID.

        //if this is an update, we need to update the associated post
        if($statusToUpdate == 'statAfterEdit' || $statusToUpdate == 'adminEditNoChange'){
          //$form = GFAPI::get_form($entry['form_id']);

          //gf_gftocpt()->update_post( $post_id, $feed, $entry, $form );
          if($statusToUpdate = 'adminEditNoChange'){
            $statusToUpdate = 'no-change'; //if this is an admin edit, do not change the status
          }
        }

        //loop through the feeds and update status of the post
        if(isset($feed['meta'][$statusToUpdate]) && $feed['meta'][$statusToUpdate] != 'no-change'){
          $data = array('ID' => $post_id,'post_status' => $feed['meta'][$statusToUpdate]);
          wp_update_post( $data );
        }
      }
		}
	}
}

add_action('gravityview/delete-entry/deleted','gftocpt_delete_entry', 10, 1 );
add_action('gravityview/delete-entry/trashed','gftocpt_delete_entry', 10, 1 );
add_action('gform_delete_entry', 'gftocpt_delete_entry', 10, 1 );

//when the entry is deleted, we need to ensure the associated
function gftocpt_delete_entry($entry_id){
  //mark entry as unapproved
  gform_update_meta( $entry_id, GravityView_Entry_Approval::meta_key,
          GravityView_Entry_Approval_Status::UNAPPROVED);

  // Get post IDs associated with this entry id
	$created_posts = gform_get_meta( $entry_id, 'gravityformsgftocpt_post_id' );

  //if no posts are found, exit this function
  if(empty($created_posts)){
    return;
  }

  //trash entry
  // Loop through created posts.
  foreach ( $created_posts as $post_info ) {
    $post_id = absint( rgar( $post_info, 'post_id' ) ); // Get post ID.
    $data = array('ID' => $post_id,'post_status' => 'trash');
    wp_update_post( $data );
  }
}
