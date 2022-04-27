<?php
//new rest API to build the universalNav
add_action( 'rest_api_init', function () {
  register_rest_route( 'MakeHub/v1', '/userNav/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'make_user_info',
  ) );
} );

// Returns the User Information for the right hand side of the Universal nav
function make_user_info( $data ) {
  $userID = $data['id'];
  $userEmail = "alicia@make.co";
  //first let's call rimark
  $coinBalance = get_make_coins($userEmail);

  $return = array();
  $return['makeLogin'] = '<span class="login-section" style="display: block;">
                  <a id="LoginBtn" href="/wp-login.php?redirect_to=https://www.makehub.local/dashboard/%3Flogin%3Dtrue" title="Log In" style="display: none;">Log In</a>
                  <div id="profile-view" class="dropdown v-select" style="display: flex;">
                      <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <img class="avatar" style="width: 38px; display: block;" alt="avatar" src="https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png">
                      </a>

                      <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                          <div class="profile-info">
                              <img class="avatar" style="width:80px" alt="avatar" src="https://www.makehub.local/wp-content/uploads/avatars/2/5e8cd4bac4027-bpthumb.png">
                              <div class="profile-text">
                                  <div class="profile-name">makey</div>
                                  <div class="profile-email">webmaster@make.co</div>
                              </div>
                          </div>
                          <div class="dropdown-links" id="profileLinks">
                          <ul id="header-my-account-menu" class="bb-my-account-menu has-icon"><li class="menu-item icon-added"><a href="https://make.co/dashboard"><i class="_mi _before buddyboss bb-icon-board-list" aria-hidden="true"></i><span>My Dashboard</span></a></li><li class="menu-item menu-item-facilitator-portal"><a href="https://make.co/edit-submission/"><i class="_mi _before buddyboss bb-icon-graduation-cap" aria-hidden="true"></i><span>Facilitator Portal</span></a></li><li class="menu-item menu-item-event-cart"><a href="https://make.co/registration-checkout/?event_cart=view"><i class="_mi _before buddyboss bb-icon-shopping-cart" aria-hidden="true"></i><span>Event Cart</span></a></li><li class="bp-menu bp-profile-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/profile/"><i class="_mi _before buddyboss bb-icon-user-alt" aria-hidden="true"></i><span>Profile</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-public-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/">View</a></li><li class="bp-menu bp-edit-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/edit/">Edit</a></li><li class="bp-menu bp-change-avatar-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/change-avatar/">Profile Photo</a></li><li class="bp-menu bp-change-cover-image-sub-nav menu-item no-icon"><a href="https://make.co/members/me/profile/change-cover-image/">Cover Photo</a></li></ul></div></li><li class="bp-menu bp-settings-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/settings/"><i class="_mi _before buddyboss bb-icon-settings" aria-hidden="true"></i><span>Account</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-settings-notifications-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/notifications/">Email Preferences</a></li><li class="bp-menu bp-view-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/profile/">Privacy</a></li><li class="bp-menu bp-blocked-members-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/blocked-members/">Blocked Members</a></li><li class="bp-menu bp-group-invites-settings-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/invites/">Group Invites</a></li><li class="bp-menu bp-export-sub-nav menu-item no-icon"><a href="https://make.co/members/me/settings/export/">Export Data</a></li></ul></div></li><li class="bp-menu bp-friends-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/friends/"><i class="_mi _before buddyboss bb-icon-users" aria-hidden="true"></i><span>Connections</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-my-friends-sub-nav menu-item no-icon"><a href="https://make.co/members/me/friends/">My Connections</a></li><li class="bp-menu bp-requests-sub-nav menu-item no-icon"><a href="https://make.co/members/me/friends/requests/">Requests</a></li></ul></div></li><li class="bp-menu bp-groups-nav menu-item menu-item-has-children"><a href="https://make.co/members/me/groups/"><i class="_mi _before buddyboss bb-icon-groups" aria-hidden="true"></i><span>Groups</span></a><div class="wrapper ab-submenu"><ul class="bb-sub-menu"><li class="bp-menu bp-groups-create-nav menu-item no-icon"><a href="https://make.co/groups/create/">Create Group</a></li><li class="bp-menu bp-my-groups-sub-nav menu-item no-icon"><a href="https://make.co/members/me/groups/">My Groups</a></li><li class="bp-menu bp-group-invites-sub-nav menu-item no-icon"><a href="https://make.co/members/me/groups/invites/">Invitations</a></li></ul></div></li></ul></div>
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
  }else{
    $coins = 0;
  }

  return number_format($coins,0);
}
?>
