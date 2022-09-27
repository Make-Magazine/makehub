<?php
include 'db_connect.php';

if(isset($_GET['action']) && isset($_GET['bp_gid']) && isset($_GET['users'])) {
    $group_id = $_GET['bp_gid'];
    $users = $_GET['users'];
    $users_array = explode(",", $users);

    foreach ($users_array as $user_id) {
      echo 'adding user '.$user_id.' to group '.$group_id.'<br/>';
      $result = groups_join_group( $group_id, $user_id );
      if(!$result){
        echo 'error in adding '.$user_id.' to group '.$group_id.'<br/>';
      }
    }
}else{
  if(!isset($_GET['action']))                        echo 'Please submit with URL variable: action (any value)<br/>';
  if(!isset($_GET['bp_gid']) || $_GET['bp_gid']=='') echo 'Please submit with URL variable bp_gid set with the group you want to add users to<br/>';
  if(!isset($_GET['users']) || $_GET['users']=='')   echo 'Enter users as comma separated Array in URL variable: users<br/>';
}
