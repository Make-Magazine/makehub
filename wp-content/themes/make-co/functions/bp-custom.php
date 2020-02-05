<?php

//Page Slug Body Class
function add_slug_body_class($classes) {
    $urlPath = trim(str_replace("/", "-", $_SERVER['REQUEST_URI']), '-');
    if ($urlPath) {
        $classes[] = "page-" . $urlPath;
    }
    return $classes;
}

add_filter('body_class', 'add_slug_body_class');

function youzer_add_custom_meta_fields() {
    // if a field is set to hidden, we'll save it to an array to check whether we should display it or not
    $hidden_fields = bp_xprofile_get_hidden_fields_for_user(bp_get_member_user_id());

    echo("<span class='yz-name'>");
    if (xprofile_get_field_data('country', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('country'), $hidden_fields)) {
        echo "<i class='fas fa-globe-americas'></i> " . xprofile_get_field_data('country', bp_get_member_user_id());
    }
    echo("</span>");
    /*
      echo("<span class='yz-name'>");
      if(xprofile_get_field_data('city', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('city'), $hidden_fields)) {
      echo "<i class='fas fa-city'></i> " . xprofile_get_field_data('city', bp_get_member_user_id());
      }
      echo("</span>");
     */
}

add_action('bp_directory_members_item_meta', 'youzer_add_custom_meta_fields');

// remove last active status from member directory
add_filter('bp_nouveau_get_member_meta', 'ps_remove_last_active', 10, 3);

function ps_remove_last_active($meta, $member, $is_loop) {
    $meta['last_activity'] = '';
    return $meta;
}

//********************************************//
//         Members Directory Sidebar          //
//********************************************//
// add sidebar to members directory
function yzc_register_members_directory_sidebars() {
    register_sidebar(
            array(
                'name' => __('Members Directory Sidebar', 'youzer'),
                'id' => 'yz-members-directory-sidebar',
                'description' => __('Members Directory Sidebar', 'youzer'),
                'before_widget' => '<div id="%1$s" class="widget-content %2$s">',
                'after_widget' => "</div>",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            )
    );
}

add_action('widgets_init', 'yzc_register_members_directory_sidebars');

/**
 * Call Sidebar.
 */
function yzc_members_directory_sidebar() {
    if (!bp_is_members_directory()) {
        return;
    }
    if (is_active_sidebar('yz-members-directory-sidebar')) {
        echo '<div class="yz-sidebar-column yz-members-directory-sidebar youzer-sidebar"><div class="yz-column-content">';
        dynamic_sidebar('yz-members-directory-sidebar');
        echo '</div></div>';
    }
}

add_action('bp_after_directory_members', 'yzc_members_directory_sidebar');

/**
 * If there is certain text in Youzer that isn't descriptive enough, we can change it here
 */
function yz_translate_youzer_text($translated_text) {
    switch ($translated_text) {
        case 'Widgets Settings' :
        case 'widgets settings' :
            $translated_text = __('Advanced Profile settings', 'youzer');
            break;
        case 'Profile Widgets Settings' :
            $translated_text = __('Add widgets, media and more!', 'youzer');
            break;
        case 'Filter' :
            $translated_text = __('Order', 'youzer');
            break;
        case 'Link Backgorund Image' :
            $translated_text = __('Link Background Image', 'youzer');
            break;
        case 'Posts' :
            $translated_text = __('Blog Posts', 'youzer');
            break;
        case 'New Post' :
            $translated_text = __('New Blog Post', 'youzer');
            break;
        case 'Ultimate Membership Pro' :
            $translated_text = __('Membership Details', 'youzer');
            break;
        case 'All Members %s' :
            $translated_text = __('All %s', 'buddypress');
            break;
        case 'Order By:' :
            $translated_text = __('Order:', 'buddypress');
            break;
    }
    return $translated_text;
}

add_filter('gettext', 'yz_translate_youzer_text', 10);

//****************************************************************//
//         Excluded Users: Opt Out and Unconfirmed          //
//****************************************************************//
function buddydev_exclude_users( $args ) {
    $excluded = isset( $args['exclude'] ) ? $args['exclude'] : array();
    if ( ! is_array( $excluded ) ) {
        $excluded = explode( ',', $excluded );
    }
	
	 $query_args = array(
    		  'meta_key' => 'registryoptout', 
			  'meta_value' => 'a:1:{i:0;s:3:"Yes";}',
    		  'fields' => 'ID'
    	  );
    $user_ids = get_users($query_args);
 
    $excluded = array_merge( $excluded, $user_ids );
    $args['exclude'] = $excluded;
    return $args;
}
 
add_filter( 'bp_after_has_members_parse_args', 'buddydev_exclude_users' );

// Exclude user from count as well
function exclude_users_from_count(){
	$query_args = array(
		  'meta_key' => 'registryoptout', 
		  'meta_value' => 'a:1:{i:0;s:3:"Yes";}',
		  'fields' => 'ID'
	  );
	$user_ids = get_users($query_args);
	$users_excluded = count($user_ids);
	return (get_user_count() - $users_excluded);
}
add_filter('bp_get_total_member_count','exclude_users_from_count');

