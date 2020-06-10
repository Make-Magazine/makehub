<?php

include '../../../../wp-load.php';
/*
 * This script is used to make existing users members if they don't have another member type
 */

// Define the URL that will be accessed.
$page_size  = (isset($_GET['page_size'])?$_GET['page_size']:100);
$page       = (isset($_GET['page'])?$_GET['page']:1);
$offest     = ($page-1) *$page_size;

$form       = (isset($_GET['form']) ? $_GET['form']:230);
$faire      = (isset($_GET['faire']) ? $_GET['faire']: 'Bay Area');
$year       = (isset($_GET['year']) ? $_GET['year']: '2019');
$form_type  = (isset($_GET['type']) ? $_GET['type']: 'Exhibit');

$url = "https://makerfaire.com/wp-json/gf/v2/forms/".$form."/entries?paging[page_size]=".$page_size."&page=".$page.'&paging[offset]='.$offest;
echo 'pulling from '.$url.'<br/>';
$formurl = "https://makerfaire.com/wp-json/gf/v2/forms/".form; //?paging[page_size]=900
$categoryURL = "https://makerfaire.com/wp-json/wp/v2/makerfaire_category?per_page=100";

$consumer_key = 'ck_af51f732cf514468cb8e3d02c217a716038f8308';
$consumer_secret = 'cs_8f5df179ff4f061337b87d032f7c6b32144fd848';

$headers = array('Authorization' => 'Basic ' . base64_encode("{$consumer_key}:{$consumer_secret}"));

//Get the GF Entries
$response = wp_remote_get($url, array('headers' => $headers));
// Check the response code.
if (wp_remote_retrieve_response_code($response) != 200 || ( empty(wp_remote_retrieve_body($response)) )) {
    // If not a 200, HTTP request failed.
    die('There was an error attempting to access the API for entries.');
}

//Get the GF Form data
$form_response = wp_remote_get($formurl, array('headers' => $headers));
// Check the response code.
if (wp_remote_retrieve_response_code($form_response) != 200 || ( empty(wp_remote_retrieve_body($form_response)) )) {
    // If not a 200, HTTP request failed.
    die('There was an error attempting to get the Form Data');
}

//Get the MF Category information
$cat_response = wp_remote_get($categoryURL, array('headers' => $headers));
// Check the response code.
if (wp_remote_retrieve_response_code($cat_response) != 200 || ( empty(wp_remote_retrieve_body($cat_response)) )) {
    // If not a 200, HTTP request failed.
    die('There was an error attempting to get the Category information');
}


// Result is in the response body and is json encoded.
$body = json_decode($response['body'], true); //entries
$form = json_decode($form_response['body'], true); //form
$catReturn = json_decode($cat_response['body'], true); //categories
//create an array of categories
$catArray = array();
foreach ($catReturn as $category) {
    $catArray[$category['id']] = $category['name'];
}

echo $form['title'] . ' ' . $body['total_count'] . ' entries found<br/>';

