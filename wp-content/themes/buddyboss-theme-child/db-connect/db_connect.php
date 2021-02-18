<?php
/*
 * this file sets up a new mysqli connection based on the database defined in a previous step
 */

$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
