<?php 
    include '../../../../wp-load.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $email  = (isset($_POST['email']) ? $_POST['email'] : '');
    $pass   = (isset($_POST['pass'])  ? $_POST['pass'] : '');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
      <h1> BlueToad login test:</h1>      
        <form method="post" enctype="multipart/form-data">
          Email:<br/>   
          <input type="text" name="email" value="" size="50" /><br/>
          Password:<br/>   
          <input type="text" name="pass" value="" size="50" /><br/><br/>       
          <input type="submit" value="Go" name="BTverify">
        </form>
        <br/><br/>
        <?php
        if (isset($_POST['BTverify'])) {
          if($email=='' || $pass==''){
            echo 'Please enter in an email and password to verify';
          }else{
            //echo 'NETWORK_HOME_URL='.NETWORK_HOME_URL.'<br/>';
            //change the makezine url based on where we are
            $url="https://makezine.com/";      
            $test_output = FALSE;
            if (strpos(NETWORK_HOME_URL, '.local') !== false || strpos(NETWORK_HOME_URL, '.test') !== fals ) { // wpengine local environments
              $url="https://makezine.local/";
              $test_output = TRUE;
            }elseif(strpos(NETWORK_HOME_URL, 'stagemakehub')  !== false){
              $url="https://mzinestage.wpengine.com/";            
              $test_output = TRUE;
            }elseif(strpos(NETWORK_HOME_URL, 'devmakehub')  !== false){  
              $url="https://mzinedev.wpengine.com/";
              $test_output = TRUE;
            }    

            if($test_output)  echo 'Calling '.$url.' with username = '.$email.' and password '.$pass.'<br/>';
            
            $url .= "BlueToad_omedaMake.php?brand=MK&productID=7&namespace=AUTHMAKE&appID=0387143E-E0DB-4D2F-8441-8DAB0AF47954";
            
            $data = array('pass'      => $pass, 'email'     => $email);
            
            $ch = curl_init();            

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);          
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $output = curl_exec($ch);
            $info   = curl_getinfo($ch);
            if($test_output) {
              echo 'Return code = ' . $info['http_code'].'<br/>';
              var_dump($output);
              echo '<br/>';
            }
            curl_close($ch);  

            echo '<h2>Result: ';
            if(!$output){
              echo 'User is not authorized';
            }else{
              $auth_array = explode(':',$output);
              if(is_array($auth_array)){
                if($auth_array[0]){
                //TBD - put code here to check for approved, not to just assume
                  echo 'Approved<br/>';
                  if(isset($auth_array[1])){
                    echo 'Expiration Date - '.$auth_array[1].'<br/>';
                  }
                  if(isset($auth_array[2])){
                    echo 'Start Date - '.$auth_array[1];
                  }                  
                }else{
                  echo 'Error in BT response. Please send this to Alicia<br/>';
                  var_dump($output);  
                }
              }else{                
                echo 'New output detected. Please send this to Alicia<br/>';
                var_dump($output);
              }
                          
            }
            echo '</h2>';
          }
        }
        ?>
    </body>
</html>