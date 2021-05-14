<?php
add_action( 'plugins_loaded', 'ldms_load_integrations', 999 );
function ldms_load_integrations() {

     if( class_exists('Simply_Schedule_Appointments') ) {
          include_once('simply-schedule-appointments.php');
     }

}
