<?php
include 'db_connect.php';
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo("<h1>We are replacing all the Topic interests users have picked to match the categories on makezine</h1>");

global $wpdb;
$user_query = 'SELECT DISTINCT user_id FROM wp_bp_xprofile_data WHERE value IS NOT NULL AND field_id = 208 ORDER BY user_id';
$users = $wpdb->get_results($user_query);


echo "<table>";
foreach ( $users as $user) {
    $user_id = $user->user_id;
    $interest_query = 'SELECT value FROM wp_bp_xprofile_data WHERE user_id = ' . $user_id . ' AND field_id = 208';
    $interests = unserialize($wpdb->get_results($interest_query)[0]->value);
    echo '<tr><td>' . $user_id . '</td><td>' . implode(", ", $interests) . '</td></tr>';
	$new_interests = array();
	foreach($interests as $interest) {
	    switch($interest) {
	        case "3D Imaging":
	            array_push($new_interests, "3D Imaging");
	            break; 
	        case "3D Printing":
	            array_push($new_interests, "3D Printing");
	            break; 
	        case "Alternative Energy":
                array_push($new_interests, "Energy &amp; Sustainability");
	            break; 
	        case "Arduino":
                array_push($new_interests, "Arduino");
	            break;
	        case "Art &amp; Design":
                array_push($new_interests, "Craft &amp; Design");
	            break;
	        case "Artificial Intelligence":
                array_push($new_interests, "Artificial Intelligence"); // there was a note that the MZ match was "not the same"
	            break;
	        case "Augmented Reality":
	            array_push($new_interests, "Augmented Reality"); // doesn't exist on MZ
	            break;
	        case "Bicycles":
                array_push($new_interests, "Bikes");
	            break;
	        case "Biology":
                array_push($new_interests, "Biotech");
	            break;
	        case "Climate Protection":
	            array_push($new_interests, "Climate Protection");  // can't rename more than one interest to "Energy & Sustainability"
	            break;
	        case "Computers &amp; Mobile Devices":
                array_push($new_interests, "Computers &amp; Mobile");
	            break;
	        case "Costumes &amp; Cosplay":
                array_push($new_interests, "Makeup &amp; Costumes");
	            break;
	        case "Drones":
                array_push($new_interests, "Drones");
	            break;
	        case "Education":
                array_push($new_interests, "Education");
	            break;
	        case "Electronics":
                array_push($new_interests, "Electronics");
	            break;
	        case "Engineering":
	            array_push($new_interests, "Engineering"); // doesn't exist on MZ
	            break;
	        case "Fabrication":
                array_push($new_interests, "Digital Fabrication");
	            break;
	        case "Fashion":
	            array_push($new_interests, "Fashion");  // can't rename more than one interest to "Wearables"
	            break;
	        case "Flying &amp; Aeronatics":
                array_push($new_interests, "Planes");
	            break;
	        case "Food &amp; Beverages":
                array_push($new_interests, "Food &amp; Beverage");
	            break;
	        case "Fun &amp; Games":
                array_push($new_interests, "Fun &amp; Games");
	            break;
	        case "Gaming":
	            array_push($new_interests, "Gaming");  // can't rename more than one interest to "Fun & Games"
	            break;
	        case "Hacks":
	            array_push($new_interests, "Hacks"); // doesn't exist on MZ
	            break;
	        case "Health":
	        case "Health &amp; Biohacking":
                array_push($new_interests, "Health &amp; Biohacking");
	            break;
	        case "Home":
                array_push($new_interests, "Home");
	            break;
	        case "Internet of Things":
                array_push($new_interests, "Internet of Things");
	            break;
	        case "Kids &amp; Family":
                array_push($new_interests, "Kids &amp; Family"); // doesn't exist on MZ
	            break;
	        case "Kids Under 5 yo":
                array_push($new_interests, "Kids Under 5 yo"); // doesn't exist on MZ
	            break;
	        case "Makerspaces":
                array_push($new_interests, "Makerspace");
	            break;
	        case "Microcontrollers":
                array_push($new_interests, "Microcontrollers");
	            break;
	        case "Music":
                array_push($new_interests, "Music");
	            break;
	        case "Open Source":
	            array_push($new_interests, "Open Source");  // doesn't exist on MZ
	            break;
	        case "Photography &amp; Video":
                array_push($new_interests, "Photography &amp; Video");
	            break;
	        case "Raspberry Pi":
                array_push($new_interests, "Raspberry Pi");
	            break;
	        case "Robotics":
                array_push($new_interests, "Robotics");
	            break;
	        case "Science":
                array_push($new_interests, "Science");
	            break;
	        case "Space":
                array_push($new_interests, "Space");
	            break;
	        case "Sustainability &amp; Nature":
	            array_push($new_interests, "Sustainability &amp; Nature");  // can't rename more than one interest to "Energy & Sustainability"
	            break;
	        case "Vehicles":
	            array_push($new_interests, "Vehicles"); // the analagous category on MZ is "Drones & Vehicles" which would look redundant when we already have "Drones" as an interest
	            break;
	        case "Virtual Reality":
                array_push($new_interests, "Virtual Reality");
	            break;
	        case "Wearables":
                array_push($new_interests, "Wearables");
	            break;
	        case "Woodworking":
                array_push($new_interests, "Woodworking");
	            break;
	        case "Young Makers":
                array_push($new_interests, "Young Makers"); // doesn't exist on MZ
	            break;
	    }
	    // we've build an array with all the new names of the interests, let's rewrite it
	    $wpdb->update('wp_bp_xprofile_data', array('value'=>serialize($new_interests)), array('field_id'=>208, 'user_id'=>$user_id));
	}
}
echo "</table>";
