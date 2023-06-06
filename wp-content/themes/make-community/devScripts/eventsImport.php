<?php
include 'db_connect.php';
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8" />
<title>Test Add Event</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<h1>Test Add Event</h1>
<?php

// Change the slug to event name and date.
function ce_change_event_slug( $result, $event_obj ) {
	if ( $result ) {
		if ( $event_obj->post_id ) {
			$author_id = 2; # ID of Makey.

			$event_slug = sanitize_title($event_obj->event_name.' '.$event_obj->event_start_date);
			$args = array( 'ID' => $event_obj->post_id,
				      'post_name' => $event_slug,
				      'post_author' => $author_id,
				      'post_status' => 'publish'
				     );
			wp_update_post( $args );
		}
	}
	
	return $result;
}


//info from mf.com
$faire_info = array("ID" => "2457",
					"name" => "Maker Faire Rangsit", //check
					"description" => "Maker Faire Rangsit", //check
					"category" => "Mini",
					"faire_shortcode" => "", //n/a
					"faire_name" => "Maker Faire Rangsit",
					"faire_year" => "2023", 
					"event_type" => "Mini",
					"event_dt" => "April 8-9, 2023",
					"event_start_dt" => "04/08/2023 12:00:00 am",
					"event_end_dt" => "04/09/2023 12:00:00 am",
					"cfm_start_dt" => "0000-00-00 00:00:00",
					"cfm_end_dt" => "0000-00-00 00:00:00",
					"cfm_url" => "",
					"faire_url" => "https://makerfairerangsit.com/2023/",
					"ticket_site_url" => "",
					"free_event" => "",
					"venue_address_street" => "The Hub Rangsit",
					"venue_address_city" => "Pathumthani",
					"venue_address_state" => "",
					"venue_address_country" => "Thailand",
					"venue_address_postal_code" => "",
					"venue_address_region" => "Asia",
					"lat" => "13.961440",
					"lng" => "100.619965");

	//Mini = Community - 675
    //Featured = Featured - 673
    //Flagship = Flagship - 674
switch ($faire_info['category']) {
	case 'Featured':
		$event_category = 673;
		break;	
	case 'Flagship':
		$event_category = 674;
		break;			
	case 'Mini':
		$event_category = 675;
		break;
	default:
		$event_category = '';			
}

//Location Information
$location_id = '';
$country = array_search($faire_info['venue_address_country'], em_get_countries());
if($country){ 
	$EM_Location = new EM_Location();
	$EM_Location->location_name = (isset($faire_info['faire_name'])? $faire_info['faire_name']:'');
	$EM_Location->location_address = (isset($faire_info['venue_address_street'])? $faire_info['venue_address_street']:'');
	$EM_Location->location_town = (isset($faire_info['venue_address_city'])? $faire_info['venue_address_city']:'');
	$EM_Location->location_state = (isset($faire_info['venue_address_state'])? $faire_info['venue_address_state']:'');
	$EM_Location->location_postcode = (isset($faire_info['venue_address_postal_code'])? $faire_info['venue_address_postal_code']:'');
	$EM_Location->location_region = (isset($faire_info['venue_address_region'])? $faire_info['venue_address_region']:'');
	$EM_Location->location_country = $country;
	$EM_Location->location_latitude = (isset($faire_info['lat'])? $faire_info['lat']:'');
	$EM_Location->location_longitude = (isset($faire_info['lng'])? $faire_info['lng']:'');

	// validate and publish
	if($EM_Location->validate()){		
		//set status to publish
		$EM_Location->post_status  = 1;
		$EM_Location->event_status = 'publish';
		
		$EM_Location->save();	
		var_dump($EM_Location);
		echo '<br/><br/>';
		$location_id = $EM_Location->post_id; 
		var_dump(em_get_location($location_id,'post_id'));

		
		echo '<p>Location POST ID: '. $location_id . '</p>';
		
	}else{		
		var_dump($EM_Location->get_errors());	
	}	
}


//event information
$event_name = $faire_info['name'] . ($faire_info['faire_year']!=''? ' - '.$faire_info['faire_year']:'');
$event_description = '<a href="'.$faire_info['faire_url'].'" target="_blank">'. $faire_info['description'].'</a>';

//start date
$date = new DateTime($faire_info['event_start_dt']);
$event_start_date = $date->format('Y-m-d');
$event_start_time = $date->format('H:i:s');

//end date
$date = new DateTime($faire_info['event_end_dt']);
$event_end_date = $date->format('Y-m-d');
$event_end_time = $date->format('H:i:s');
$event_status = 'publish';

// STOP EDITING.                        
$EM_Event = new EM_Event();
$EM_Event->event_name = $event_name;
$EM_Event->post_content = $event_description;

if($location_id!='')
	$EM_Event->location_id = $location_id;

$EM_Event->event_start_date = $event_start_date;
$EM_Event->event_end_date = $event_end_date;
$EM_Event->event_start_time = $event_start_time;
$EM_Event->event_end_time = $event_end_time;

$EM_Event->event_rsvp = false;  // Set to false to fix bug introduced in Events Manager in 5.8.

add_filter( 'em_event_save', 'ce_change_event_slug', 10, 2 );
$EM_Event->save();
remove_filter( 'em_event_save', 'ce_change_event_slug', 10, 2 );

//assign the makerfaire category to the event
wp_set_post_terms( $EM_Event->post_id, array(672, $event_category), 'event-categories' );

echo '<p>Event post ID: ', $EM_Event->post_id, '</p>';

?>
</body>