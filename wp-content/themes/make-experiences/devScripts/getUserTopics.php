<?php
include 'db_connect.php';
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$users = get_users();
echo "<table>";
foreach ( $users as $user ) {
	if(xprofile_get_field_data(208, $user->ID, "comma")) {
    	echo '<tr><td>' . esc_html( $user->display_name ) . '</td><td>' . $user->ID . '</td><td>' . xprofile_get_field_data(208, $user->ID, "comma") . '</td></tr>';
	}
}
echo "</table>";
