<?php
/**
 * Update the Auth0 data
 * parameters -
 *  $user_id (int) WP user ID of user to update
 *  $dataToUpdate (array) array of fields to update on auth0. valid keys are:
  * first_name, last_name, picture,  membership_level
 */
function auth0_user_update( $user_id = '', $dataToUpdate = array() ) {
  //exit function if user id or dataToUpdate are not set
  if($user_id=='' || empty($dataToUpdate) ){
    return;
  }

  $update_data = array();

  //loop through the $dataToUpdate array to set which variables are getting updated
  foreach($dataToUpdate as $key=>$value){
    $update_data['user_metadata'][$key] = $value;
  }

  //only call auth0 to update if there is something to update
  if(!empty($update_data['user_metadata'])){
    //call Auth0 to get access token
    $post_data = array("client_id" => AUTH0_CLIENTID, 
                       "client_secret" => AUTH0_SECRET, 
                       "audience" => "https://makermedia.auth0.com/api/v2/", 
                       "grant_type" => "client_credentials");
    $response = postCurl("https://makermedia.auth0.com/oauth/token", array("content-type: application/json"), json_encode($post_data));

    //the response has the access token used to update the user metadata
    $json_response = json_decode($response);
    $access_token = $json_response->access_token;

    //get the auth0 id from the wp user meta
  	$auth0UserID = get_user_meta($user_id, 'wp_auth0_id');

    if(isset($auth0UserID[0]) && $auth0UserID[0] != ''){
        $url = "https://makermedia.auth0.com/api/v2/users/" . $auth0UserID[0]; //update user
        $curl_type = "PATCH";
    }else{
      //if the auth0 id is not set, let's attempt to add them to auth0
      //get user information
      $user_info = get_userdata($user_id);

      //add user
      $update_data["email"] = $user_info->user_email;
      if($user_info->user_firstname!='')
        $update_data["given_name"] = $user_info->user_firstname;
      if($user_info->user_lastname!='')
        $update_data["family_name"] = $user_info->user_lastname;
      $update_data["password"] = "ResetMe12!@";
      $update_data["connection"] = "DB-Make-Community";
      $url = "https://makermedia.auth0.com/api/v2/users";
      $curl_type = "POST";
    }

    //update auth0
    $headers = array("authorization: Bearer ".$access_token, "content-type: application/json");
    $authRes = postCurl($url, $headers, json_encode($update_data),$curl_type);

  }
}


/*    Update First and Last Name on Auth0
 *
 *      Parameters
 * $value - (int) Displayed user ID.
 * $posted_field_ids - (array) Array of field IDs that were edited.
 * $errors - (bool) Whether or not any errors occurred.
 * $old_values - (array) Array of original values before updated.
 * $new_values - (array) Array of newly saved values after update.
 *
*/
function user_profile_data_updated($user_id, $posted_field_ids, $errors, $old_values, $new_values){
  //If user has update their first name or last name, we need to update auth0
  $dataToUpdate = array();
  //check if the first name (field 1) has changed
  if($new_values[1]['value'] != $old_values[1]['value']) {
    $dataToUpdate['first_name'] = $new_values[1]['value'];
  }
  //check if the last name (field 635) has changed
  if($new_values[635]['value'] != $old_values[635]['value']) {
    $dataToUpdate['last_name'] = $new_values[635]['value'];
  }
  if(!empty($dataToUpdate)){
    auth0_user_update($user_id, $dataToUpdate);
  }
}
add_action( 'xprofile_updated_profile', 'user_profile_data_updated', 1, 5 );

/*    Update Avatar on Auth0
 *
 *      Parameters
 * @param string $user_id     Inform about the user id the avatar was set for.
 * @param string $type        Inform about the way the avatar was set ('camera').
 * @param array  $avatar_data Array of parameters passed to the avatar handler.
 *
*/
function user_avatar_updated ($user_id, $type, $avatar_data ){
  //this function only returns the full avatar, we want to update auth0 with the thumb avatar
  $dataToUpdate['picture']=bp_core_fetch_avatar (array(  'item_id' => $user_id, 'object'=>'user','type'    => 'thumb','html'   => FALSE));
  auth0_user_update($user_id, $dataToUpdate);

  return;
}
add_action('xprofile_avatar_uploaded','user_avatar_updated',10,3);

/*    Membership Type updates to Auth0
 *
 *  We will update membership type on Auth0:
 *     - when a new transaction comes in
 *     - when a transaction expires and the subscription is not active (ie they renewed)
 *     - when a transaction is deleted
 */

//Called after completed payment (free and paid)
function mepr_pymnt_complete($txn) {
  $user = $txn->user();
  $dataToUpdate = array('membership_level' => checkMakeCoMems($user));
  auth0_user_update($user->ID, $dataToUpdate);
}
add_action('mepr-txn-status-complete', 'mepr_pymnt_complete');

//deleted transactions
function mepr_post_delete_transaction_fn($id, $user, $result) {
  $dataToUpdate= array('membership_level'=> checkMakeCoMems($user));
  auth0_user_update($user->ID, $dataToUpdate);
}
add_action('mepr_post_delete_transaction', 'mepr_post_delete_transaction_fn', 3, 10);

/*      Capture a Transaction expired event
    if the subscription is no longer valid, update auth0
    BE CAREFUL WITH THIS ONE - This could be a prior recurring transaction that has expired */
function mepr_capture_expired_transaction($event) {
  $updateAuth0=true;

  //get user
  $transaction = $event->get_data();
  $user = $transaction->user();

  //user found?
  if($user){
    $subscription = $transaction->subscription(); //This may return false if it's a one-time transaction that has expired
    // if the $subscription exists and the $subscription->status is 'active'
    if($subscription && $subscription->status == 'active'){
      // if so, then it's possible the user is not really expired on it
      if(isset($transaction->product_id) && $user->is_already_subscribed_to($transaction->product_id)){
        //user is still subscribed, no update
        $updateAuth0=false;
      }
    }

    if($updateAuth0){
      $user = $transaction->user();
      $dataToUpdate = array('membership_level' => checkMakeCoMems($user));
      auth0_user_update($user->ID, $dataToUpdate);
    }
  }

  return;
}
add_action('mepr-event-transaction-expired', 'mepr_capture_expired_transaction');
