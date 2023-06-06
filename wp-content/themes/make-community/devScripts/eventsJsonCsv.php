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
/*
The Below code is in case we ever want to pull directly from the gravity form
//pull entries from form 253
$api_url = "https://makerfaire.com/wp-json/gf/v2/entries?form_ids[0]=253";
//$api_url = "https://mfairestage.wpengine.com/wp-json/gf/v2/entries?form_ids[0]=253";

//curl call goes here

// Decode the JSON in the file
$faires = json_decode($faire_content, true);
if($faires){
	echo $faires['total_count'] . ' faires found<br/>';
	$faire_count = 0;
	foreach($faires['entries'] as $faire){
		if($faire["is_approved"] != "1"){
			//continue;
		}
		$faire_count++;
		$event_name = $faire['5'];
		$event_desc = $faire['403'];
		$event_url = $faire['399'];
		$event_date = $faire['402'];
		$event_single_date = $faire['399'];
		$event_end_date = $faire['401'];
		$cfm_date = $faire['341'];
		$event_type = $faire['336'];
	}
	echo 'total approved faires: '.$faire_count.'<br/>';
}else{
	var_dump($faire_content);
}
var_dump($faires);
die();*/

$api_url = 'https://makerfaire.com/query/?type=map&categories=mini,featured,flagship&upcoming=true';

$user_name = 'ck_af51f732cf514468cb8e3d02c217a716038f8308';
$password  = 'cs_8f5df179ff4f061337b87d032f7c6b32144fd848';
$headers = array(
'Content-Type: application/json',
'Authorization: Basic '. base64_encode("$user_name:$password")
);

$faire_content = basicCurl($api_url, $headers);
$faires = json_decode($faire_content, true);

// CSV file name
$csv = 'files/global_mfaires.csv';

// File pointer in writable mode
$file = fopen($csv, 'w');

 // send the column headers
fputcsv($file, array('event_id', 'event_categories', 'event_name','event_slug', 'post_content', 
                     'event_start_date', 'event_end_date', 'event_all_day', 'featured_image', 
                     'location_name', 'location_slug', "location_address", "location_town", 
                     "location_state", "location_country", "location_postcode", "location_region",
					 "location_latitude", "location_longitude"));

$count=0;
echo $faires['header']['results']. ' results returned<br/>';

//any upcoming faires?
if(isset($faires['Locations'])) {
	$em_countries = em_get_countries();	//get a list of countries from event manager
	
	foreach($faires['Locations'] as $faire){    
		$count++; 
		$event_type	= ($faire["event_type"]=='Mini'? 'Community':$faire["event_type"]);
		$faire_name = $faire["faire_name"].($faire["faire_year"]!=''?' - ' .$faire["faire_year"]:'');

		//find country code
		$country = '';
		if($faire['venue_address_country'] !=''){
			$country = array_search($faire['venue_address_country'], $em_countries);
			if($country === false){
				$country = '';
				echo 'country not found -';
				var_dump($faire);
				echo '<br/><br/>';
			}
		}		

		$location_name = $faire['name'].($faire[ "venue_address_street"]!=''?' '.$faire[ "venue_address_street"]:'').
						($faire["venue_address_city"]  	!=''?' '.$faire["venue_address_city"]:''). 
						($faire["venue_address_state"] 	!=''?' '.$faire["venue_address_state"]:'').  
						($country						!='' ? ' '. $country:'').  
						($faire["venue_address_postal_code"]!='' ? ' '. $faire["venue_address_postal_code"]:''). 
						($faire["venue_address_region"]		!='' ? ' '. $faire["venue_address_region"]:'');
	    
	    // Write the data to the CSV file    
	    $output = array($faire["ID"], $event_type, 
	    	$faire_name, //event name
	    	str_replace(' ', '_', $faire_name), //event slug						
	    	//post content	    	
			'For more information, check out the <a target="_blank" href="'.$faire['faire_url'].'">'.$faire_name.' website.</a>', 
			//'event_start_date', 'event_end_date', 'event_all_day',
			$faire['event_start_dt'], $faire['event_end_dt'],true, 
			$faire['faire_image'],
			//location info
			$faire["faire_name"], //location_name
			str_replace(' ', '_', $location_name), //location_slug
			//location address info
			$faire[ "venue_address_street"], $faire["venue_address_city"], $faire["venue_address_state"], $country,
			$faire["venue_address_postal_code"], $faire["venue_address_region"], $faire["lat"], $faire["lng"]);

	    fputcsv($file, $output);
	}
}
fclose( $file );
echo 'done writing file '.$csv.'. '.$count.' records written';
?>
</body>