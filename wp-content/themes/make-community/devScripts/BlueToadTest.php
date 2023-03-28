<?php 
    include '../../../../wp-load.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $email  = (isset($_POST['email']) ? $_POST['email'] : '');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>
      <h1> BlueToad login test:</h1>      
        <form method="post" enctype="multipart/form-data">
          Enter in the email address you would like to test<br/>   
          <input type="text" name="email" value="" size="50" /><br/><br/>       
          <input type="submit" value="Go" name="BTverify">
        </form>
        <br/><br/>
        <?php
        if (isset($_POST['BTverify'])) {
          if($email==''){
            echo 'Please enter in an email to verify';
          }else{
                /*
                  Required fields:
                  $brand = $_GET['brand']; MK
                  $productID = $_GET['productID']; 7
                  $appID = $_GET['appID'];x-omeda-appid
                  $namespace = $_GET['namespace'];  AUTHMAKE
                  $pass = $_POST['pass']; 
                  $email = $_POST['email'];                                  
                  */

            $url="https://makezine.com/BlueToad_omedaMake.php?brand=MK&productID=7&namespace=AUTHMAKE&appID=0387143E-E0DB-4D2F-8441-8DAB0AF47954";

            //echo 'calling '.$url.'<br/>';
            $data = array('pass'      => $email, 'email'     => $email);
            
            $ch = curl_init();            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);          
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $output = curl_exec($ch);
            $info   = curl_getinfo($ch);

            curl_close($ch);  

            echo '<h2>Result: ';
            if(!$output){
              echo 'User is not authorized';
            }else{
              $auth_array = explode(':',$output);
              if(is_array($auth_array)){
                if($auth_array[0]){
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