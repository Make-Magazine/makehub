<?php //
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$membersFile = dirname(__FILE__).'/import/auth0Resp/merged-file.json';
if(!file_exists($membersFile)){
  die('Members file not found');
}
$errors = file_get_contents(dirname(__FILE__).'/import/auth0Resp/merged-file.json');

// Decode the JSON file
$json_data = json_decode($errors,true);
$users = array();
$errors = array();
foreach($json_data as $json_error){
  $user = $json_error['user'];

  $userMetaData = $user['user_metadata'];

  $users[] = array("email"=>$user['email'],
  "first_name"=>$userMetaData['first_name'],
  "last_name"=>$userMetaData['last_name'],
  "picture"=>$userMetaData['picture'],
  "membership_level"=>$userMetaData['membership_level']);

  $errors[$json_error['errors'][0]['code']] = $json_error['errors'][0]['message'];
}
echo count($users).' users in error<br/>';

/*
//build the json output
$users = json_encode($users);

//save json output to a file
$filename = dirname(__FILE__).'/import/auth0Retry.json';
echo 'writing to file '.$filename.'<br/>';
file_put_contents($filename, $users,FILE_APPEND);
*/

//create CSV output
// Open a file in write mode ('w')
echo 'writing to file '.dirname(__FILE__).'/import/users.csv'.'<br/>';
$fp = fopen(dirname(__FILE__).'/import/users.csv', 'w');

// Loop through file pointer and a line
foreach ($users as $user) {
    fputcsv($fp, $user);
}

fclose($fp);

die();
$count=0;
$user = array();
//loop through the members file - format email,	first_name,	last_name,	mem level
while (($line = fgetcsv($file)) !== FALSE) {
  $count++;
  $email = $line[0];
  $first_name = $line[1];
  $last_name = $line[2];
  $membership_level = $line[3];

  $user = get_user_by('email',$email);
  if($user && isset($user->ID)){
    //retrieve the members photo
    $picture = bp_core_fetch_avatar (array(  'item_id' => $user->ID, 'object' => 'user', 'type' => 'thumb',
                        'html'   => FALSE));

    $users[] =  array(
      "email" => $email,
      "user_metadata" =>
                  array("first_name" => $first_name,
                        "last_name" => $last_name,
                        "picture" => $picture,
                        "membership_level" => $membership_level));

  }else{
    echo 'error getting user information for '.$email.'<br/>';
    echo 'response is:<br/>';
    var_dump($user);
  }
}
echo 'members file had '.$count.' users<br/>';
echo 'found '.count($users).' to update<br/>';

//build the json output
$users = json_encode($users);

//save json output to a file
$filename = dirname(__FILE__).'/import/auth0Update.json';

echo 'writing to file '.$filename.'<br/>';
file_put_contents($filename, $users,FILE_APPEND);
