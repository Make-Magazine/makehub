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

//call Auth0 to get authorization token
$post_data = array("client_id" => AUTH0_CLIENTID, "client_secret" => AUTH0_SECRET, "audience" => "https://makermedia.auth0.com/api/v2/", "grant_type" => "client_credentials");
$response = postCurl("https://makermedia.auth0.com/oauth/token", array("content-type: application/json"), json_encode($post_data));

// the response has our token for update metadata
$json_response = json_decode($response);

echo 'try update user<br/>';
//update user
$url = "https://makermedia.auth0.com/api/v2/users/auth0|Make-Community|2";
echo 'calling '.$url.'<br/>';

$access_token = $json_response->access_token;

//call Auth0 to get update user information
$post_data = array("user_metadata" =>
                    array("first_name" => "Makey",
                          "last_name" => "Robot",
                          "picture" => "https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png",
                    )
                  );
$headers = array("authorization: Bearer ".$access_token, "content-type: application/json");

echo '$post_data<br/>';
var_dump(json_encode($post_data));
echo '<br/>';
$authRes = postCurl($url, $headers, json_encode($post_data),"PATCH");
echo 'authRes =<br/>';
var_dump($authRes);
echo '<br/>';
?>
</body>
</html>
