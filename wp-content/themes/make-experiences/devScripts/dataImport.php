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
            if ($fieldIDs[$rowKey]=='created_by')       $created_by = $rowData;
            if ($fieldIDs[$rowKey]=='date_created')     $date_created = $rowData;
            if ($fieldIDs[$rowKey]=='source_url')       $source_url = $rowData;	
            if ($fieldIDs[$rowKey]=='user_agent')       $user_agent = $rowData;	
            if ($fieldIDs[$rowKey]=='ip')               $ip = $rowData;            
                
            if ($fieldIDs[$rowKey] != '' && $rowData != '') {
                $pos = strpos($fieldIDs[$rowKey],'NF-');
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
        $nstEntry = array(  'form_id' => 10, 'status' => 'active', 'created_by' => $created_by,
                            GPNF_Entry::ENTRY_PARENT_KEY => $entryId, // The ID of the parent entry.
                            GPNF_Entry::ENTRY_PARENT_FORM_KEY => 7, // The ID of the parent form.
                            GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY => 156, // The ID of the Nested Form field on the parent form
                            '4' => $nstArray[4], '5' => $nstArray[5],    '12'=> $nstArray[12],
                            '13'=> $nstArray[13], '15' => $nstArray[15], '16' => $nstArray[16],                                                                                    
        );
        
        //loop through the nested array to build the nested entry
        if($nstArray['oneTime']=='yes' || $nstArray['oneTime']=='yes'){
            $preferedSchedule = array(array('Date'=>$nstArray['9.1'],"Start Time"=>$nstArray['9.2'],"End Time"=>$nstArray['9.3']));
            $nstEntry['9'] = serialize($preferedSchedule);
        }else{
            $preferedSchedule = array();
                                    
            switch ($nstArray['recurring']){                
                case 'Every Week':
                    $step="+7 day";                        
                    break;
                case 'Every Month':
                    $step="+1 month";
                    break;
                case 'Every Day':
                    $step="+1 day";
                    break;
            }
            
            $date = strtotime($nstArray['9.1']);                              
            //loop until you are at or greater than the preferredEndDate
            while($date<=strtotime($nstArray['preferredEndDate'])){
                $preferedSchedule[] = array('Date'=> date('m/d/Y', $date), "Start Time"=>$nstArray['9.2'],"End Time"=>$nstArray['9.3']);
                $date = strtotime($step, $date);
            }            
            
            $nstEntry['9'] = serialize($preferedSchedule);
        }
        
        //set alt schedule
        $altSchedule = array(array('Date'=>$nstArray['11.1'],"Start Time"=>$nstArray['11.2'],"End Time"=>$nstArray['11.3']));  
        $nstEntry['11'] = serialize($altSchedule);                
        $nstentry_id = gfapi::add_entry($nstEntry);               
        
        $form=gfapi::get_form('7');
        
        //reset so we get all the goodies
        $entry = gfapi::get_entry($entryId);
        
        //save access codes to db
        $dbSQL = 'INSERT INTO `wp_gf_entry_meta`(`form_id`, `entry_id`, `meta_key`, `meta_value`) VALUES '
                . '("7",'.$entryId.',"156","'.$nstentry_id.'")';

        $wpdb->get_results($dbSQL);
    
        //update parent with child information
        gfapi::update_entry($entry);
        $entry = gfapi::get_entry($entryId);
        $entry['156'] = $nstentry_id;   
        
        $nstentry = gfapi::get_entry($nstentry_id);        
        create_event($entry, $form); 
    }    
}