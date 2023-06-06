<?php //This script is used to bulk add users by email or by user id to BB groups and membership
include 'db_connect.php';
$emailList = (isset($_POST['emailList'])?$_POST['emailList']:'');
$email_array = explode(",", $emailList);

$memberID = (isset($_POST['memberID']) ? $_POST['memberID'] : '');
$bbGroup  = (isset($_POST['bbGroup'])  ? $_POST['bbGroup']  : '');
$group_array = explode(",", $bbGroup);

$user_pass = (isset($_POST['user_pass'])?$_POST['user_pass']:'');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
      <h1> Bulk add users by email to membership and BB groups</h1>
        <form method="post" enctype="multipart/form-data">
          What group(s) would you like to add the users to (comma separated)?
          <input type="text" name="bbGroup" value="<?php echo $bbGroup;?>" /><br/>
          Enter membership ID that you would like to give(optional)
          <input type="text" name="memberID" value="<?php echo $memberID;?>" /><br/>
          Password for new users
          <input type="text" name="user_pass" value="Make$Member" value="<?php echo $user_pass;?>" /><br/>
          List of email address (comma separated)<br/>
          <textarea name="emailList" rows="10" cols="50"><?php echo $emailList;?></textarea><br/><br/>
          <input type="submit" value="Go" name="bbgroupadd">
        </form>
    </body>
</html>

<?php
include '../../../../wp-load.php';
$error = '';
if (isset($_POST['bbgroupadd'])) {
  if($emailList == '') {
    $error .= "You must submit a list of emails.<br/>";
  }else{
    if($bbGroup =='' && $memberID == ''){
      $error .= "You must specify either a group or a membership to add.<br/>";
    }
    if($user_pass==''){
      $error .= "User password cannot be blank.";
    }
  }

  //any errors found?
  if($error==''){
    //loop through emails
    foreach ($email_array as $email) {
      //Check if email is a current Make: user
      $user = get_user_by( 'email', $email);
      if($user){
          $user_id = $user->ID;
      }else{ //if not, add them as a user. Set Password to - MEF2022
        //First, find them a username
        $username = strstr($email, '@', true); //first try username being the first part of the email
        if(username_exists( $username )){  //username exists try something else
          $count=1;
          $exists = true;
          while($exists){
            $username = $username.$count;
            if(!username_exists($username)){
              $exists = false;
            }
            $count++;
          }
        }

        //create user
        $user_id = wp_create_user( $username, $user_pass, $email);
        echo $email.' was not a WP user. User added with password '.$user_pass.'<br/>';
      }

      $first_name = get_user_meta( $user_id, 'first_name', true );
      $last_name = get_user_meta( $user_id, 'last_name', true );

      //now that we have a user id, let's add them to the requested membership
      if($memberID!=''){
        //Do they already have this membership?
        $mpInfo = json_decode(basicCurl(NETWORK_HOME_URL . '/wp-json/mp/v1/members/' . $user_id, setMemPressHeaders()));
        //var_dump($mpInfo->active_memberships);
        $key = array_search($memberID, array_column($mpInfo->active_memberships, 'id'));

        if($key === false){
          //user does not have this membership, go ahead and add it.
          $expiresAt = date('Y-m-d H:i:s', strtotime('+1 year'));

          $url = NETWORK_HOME_URL . '/wp-json/mp/v1/transactions';
          $datastring = json_encode(
            [
                'member'      => $user_id,
                'membership'  => $memberID, // ID of the Membership
                'trans_num'   => 'mp-txn-'.uniqid(),
                'status'      => 'complete',
                'gateway'     => 'free',
                'created_at'  => date("Y-m-d H:i:s"),
                'expires_at'  => $expiresAt

            ]
          );

          $headers = setMemPressHeaders($datastring);

          postCurl($url, $headers, $datastring);
          echo 'added '.$email.' to membership '.$memberID.'<br/>';
        }else{
          echo $email.' already has this membership. User skipped<br/>';
        }
      }

      //let's add them to the requested groups
      foreach($group_array as $group_id){
        echo 'added '.$email.' to group '.$group_id.'<br/>';
        groups_join_group( $group_id, $user_id );
      }
      echo '<br/>';
    }
  }
}

if($error!=''){
  echo '<h3>Error:</h3>' .$error;
}
