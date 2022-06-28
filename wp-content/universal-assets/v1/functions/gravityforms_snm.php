<?php
add_action('gform_entry_created', 'snm_automation', 10, 2);
//add_action('gform_after_update_entry', 'pre_makerspace_to_community', 10, 2); //via the entry detail page.
//add_action( 'gravityview/edit_entry/after_update', 'gravityview_trigger_feeds', 10, 3 );

//new SNM (Science Near Me) form submitted
function snm_automation($entry, $form) {
  $form_type = rgar($form, 'form_type');

  //the following fields must be in an array format:
  $arr_fields = array('start_datetimes', 'end_datetimes', 'opp_venue', 'opp_topics',
                      'opp_descriptor', 'tags', 'languages', 'opp_hashtags','opp_social_handles');

  if(isset($form_type) && $form_type == 'SNM'){
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
  }else{
    return;
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

  //format the data to prepare to send to SNM
  $dataToSend = json_encode($postFields);

  //Send to Science Near Me

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
    $token = $authRes->token;

    $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
    $headers = array("authorization: Bearer ".$token ,"content-type: application/json");

    $authRes  = json_decode(postCurl($url, $headers, $dataToSend));
    if(isset($authRes->accepted) && $authRes->accepted){
      // Write UID and slug to the entry so users can access and update
      gform_update_meta( $entry_id, 'snm_uid', $authRes->uid );
      gform_update_meta( $entry_id, 'snm_slug', $authRes->slug );
      echo 'The UID for this is '.$authRes->uid.'<br/>';
      echo 'The slug for this is '.$authRes->slug;

    }else{
      var_dump($authRes);
    }
  }else{
    echo 'no token returned<br/>';
    var_dump($authRes);
  }
  die('you die now');
}
 ?>
