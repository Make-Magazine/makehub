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
            . '     wp_mf_entity.status, wp_mf_entity.faire, wp_mf_entity.project_photo, wp_mf_entity.desc_short, wp_mf_faire.faire_name '
            . 'FROM `wp_mf_maker` '
            . 'left outer join wp_mf_maker_to_entity on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id '
            . 'left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id '
            . 'left outer join wp_mf_faire on wp_mf_entity.faire=wp_mf_faire.faire '
            . 'where Email like "' . $user_email . '" and wp_mf_entity.status="Accepted"  and maker_type!="contact" '
            . 'order by entity_id desc';
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    echo '<div class="item-grid">';
    foreach ($entries as $entry) {
        //new
        echo '<div class="item-wrapper">
		<a href="https://' . html_entity_decode($entry['faire_name'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '/maker/entry/' . $entry['entity_id'] . '" target="_blank">
                    <article class="item-article">
                        <div class="item-info">
                            <div clas="top-line">' .
                                '<h3>' . html_entity_decode($entry['presentation_title'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</h3>' .
                                 html_entity_decode($entry['faire_name'], ENT_QUOTES | ENT_XML1, 'UTF-8') . 
                            '</div>' .
                        '</div>
			<div class="item-image" style="background-image:url(' . $entry['project_photo'] . ')";>
                            <div class="item-description">' . html_entity_decode($entry['desc_short'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</div>
			</div>
                    </article>
		</a>
            </div>';
    }
    echo '</div>';

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
    $entryData = array();
    echo '<div class="item-grid">';
    foreach ($entries as $entry) {
        //get faire name
        $faire_sql = "SELECT option_value FROM `wp_" . $entry['blog_id'] . "_options` where option_name = 'blogname'";
        $result = $mysqli->query($faire_sql);
        $value = $result->fetch_array(MYSQLI_NUM);
        $faire_name = is_array($value) ? $value[0] : html_entity_decode($entry['faire_name'], ENT_QUOTES | ENT_XML1, 'UTF-8');
        echo '<div class="item-wrapper">
		<a href="https://' . html_entity_decode($entry['faire_name'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '/maker/entry/' . $entry['entity_id'] . '" target="_blank">
                    <article class="item-article">
                        <div class="item-info">
                            <div clas="top-line">' .
        '<h3>' . html_entity_decode($entry['presentation_title'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</h3>' .
        $faire_name . ' ' . $entry['faire_year'] .
        '</div>' .
        '</div>
			<div class="item-image" style="background-image:url(' . $entry['project_photo'] . ')";>
                            <div class="item-description">' . html_entity_decode($entry['desc_short'], ENT_QUOTES | ENT_XML1, 'UTF-8') . '</div>
			</div>
                    </article>
		</a>
            </div>';
    }
    echo '</div>';
}
