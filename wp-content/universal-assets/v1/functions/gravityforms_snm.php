<?php
add_action('gform_entry_created', 'snm_automation', 10, 2);
//add_action('gform_after_update_entry', 'pre_makerspace_to_community', 10, 2); //via the entry detail page.
//add_action( 'gravityview/edit_entry/after_update', 'gravityview_trigger_feeds', 10, 3 );

//new SNM (Science Near Me) form submitted
function snm_automation($entry, $form) {
  $form_type = rgar($form, 'form_type');
  echo 'form type is '.$form_type.'<br/>';

  //the following fields must be in an array format:
  $arr_fields = array('start_datetimes', 'end_datetimes', 'opp_venue', 'opp_topics',
                      'opp_descriptor', 'tags', 'languages', 'opp_hashtags','opp_social_handles');
  $obj_fields = array('opp_social_handles'); //{"twitter": "@SciStarter"}
// true/false - is_online, ticket_required, has_end

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
      $postFields = array();
      foreach($snmFieldArr as $fieldID=>$snmField){
        switch ($snmField['type']) {
          case 'text':
          case 'textarea':
          case 'website':
          case 'email':
          case 'number':
          case 'phone':
          case 'select':
          case 'radio':
          case 'fileupload':
            //if the field is not blank, lets pass it to snm
            if($entry[$fieldID]!=''){
              //check if the SNM field needs to be an array or text
              if(in_array($snmField['snm_field'],$arr_fields)){
                $postFields[$snmField['snm_field']][] = $entry[$fieldID];
              }else{
                $postFields[$snmField['snm_field']] = $entry[$fieldID];
              }
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
                $postFields[$snmField['snm_field']][] = implode(", ", $checkbox_values);
              }else{
                $postFields[$snmField['snm_field']] = implode(", ", $checkbox_values);
              }
            }
            break;
          case 'list':
            $listArr = unserialize($entry[$fieldID]);
            if($snmField['snm_field']=='opp_social_handles'){
              foreach($listArr as $listRow){
                $postFields[$snmField['snm_field']][] = array(strtolower($listRow['Platform'])=>$listRow['Handle']);
              }
            }
            break;
        }
      }
      //opp_social_handles needs to be type casted to an object
      if(isset($postFields['opp_social_handles'])){
        $postFields['opp_social_handles'] = (object) $postFields['opp_social_handles'];
      }
      $post_data = array($postFields);
    }
  }else{
    return;
  }

  //format the data to prepare to send to SNM
  $dataToSend = json_encode($post_data);
  echo 'SNM output<br/>';
  var_dump($dataToSend);
  die('you die now');
  $url = "https://beta.sciencenearme.org/api/v1/opportunity/";
  $authRes  = curlCall($url, $dataToSend, $token);
  if(isset($authRes->accepted) && $authRes->accepted){
    echo 'The UID for this is '.$authRes->uid.'<br/>';
    echo 'The slug for this is '.$authRes->slug;
  }else{
    var_dump($authRes);
  }
}
 ?>
