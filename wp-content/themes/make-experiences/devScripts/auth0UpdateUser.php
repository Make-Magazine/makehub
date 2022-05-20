<?php // ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>
    <?php
    //place this before any script you want to calculate time
    $time_start = microtime(true);
    include '../../../../wp-load.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $offset = 0;
    $args = array('meta_key'=>'wp_auth0_id', 'meta_value'   => '', 'meta_compare' => '!=',
                  'offset'=>$offset,'number'=>100,'orderby'=>'ID');

    $allusers = get_users($args);
    echo 'number of users found '.count($allusers).'<br/>';

    $userToUpdate = array();

    foreach($allusers as $user){
      //get user first and last name
      $last_name = get_user_meta( $user->ID, 'last_name', true );
      $first_name = get_user_meta( $user->ID, 'first_name', true );

      //get user avatar
      $user_image =
       bp_core_fetch_avatar (
           array(  'item_id' => $user->ID, // id of user for desired avatar
                   'type'    => 'full',
                   'html'   => FALSE     // FALSE = return url, TRUE (default) = return img html
           )
       );

       //get user membership level
       $membershipType = checkMakeCoMems($user);
       if($membershipType!='none'){
         $userToUpdate[] = array(
           "email" => $user->user_email,
           "user_metadata" =>
                       array("first_name" => $first_name,
                             "last_name" => $last_name,
                             "picture" => $user_image,
                             "membership_level" => $membershipType));
           echo $user->ID.' '.$user->user_email.' '.$first_name.' '.$last_name.' '.$membershipType.'<br/>';
       }
    }
    echo 'users to update '.count($userToUpdate).'<br/>';

    $filename = dirname(__FILE__).'/import/auth0Update'.$offset.'.json';

    echo 'writing to file '.$filename.'<br/>';
    file_put_contents($filename, json_encode($userToUpdate),FILE_APPEND);

    //call Auth0 to get authorization token
    $post_data = array("client_id" => "Ya3K0wmP182DRTexd1NdoeLolgXOlqO1",
                       "client_secret" => "eu9e8LC7fvrKb9ou5JglKdIv67QDvhkiMg32vm0q433SMXD5PW3elCV7OuiSFs6n",
                       "audience" => "https://makermedia.auth0.com/api/v2/",
                       "grant_type" => "client_credentials");
    $response = postCurl("https://makermedia.auth0.com/oauth/token",
                array("content-type: application/json"), json_encode($post_data));
    $response = json_decode($response);

    if (!isset($response->access_token)) {
      var_dump($response);
      die('access token not set');
    }

    $access_token = $response->access_token;
    echo '$access_token = '.$access_token.'<br/>';

    //call auth0 to get connection
    $url = "https://makermedia.auth0.com/api/v2/connections";
    $headers = array("authorization: Bearer " . $access_token, "content-type: application/json");
    $authRes = basicCurl($url, $headers);
    $authRes = json_decode($authRes);

    $connection = '';
    foreach($authRes as $result){
       if($result->name==="DB-Make-Community"){
          $connection = $result->id;
       }
    }

    if($connection==''){
      var_dump($authRes);
      die('connection not set');
    }

    echo '$connection id = '.$connection .'<br/>';

?>
</body>
</html>
