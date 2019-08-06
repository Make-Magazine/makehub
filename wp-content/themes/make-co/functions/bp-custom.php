<?php 
/**
* If you are using BP 2.1+, this will insert a Country selectbox.
* Add the function to bp-custom.php and then visit .../wp-admin/users.php?page=bp-profile-setup
*/
function bp_add_custom_country_list() {
 
	if ( !xprofile_get_field_id_from_name('Country') && 'bp-profile-setup' == $_GET['page'] ) {
 
		$country_list_args = array(
		       'field_group_id'  => 1,
		       'name'            => 'Country',
		       'description'	 => 'Please select your country',
		       'can_delete'      => false,
		       'field_order' 	 => 2,
		       'is_required'     => false,
		       'type'            => 'selectbox',
		       'order_by'	 => 'custom'
 
		);
 
		$country_list_id = xprofile_insert_field( $country_list_args );
 
		if ( $country_list_id ) {
 
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
			
			foreach (  $countries as $country ) {
				
				xprofile_insert_field( array(
					'field_group_id'	=> 1,
					'parent_id'		=> $country_list_id,
					'type'			=> 'option',
					'name'			=> $country,
					'option_order'   	=> $i++
				));
				
			}
 
		}
	}
}
add_action('bp_init', 'bp_add_custom_country_list');

/* add more info to the member loop if it exists
function add_info_to_members_loop() {
	// if a field is set to hidden, we'll save it to an array to check whether we should display it or not
	$hidden_fields = bp_xprofile_get_hidden_fields_for_user(bp_get_member_user_id());

	if(xprofile_get_field_data('country', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('country'), $hidden_fields)) {
		echo "<p class='profile-field'><b>Country:</b> " . xprofile_get_field_data('country', bp_get_member_user_id()) . "</p>";
	}
	if(xprofile_get_field_data('city', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('city'), $hidden_fields)) {
   	echo "<p class='profile-field'><b>City:</b> " . xprofile_get_field_data('city', bp_get_member_user_id()) . "</p>";
	}
	//this gets the cover image, but not sure how to add it to the css for each entry
	$member_cover_image_url = bp_attachments_get_attachment('url', array(
          'object_dir' => 'members',
          'item_id' =>bp_get_member_user_id(),
        ));
}
add_action( 'bp_directory_members_item', 'add_info_to_members_loop' ); */

function youzer_add_custom_meta_fields() {
	// if a field is set to hidden, we'll save it to an array to check whether we should display it or not
	$hidden_fields = bp_xprofile_get_hidden_fields_for_user(bp_get_member_user_id());
	
	echo("<span class='yz-name'>");
	if(xprofile_get_field_data('country', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('country'), $hidden_fields)) {
		echo "<i class='fas fa-globe-americas'></i> " . xprofile_get_field_data('country', bp_get_member_user_id());
	}
	echo("</span>");
	echo("<span class='yz-name'>");
	if(xprofile_get_field_data('city', bp_get_member_user_id()) && !in_array(xprofile_get_field_id_from_name('city'), $hidden_fields)) {
		echo "<i class='fas fa-city'></i> " . xprofile_get_field_data('city', bp_get_member_user_id());
	} 
	echo("</span>");
}
add_action( 'bp_directory_members_item_meta', 'youzer_add_custom_meta_fields' );

// remove last active status from member directory
add_filter( 'bp_nouveau_get_member_meta', 'ps_remove_last_active',10,3 );
function ps_remove_last_active ( $meta, $member, $is_loop ){
	$meta['last_activity'] = '';
	return $meta;
} 

// add sidebar to members directory
function yzc_register_members_directory_sidebars() {
    register_sidebar(
        array (
            'name' => __( 'Members Directory Sidebar', 'youzer' ),
            'id' => 'yz-members-directory-sidebar',
            'description' => __( 'Members Directory Sidebar', 'youzer' ),
            'before_widget' => '<div id="%1$s" class="widget-content %2$s">',
            'after_widget' => "</div>",
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );
}
add_action( 'widgets_init', 'yzc_register_members_directory_sidebars' );
/**
 * Call Sidebar.
 */
function yzc_members_directory_sidebar() {
    if ( ! bp_is_members_directory() ) {
        return;
    }
    if ( is_active_sidebar( 'yz-members-directory-sidebar' ) ) {
        echo '<div class="yz-sidebar-column yz-members-directory-sidebar youzer-sidebar"><div class="yz-column-content">';
        dynamic_sidebar( 'yz-members-directory-sidebar' );
        echo '</div></div>';
    }
}
add_action( 'bp_after_directory_members', 'yzc_members_directory_sidebar' );

/**
 * If there is certain text in Youzer that isn't descriptive enough, we can change it here
 */
function yz_translate_youzer_text( $translated_text ) {
    switch ( $translated_text ) {
        case 'widgets settings' :
            $translated_text = __( 'Overview Settings', 'youzer' );
            break;
		  case 'Filter' :
            $translated_text = __( 'Order', 'youzer' );
            break;
    }
    
    return $translated_text;
}
add_filter( 'gettext', 'yz_translate_youzer_text', 10 );
