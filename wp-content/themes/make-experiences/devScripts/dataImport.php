<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>

        <h2>Import Form entries</h2>
        <form method="post" enctype="multipart/form-data">
            Select File to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload" name="submit">
        </form>

        <ul>
            <li>Note: File format should be CSV</li>
            <li>Row 1: Field ID's</li>
            <li>Row 2: Start of Data</li>            
        </ul>
    </body>
</html>

<?php
include 'db_connect.php';
ini_set("auto_detect_line_endings", "1");

//$success = GFAPI::update_entry_field( $entry_id, $input_id, $value );
if (isset($_POST["submit"])) {
    echo 'start process';
    $csv = [];
    if (isset($_FILES["fileToUpload"])) {
        //if there was an error uploading the file
        if ($_FILES["fileToUpload"]["error"] > 0) {
            echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";
        } else {
            //save the file
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir("uploads/", 0777);
            }
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]) . date('dmyhi');

            $name = $_FILES['fileToUpload']['name'];
            $nameArr = explode('.', $name);

            $ext = strtolower(end($nameArr));

            $type = $_FILES['fileToUpload']['type'];
            $tmpName = $_FILES['fileToUpload']['tmp_name'];

            //Print File Details
            echo "Upload: " . $name . "<br />";
            echo "Type: " . $type . "<br />";
            echo "Size: " . ($_FILES["fileToUpload"]["size"] / 1024) . " Kb<br />";
            echo "Temp file: " . $tmpName . "<br />";

            //Save file to server
            //if file already exists
            $savedFile = "/dataUpload/upload/" . $name;
            $savedFile = $target_file;
            if (file_exists($savedFile)) {
                echo $name . " already exists. ";
            } else {
                if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
                    //Store file in directory
                    if (move_uploaded_file($tmpName, $savedFile)) {
                        echo "Stored in: " . $savedFile . "<br />";
                    } else {
                        echo "Not uploaded<br/>";
                    }
                }
            }

            if (($handle = fopen($savedFile, 'r')) !== FALSE) {
                // necessary if a large csv file
                set_time_limit(0);
                $row = 0;
                while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
                    // number of fields in the csv
                    foreach ($data as $value) {
                        $csv[$row][] = trim($value);
                    }
                    // inc the row
                    $row++;
                }
                fclose($handle);
            }
        }
    } else {
        echo "No file selected <br />";
    }

    //row 0 contains field id's    
    $fieldIDs = $csv[0];

    unset($csv[0]);
    $tableData = [];
    $APIdata = [];
    $catArray = [];

    foreach ($csv as $row) {
        $entry = array('form_id' => 7, 'status' => 'active');
        //Create the Parent Entry 
        foreach ($row as $rowKey => $rowData) {
            if ($fieldIDs[$rowKey] == 'created_by')
                $created_by = $rowData;
            if ($fieldIDs[$rowKey] == 'date_created')
                $date_created = $rowData;
            if ($fieldIDs[$rowKey] == 'source_url')
                $source_url = $rowData;
            if ($fieldIDs[$rowKey] == 'user_agent')
                $user_agent = $rowData;
            if ($fieldIDs[$rowKey] == 'ip')
                $ip = $rowData;

            if ($fieldIDs[$rowKey] != '' && $rowData != '') {
                $pos = strpos($fieldIDs[$rowKey], 'NF-');
                if ($pos !== false) {
                    //build nested form here                    
                    $nst_fieldId = str_replace('NF-', '', $fieldIDs[$rowKey]);
                    $nstArray[$nst_fieldId] = htmlentities($rowData);
                } else {
                    $entry[$fieldIDs[$rowKey]] = htmlentities($rowData);
                }
            }
        }
        $entryId = gfapi::add_entry($entry);

        //Create the Child/nested Entry
        $nstEntry = array('form_id' => 10, 'status' => 'active', 'created_by' => $created_by,
            GPNF_Entry::ENTRY_PARENT_KEY => $entryId, // The ID of the parent entry.
            GPNF_Entry::ENTRY_PARENT_FORM_KEY => 7, // The ID of the parent form.
            GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY => 156, // The ID of the Nested Form field on the parent form
            '4' => $nstArray[4], '5' => $nstArray[5], '12' => $nstArray[12],
            '13' => $nstArray[13], '15' => $nstArray[15], '16' => $nstArray[16],
        );

        //loop through the nested array to build the nested entry
        if ($nstArray['oneTime'] == 'yes' || $nstArray['oneTime'] == 'yes') {
            $preferedSchedule = array(array('Date' => $nstArray['9.1'], "Start Time" => $nstArray['9.2'], "End Time" => $nstArray['9.3']));
            $nstEntry['9'] = serialize($preferedSchedule);
        } else {
            $preferedSchedule = array();

            switch ($nstArray['recurring']) {
                case 'Every Week':
                    $step = "+7 day";
                    break;
                case 'Every Month':
                    $step = "+1 month";
                    break;
                case 'Every Day':
                    $step = "+1 day";
                    break;
            }

            $date = strtotime($nstArray['9.1']);
            //loop until you are at or greater than the preferredEndDate
            while ($date <= strtotime($nstArray['preferredEndDate'])) {
                $preferedSchedule[] = array('Date' => date('m/d/Y', $date), "Start Time" => $nstArray['9.2'], "End Time" => $nstArray['9.3']);
                $date = strtotime($step, $date);
            }

            $nstEntry['9'] = serialize($preferedSchedule);
        }

        //set alt schedule
        $altSchedule = array(array('Date' => $nstArray['11.1'], "Start Time" => $nstArray['11.2'], "End Time" => $nstArray['11.3']));
        $nstEntry['11'] = serialize($altSchedule);
        $nstentry_id = gfapi::add_entry($nstEntry);

        $form = gfapi::get_form('7');

        //reset so we get all the goodies
        $entry = gfapi::get_entry($entryId);

        //save access codes to db
        $dbSQL = 'INSERT INTO `wp_gf_entry_meta`(`form_id`, `entry_id`, `meta_key`, `meta_value`) VALUES '
                . '("7",' . $entryId . ',"156","' . $nstentry_id . '")';

        $wpdb->get_results($dbSQL);

        //update parent with child information
        gfapi::update_entry($entry);
        $entry = gfapi::get_entry($entryId);
        $entry['156'] = $nstentry_id;

        $nstentry = gfapi::get_entry($nstentry_id);
        create_import_event($entry, $form);
    }
    echo 'finished import';
}

