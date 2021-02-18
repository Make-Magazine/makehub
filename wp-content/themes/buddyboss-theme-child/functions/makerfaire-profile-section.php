<?php

/*
 * Pulls makre information from the makerfaire.com database
 */

function profile_tab_makerfaire_infoname() {
    global $bp;
    $user_id = bp_displayed_user_id();
    $type = bp_get_member_type(bp_displayed_user_id());
    if ($user_id != 0) {
        bp_core_new_nav_item(array(
            'name' => 'Maker Faire Information',
            'slug' => 'makerfaire_info',
            'screen_function' => 'makerfaire_info_screen',
            'position' => 40,
            'parent_url' => bp_loggedin_user_domain() . '/makerfaire_info/',
            'parent_slug' => $bp->profile->slug,
            'default_subnav_slug' => 'makerfaire_info'
        ));
    }
}

add_action('bp_setup_nav', 'profile_tab_makerfaire_infoname');

function makerfaire_info_screen() {
    // Add title and content here - last is to call the members plugin.php template.
    add_action('bp_template_title', 'makerfaire_info_title');
    add_action('bp_template_content', 'makerfaire_info_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function makerfaire_info_title() {
    echo 'Maker Faire Information';
}

function makerfaire_info_content() {
    global $wpdb;
    
    $user_id = bp_displayed_user_id();
    $type = bp_get_member_type(bp_displayed_user_id());
    
    //get the users email
    $user_info = get_userdata($user_id);
    $user_email = $user_info->user_email;

    //access the makerfaire database.    
    include(get_stylesheet_directory() . '/db-connect/mf-config.php');
    include(get_stylesheet_directory() . '/db-connect/db_connect.php');
    $sql = "SELECT * FROM `wp_mf_maker`";
    //loop thru entry data
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    foreach ($entries as $entry) {
        var_dump($entry);
        die();
    }    
    die();
        $entry = array();
        foreach ($results as $row) {
            if(is_decimal($row->meta_key)){
                $entry[floor($row->meta_key)][$row->meta_key] = $row->meta_value;
            }else{
                $entry[$row->meta_key] = $row->meta_value;            
            }
        }        
        
        //id, label, type, other
        $fieldArray = array(                
                array('15', 'What days is the makerspace open?', 'checkbox',''),
                array('96', 'Is your space located in a:', 'checkbox','97'),
                array('98', 'Does it cost money to use this makerspace?',	'yesno','99','100'),
                array('101', 'What ages are allowed to use the makerspace?', 'checkbox',''),
                array('102', 'Does the makerspace offer safety and basic use training?', 'radio',''),
                array('103', 'What kind of classes does the makerspace offer?', 'checkbox', '104'),
                array('105', 'Does the makerspace provide safety equipment (goggles, welding gloves & jackets, ear protection, etc.)?', 'radio',''),
                array('107', 'Does the makerspace have a kitchen for use by members?', 'radio', ''),
                array('123', 'Does the makerspace offer:', 'checkbox', '124'),
                array('125', 'Tools/materials provided for:', 'checkbox', '126'),
                array('68', 'Woodworking Equipment:', 'yesno','109','110'),
                array('67', 'Metalworking Equipment:', 'yesno','111','112'),
                array('113', 'Welding Equipment:', 'yesno','114','115'),
                array('65', 'Electronic Equipment:', 'yesno','116','117'),
                array('74', 'Textile Equipment:', 'yesno','118','119'),
                array('120', 'Welding Equipment:', 'yesno','121','122'),
            );
        
        echo '<div class="ms_info">';
        foreach($fieldArray as $field){
            $fieldID     = $field[0];            
            if(isset($entry[$fieldID])){
                echo '<div class="fieldInfo">';
                $fieldLabel  = $field[1];
                $fieldType   = $field[2];
                $fieldOther1 = (isset($field[3])?$field[3]:'');
                $fieldOther2 = (isset($field[4])?$field[4]:'');
                //echo $fieldID.' '. $fieldLabel.' '.$fieldType.'<br/>';
                //radio field
                if($fieldType == 'radio'){
                    echo '<span class="fieldLabel">'.$fieldLabel.'</span>';
                    echo '<div class="fieldData">'.$entry[$fieldID].'</div>';
                }
                
                //checkbox
                if($fieldType == 'checkbox'){
                    echo '<span class="fieldLabel">'.$fieldLabel.'</span>';
                    echo '<ul>';
                    foreach($entry[$fieldID] as $value){
                        echo '<li>'.$value.'</li>';
                    }
                    
                    if(isset($entry[$fieldOther1])) {
                        //if the other field is an array loop through it
                        if(is_array($entry[$fieldOther1])){                       
                            foreach($entry[$fieldOther1] as $other_value){
                                echo '<li>'.$other_value.'</li>';
                            }
                        }else{
                            echo '<li>'.$entry[$fieldOther1].'</li>';
                        }
                    }
                    echo '</ul>';                    
                }

                // yes/no fields
                if($fieldType == 'yesno'){
                    if($entry[$fieldID]=='Yes'){
                       echo '<span class="fieldLabel">'.$fieldLabel.'</span>';
                       echo '<ul>';
                       foreach($entry[$fieldOther1] as $value){
                           echo '<li>'.$value.'</li>';
                       }

                       if(isset($entry[$fieldOther2])) {
                           //if the other field is an array loop through it
                           if(is_array($entry[$fieldOther2])){                       
                               if(isset($entry[$fieldOther2])){                
                                    echo '<li>'.$entry[$fieldOther2].'</li>';
                                }
                           }else{
                               echo '<li>'.$entry[$fieldOther2].'</li>';
                           }
                       }
                       echo '</ul>';
                    }                    
                }     
                echo '</div>';
            }
        }                        
        echo '</div>';
        
}

