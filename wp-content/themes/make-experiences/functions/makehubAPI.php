<?php
//new rest API to build the universalNav
add_action( 'rest_api_init', function () {
  register_rest_route( 'MakeHub/v1', '/userNav', array(
    'methods' => 'GET',
    'callback' => 'make_user_info',
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
  $userID = $data['id'];
  $userEmail = $data['email'];
  $user   = get_user_by_email($userEmail );
  $avatar = get_avatar_url($userEmail);

  //first let's call rimark
  $coinBalance = get_make_coins($userEmail);
  $userProfileMenu = wp_nav_menu( array('menu' => 'Profile Dropdown', 'echo' => false));
  $return = array();
  $return['makeLogin'] =
    '<span class="login-section" style="display: block;">
        <div id="profile-view" class="dropdown v-select" style="display: flex;">
           <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="avatar" style="width: 38px; display: block;" alt="avatar" src="'.$avatar.'">
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <div class="profile-info">
                  <img class="avatar" style="width:80px" alt="avatar" src="'.$avatar.'">
                  <div class="profile-text">
                      <div class="profile-name">'.$user->display_name.'</div>
                      <div class="profile-email">'.$userEmail.'</div>
                  </div>
              </div>
              <div class="dropdown-links" id="profileLinks">'
                . $userProfileMenu .
             '</div>
              <a id="LogoutBtn" href="/wp-login.php?action=logout" title="Log Out" style="display: flex;">Log Out</a>
          </div>
      </div>
   </span>';
  $return['makeCoins'] = $coinBalance; //$coinBalance
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

  $response = new WP_REST_Response($return);
  $response->set_status(200);

  return $response;
}

//Rimark
function get_make_coins($user_email){
  $userEmail = "alicia@make.co"; //override as i'm the only one with coins
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
  if(!empty($rimarkResp->data)){
    $coins = $rimarkResp->data[0]->attributes->make_wallet->data->attributes->total_supply_owned;
    $coins = number_format($coins,4); //format with a thousand separators and up to 4 decimal places
  }else{
    $coins = 'Learn More';
  }

  return $coins;
}
?>