function create_import_event($entry, $form) {
    //pull created by user email    
    $current_user = get_user_by('id', $entry['created_by']);
    $userID = $entry['created_by'];
    $userEmail = (string) $current_user->user_email;

    //find all fields set with a parameter name 
    $parameter_array = find_field_by_parameter($form);

    //first create the event
    $eventName = getFieldByParam('event-name', $parameter_array, $entry); //event-name
    $longDescription = getFieldByParam('long-description', $parameter_array, $entry); //long-description
    $shortDescription = getFieldByParam('short-description', $parameter_array, $entry); //short_description

    $currDateTime = date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));
    
    //check entry is_approved status
    //Approved = 1
    //Disapproved = 2
    //Unapproved = 3    
    $status = ($entry['is_approved']==1 ? 'publish' : 'pending');
    $event = EE_Event::new_instance(
                    array('EVT_name' => $eventName,
                        'EVT_desc' => $longDescription,
                        'EVT_short_desc' => $shortDescription,
                        'EVT_wp_user' => $userID,
                        'status' => $status,
                        'EVT_visible_on' => $currDateTime
    ));
    $event->save();
    $eventID = $event->ID();
    echo 'add event '.$eventName.' ('.$eventID.')<br/>';
    
    // assign basic questions to event
    $qgroups = EEM_Event_Question_Group::instance()->get_one_by_ID(3);
    $event->_add_relation_to($qgroups, 'Event_Question_Group'); //link the question group
    
    //set ticket schedue
    setSchedTicket($parameter_array, $entry, $eventID);

    $userBio = getFieldByParam('user-bio', $parameter_array, $entry);
    $userFname = getFieldByParam('user-fname', $parameter_array, $entry);
    $userLname = getFieldByParam('user-lname', $parameter_array, $entry);
    $userBio = strip_tags(htmlspecialchars_decode($userBio));
    
    //check if facilitator exists
    $person = EEM_Person::instance()->get_one([['PER_email' => $userEmail]]);

    if ($person) {
        $personID = $person->ID();
        //update bio fname and lname if changed
        updatePerson($parameter_array, $entry, $person);
    } else { //if they do not exist, add user
        $person = EE_Person::new_instance(array(
                    "PER_full_name" => $userFname . ' ' . $userLname,
                    "PER_bio" =>   $userBio,
                    "PER_fname" => $userFname,
                    "PER_lname" => $userLname,
                    "PER_email" => $userEmail
        ));
        $person->save();
        $personID = $person->ID();
    }
    echo 'for ' . $userEmail . ' image is ' . $entry['118'] . '<br/>';

    // set person image
    $attachID = upload_CSV_image($entry['118']);
    if($attachID)
        set_post_thumbnail($personID, $attachID); //user image is in field 118 of the submitted entry
        
    //assign that user to this event
    $per_post = EE_Person_Post::new_instance(array('PER_ID' => $personID, 'OBJ_ID' => $eventID, 'PT_ID' => '124')); //67 is the people type of facilitator
    $per_post->save();

    //now lets look for additional hosts
    // this will update the organizer social, website, and facilitator info
    update_organizer_data($entry, $form, $personID, $parameter_array);

    /*
     * Now that the event is created, let's transfer data from the entry to the event
     */
    CSV_event_post_meta($entry, $form, $eventID, $parameter_array); // update taxonomies, featured image, etc    
    CSV_update_event_acf($entry, $form, $eventID, $parameter_array); // Set the ACF data    
    //update_event_additional_fields($entry, $form, $eventID); // Set event custom fields for filtering

    //set the post id
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'gf_entry', array('post_id' => $eventID), array('id' => $entry['id']));

    // now, give the user a basic membership level, if they don't have one already
    $user_meta = get_user_meta($userID);
    $user_level = (isset($user_meta['ihc_user_levels'][0]) ? $user_meta['ihc_user_levels'][0] : '');
    $time_data = ihc_get_start_expire_date_for_user_level($userID, $user_level);
    if (empty($user_meta['ihc_user_levels']) || time() > strtotime($time_data['expire_time'])) {
        // create basic membership starting now, and lasting for 10 years (default)
        $now = time();
        ihc_handle_levels_assign($userID, 14, $now);
        // membership is assigned, but inactive
        // ihc_set_level_status($userID, 17, 1); this is doing nothing now
    } else {
        //error_log("user already has active membership");
    }   
}