if ($body['total_count'] > 0) {
    $entries = $body['entries'];
    $updates = array();
    foreach ($entries as $entry) {
        if ($entry['status'] == 'active') {
            $entry_id = $entry['id'];

            //find all instances of 304 in the keys            
            $flags = array();
            $categories = array();
            if (isset($entry['320']) && $entry['320'] != '') {
                $categories[] = $catArray[$entry['320']];
            }

            $keyArray = array_keys($entry);
            foreach ($keyArray as $key) {
                //find flags
                $pos = strpos($key, '304.');
                if ($pos !== false && $entry[$key] != '') {
                    $flags[] = $entry[$key];
                }
                //find categories
                $pos = strpos($key, '321.');
                if ($pos !== false && $entry[$key] != '') {
                    $categories[] = $catArray[$entry[$key]];
                }
            }

            //populate arrays            
            $wp_mf_entity = array(
                'lead_id' => $entry_id,
                'form_id' => $entry['form_id'],
                'faire' => $faire,
                'mf_year' => $year,
                'status' => (isset($entry['303']) ? $entry['303'] : ''),
                'title' => (isset($entry['151']) ? $entry['151'] : ''),
                'project_photo' => (isset($entry['22']) ? $entry['22'] : ''),
                'project_video' => (isset($entry['32']) ? $entry['32'] : ''),
                'desc_long' => (isset($entry['11']) ? $entry['11'] : ''),
                'desc_short' => (isset($entry['16']) ? $entry['16'] : ''),
                'plans_at_faire' => (isset($entry['55']) ? $entry['55'] : ''),
                'hands_on_activity' => (isset($entry['66']) ? $entry['66'] : ''),
                'describe_hands_on' => (isset($entry['67']) ? $entry['67'] : ''),
                'category' => $categories,
                'inspiration' => (isset($entry['287']) ? $entry['287'] : ''),
                'first_time_mf' => (isset($entry['130']) ? $entry['130'] : ''),
                'prev_mf' => (isset($entry['131']) ? $entry['131'] : ''),
                'prev_mf_loc_year_list' => (isset($entry['132']) ? (is_array($entry['132']) ? json_encode($entry['132']) : "") : ''),
                'flags' => $flags,
                'at_exhibit_learn' => (isset($entry['319']) ? $entry['319'] : ''),
                'project_website' => (isset($entry['27']) ? $entry['27'] : ''),
                'last_change_date' => ''
            );

            $isGroup = false; //default to false
            $isOneMaker = true;
            if (isset($entry['105']) && $entry['105'] != '') {
                $isGroup = (strpos($entry['105'], 'group') !== false ? true : false);
                $isOneMaker = (strpos($entry['105'], 'One') !== false ? true : false);
            }

            $wp_mf_group = array();
            $makerArray = array();
            if ($isGroup) {
                $wp_mf_group = array(
                    'entity_id' => $entry_id,
                    'group_name' => (isset($entry['109']) ? $entry['109'] : ''),
                    'group_bio' => (isset($entry['110']) ? $entry['110'] : ''),
                    'group_photo' => (isset($entry['111']) ? $entry['111'] : ''),
                    'group_website' => (isset($entry['112']) ? $entry['112'] : ''),
                    'group_general_age' => (isset($entry['309']) ? $entry['309'] : ''),
                    'group_social' => (isset($entry['828']) ? (is_array($entry['828']) ? json_encode($entry['828']) : "") : '')
                );
            } else {
                //maker data
                //$wp_mf_maker - contact            
                $makerArray[] = array(
                    'type' => 'contact',
                    'first_name' => (isset($entry['96.3']) ? $entry['96.3'] : ''),
                    'last_name' => (isset($entry['96.6']) ? $entry['96.6'] : ''),
                    'bio' => '',
                    'email' => (isset($entry['98']) ? $entry['98'] : ''),
                    'phone' => (isset($entry['99']) ? $entry['99'] : ''),
                    'twitter' => (isset($entry['201']) ? $entry['201'] : ''),
                    'photo' => '',
                    'website' => '',
                    'phone_type' => (isset($entry['148']) ? $entry['148'] : ''),
                    'age_range' => '',
                    'city' => (isset($entry['101.3']) ? $entry['101.3'] : ''),
                    'state' => (isset($entry['101.4']) ? $entry['101.4'] : ''),
                    'country' => (isset($entry['101.6']) ? $entry['101.6'] : ''),
                    'zipcode' => (isset($entry['101.5']) ? $entry['101.5'] : ''),
                    'address' => (isset($entry['101.1']) ? $entry['101.1'] : ''),
                    'address2' => (isset($entry['101.2']) ? $entry['101.2'] : ''),
                    'gender' => '',
                    'social' => '',
                    'role' => 'contact'
                );
                //$wp_mf_maker - maker 1
                $email = (isset($entry['161']) ? $entry['161'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['160.3']) ? $entry['160.3'] : ''),
                        'last_name' => (isset($entry['160.6']) ? $entry['160.6'] : ''),
                        'bio' => (isset($entry['234']) ? $entry['234'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['185']) ? $entry['185'] : ''),
                        'twitter' => (isset($entry['201']) ? $entry['201'] : ''),
                        'photo' => (isset($entry['217']) ? $entry['217'] : ''),
                        'website' => (isset($entry['209']) ? $entry['209'] : ''),
                        'phone_type' => (isset($entry['200']) ? $entry['200'] : ''),
                        'age_range' => (isset($entry['310']) ? $entry['310'] : ''),
                        'city' => (isset($entry['369.3']) ? $entry['369.3'] : ''),
                        'state' => (isset($entry['369.4']) ? $entry['369.4'] : ''),
                        'country' => (isset($entry['369.6']) ? $entry['369.6'] : ''),
                        'zipcode' => (isset($entry['369.5']) ? $entry['369.5'] : ''),
                        'address' => (isset($entry['369.1']) ? $entry['369.1'] : ''),
                        'address2' => (isset($entry['369.2']) ? $entry['369.2'] : ''),
                        'gender' => (isset($entry['751']) ? $entry['751'] : ''),
                        'social' => (isset($entry['821']) ? $entry['821'] : ''),
                        'role' => (isset($entry['443']) ? $entry['443'] : ''), //not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 2
                $email = (isset($entry['162']) ? $entry['162'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['158.3']) ? $entry['158.3'] : ''),
                        'last_name' => (isset($entry['158.6']) ? $entry['158.6'] : ''),
                        'bio' => (isset($entry['258']) ? $entry['258'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['192']) ? $entry['192'] : ''),
                        'twitter' => (isset($entry['208']) ? $entry['208'] : ''),
                        'photo' => (isset($entry['224']) ? $entry['224'] : ''),
                        'website' => (isset($entry['216']) ? $entry['216'] : ''),
                        'phone_type' => (isset($entry['199']) ? $entry['199'] : ''),
                        'age_range' => (isset($entry['311']) ? $entry['311'] : ''),
                        'city' => (isset($entry['370.3']) ? $entry['370.3'] : ''),
                        'state' => (isset($entry['370.4']) ? $entry['370.4'] : ''),
                        'country' => (isset($entry['370.6']) ? $entry['370.6'] : ''),
                        'zipcode' => (isset($entry['370.5']) ? $entry['370.5'] : ''),
                        'address' => (isset($entry['370.1']) ? $entry['370.1'] : ''),
                        'address2' => (isset($entry['370.2']) ? $entry['370.2'] : ''),
                        'gender' => (isset($entry['752']) ? $entry['752'] : ''),
                        'social' => (isset($entry['822']) ? $entry['822'] : ''),
                        'role' => (isset($entry['444']) ? $entry['444'] : '')//not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 3
                $email = (isset($entry['167']) ? $entry['167'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['155.3']) ? $entry['155.3'] : ''),
                        'last_name' => (isset($entry['155.6']) ? $entry['155.6'] : ''),
                        'bio' => (isset($entry['259']) ? $entry['259'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['190']) ? $entry['190'] : ''),
                        'twitter' => (isset($entry['207']) ? $entry['207'] : ''),
                        'photo' => (isset($entry['223']) ? $entry['223'] : ''),
                        'website' => (isset($entry['215']) ? $entry['215'] : ''),
                        'phone_type' => (isset($entry['193']) ? $entry['193'] : ''),
                        'age_range' => (isset($entry['312']) ? $entry['312'] : ''),
                        'city' => (isset($entry['371.3']) ? $entry['371.3'] : ''),
                        'state' => (isset($entry['371.4']) ? $entry['371.4'] : ''),
                        'country' => (isset($entry['371.6']) ? $entry['371.6'] : ''),
                        'zipcode' => (isset($entry['371.5']) ? $entry['371.5'] : ''),
                        'address' => (isset($entry['371.1']) ? $entry['371.1'] : ''),
                        'address2' => (isset($entry['371.2']) ? $entry['371.2'] : ''),
                        'gender' => (isset($entry['753']) ? $entry['753'] : ''),
                        'social' => (isset($entry['823']) ? $entry['823'] : ''),
                        'role' => (isset($entry['445']) ? $entry['445'] : '')//not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 4

                $email = (isset($entry['166']) ? $entry['166'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['156.3']) ? $entry['156.3'] : ''),
                        'last_name' => (isset($entry['156.6']) ? $entry['156.6'] : ''),
                        'bio' => (isset($entry['260']) ? $entry['260'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['191']) ? $entry['191'] : ''),
                        'twitter' => (isset($entry['206']) ? $entry['206'] : ''),
                        'photo' => (isset($entry['222']) ? $entry['222'] : ''),
                        'website' => (isset($entry['214']) ? $entry['214'] : ''),
                        'phone_type' => (isset($entry['198']) ? $entry['198'] : ''),
                        'age_range' => (isset($entry['313']) ? $entry['313'] : ''),
                        'city' => (isset($entry['372.3']) ? $entry['372.3'] : ''),
                        'state' => (isset($entry['372.4']) ? $entry['372.4'] : ''),
                        'country' => (isset($entry['372.6']) ? $entry['372.6'] : ''),
                        'zipcode' => (isset($entry['372.5']) ? $entry['372.5'] : ''),
                        'address' => (isset($entry['372.1']) ? $entry['372.1'] : ''),
                        'address2' => (isset($entry['372.2']) ? $entry['372.2'] : ''),
                        'gender' => (isset($entry['754']) ? $entry['754'] : ''),
                        'social' => (isset($entry['824']) ? $entry['824'] : ''),
                        'role' => (isset($entry['446']) ? $entry['446'] : '')//not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 5
                $email = (isset($entry['165']) ? $entry['165'] : '');
                if ($email != '') {

                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['157.3']) ? $entry['157.3'] : ''),
                        'last_name' => (isset($entry['157.6']) ? $entry['157.6'] : ''),
                        'bio' => (isset($entry['261']) ? $entry['261'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['189']) ? $entry['189'] : ''),
                        'twitter' => (isset($entry['205']) ? $entry['205'] : ''),
                        'photo' => (isset($entry['220']) ? $entry['220'] : ''),
                        'website' => (isset($entry['213']) ? $entry['213'] : ''),
                        'phone_type' => (isset($entry['195']) ? $entry['195'] : ''),
                        'age_range' => (isset($entry['314']) ? $entry['314'] : ''),
                        'city' => (isset($entry['373.3']) ? $entry['373.3'] : ''),
                        'state' => (isset($entry['373.4']) ? $entry['373.4'] : ''),
                        'country' => (isset($entry['373.6']) ? $entry['373.6'] : ''),
                        'zipcode' => (isset($entry['373.5']) ? $entry['373.5'] : ''),
                        'address' => (isset($entry['373.1']) ? $entry['373.1'] : ''),
                        'address2' => (isset($entry['373.2']) ? $entry['373.2'] : ''),
                        'gender' => (isset($entry['755']) ? $entry['755'] : ''),
                        'social' => (isset($entry['825']) ? $entry['825'] : ''),
                        'role' => (isset($entry['447']) ? $entry['447'] : '')//not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 6            
                $email = (isset($entry['164']) ? $entry['164'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['159.3']) ? $entry['159.3'] : ''),
                        'last_name' => (isset($entry['159.6']) ? $entry['159.6'] : ''),
                        'bio' => (isset($entry['262']) ? $entry['262'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['188']) ? $entry['188'] : ''),
                        'twitter' => (isset($entry['204']) ? $entry['204'] : ''),
                        'photo' => (isset($entry['221']) ? $entry['221'] : ''),
                        'website' => (isset($entry['211']) ? $entry['211'] : ''),
                        'phone_type' => (isset($entry['197']) ? $entry['197'] : ''),
                        'age_range' => (isset($entry['315']) ? $entry['315'] : ''),
                        'city' => (isset($entry['374.3']) ? $entry['374.3'] : ''),
                        'state' => (isset($entry['374.4']) ? $entry['374.4'] : ''),
                        'country' => (isset($entry['374.6']) ? $entry['374.6'] : ''),
                        'zipcode' => (isset($entry['374.5']) ? $entry['374.5'] : ''),
                        'address' => (isset($entry['374.1']) ? $entry['374.1'] : ''),
                        'address2' => (isset($entry['374.2']) ? $entry['374.2'] : ''),
                        'gender' => (isset($entry['756']) ? $entry['756'] : ''),
                        'social' => (isset($entry['827']) ? $entry['827'] : ''),
                        'role' => (isset($entry['448']) ? $entry['448'] : '')//not set for performance/presentation
                    );
                }

                //$wp_mf_maker - maker 7
                $email = (isset($entry['163']) ? $entry['163'] : '');
                if ($email != '') {
                    $makerArray[] = array(
                        'type' => 'maker',
                        'first_name' => (isset($entry['154.3']) ? $entry['154.3'] : ''),
                        'last_name' => (isset($entry['154.6']) ? $entry['154.6'] : ''),
                        'bio' => (isset($entry['263']) ? $entry['263'] : ''),
                        'email' => $email,
                        'phone' => (isset($entry['187']) ? $entry['187'] : ''),
                        'twitter' => (isset($entry['203']) ? $entry['203'] : ''),
                        'photo' => (isset($entry['219']) ? $entry['219'] : ''),
                        'website' => (isset($entry['212']) ? $entry['212'] : ''),
                        'phone_type' => (isset($entry['196']) ? $entry['196'] : ''),
                        'age_range' => (isset($entry['316']) ? $entry['316'] : ''),
                        'city' => (isset($entry['375.3']) ? $entry['375.3'] : ''),
                        'state' => (isset($entry['375.4']) ? $entry['375.4'] : ''),
                        'country' => (isset($entry['375.6']) ? $entry['375.6'] : ''),
                        'zipcode' => (isset($entry['375.5']) ? $entry['375.5'] : ''),
                        'address' => (isset($entry['375.1']) ? $entry['375.1'] : ''),
                        'address2' => (isset($entry['375.2']) ? $entry['375.2'] : ''),
                        'gender' => (isset($entry['757']) ? $entry['757'] : ''),
                        'social' => (isset($entry['826']) ? $entry['826'] : ''),
                        'role' => (isset($entry['449']) ? $entry['449'] : '')//not set for performance/presentation
                    );
                }
            }
            $updates[] = array(
                'mf_group' => $wp_mf_group,
                'mf_entity' => $wp_mf_entity,
                'makerArray' => $makerArray
            );

            echo 'id - ' . $entry['id'] . ' form - ' . $entry['form_id'] . ' title - ' . $entry['151'] . '<br/>';
        }
    }

    //youzer social
    //$yz_social_networks = get_option( 'yz_social_networks' );
    //var_dump($yz_social_networks);
    
    
    //now let's start updating database tables
    foreach ($updates as $update) {
        $entityData = $update['mf_entity'];

        $wp_mf_entitysql = 'INSERT INTO wp_mf_entity (lead_id, form_id, faire, mf_year, status, title, project_photo, '
                . 'project_video, form_type, desc_long, desc_short, plans_at_faire, '
                . 'hands_on_activity, describe_hands_on, category, inspiration, '
                . 'first_time_mf, prev_mf, prev_mf_loc_year_list, flags, at_exhibit_learn, '
                . 'project_website, last_change_date) VALUES (' . $entityData['lead_id'] . ',' . $entityData['form_id'] . ','
                . ' "' . $entityData['faire'] . '", '
                . ' "' . $entityData['mf_year'] . '", '
                . ' "' . $entityData['status'] . '", '
                . ' "' . $entityData['title'] . '", '
                . ' "' . $entityData['project_photo'] . '", '
                . ' "' . $entityData['project_video'] . '", '
                . ' "' . $form_type . '", '                
                . ' "' . $entityData['desc_long'] . '", '
                . ' "' . $entityData['desc_short'] . '", '
                . ' "' . $entityData['plans_at_faire'] . '", '
                . ' "' . $entityData['hands_on_activity'] . '", '
                . ' "' . $entityData['describe_hands_on'] . '", '
                . ' "' . implode(",", $entityData['category']) . '", '
                . ' "' . $entityData['inspiration'] . '", '
                . ' "' . $entityData['first_time_mf'] . '", '
                . ' "' . $entityData['prev_mf'] . '", '
                . ' "' . $entityData['prev_mf_loc_year_list'] . '", '
                . ' "' . implode(",", $entityData['flags']) . '", '
                . ' "' . $entityData['at_exhibit_learn'] . '", '
                . ' "' . $entityData['project_website'] . '", '
                . ' now()'
                . ') '
                . ' ON DUPLICATE KEY UPDATE title                   = "' . $entityData['title'] . '", '
                . '                         status                  = "' . $entityData['status'] . '", '
                . '                         project_photo           = "' . $entityData['project_photo'] . '", '
                . '                         project_video           = "' . $entityData['project_video'] . '", '
                . '                         desc_short              = "' . $entityData['desc_short'] . '", '
                . '                         desc_long               = "' . $entityData['desc_long'] . '", '
                . '                         plans_at_faire          = "' . $entityData['plans_at_faire'] . '", '
                . '                         hands_on_activity       = "' . $entityData['hands_on_activity'] . '", '
                . '                         describe_hands_on       = "' . $entityData['describe_hands_on'] . '", '
                . '                         category                = "' . implode(",", $entityData['category']) . '", '
                . '                         inspiration             = "' . $entityData['inspiration'] . '", '
                . '                         first_time_mf           = "' . $entityData['first_time_mf'] . '",'
                . '                         prev_mf                 = "' . $entityData['prev_mf'] . '", '
                . '                         prev_mf_loc_year_list   = "' . $entityData['prev_mf_loc_year_list'] . '", '
                . '                         flags                   = "' . implode(",", $entityData['flags']) . '", '
                . '                         at_exhibit_learn        = "' . $entityData['at_exhibit_learn'] . '", '
                . '                         project_website         = "' . $entityData['project_website'] . '", '
                . '                         last_change_date        = now()';
        $wpdb->get_results($wp_mf_entitysql);
        $wpdb->print_error();

        //group information
        if (isset($update['mf_group']) && !(empty($update['mf_group']))) {
            $group = $update['mf_group'];
            $sql = 'INSERT INTO `wp_mf_group`(`entity_id`, `group_name`, `group_bio`, `group_photo`, `group_website`, '
                    . '`group_general_age`, `group_social`) '
                    . 'VALUES (' . $group['entity_id'] . ',"' . $group['group_name'] . '","' . $group['group_bio'] . '","' . $group['group_photo'] . '",'
                    . '"' . $group['group_website'] . '","' . $group['group_general_age'] . '",'
                    . "'" . $group['group_social'] . "')"
                    . ' ON DUPLICATE KEY UPDATE group_name   = "' . $group['group_name'] . '", '
                    . '                         group_bio    = "' . $group['group_bio'] . '", '
                    . '                         group_photo  = "' . $group['group_photo'] . '", '
                    . '                         group_general_age  = "' . $group['group_general_age'] . '", '
                    . "                         group_social  = '" . $group['group_social'] . "'";

            $wpdb->get_results($sql);
            $wpdb->print_error();
        }

        //create users for each maker in community
        $makers = array_unique($update['makerArray'], SORT_REGULAR);

        $faireData  = '<div class="faireData">'
                        . ' <a target="_blank" href="'.'https://makerfaire.com/maker/entry/'.$entityData['lead_id'].'"><div class="entryFaire">'.$faire.'&nbsp;'.$year.'</div></a>'
                        . ' <a target="_blank" href="'.'https://makerfaire.com/maker/entry/'.$entityData['lead_id'].'"><div class="entryTitle">'.$entityData['title'].'</div></a>';
        if($entityData['project_photo']!=''){
            $faireData  .= '<a target="_blank" href="'.'https://makerfaire.com/maker/entry/'.$entityData['lead_id'].'">'
                        .       '<img class="entryPhoto" src="'.$entityData['project_photo'].'" />'
                        .  '</a>';
        }        
        $faireData  .= '</div>';
        //var_dump($makers);
        foreach ($makers as $maker) {
            maker_to_community($maker, $form, $entityData);
        }
    }
}

