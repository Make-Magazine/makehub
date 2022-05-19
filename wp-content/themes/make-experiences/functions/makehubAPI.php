<?php
function initCors() {
	$origin_url = '*';
	// Check if production environment or not
	if (NETWORK_HOME_URL === 'https://make.co') {
		$origin_url = NETWORK_HOME_URL;
	}
	header( 'Access-Control-Allow-Origin: ' . $origin_url );
	header( 'Access-Control-Allow-Methods: GET' );
	header( 'Access-Control-Allow-Credentials: true' );
}

//new rest API to build the universalNav
add_action( 'rest_api_init', function () {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', initCors());
	register_rest_route( 'MakeHub/v1', '/userNav', array(
		'methods' => 'GET',
		'callback' => 'make_user_info',
		'permission_callback' => '__return_true',
		'args' => array (
			'email' => array (
			    'required' => true,
			    'validate_callback' => 'is_email'
			)
		)
	) );
} );


// Returns the User Information for the right hand side of the Universal nav
function make_user_info( $data ) {
  //retrieve user specific information
  $userEmail = $data['email'];

  //Determine $make coin balance
  $coinBalance = get_make_coins($userEmail);

  $return['makeCoins'] = $coinBalance; //$coinBalance

  $response = new WP_REST_Response($return);
  $response->set_status(200);

  return $response;
}

//Rimark
function get_make_coins($user_email){
  //First do the authentication
  $url = "https://devapi.rimark.io/api/auth/local";

  $post_data = array("identifier" => "webmaster@make.co",
                     "password"   => "AHxv2sj3hK*rWpF");
  $headers = array("content-type: application/json");
  $authRes = postCurl($url, $headers, json_encode($post_data));
  $authRes = json_decode($authRes);

  //returns jwt key
  $jwt = $authRes->jwt;
	//$jwt = "d235e4ae83b20485e600769f5df678dcfedb0fbcf61a97d96b361c9ba753f84bbba56c54f8d10957acd4b43759d85eabfdcc24b14dbec8fe20a3f42e36de82e13c9604709ce043188ca0ed6919c9691f729ac14a3d2caa7eda6278b36bcca5f7027bc91bc8b10857b997b613bd157cdc26dd1a4920764ed88588767640e472ed";

	//now let's see how many coins they have
  $url = 'https://devapi.rimark.io/api/makes?filters[user_email][$eq]='.$user_email;
  $headers = array("authorization: Bearer ".$jwt);
  $rimarkResp = json_decode(basicCurl($url, $headers));

  $coins = '';
  if(!empty($rimarkResp->data)){
    $coins = $rimarkResp->data[0]->attributes->total_supply_owned;
    $coins = number_format($coins,2); //format with a thousand separators and up to 2 decimal places
		$coins = '$MAKE<br/><a target="_blank" href="https://beta.rimark.io/?target=219f76ovo2v0fi2nn9es0x9wf">'.$coins.'</a>';
  }else{
		$coins = '$MAKE<br/><a target="_blank" href="#">Learn More</a>';
	}

  return $coins;
}
?>