//
function upload_CSV_image($image_url) {
    // Add Featured Image to Post    
    $image_name = basename($image_url);

    $upload_dir = wp_upload_dir(); // Set upload folder
    if($image_url=='') {
        return false;
    }
    //check if image exists
    $imgSize = getimagesize($image_url);
    
    //if it does, set it as the alternate image, else use the makey pedastal
    if ($imgSize == FALSE) {
        return false;
    }
    
    $image_data = file_get_contents($image_url); // Get image data
    $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
    $filename = basename($unique_file_name); // Create image file name
    
    // Check folder permission and define file location
    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    // Create the image  file on the server
    file_put_contents($file, $image_data);

    // Check image file type
    $wp_filetype = wp_check_filetype($filename, null);

    // Set attachment data
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    // Create the attachment
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);

    // Include image.php
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Define attachment metadata
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);

    // Assign metadata to attachment
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}

function CSV_event_post_meta($entry, $form, $post_id, $parameter_array) {
    // Set the taxonomies       
    $expType = getFieldByParam('exp-type', $parameter_array, $entry);
    $expCats = getFieldByParam('exp-cats', $parameter_array, $entry);

    wp_set_object_terms($post_id, $expType, 'event_types'); //program type    
    wp_set_object_terms($post_id, $expCats, 'espresso_event_categories');  //event Categories
    // Set the featured Image
    // set person image
    $attachID = upload_CSV_image($entry['9']);
    if($attachID)
        set_post_thumbnail($post_id, $attachID); //user image is in field 118 of the submitted entry    
}