// hide the overview edit tab if you're not on your page
function hide_overview_edit_tab() {
    if (bp_displayed_user_id() != get_current_user_id()) {
        bp_core_remove_nav_item('overview-edit');
    }
}

add_action('bp_actions', 'hide_overview_edit_tab');

/**
 * Add Groups link to Membership Directory
 */
function bp_groups_tab() {
    $button_args = array(
        'id' => 'groups',
        'component' => 'members',
        'link_text' => sprintf(__('Groups %s', 'buddypress'), '<div>' . groups_get_total_group_count() . '</div>'),
        'link_title' => __('Groups', 'buddypress'),
        'link_class' => 'groups no-ajax',
        'link_href' => '/groups',
        'wrapper' => false,
        'block_self' => false,
        'must_be_logged_in' => false
    );
    ?>
    <li id="groups-all"><?php echo bp_get_button($button_args); ?></li>
    <?php
}

add_action('bp_members_directory_member_types', 'bp_groups_tab');

/* Something like this is needed to limit what activity shows in the scroll */
function remove_bp_activity($activity_object) {
    $exclude = array('new_avatar', 'new_cover', 'friendship_created', 'joined_group');
    //error_log(print_r($activity_object->type, TRUE)); /* shows each activity name as it happens */
    if (in_array($activity_object->type, $exclude))
        $activity_object->type = false;
}

add_action('bp_activity_before_save', 'remove_bp_activity', 1, 15);

//********************************************//
//           Member type: Members             //
//********************************************//
// make each user a 'Member' user type when they successfully subscribe
add_action('ihc_action_after_subscription_activated', 'default_member_type', 10, 1);

function default_member_type($user_id) {
    if (!bp_get_member_type($user_id)) {
        bp_set_member_type($user_id, 'member');
    }
}

// if membership lapses, is canceled or deleted, remove the member type so they don't show up in the members directory count
add_action('ihc_action_after_cancel_subscription,', 'remove_member_types', 10, 1);
add_action('ihc_action_after_subscription_delete', 'remove_member_types', 10, 1);
add_action('iihc_action_level_has_expired', 'remove_member_types', 10, 1);

function remove_member_types($user_id) {
    if (bp_get_member_type($user_id) == 'member') {
        bp_set_member_type($user_id, '');
    }
}


//********************************************//
//        Makerspace related functions        //
//********************************************//
// if the membership level is makerspace/smallbusiness, add a class to the user card
// we can then see if they have the member type class and level class to determine if they should be featured
function add_featured_ms_class_directory($classes) {
    global $members_template;
    $uid = $members_template->member->ID;
    $user_meta = get_user_meta($uid);
    $user_level = $user_meta['ihc_user_levels'][0];
    $time_data = ihc_get_start_expire_date_for_user_level($uid, $user_level);
    if (!empty($user_meta['ihc_user_levels']) && strtotime($time_data['expire_time']) > time()) {
        if ($user_level == 7) {
            $classes[] = "member-level-makerspace";
        }
    }
    return $classes;
}

add_filter('bp_get_member_class', 'add_featured_ms_class_directory');

function add_featured_ms_class_profile($classes) {
    $uid = bp_displayed_user_id();
    $user_meta = get_user_meta($uid);
    $user_level = $user_meta['ihc_user_levels'][0];
    $time_data = ihc_get_start_expire_date_for_user_level($uid, $user_level);
    if (!empty($user_meta['ihc_user_levels']) && strtotime($time_data['expire_time']) > time()) {
        if ($user_level == 7) {
            $classes[] = "member-level-makerspace";
        }
    }
    return $classes;
}

add_filter('body_class', 'add_featured_ms_class_profile', 12);

// by adding last activity at init, we'll bring the featured makerspaces to the top
function featured_makerspaces_to_the_top() {
    $members = get_users('blog_id=1&fields=ID');
    foreach ($members as $user_id) {
        $user_meta = get_user_meta($user_id);
        if (!empty($user_meta['ihc_user_levels'])) {
            $user_level = $user_meta['ihc_user_levels'][0];
            $member_type = bp_get_member_type($user_id);
            if ($user_meta['ihc_user_levels'][0] == 7 && $member_type == "maker_space") {
                bp_update_user_last_activity($user_id, bp_core_current_time());
            }
        }
    }
}
add_action('bp_init', 'featured_makerspaces_to_the_top');

