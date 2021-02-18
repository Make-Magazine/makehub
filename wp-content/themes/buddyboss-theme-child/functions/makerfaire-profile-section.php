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
    //add_action('bp_template_title', 'makerfaire_info_title');
    add_action('bp_template_content', 'makerfaire_info_content');
    bp_core_load_template('buddypress/members/single/plugins');
}

function makerfaire_info_title() {
    //echo 'Maker Faire Information';
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
    
    //pull maker information from database.    
    $sql = 'SELECT  wp_mf_maker_to_entity.entity_id, wp_mf_maker_to_entity.maker_type, '
            . '     wp_mf_maker_to_entity.maker_role, wp_mf_entity.presentation_title, '
            . '     wp_mf_entity.status, wp_mf_entity.faire, wp_mf_entity.project_photo, wp_mf_entity.desc_short, wp_mf_faire.faire_name '
         . 'FROM `wp_mf_maker` '
         . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
         . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id '
         . 'left outer join wp_mf_faire on wp_mf_entity.faire=wp_mf_faire.faire '
         . 'where Email like "'.$user_email.'" and wp_mf_entity.status="Accepted"  and maker_type!="contact" '
         . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    foreach ($entries as $entry) {
        echo '<h2>'.$entry['faire_name'].' - Entry '.$entry['entity_id'].'</h2>'.                 
                '<h3><a target="_blank" href="https://makerfaire.com/maker/entry/'.$entry['entity_id'].'/">'.$entry['presentation_title'].'</a></h3>'.
                '<img style="width:200px;height:auto;padding-right:20px;" src="'.$entry['project_photo'].'" align="left" />'.
                $entry['desc_short'].
                '<div style="clear:both"></div><br/><br/>';
    }    
                                                  
}

