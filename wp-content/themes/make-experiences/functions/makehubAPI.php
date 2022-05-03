<?php
header("Access-Control-Allow-Origin: *");
//new rest API to build the universalNav
add_action( 'rest_api_init', function () {
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
  $user   	 = get_user_by('email', $userEmail);

  $banner_text = "Join Now";
  $banner = "https://make.co/wp-content/universal-assets/v1/images/join-now-banner.png";
  //get membership
  if(isset($user) && isset($user->ID)){
    $userName = $user->display_name;
    $avatar = get_avatar_url($userEmail);

    $headers = setMemPressHeaders();
    $memberInfo = basicCurl("https://make.co/wp-json/mp/v1/members/".$user->ID, $headers);
    $memberArray = json_decode($memberInfo);
	$membershipButton = "<a href='https://make.co/join/' class='btn membership-btn'>Join Now!</a>";
    if(isset($memberArray->active_memberships) &&
           is_array($memberArray->active_memberships)
        && !empty($memberArray->active_memberships)){

      //see if they are an active premium member
      $key = array_search('Premium Member', array_column($memberArray->active_memberships, 'title'));
      if($key!== false){
        //Premium Membership
        $banner_text = "Premium Member";
        $banner = "https://make.co/wp-content/universal-assets/v1/images/premium-banner.png";
		$membershipButton = "";
      }else{
        //free membership, upgrade now
        $banner_text = "Upgrade Membership";
        $banner = "https://make.co/wp-content/universal-assets/v1/images/upgrade-banner.png";
		$membershipButton = "<a href='https://make.co/join/' class='btn membership-btn'>Upgrade Membership</a>";
      }
    }else{
      //not a member on make.co
    }
  }else{
    //not a user on make.co
    $userName = '';
    $avatar = "https://make.co/wp-content/universal-assets/v1/images/default-makey.png";
  }
  //if non member - banner with join - grey makey

  //if previously member - banner with join - use their icon
  //it free membership - banner with upgrade
  //if Premium - banner with premium
  //$banner =
  //$banner_text =

  //build user drop down
  $userProfileMenu = wp_nav_menu( array('menu' => 'Profile Dropdown', 'echo' => false));

  $return = array();
  $return['makeLogin'] =
    '<span class="login-section" style="display: block;">
        <div id="profile-view" class="dropdown v-select" style="display: flex;">
           <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="avatar" alt="avatar" src="'.$avatar.'">
              <img class="avatar-banner" src="'.$banner.'" alt="'.$banner_text.'" />
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <div class="profile-info">
                  <img class="avatar" style="width:80px" alt="avatar" src="'.$avatar.'">
                  <div class="profile-text">
                      <div class="profile-name">'.$userName.'</div>
                      <div class="profile-email">'.$userEmail.'</div>
                  </div>
              </div>
			  '.$membershipButton.'
              <div class="dropdown-links" id="profileLinks">'
                . $userProfileMenu .
             '</div>
              <a id="LogoutBtn" href="/wp-login.php?action=logout" title="Log Out" style="display: flex;">Log Out</a>
          </div>
      </div>
   </span>';

  $return['makeJoin'] = '';
              /*'<span class="search-separator nav-separator"></span>
                <div class="search-button-wrapper">
                    <div class="subscribe-call-out">
                        <div class="subscribe-text">
                            <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-call-out&amp;utm_content=launch">
                                Join
                            </a>
                        </div>
                        <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-call-out&amp;utm_content=launch">
                            <img src="https://make.co/wp-content/universal-assets/v1/images/magazine-nav-subscribe-single.jpg?v=80" id="nav-subscribe-img" alt="Get Make: Magazine Issue 79">
                        </a>
                        <div class="subscribe-pop-out">
                            <a target="_none" href="https://make.co/join/?utm_source=make&amp;utm_medium=universalnav&amp;utm_campaign=subscribe-popout&amp;utm_content=launch">
                                <img src="https://make.co/wp-content/universal-assets/v1/images/subscribe-today.jpg?v=80" alt="Subscribe Today to Make: Magazine">
                            </a>
                        </div>
                    </div>

                    <div id="sb-search" class="sb-search"></div>

                </div>
                <div class="user-wrap user-wrap-container menu-item-has-children">
                </div>
            </div>';*/

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

  //now let's see how many coins they have
  $url = 'https://devapi.rimark.io/api/makes?populate=*&filters[user_email][$eq]='.$user_email;
  $headers = array("authorization: Bearer ".$jwt);
  $rimarkResp = json_decode(basicCurl($url, $headers));

  $coins = '';
  if(!empty($rimarkResp->data)){
    $coins = $rimarkResp->data[0]->attributes->make_wallet->data->attributes->total_supply_owned;
    $coins = number_format($coins,4); //format with a thousand separators and up to 4 decimal places
  }

  return $coins;
}
?>
