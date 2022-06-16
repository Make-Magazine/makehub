<?php
/* Used to set the memberpress headers for the api call */
function setMemPressHeaders($datastring = null) {
 $headers = array();
 $headers[] = 'MEMBERPRESS-API-KEY: apXPTMEf4O'; // Your API KEY from MemberPress Developer Tools Here -- 0n8p2YkomO for local apXPTMEf4O for prod
 $headers[] = 'Content-Type: application/json';
 if($datastring){
   $headers[] = 'Content-Length: ' . strlen($datastring);
 }
 return $headers;
}

/* This function will check if user is a premium member, non member or eligible for upgrade */
function checkMakeCoMems($user) {
  if(!isset($user->ID)){
    error_log('missing user id in checkMakeCoMems!!!');
    error_log(print_r($user,TRUE));
    return;
  }
  $headers = setMemPressHeaders();
  $memberInfo = basicCurl(NETWORK_HOME_URL."/wp-json/mp/v1/members/".$user->ID, $headers);
  $memberArray = json_decode($memberInfo);

  $membershipType = 'none';
  if(isset($memberArray->active_memberships)) {
    //create an array of memberships using the title field
    $memArray = array_column($memberArray->active_memberships, 'title');

    if(!empty($memArray)){
      //look for the needle in any part of the title field in the multi level array
      if(array_find('premium', $memArray, 'title') !== false ||
         array_find('multi-seat', $memArray, 'title') !== false ||
         array_find('school maker faire', $memArray, 'title') !== false ||
         array_find('magazine', $memArray, 'title') !== false
       ){
        //Premium Membership
        $membershipType = "premium";
      }else{
        //free membership, upgrade now
        $membershipType = "upgrade";
      }
    }
  }else{
    $membershipType = "none";
  }
  return $membershipType;
}
