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

    //set the headers and URL for the active campaign call
    $headers = array('Api-Token: 6a90725830fc5e03e6cddebdbc550ee624b30aea80abcaf9d1c239ea6ffeb30ea2f86075');
    $url = "https://make.api-us1.com/api/3/import/bulk_import";

    //pull the Omeda file
    /* Omeda file layout -
        0 - Postal Address Id     1 - First Name    2 - Last Name   3 - Email Address
        4 - Company Name          5 - Street 1      6 - Street 2    7 - City
        8 - State                 9 - Zip Code_Postal Code  10 - Country
        11 - Rollup Expire Date   12 - Promo Code           13 - Order Date
        14 - Requested Version    15 - Class Description    16 - Payment Status Description
    */


    if(!file_exists(ABSPATH."/_wpeprivate/omeda/make-active-customers.csv")){
      omeda_log('Error!! make-active-customers.csv not found');
      die('no file for you');
    }

    $file = fopen(ABSPATH."/_wpeprivate/omeda/make-active-customers.csv", 'r');
    fgetcsv($file); //skip the header row

    $omedaData = array();
    $count = 1;
    while (($line = fgetcsv($file)) !== FALSE) {
      if($line[3]=="ivar@miljeteig.no"){
        echo 'before:'.$line[7].' after'.utf8_encode($line[7]);
      }
      //build the contacts
      $contacts[] = array(
        "email" => utf8_encode($line[3]),
        "first_name" => utf8_encode($line[1]),
        "last_name" => utf8_encode($line[2]),
        "tags"    => array("Omeda Subscriber"),
        "fields"  => array(
          array("id" => 1,    "value" => utf8_encode($line[5])), //address
          array("id" => 162,  "value" => utf8_encode($line[6])), //address 2
          array("id" => 2,    "value" => utf8_encode($line[7])), //city
          array("id" => 3,    "value" => utf8_encode($line[8])), //state
          array("id" => 4,    "value" => utf8_encode($line[9])), //Zip Code
          array("id" => 5,    "value" => utf8_encode($line[10])), //Country
          array("id" => 156,  "value" => utf8_encode($line[11])), //rollup expire date
          array("id" => 157,  "value" => utf8_encode($line[12])), //promo code
          array("id" => 158,  "value" => utf8_encode($line[13])), //order date
          array("id" => 159,  "value" => utf8_encode($line[14])), //Requested Version
          array("id" => 160,  "value" => utf8_encode($line[15])), //Class Description
          array("id" => 161,  "value" => utf8_encode($line[16])), //Payment Status Description
        ),
        //"subscribe"  => array(
        //  "listid" => 8, //make community
        //  "listid" => 18 //make magazine
        //),
      );
    }
    fclose($file);
    echo 'Writing '.count($contacts).' Omeda contacts to Omeda<br/>';
    omeda_log('Writing '.count($contacts).' Omeda contacts to Omeda');

    //Omeda API can only handle 250 contacts at a time.
    $contactOut = array_chunk($contacts,250);

    foreach($contactOut as $contact_out){
      //each call to AC should call makehub when done to report results
      $body = array('contacts'=>$contact_out,
        "callback"=> array(
          "url" => "https://make.co/wp-json/makehub/v1/AcToMake",
          "requestType" => "POST",
          "detailed_results" => "true"
        )
      );

      if(!json_encode($body)){
        var_dump($body);
        die();
      }
/*
      //send api request here
      $response = postCurl($url, $headers, json_encode($body));
      $response = json_decode($response);

      //results output here
      if($response->success==1){
        omeda_log('File sent to Active Campaign Successfully. BatchID = '.$response->batchId);
      }else{
        omeda_log('Failure on send to Active Campaign. '.$response->message);
        echo 'Failure on send to Active Campaign. '.$response->message;
        var_dump($response->failureReasons);
        echo 'here is the file being sent ';
        var_dump(json_encode($body));

        die('look into this');

        foreach($response->failureReasons as $failureReason){
          omeda_log(print_r($failureReason,TRUE));
        }
      }*/
      //die('stopping after one');
    }
    echo 'complete. go check the <a href="https://make.co/wp-content/ACtoMake_log.log">log</a>';
    ?>
  </body>
</html>
