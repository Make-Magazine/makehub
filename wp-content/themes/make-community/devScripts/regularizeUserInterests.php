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
	        case "3D Printing":
			case "3D Imaging":
			case "3D Printing &amp; Imaging":
	            array_push($new_interests, "3D Printing &amp; Imaging");
	            break; 
	        case "Alternative Energy":
	        case "Energy &amp; Sustainability":
                array_push($new_interests, "Energy &amp; Sustainability");
	            break; 
	        case "Arduino":
                array_push($new_interests, "Arduino");
	            break;
	        case "Art &amp; Design":
				array_push($new_interests, "Art &amp; Sculpture");
	            break;
	        case "Artificial Intelligence":
                array_push($new_interests, "Artificial Intelligence"); // doesn't exist on MZ
	            break;
	        case "Augmented Reality":
	            array_push($new_interests, "Augmented Reality"); // doesn't exist on MZ
	            break;
	        case "Bicycles":
                array_push($new_interests, "Bikes");
	            break;
	        case "Biology":
	        case "Biotech":
                array_push($new_interests, "Biohacking");
	            break;
			case "Business":
				array_push($new_interests, "Business");
				break;
			case "CAD":
				array_push($new_interests, "CAD");
				break;
	        case "Climate Protection":
			case "Environment":
			case "Sustainability &amp; Nature":
				array_push($new_interests, "Energy &amp; Sustainability");
	            break;
	        case "Computers &amp; Mobile Devices":
	        case "Computers &amp; Mobile":
                array_push($new_interests, "Computers &amp; Mobile");
	            break;
			case "Connected Home":
				array_push($new_interests, "Connected Home");
				break;
	        case "Costumes &amp; Cosplay":
            case "Makeup &amp; Costumes":
                array_push($new_interests, "Costumes, Cosplay, and Props");
	            break;
			case "Craft &amp; Design":
				array_push($new_interests, "Craft &amp; Design");
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
			case "Entrepreneurship":
				array_push($new_interests, "Entrepreneurship");
				break;
	        case "Fabrication":
	        case "Digital Fabrication":
                array_push($new_interests, "Digital Fabrication");
	            break;
	        case "Flying &amp; Aeronatics":
	        case "Planes":
                array_push($new_interests, "Airplanes");
	            break;
	        case "Food &amp; Beverages":
	        case "Food &amp; Beverage":
                array_push($new_interests, "Food &amp; Beverage");
	            break;
	        case "Fun &amp; Games":
			case "Gaming":
                array_push($new_interests, "Fun &amp; Games");
	            break;
			case "Furniture &amp; Lighting":
				array_push($new_interests, "Furniture &amp; Lighting");
				break;
	        case "Health":
	        case "Health &amp; Biohacking":
                array_push($new_interests, "Health");
	            break;
	        case "Home":
                array_push($new_interests, "Home");
	            break;
	        case "Internet of Things":
                array_push($new_interests, "Internet of Things");
	            break;
			case "Laser Cutting":
				array_push($new_interests, "Laser Cutting");
				break;
			case "Machine Learning":
				array_push($new_interests, "Machine Learning");
				break;
			case "Maker Faire":
				array_push($new_interests, "Maker Faire");
				break;
	        case "Makerspaces":
	        case "Makerspace":
                array_push($new_interests, "Makerspace");
	            break;
			case "Metalworking":
				array_push($new_interests, "Metalworking");
				break;
	        case "Microcontrollers":
                array_push($new_interests, "Other Boards");
	            break;
	        case "Music":
                array_push($new_interests, "Music");
	            break;
	        case "Open Source":
	            array_push($new_interests, "Open Source");  // doesn't exist on MZ
	            break;
			case "Paper Crafts":
				array_push($new_interests, "Paper Crafts");
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
			case "Technology":
				array_push($new_interests, "Technology");
				break;
	        case "Vehicles":
	        case "Cars":
	            array_push($new_interests, "Cars");
	            break;
	        case "Virtual Reality":
                array_push($new_interests, "Virtual Reality"); // doesn't exist on MZ
	            break;
	        case "Wearables":
			case "Fashion":
                array_push($new_interests, "Wearables");
	            break;
	        case "Woodworking":
                array_push($new_interests, "Woodworking");
	            break;
			case "Yarncraft":
				array_push($new_interests, "Yarncraft");
				break;
	        case "Young Makers":
			case "Kids &amp; Family":
			case "Kids Under 5 yo":
                array_push($new_interests, "Young Makers"); // doesn't exist on MZ
	            break;
	    }
	    // we've build an array with all the new names of the interests, let's rewrite it
	    $wpdb->update('wp_bp_xprofile_data', array('value'=>serialize($new_interests)), array('field_id'=>208, 'user_id'=>$user_id));
	}
}
echo "</table>";
