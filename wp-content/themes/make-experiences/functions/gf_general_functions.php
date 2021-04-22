<?php
//change save and continue retention from 30 days to 90 days
add_filter( 'gform_incomplete_submissions_expiration_days', 'gwp_days', 1, 10 );
function gwp_days( $expiration_days ) {
    // change this value
    $expiration_days = 90;
    return $expiration_days;
}