function CSV_update_event_acf($entry, $form, $post_id, $parameterArray) {
    //field mapping - ** note - upload fields don't work here. use post creation feed for that **
    //0 indicie = gravity form field id
    //1 indicie = acf field name/event meta fields
    //2 indicie (optional) = acf field key or subfield key (for repeaters)
    //can't set parameter names on image fields, so we have touse the field ids
    $field_mapping = array(
        array('140', 'image_1'),
        array('141', 'image_2'),
        array('142', 'image_3'),
        array('143', 'image_4'),
        array('144', 'image_5'),
        array('145', 'image_6'),
        array('150', 'about'),
        array('', 'promo_videos', 'field_5f7cd1ffdd06a'),
        array('', 'short_description'),
        array('', 'audience', 'field_5f35a5f833a04'),
        array('', 'location'),
        array('', 'materials'),
        array('', 'kit_required'),
        array('', 'kit_price_included'),
        array('', 'kit_supplier'),
        array('', 'other_kit_supplier'),
        array('', 'kit_shipping_time'),
        array('', 'kit_url'),
        array('', 'wish_list_urls', 'field_5f7cc93ab762c'),
        array('', 'prior_hosted_event'),
        array('', 'hosted_live_stream'),
        array('', 'video_conferencing', 'field_5f60f9bfa1d1e'),
        array('', 'prev_session_links'),
        array('', 'comfort_level'),
        array('', 'technical_setup'),
        array('', 'basic_skills'),
        array('', 'skills_taught'),
        array('', 'public_email'),
        array('', 'attendee_communication_email'),
        array('', 'webinar_link'),
        array('', 'program_expertise')
    );

    //update the acf fields with the submitted values from the form
    foreach ($field_mapping as $field) {
        $fieldID = 0;
        if ($field[0] == '') {
            //determine field id by parameter name
            $paramName = $field[1];

            if (isset($parameterArray[$paramName])) {
                $fieldInfo = $parameterArray[$paramName];
                if (isset($fieldInfo)) {
                    $fieldID = (string) $fieldInfo->id;
                }
            }
        } else {
            $fieldID = $field[0];
        }

        $meta_field = $field[1];
        $field_key = (isset($field[2]) ? $field[2] : '');
        $fieldData = GFAPI::get_field($form, $fieldID);

        if ($fieldID != 0 && isset($entry[$fieldID])) {
            if ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'list' || $fieldData->type == 'list') {
                $listArray = explode(', ', $fieldData->get_value_export($entry));
                $num = 1;
                $repeater = [];
                foreach ($listArray as $value) {
                    $repeater[] = array($field_key => $value);
                    $num++;
                }
                update_field($meta_field, $repeater, $post_id);
            } else if (strpos($meta_field, 'image') !== false) {
                $attachID = upload_CSV_image($entry[$fieldID]);
                if($attachID)
                    update_post_meta($post_id, $meta_field, $attachID); //user image is in field 118 of the submitted entry                  
            } else {
                //update_post_meta($post_id, $meta_field, $entry[$fieldID]);
                update_field($meta_field, $entry[$fieldID], $post_id);
            }
        }
        // checkboxes are set with a decimal point for each selection so theisset in entry doesn't work
        if (isset($fieldData->type)) {
            if ($fieldData->type == 'checkbox' || ($fieldData->type == 'post_custom_field' && $fieldData->inputType == 'checkbox')) {
                $checked = $fieldData->get_value_export($entry);
                $values = explode(', ', $checked);
                update_field($field_key, $values, $post_id);
            }
        }
    }
}