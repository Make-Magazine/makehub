<?php
/* Used to set the memberpress headers for the api call, use a key set in config constants */
function setMemPressHeaders($datastring = null, $key) {
 $headers = array();
 $headers[] = 'MEMBERPRESS-API-KEY: ' . $key; // Your API KEY from MemberPress Developer Tools
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
  
  $membershipType = "";
  //This needs to stay here instead of being moved to a function for max efficiency
  if(class_exists('MeprUtils')) {       
    $member = new MeprUser(); // initiate the class
    $member->ID = $user->ID; // if using this in admin area, you'll need this to make user id the member id
    $memLevel = $member->get_active_subscription_titles(); //MeprUser function that gets subscription title    
    
    if($memLevel==''){
      $membershipType = 'none';
    }elseif(stripos($memLevel, 'premium') !== false ||
        stripos($memLevel, 'school maker faire') !== false ||
        stripos($memLevel, 'multi-seat') !== false) {
        //Premium Membership
        $membershipType = "premium";
    }else{
      //free membership, upgrade now
      $membershipType = "upgrade";    
    }
  }
  return $membershipType;
}
