<?php
//duplicate entry
add_action('acf/update_value', 'update_people_info', 10, 4);

function update_people_info($value, $post_id, $field, $original) {    
    $personID = $post_id;    
    if($field['name']=='first_name'|| $field['name']=='last_name'){
        $person = EEM_Person::instance()->get_one_by_ID($post_id);
        $fname = $person->fname();
        $lname = $person->lname();
    }
    
    $update_people_info = false;
    switch ($field['name']){
        case 'facilitator_image':
            set_post_thumbnail( $post_id, $value );
            break;
        case 'facilitator_info':
            $person_values = array("PER_bio" => $value);
            $updatePerson = true;
            break;        
        case 'first_name':            
            $person_values = array("PER_fname" => $value, "PER_full_name" => $value . ' ' . $lname);
            $updatePerson = true;
            break;
        case 'last_name':
            $person_values = array("PER_lname" => $value, "PER_full_name" => $fname . ' ' . $value);
            $updatePerson = true;
            break;
    }
    
    if($updatePerson){                
       $success = EEM_Person::instance()->update_by_ID($person_values, $personID);
    }

    return $value;    
}