// Add all the countries
function bp_add_custom_country_list() {
    if (!xprofile_get_field_id_from_name('Country') && 'bp-profile-setup' == $_GET['page']) {
        $country_list_args = array(
            'field_group_id' => 1,
            'name' => 'Country',
            'description' => '',
            'can_delete' => false,
            'field_order' => 2,
            'is_required' => false,
            'type' => 'selectbox',
            'order_by' => 'custom'
        );
        $country_list_id = xprofile_insert_field($country_list_args);
        if ($country_list_id) {
            $countries = array(
                "United States",
                "Afghanistan",
                "Albania",
                "Algeria",
                "Andorra",
                "Angola",
                "Antigua and Barbuda",
                "Argentina",
                "Armenia",
                "Australia",
                "Austria",
                "Azerbaijan",
                "Bahamas",
                "Bahrain",
                "Bangladesh",
                "Barbados",
                "Belarus",
                "Belgium",
                "Belize",
                "Benin",
                "Bhutan",
                "Bolivia",
                "Bosnia and Herzegovina",
                "Botswana",
                "Brazil",
                "Brunei",
                "Bulgaria",
                "Burkina Faso",
                "Burundi",
                "Cambodia",
                "Cameroon",
                "Canada",
                "Cape Verde",
                "Central African Republic",
                "Chad",
                "Chile",
                "China",
                "Colombi",
                "Comoros",
                "Congo (Brazzaville)",
                "Congo",
                "Costa Rica",
                "Cote d'Ivoire",
                "Croatia",
                "Cuba",
                "Cyprus",
                "Czech Republic",
                "Denmark",
                "Djibouti",
                "Dominica",
                "Dominican Republic",
                "East Timor (Timor Timur)",
                "Ecuador",
                "Egypt",
                "El Salvador",
                "Equatorial Guinea",
                "Eritrea",
                "Estonia",
                "Ethiopia",
                "Fiji",
                "Finland",
                "France",
                "Gabon",
                "Gambia, The",
                "Georgia",
                "Germany",
                "Ghana",
                "Greece",
                "Grenada",
                "Guatemala",
                "Guinea",
                "Guinea-Bissau",
                "Guyana",
                "Haiti",
                "Honduras",
                "Hungary",
                "Iceland",
                "India",
                "Indonesia",
                "Iran",
                "Iraq",
                "Ireland",
                "Israel",
                "Italy",
                "Jamaica",
                "Japan",
                "Jordan",
                "Kazakhstan",
                "Kenya",
                "Kiribati",
                "Korea, North",
                "Korea, South",
                "Kuwait",
                "Kyrgyzstan",
                "Laos",
                "Latvia",
                "Lebanon",
                "Lesotho",
                "Liberia",
                "Libya",
                "Liechtenstein",
                "Lithuania",
                "Luxembourg",
                "Macedonia",
                "Madagascar",
                "Malawi",
                "Malaysia",
                "Maldives",
                "Mali",
                "Malta",
                "Marshall Islands",
                "Mauritania",
                "Mauritius",
                "Mexico",
                "Micronesia",
                "Moldova",
                "Monaco",
                "Mongolia",
                "Morocco",
                "Mozambique",
                "Myanmar",
                "Namibia",
                "Nauru",
                "Nepal",
                "Netherlands",
                "New Zealand",
                "Nicaragua",
                "Niger",
                "Nigeria",
                "Norway",
                "Oman",
                "Pakistan",
                "Palau",
                "Panama",
                "Papua New Guinea",
                "Paraguay",
                "Peru",
                "Philippines",
                "Poland",
                "Portugal",
                "Qatar",
                "Romania",
                "Russia",
                "Rwanda",
                "Saint Kitts and Nevis",
                "Saint Lucia",
                "Saint Vincent",
                "Samoa",
                "San Marino",
                "Sao Tome and Principe",
                "Saudi Arabia",
                "Senegal",
                "Serbia and Montenegro",
                "Seychelles",
                "Sierra Leone",
                "Singapore",
                "Slovakia",
                "Slovenia",
                "Solomon Islands",
                "Somalia",
                "South Africa",
                "Spain",
                "Sri Lanka",
                "Sudan",
                "Suriname",
                "Swaziland",
                "Sweden",
                "Switzerland",
                "Syria",
                "Taiwan",
                "Tajikistan",
                "Tanzania",
                "Thailand",
                "Togo",
                "Tonga",
                "Trinidad and Tobago",
                "Tunisia",
                "Turkey",
                "Turkmenistan",
                "Tuvalu",
                "Uganda",
                "Ukraine",
                "United Arab Emirates",
                "United Kingdom",
                "Uruguay",
                "Uzbekistan",
                "Vanuatu",
                "Vatican City",
                "Venezuela",
                "Vietnam",
                "Yemen",
                "Zambia",
                "Zimbabwe"
            );
            foreach ($countries as $country) {
                xprofile_insert_field(array(
                    'field_group_id' => 1,
                    'parent_id' => $country_list_id,
                    'type' => 'option',
                    'name' => $country,
                    'option_order' => $i++
                ));
            }
        }
    }
}

add_action('bp_init', 'bp_add_custom_country_list');

//********************************************//
//               MISC: Functions              //
//********************************************//
// if we want some random page to behave like a buddy press page (e.g. the blog pages)
function set_displayed_user($user_id) {
    global $bp;
    $bp->displayed_user->id = $user_id;
    $bp->displayed_user->domain = bp_core_get_user_domain($bp->displayed_user->id);
    $bp->displayed_user->userdata = bp_core_get_core_userdata($bp->displayed_user->id);
    $bp->displayed_user->fullname = bp_core_get_user_displayname($bp->displayed_user->id);
}