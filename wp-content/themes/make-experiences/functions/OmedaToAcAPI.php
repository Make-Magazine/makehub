<?php
//This will process the response from AC after they have processed our daily upload file
add_action( 'rest_api_init', function () {
  register_rest_route( 'makehub/v1', '/AcToMake', array(
    'methods' => 'POST',
    'callback' => 'AC_callback',
  ) );
} );

function AC_callback( WP_REST_Request $request ) {
  //access returned data
  $success = $request['Success'];
  $failures = $request['Failures'];
  $failure_reasons = $request['failure_reasons'];

  //if there were any failures, write them to the log
  if($failures != 0){
    // call to function
    omeda_log('Error in processing the Omeda to Active Campaign feed. There were '.$success.' successfull requests and '.$failures.' failed requests. Please see errors below:');
    foreach($failure_reasons as $failure_reason){
      omeda_log($failure_reason);
    }
  }

}

//create a log file
function omeda_log($log_msg) {
    $log_filename = ABSPATH."/wp_content/ACtoMake_log";
    if (!file_exists($log_filename)){
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }

    $log_file_data = $log_filename . '.log';

    // add message to the log file prepended by todays date and time
    file_put_contents($log_file_data, date('[m/d/Y H-i-s e] ').$log_msg . "\n", FILE_APPEND);
}

?>
