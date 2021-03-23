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
    $mysqli = new mysqli($host, $user, $password, $database);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    //pull maker information from database.    
    $sql = 'SELECT  wp_mf_maker_to_entity.entity_id, wp_mf_maker_to_entity.maker_type, '
            . '     wp_mf_maker_to_entity.maker_role, wp_mf_entity.presentation_title, '
            . '     wp_mf_entity.status, wp_mf_entity.faire, wp_mf_entity.project_photo, wp_mf_entity.desc_short, '
            . '     wp_mf_faire.faire_name, year(wp_mf_faire.start_dt) as faire_year '
            . 'FROM `wp_mf_maker` '
            . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
            . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id '
            . 'left outer join wp_gf_entry on wp_mf_entity.lead_id = wp_gf_entry.id  '            
            . 'left outer join wp_mf_faire on wp_mf_entity.faire=wp_mf_faire.faire '
            . 'where Email like "' . $user_email . '" and wp_mf_entity.status="Accepted"  and maker_type!="contact" and wp_gf_entry.status !="trash" '
            . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();

    foreach ($entries as $entry) {        
        $faire_name = ($entry['faire']=='NMF16'?'National Maker Faire 2016': $entry['faire_name']);
        $entryData[] = array( 'entry_id'      =>  $entry['entity_id'], 
                            'title'         =>  $entry['presentation_title'], 
                            'faire_url'     =>  'makerfaire.com',
                            'faire_name'    =>  $faire_name, 
                            'year'          =>  $entry['faire_year'],
                            'photo'         =>  $entry['project_photo'],
                            'faire_logo'     =>  "https://makerfaire.com/wp-content/uploads/2017/03/MF15_Makey-Pedestal.jpg",
                            'desc_short'    =>  $entry['desc_short']);        
    }   

    //pull in global faires now
    include(get_stylesheet_directory() . '/db-connect/globalmf-config.php');
    $mysqli = new mysqli($host, $user, $password, $database);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    
    //pull maker information from database.    
    $sql = 'SELECT  wp_mf_maker_to_entity.entity_id, wp_mf_maker_to_entity.maker_type, '
            . '     wp_mf_maker_to_entity.maker_role, wp_mf_entity.presentation_title, '
            . '     wp_mf_entity.status, wp_mf_entity.faire as faire_name, wp_mf_entity.project_photo, wp_mf_entity.desc_short,'
            . '     wp_mf_entity.faire_year, wp_mf_entity.blog_id '
            . 'FROM `wp_mf_maker` '
            . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
            . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id  and wp_mf_maker_to_entity.blog_id = wp_mf_entity.blog_id '
            . 'where Email like "' . $user_email . '" and wp_mf_entity.status="Accepted"  and maker_type!="contact" '
            . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");    
    
    foreach ($entries as $entry) {
        //get faire name
        $faire_sql = "SELECT option_name, option_value FROM `wp_" . $entry['blog_id'] . "_options` where option_name = 'blogname' OR option_name = 'theme_mods_MiniMakerFaire'";
        $faire_data = $mysqli->query($faire_sql) or trigger_error($mysqli->error . "[$faire_sql]");    
        $faire_name = '';
        $faire_logo = '';
        foreach($faire_data as $fdata){
            if($fdata['option_name']=='blogname')
                $faire_name = $fdata['option_value'];
            if($fdata['option_name']=='theme_mods_MiniMakerFaire'){
                $theme_mods_MiniMakerFaire = unserialize($fdata['option_value']);
                //check if image exists
                $imgSize = getimagesize($theme_mods_MiniMakerFaire['header_logo']);
                //if it does, set it as the alternate image, else use the makey pedastal
                $faire_logo = ($imgSize==FALSE?"https://makerfaire.com/wp-content/uploads/2017/03/MF15_Makey-Pedestal.jpg":$theme_mods_MiniMakerFaire['header_logo']);
            }
        }       
        
        $entryData[] = array( 'entry_id'    =>  $entry['entity_id'], 
                            'title'         =>  $entry['presentation_title'], 
                            'faire_url'     =>  $entry['faire_name'],
                            'faire_name'    =>  $faire_name . ' ' .$entry['faire_year'], 
                            'year'          =>  $entry['faire_year'],
                            'photo'         =>  $entry['project_photo'], 
                            'faire_logo'    =>  $faire_logo,
                            'desc_short'    =>  $entry['desc_short']);        
    }   
    
    $entryDataUnique = array_unique($entryData, SORT_REGULAR);    
    //sort entry data by year, newest first
    usort($entryDataUnique, function($a, $b) {
        return -($a['year'] <=> $b['year']);
    });
    
    //build outpupt
    echo '<div class="item-grid">';
    
    
    foreach($entryDataUnique as $entry){
        $imgSize = getimagesize($entry['photo']);
        $photo = ($imgSize==FALSE?$entry['faire_logo']:$entry['photo']);
        
        echo '<div class="item-wrapper">
		<a href="https://'.$entry['faire_url'].'/maker/entry/' . $entry['entry_id'] . '" target="_blank">
                    <article class="item-article">
                        <div class="item-info">
                            <div clas="top-line">' .
                                '<h3>' . html_entity_decode($entry['title'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</h3>' .
                                 html_entity_decode($entry['faire_name'], ENT_QUOTES | ENT_XML1, 'UTF-8') .
                            '</div>' .
                        '</div>
                         
                        <div class="item-image" style="background-image:url(' . $photo . ')";>
                            <div class="item-description">' . html_entity_decode($entry['desc_short'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</div>
                        </div>                       
                    </article>
		</a>
            </div>';
    }
    echo '</div>';
}