//create maker records in community
function maker_to_community($maker, $form,$entry ) {
    global $wpdb;
    $lead_id = $entry['lead_id'];
    $email = $maker['email'];
    echo 'adding maker ' . $email . '<br/>';
    if ($email == '')
        return false;


    //Check whether the user already exist or not
    $user_details = get_user_by("email", trim($email));

    if ($user_details) { //does user exist?        
        //If user already exists then assign ID and update what??
        $userdata = array('user_url' => $maker['email'], 'display_name' => $maker['first_name'] . ' ' . $maker['last_name']);

        $userdata['ID'] = $user_details->data->ID;
        $user_id = $user_details->data->ID;
        add_user_to_blog(1, $user_id, 'subscriber'); //ensure they are part of the community blog
        wp_update_user($userdata);
    } else { //no, add them      
        //come up with unique user_login, user_nicename
        $username = findUniqueUname($maker['first_name'] . $maker['last_name']); //pass firstname lastname
        $userdata = array('user_email' => $email,
            'user_url' => $maker['website'],
            'display_name' => $maker['first_name'] . ' ' . $maker['last_name'],
            'first_name' => $maker['first_name'],
            'last_name' => $maker['last_name'],
            'user_nicename' => $username,
            'user_pass' => $email,
            'user_login' => $username);
        $user_id = wp_insert_user($userdata);
        wp_update_user(array('ID' => $user_id, 'role' => 'subscriber'));
        //uncomment the below line for production!!!
        //update_user_meta($user_id, 'registryoptout', 'Yes');
        add_user_to_blog(1, $user_id, 'subscriber'); //add user to main blog           
        //Upload user avatar if image is set
        if ($maker['photo']) {
            $photo = $maker['photo'];
            if (!defined('AVATARS'))
                define('AVATARS', ABSPATH . 'wp-content/uploads/avatars');
            if (!file_exists(AVATARS))
                mkdir(AVATARS, 0777);
            $image_dir = AVATARS . '/' . $user_id;
            if (!file_exists($image_dir))
                mkdir($image_dir, 0777);
            $current_time = time();
            $destination_bpfull = $image_dir . '/' . $current_time . '-bpfull.jpg';
            $destination_bpthumb = $image_dir . '/' . $current_time . '-bpthumb.jpg';

            $photo = str_replace(' ', '%20', $photo);
            $bpfull = $bpthumb = wp_get_image_editor($photo);

            // Handle 404 avatar url
            if (!is_wp_error($bpfull)) {
                $bpfull->resize(150, 150, true);
                $bpfull->save($destination_bpfull);
                $bpthumb->resize(50, 50, true);
                $bpthumb->save($destination_bpthumb);
                // And make sure it updates on the bp side
                update_user_meta($user_id, 'author_avatar', $destination_bpfull);
            }
        }
    }

    //set member type to maker
    switch_to_blog(1);
    $member_type = bp_set_member_type($user_id, 'maker');
    $tt_ids = wp_set_object_terms($user_id, 'maker', bp_get_member_type_tax_name());
    restore_current_blog();

    //set extended profile data TBD - only if user exists    
    /*
     * twitter
     * social
     * faire/project/role
     */
        
    /*
      $makerArray[] = array(            
      'twitter' => (isset($entry['203']) ? $entry['203'] : ''),      
      'social' => (isset($entry['826']) ? $entry['826'] : ''),      
      );
     */

    
    // Get xprofile field visibility
    $xprofile_visibility_sql = 'SELECT object_id, meta_value FROM wp_bp_xprofile_meta WHERE meta_key = "default_visibility"';
    $bp_fields_visibility = $wpdb->get_results($xprofile_visibility_sql);
    $xprofile_fields_visibility = array(1 => 'public');

    foreach ((array) $bp_fields_visibility as $bp_field_visibility) {
        if($bp_field_visibility->object_id== '207' || $bp_field_visibility->object_id == '201' || $bp_field_visibility->object_id =='203' ){
            $xprofile_fields_visibility[$bp_field_visibility->object_id] = 'adminsonly';
        }else{
            $xprofile_fields_visibility[$bp_field_visibility->object_id] = $bp_field_visibility->meta_value;
        }
            
        
    }

    //Create an array of BP fields   
    $bp_extra_fields = $wpdb->get_results('SELECT id, type, name FROM wp_bp_xprofile_fields');

    foreach ($bp_extra_fields as $value) {        
        $bp_fields_type[$value->id] = $value->type;
    }

    // Insert xprofile field visibility state for user level.
    update_user_meta($user_id, 'bp_xprofile_visibility_levels', $xprofile_fields_visibility);

     //field 642 contains faire information
    global $faireData;     
    $faireData = $faireData . xprofile_get_field_data(642,$user_id); //get existing data    
    
    //set xprofile fields with maker data
    $bpmeta = array(1 => $maker['first_name'] . ' ' . $maker['last_name'],
        2   => $maker['website'], 259 => $maker['bio'], 202 => $maker['city'],
        391 => $maker['state'], 392 => $maker['country'], 393 => $maker['zipcode'],
        636 => $maker['age_range'], 637 => $maker['gender'], 207 => $maker['phone'],
        201 => $maker['address'], 203 => $maker['address2'], 642 => $faireData);
    
    if (isset($bpmeta)) {
        //Added an entry in user_meta table for current user meta key is last_activity
        bp_update_user_last_activity($user_id, date('Y-m-d H:i:s'));

        foreach ($bpmeta as $bpmetakeyid => $bpmetavalue) {
            $current_field_type = (isset($bp_fields_type[$bpmetakeyid]) ? $bp_fields_type[$bpmetakeyid] : '');
            if($bpmetakeyid==638)   echo '638 field type is '. $current_field_type.'<br/>';
            if ('image' === $current_field_type || $current_field_type === 'file') {
                $sql = 'SELECT id FROM wp_bp_xprofile_data WHERE field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
                $result = $wpdb->get_var($sql);
                $date = date('Y-m-d G:i:s');
                if ('' == $result) {
                    $sql = "insert into wp_bp_xprofile_data (`field_id`,`user_id`,`value`, `last_updated`) VALUES($bpmetakeyid, $user_id, '$bpmetavalue', '$date')";
                } else {
                    $sql = 'UPDATE wp_bp_xprofile_data SET value = "' . $bpmetavalue . '", last_updated = "' . $date . '" WHERE id = ' . $result . ' AND field_id = ' . $bpmetakeyid . ' AND user_id = ' . $user_id;
                }
                $wpdb->query($sql);
            } else {
                xprofile_set_field_data($bpmetakeyid, $user_id, $bpmetavalue);
            }
        }
    }

    //finally let's create a cross reference from this user to it's projects
    //(key is on maker_id, entity_id and maker_type.  if record already exists, no update is needed)
    $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity` (`maker_id`, `entity_id`, `maker_type`,`maker_role`) "
            . ' VALUES ("' . $user_id . '",' . $lead_id . ',"' . $maker['type'] . '", "' . $maker['role'] . '")  '
            . ' ON DUPLICATE KEY UPDATE maker_id="' . $user_id . '", maker_role="' . $maker['role'] . '";';

    $wpdb->get_results($wp_mf_maker_to_entity);
}

function findUniqueUname($input = '') {
    //remove special chatacters and spaces
    $input = preg_replace("/[^a-zA-Z0-9]+/", "", $input);
    if (username_exists($input)) { //if the passed username is available, return it
        return $input;
    } else {
        $user_exists = 1;
        do {
            $rnd_str = sprintf("%0d", mt_rand(1, 999999));
            $user_exists = username_exists($input . $rnd_str);
        } while ($user_exists > 0);
        return $input . $rnd_str;
    }
}

function find_catText($category) {
    global $cat_choices;
    global $cat_inputs;
    return $cat_choices[$cat_inputs[$category]];
}
