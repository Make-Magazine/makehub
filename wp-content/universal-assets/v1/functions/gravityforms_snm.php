<?php
//Gravity View approve Entry
add_action('gravityview/approve_entries/approved', 'snm_automation', 1, 3);

/* This function is used to create a record on Science Near Me */
function snm_automation($entry_id) {
  //pull the associated entry and form
  $entry = GFAPI::get_entry($entry_id);
  $form = GFAPI::get_form($entry['form_id']);

  $form_type = rgar($form, 'form_type');

  //only continue if this is a SNM (Science Near Me) form
  if(isset($form_type) && $form_type == 'SNM'){
    //check if this entry has previously been submitted to SNM
    $snm_uid = gform_get_meta( $entry_id, 'snm_uid' );
    if($snm_uid!=''){
      //update the SNM to show
      update_snm_data($snm_uid,array('withdrawn'=>FALSE));
      return;
    }else{
      //add info to SNM
      $postFields = pullSNMinfo($entry, $form);

      //format the data to prepare to send to SNM
      $dataToSend = json_encode($postFields);

      add_snm_data($dataToSend, $entry_id);
    }
  }
  return;
}

//gravity view - set entry to unapproved or declined
add_action('gravityview/approve_entries/disapproved', 'make_update_snm', 1, 10);
add_action('gravityview/approve_entries/unapproved', 'make_update_snm', 1, 10);

/* This function will withdrawl a record from science near me */
function make_update_snm($entry_id) {
  //pull the associated entry and form
  $entry = GFAPI::get_entry($entry_id);
  $form = GFAPI::get_form($entry['form_id']);
  $form_type = rgar($form, 'form_type');

  //only continue if this is a SNM (Science Near Me) form
  if(isset($form_type) && $form_type == 'SNM'){
    $snm_uid = gform_get_meta( $entry_id, 'snm_uid' );
    if($snm_uid!=''){
      update_snm_data($snm_uid,array('withdrawn'=>TRUE));
    }
  }
}

//update entry in gravity view or in gravity forms admin entry
add_action('gform_after_update_entry', 'pre_makerspace_to_community', 10, 2); //via the entry detail page.
add_action( 'gravityview/edit_entry/after_update', 'gravityview_trigger_feeds', 10, 3 );
function update_entry( $form, $entry_id ) {
  $form_type = rgar($form, 'form_type');

  //only continue if this is a SNM (Science Near Me) form
  if(isset($form_type) && $form_type == 'SNM'){
    //format the data to prepare to send to SNM
    $postFields = pullSNMinfo($entry, $form);
    $dataToSend = json_encode($postFields);

    $snm_uid = gform_get_meta( $entry_id, 'snm_uid' );

    // If the entry is approved we will have a UID to update
    if($snm_uid!=''){
      update_snm_data($snm_uid,array('withdrawn'=>TRUE));
    }
  }
  return;
}

/* This function is used to retrieve an authorization token from Science Near Me */
function ret_SNM_token(){
  //First do the authentication to get a token
  $url = "https://beta.sciencenearme.org/api/v1/partner/authorize";

  $post_data = '{
    "uid": "b75f265a-4107-5e6b-bd5d-1b03d51c51fa",
    "secret": "KDiwBOFaZlLkduvsmVFdAdNjOY4dFRcz"
  }';
  $headers = array("content-type: application/json");
  $authRes = json_decode(postCurl($url, $headers, $post_data));

  //did we get a token?
  if(isset($authRes->token)){
    return $authRes->token;
  }else{
    return '';
  }
}

/* This function is used to update information on Science Near Me */
function update_snm_data($snm_uid,$dataToUpdate){
  //get an authorization token
  $token = ret_SNM_token();

  if($token!=''){
    //retrieve the full listing
    $url = "https://beta.sciencenearme.org/api/v1/opportunity/".$snm_uid;
    $headers = array("authorization: Bearer ".$token ,"content-type: application/json");
    $snm_data = json_decode(basicCurl($url,$headers),true);
    if(isset($snm_data['error'])){
      error_log('error returned attempting to retrieve data from "'.$url.'"');
      error_log(print_r($snm_data,true));
      return;
    }

    if($snm_data){
      foreach($dataToUpdate as $key=>$data){
        $snm_data[$key]=$data;
      }
      $dataToSend = json_encode($snm_data);

      $headers = array("authorization: Bearer ".$token ,"content-type: application/json");
      $authRes  = json_decode(postCurl($url, $headers, $dataToSend,'PUT'),true);
      if(isset($authRes['error'])){
        error_log('error updating SNM record "'.$url.'"');
        error_log(print_r($snm_data,true));
        return;
      }
    }

  }else{
    error_log('no token returned from SNM');
    error_log(print_r($authRes,TRUE));
  }
}

/* This function is used to add information on Science Near Me and to update the meta information */
function add_snm_data($dataToAdd, $entry_id){
  //Send to Science Near Me
  $token = ret_SNM_token();

  //did we get a token?
  if($token!=''){
    $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
    $headers = array("authorization: Bearer ".$token ,"content-type: application/json");

    $authRes  = json_decode(postCurl($url, $headers, $dataToSend));
    if(isset($authRes->accepted) && $authRes->accepted){
      // Write UID and slug to the entry so users can access and update
      gform_update_meta( $entry_id, 'snm_uid', $authRes->uid );
      gform_update_meta( $entry_id, 'snm_slug', $authRes->slug );
    }else{
      error_log('Error in posting new entry to SNM');
      error_log(print_r($authRes,TRUE));
    }
  }else{
    error_log('no token returned from SNM');
    error_log(print_r($authRes,TRUE));
  }
  return;
}

