<?php
//Create Cron job to process the omeda file
add_action( 'omeda_to_AC_cron', 'process_omeda_file' );
function process_omeda_file() {
  //set the headers and URL for the active campaign call
  $headers = array('Api-Token: 6a90725830fc5e03e6cddebdbc550ee624b30aea80abcaf9d1c239ea6ffeb30ea2f86075');
  $url = "https://make.api-us1.com/api/3/import/bulk_import";

  //pull the Omeda file
  if(!file_exists(ABSPATH."/_wpeprivate/omeda/make-active-customers.csv")){
    omeda_log('Error!! make-active-customers.csv not found');
    die();
  }

  $file = fopen(ABSPATH."/_wpeprivate/omeda/make-active-customers.csv", 'r');
  fgetcsv($file); //skip the header row
  
  while (($line = fgetcsv($file)) !== FALSE) {
    //build the contacts
    $contacts[] = array(
      "email" => mb_convert_encoding($line[3], 'UTF-8', 'ISO-8859-1'),
      "first_name" => mb_convert_encoding($line[1], 'UTF-8', 'ISO-8859-1'),
      "last_name" => mb_convert_encoding($line[2], 'UTF-8', 'ISO-8859-1'),
      "tags"    => array("Omeda Subscriber"),
      "fields"  => array(
        array("id" => 1,    "value" => mb_convert_encoding($line[5], 'UTF-8', 'ISO-8859-1')), //address
        array("id" => 162,  "value" => mb_convert_encoding($line[6], 'UTF-8', 'ISO-8859-1')), //address 2
        array("id" => 2,    "value" => mb_convert_encoding($line[7], 'UTF-8', 'ISO-8859-1')), //city
        array("id" => 3,    "value" => mb_convert_encoding($line[8], 'UTF-8', 'ISO-8859-1')), //state
        array("id" => 4,    "value" => mb_convert_encoding($line[9], 'UTF-8', 'ISO-8859-1')), //Zip Code
        array("id" => 5,    "value" => mb_convert_encoding($line[10], 'UTF-8', 'ISO-8859-1')), //Country
        array("id" => 156,  "value" => mb_convert_encoding($line[11], 'UTF-8', 'ISO-8859-1')), //rollup expire date
        array("id" => 157,  "value" => mb_convert_encoding($line[12], 'UTF-8', 'ISO-8859-1')), //promo code
        array("id" => 158,  "value" => mb_convert_encoding($line[13], 'UTF-8', 'ISO-8859-1')), //order date
        array("id" => 159,  "value" => mb_convert_encoding($line[14], 'UTF-8', 'ISO-8859-1')), //Requested Version
        array("id" => 160,  "value" => mb_convert_encoding($line[15], 'UTF-8', 'ISO-8859-1')), //Class Description
        array("id" => 161,  "value" => mb_convert_encoding($line[16], 'UTF-8', 'ISO-8859-1')), //Payment Status Description
      ),
      "subscribe"  => array(
        "listid" => 2 // all contacts
        //  "listid" => 8, //make community
        //  "listid" => 18 //make magazine
      ),
    );
  }
  fclose($file);

  omeda_log('Writing '.count($contacts).' Omeda contacts to Active Campaign');

  //Active Campaign API can only handle 250 contacts at a time.
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
    $response = postCurl($url, $headers, json_encode($body));
    $response = json_decode($response);

    //results output here
    if($response->success==1){
      omeda_log('File sent to Active Campaign Successfully. BatchID = '.$response->batchId);
    }else{
      omeda_log('Failure on send to Active Campaign. '.$response->message);

      foreach($response->failureReasons as $failureReason){
        omeda_log(print_r($failureReason,TRUE));
      }
    }
  }
}

//This will process the response from AC after they have processed our daily upload file
add_action( 'rest_api_init', function () {
  register_rest_route( 'makehub/v1', '/AcToMake', array(
    'methods' => 'POST',
    'callback' => 'AC_callback',
  ) );
} );

function AC_callback( WP_REST_Request $request ) {
  omeda_log('Response from Active Campaign');

  //access returned data
  $failure_reasons = (isset($request['failure_results'])?$request['failure_results']:array());

  //if there were any failures, write them to the log
  if(!empty($failure_reasons)){
    // call to function
    omeda_log('Error in processing the Omeda to Active Campaign feed. Please see errors below:');
    foreach($failure_reasons as $failure_reason){
      omeda_log($failure_reason['email'].'('.$failure_reason['code'].') - '.$failure_reason['message']);
    }
  }

}

//create a log file
function omeda_log($log_msg) {
    $log_filename = ABSPATH."wp-content/ACtoMake_log.log";

    // add message to the log file prepended by todays date and time
    file_put_contents($log_filename, date('[m/d/Y H:i:s e] ').$log_msg . "\n", FILE_APPEND);
}