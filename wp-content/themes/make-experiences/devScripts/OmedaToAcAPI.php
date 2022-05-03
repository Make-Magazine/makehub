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
    $file = fopen(ABSPATH."/_wpeprivate/omeda/make-active-customers_20220503.csv", 'r');
    fgetcsv($file); //skip the header row

    $omedaData = array();
    $count = 1;
    while (($line = fgetcsv($file)) !== FALSE) {
      //build the contacts
      $contacts[] = array(
        "email" => $line[3],
        "first_name" => $line[1],
        "last_name" => $line[2],
        "tags"    => array("Omeda Subscriber"),
        "fields"  => array(
          array("id" => 1,    "value" => $line[5]), //address
          array("id" => 162,  "value" => $line[6]), //address 2
          array("id" => 2,    "value" => $line[7]), //city
          array("id" => 3,    "value" => $line[8]), //state
          array("id" => 4,    "value" => $line[9]), //Zip Code
          array("id" => 5,    "value" => $line[10]), //Country
          array("id" => 156,  "value" => $line[11]), //rollup expire date
          array("id" => 157,  "value" => $line[12]), //promo code
          array("id" => 158,  "value" => $line[13]), //order date
          array("id" => 159,  "value"  => $line[14]), //Requested Version
          array("id" => 160,  "value"  => $line[15]), //Class Description
          array("id" => 161,  "value"  => $line[16]), //Payment Status Description
        ),
        //"subscribe"  => array(
        //  "listid" => 8, //make community
        //  "listid" => 18 //make magazine
        //),
      );
    }
    fclose($file);

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

      //send api request here
      $response = json_decode(postCurl($url, $headers, json_encode($body)));

      //results output here
      omeda_log('File sent to Active Campaign. Status of send: '.$response->Success.
      '. # of queued contacts: '.$response->queued_contacts.
      '. batchId: '.$response->batchId.
      '. message: '.$response->message);
    }
    ?>
  </body>
</html>
