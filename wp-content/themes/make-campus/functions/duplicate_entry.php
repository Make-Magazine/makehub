<?php

//hook into gravity view duplicate entry to attach 'duplicate of' to the title
add_action('gravityview/entry/duplicate/meta','gv_duplicate_details',10,3);

function gv_duplicate_details( $save_this_meta, $row, $entry){
    
    foreach($save_this_meta as $key => $data){
        if($data["meta_key"]==1){
            $save_this_meta[$key]["meta_value"] = 'Duplicate of '. $data["meta_value"];
        }
    }
        
    return $save_this_meta;
}

