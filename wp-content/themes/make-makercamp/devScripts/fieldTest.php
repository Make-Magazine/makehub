<?php
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo 'starting<br/>';

echo 'gf addon loop<br/>';
  $form = GFAPI::get_form(2);

  //gf-addon Loop
  $field = GFAPI::get_field($form, 10 );
  //echo 'steps field<br/>';
  //var_dump($field);
  //echo '<br/>';
  $nested_fieldlist = $field->gpnfFields;
  $nestedFormID = $field->gpnfForm;
  $nestedForm = GFAPI::get_form($nestedFormID);

  $field_value = "106,107";
  //is this a nested form?
  if($field->type=='form'){
    //transform comma separated list of entry ids into array
    $entry_array = explode(",", $field_value);

    //reset field_value
    $field_value = array();
    foreach($entry_array as $entry_id){
      $entry_id = trim($entry_id);
      echo '$entry_id='.$entry_id.'<br/>';

      //pull the entry from the nested form
      $nestedEntry = GFAPI::get_entry($entry_id);

      $row_array = array();
      foreach($nested_fieldlist as $fieldID){
        $row_array[$fieldID] = $nestedEntry[$fieldID];        
      }
      $field_value[] = $row_array;
    }

    $field_value = json_encode($field_value);
  }
  var_dump($field_value);
die('stop here');
  echo 'GFtocpt field loop<br/>';
  $post_id = 8716;
  $meta_key = 'field_57686cfe1bd0b';
  $meta_value = '[{"1":"step 1","2":"https:\/\/makercamp.makehub.local\/wp-content\/uploads\/sites\/7\/gravity_forms\/6-5f4ca057c2e8eda3bd0d5d27bab9d28e\/2022\/05\/maker2b8.jpg","3":"https:\/\/makercamp.makehub.local\/wp-content\/uploads\/sites\/7\/gravity_forms\/6-5f4ca057c2e8eda3bd0d5d27bab9d28e\/2022\/05\/148.png","4":"step 1 description"},{"1":"step 2","2":"https:\/\/makercamp.makehub.local\/wp-content\/uploads\/sites\/7\/gravity_forms\/6-5f4ca057c2e8eda3bd0d5d27bab9d28e\/2022\/05\/137.png","3":"https:\/\/makercamp.makehub.local\/wp-content\/uploads\/sites\/7\/gravity_forms\/6-5f4ca057c2e8eda3bd0d5d27bab9d28e\/2022\/05\/127.png","4":"step 2 description"}]';
  $field = get_field_object($meta_key);

  //check if this is an ACF Field
  if($field){
    //update the meta key to the correct field name
    $meta_key = $field['name'];

    //is this a repeater field?
    if(isset($field["type"]) && $field["type"]=='repeater'){
      //note - this is assuming you are setting the input with a list field
      if(count($field["sub_fields"])==1){
        echo 'single sub field';
        /*  when the repeater only has one field we are expecting the
            input from GF to be a comma separated string							*/

        //convert comma delimited field to an array
        $meta_array = explode(", ", $meta_value);
        $value = array();
        //loop through the submitted data to format it in an array for ACF
        foreach($meta_array as $arr_value){
          //value is set with subfield name
          $value[] = array($field["sub_fields"][0]['name'] => $arr_value);
        }

      }else{
        /*  when the repeater has multiple fields we are expecting the
            input from GF to be a json encoded string */
        echo 'multiple sub field';
        //convert json encoded field to an array
        $meta_value = json_decode($meta_value);
        $value = array();
        if(is_array($meta_value)){
          $sub_field_array = array();
          //build an array with the sub field names
          foreach($field["sub_fields"] as $sub_field){
            $sub_field_array[] = $sub_field['name'];
          }

          //loop through the returned value to populate the ACF array
          foreach($meta_value as $metaSubRow){ //
            $count=0;
            $sub_array = array();
            foreach($metaSubRow as $metaSubField){
              $sub_array[$sub_field_array[$count]]=$metaSubField;
              $count++;
            }
            $value[] = $sub_array;
          }
        }
      }

      echo '<br/>value to update<br/>';
      var_dump($value);

      //update the ACF field and then continue to next meta_field
      update_field($field['key'], $value, $post_id);
      //continue;
    }
  }
  echo '<br/>ending<br/>';