function pullSNMinfo($entry, $form){
  $postFields = array();
  //the following fields must be in an array format:
  $arr_fields = array('start_datetimes', 'end_datetimes', 'opp_venue', 'opp_topics',
                      'opp_descriptor', 'tags', 'languages', 'opp_hashtags','opp_social_handles');

  $snmFieldArr = array();

  //loop through the form fields to find any set SNM fields
  foreach($form['fields'] as $field){
    if(isset($field['snmInput']) && $field['snmInput']!=''){
      $inputs=array();
      if($field['type']=='checkbox'){
        foreach($field['inputs'] as $input){
          $inputs[]=$input["id"];
        }
      }
      $snmFieldArr[$field['id']] = array('type'=>$field['type'],'snm_field'=>$field['snmInput'],'inputs'=>$inputs);
    }
  }

  //now loop through the set snm fields to prepare the SNM API feed
  if(!empty($snmFieldArr)){
    //default data
    $postFields = array("partner_name"      =>  "Maker Camp",
                        "partner_website"   =>  "https://makercamp.make.co",
                        "opp_descriptor"    =>  array("camp"),
                        "pes_domain"        => "maker");
    foreach($snmFieldArr as $fieldID=>$snmField){
      switch ($snmField['type']) {
        case 'text':
        case 'textarea':
        case 'website':
        case 'email':
        case 'phone':
        case 'select':
        case 'radio':
        case 'fileupload':
          //if the field is not blank, lets pass it to snm
          if($entry[$fieldID]!=''){
            //check if the SNM field needs to be an array or text
            if(in_array($snmField['snm_field'],$arr_fields)){
              $postFields[$snmField['snm_field']] = explode(",", $entry[$fieldID]);
            }else{
              $postFields[$snmField['snm_field']] = $entry[$fieldID];
            }
          }
          break;
        case 'number':
          //if the field is not blank, lets pass it to snm
          if($entry[$fieldID]!=''){
            //cast the field as an integer
            $postFields[$snmField['snm_field']] = (int) $entry[$fieldID];
          }
            break;
        //name fields are formatted as first name = fieldID.3 and last name = fieldID.6
        case 'name':
          //check if the SNM field needs to be an array or text
          if(in_array($snmField['snm_field'],$arr_fields)){
            $postFields[$snmField['snm_field']][] = $entry[$fieldID.'.3'].' '.$entry[$fieldID.'.6'];
          }else{
            $postFields[$snmField['snm_field']] = $entry[$fieldID.'.3'].' '.$entry[$fieldID.'.6'];
          }
          break;
        case 'checkbox':
          if(isset($snmField['inputs'])){
            $checkbox_values = array();
            //loop through all valid input id's for this field
            foreach($snmField['inputs'] as $input_id){
              //if the field is not blank in the entry, pass it to SNM
              if(isset($entry[$input_id]) && $entry[$input_id]!=''){
                $checkbox_values[]=$entry[$input_id];
              }
            }

            //check if the SNM field needs to be an array or text
            if(in_array($snmField['snm_field'],$arr_fields)){
              $postFields[$snmField['snm_field']] = $checkbox_values;
            }else{
              $postFields[$snmField['snm_field']] = implode(", ", $checkbox_values);
            }
          }
          break;
        case 'list':
          $listArr = unserialize($entry[$fieldID]);

          //this is a comma delimited object
          //example: "opp_social_handles":{"facebook":"kaleidoscopesci", "twitter":"KaleidoscopeSci", "instagram":"kaleidoscopesci", "youtube":"UCNEA2TdAFYzrghjKC_N03xQ"},
          if($snmField['snm_field']=='opp_social_handles'){
            $social_array = array();
            foreach($listArr as $listRow){

              $social_array[strtolower($listRow['Platform'])] = $listRow['Handle'];
            }

            $postFields[$snmField['snm_field']] = $social_array;
          }
          break;
      }
    }
  }

  //set boolean fields to true or false
  $boolFields = array('is_online', 'ticket_required', 'has_end');
  foreach($boolFields as $bfield){
    if(isset($postFields[$bfield])){
      if($postFields[$bfield]=='true'){
          $postFields[$bfield] = true;
      }else{
          $postFields[$bfield] = false;
      }
    }
  }
  return $postFields;
}

//Return the SNM URL
add_filter( 'gform_replace_merge_tags', 'replace_download_link', 10, 7 );
function replace_download_link( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
    $custom_merge_tag = '{snm_link}';
    if ( strpos( $text, $custom_merge_tag ) === false ) {
        return $text;
    }
error_log('i am here');
    if($form['form_type']=='SNM'){
      $snm_slug = gform_get_meta( $entry['id'], 'snm_slug' );
      $snm_link = '<a href="https://sciencenearme.org/'.$snm_slug.'" target="_none">https://sciencenearme.org/'.$snm_slug.'</a>';
      $text = str_replace( $custom_merge_tag, $snm_link, $text );
    }

    return $text;
}
 ?>
