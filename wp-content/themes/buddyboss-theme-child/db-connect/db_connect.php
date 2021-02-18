<?php
/*
 * this file sets up a new mysqli connection based on the database defined in a previous step
 */

$mysqli = new mysqli($host,$user,$password, $database);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
