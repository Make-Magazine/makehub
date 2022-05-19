<?php // ?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
  </head>
  <body>
    <?php
    include '../../../../wp-load.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $args = array('meta_key'=>'wp_auth0_id', 'meta_value'   => '', 'meta_compare' => '!=',  'offset'=>'0','number'=>'1000');

    $allusers = get_users($args);
    echo 'number of users found '.count($allusers);

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
       $userToUpdate[] = array(
         "email" => $user->user_email,
         "user_metadata" =>
                     array("first_name" => $first_name,
                           "last_name" => $last_name,
                           "picture" => $user_image,
                           "user_memlevel" => $membershipType
                     )
                   );



    }

    die('stop here');

    //call Auth0 to get authorization token
    $post_data = array("client_id" => AUTH0_CLIENTID, "client_secret" => AUTH0_SECRET, "audience" => "https://makermedia.auth0.com/api/v2/", "grant_type" => "client_credentials");
    $response = postCurl("https://makermedia.auth0.com/oauth/token", array("content-type: application/json"), json_encode($post_data));

    // the response has our token for update metadata
    $json_response = json_decode($response);

    //update user

    echo 'calling '.$url.'<br/>';

    $access_token = $json_response->access_token;
    $headers = array("authorization: Bearer ".$access_token, "content-type: application/json");

    foreach($userToUpdate as $user_id=>$auth0ID){

      $url = "https://makermedia.auth0.com/api/v2/users/".$auth0ID;
      //call Auth0 to get update user information
      $post_data = array("user_metadata" =>
                      array("first_name" => "Makey",
                            "last_name" => "Robot",
                            "picture" => "https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png",
                            "user_memlevel" => $membershipType
                      )
                    );
      $authRes = postCurl($url, $headers, json_encode($post_data),"PATCH");

      //if there is an error, display here
      echo 'authRes =<br/>';
      var_dump($authRes);
      echo '<br/>';
    }

?>
</body>
</html>
