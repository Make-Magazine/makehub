<?php
/**
 * Content Template for the [ESPRESSO_EVENT_ATTENDEES] shortcode
 *
 * @package Event Espresso
 * @subpackage templates
 * @since 4.6.29
 * @author Darren Ethier
 *
 * Template Args that are available in this template
 * @type EE_Attendee $contact
 * @type bool       $show_gravatar  whether to show gravatar or not.
 */
if ( $show_gravatar ) {
	$gravatar = get_avatar( $contact->email(),
		(int) apply_filters( 'FHEE__loop-espresso_attendees-shortcode__template__avatar_size', 32 )
		);
} else {
	$gravatar = '';
}
?>
<?php 
do_action( 'AHEE__content-espresso_event_attendees__before', $contact, $show_gravatar );
$attendee_ID = $contact->get('ATT_ID');
$registration = EEM_Registration::instance()->get_one([['ATT_ID' => $attendee_ID]]);
$registration_ID = $registration->get('REG_ID');

//get question based on registration and attendee
$where = array(
    'REG_ID' => $registration_ID,
);
$answer = EEM_Answer::instance()->get_all(array($where));
$anything_else='';
$attendee_age = 'Declined to State';
foreach($answer as $ans){
    if($ans->get('QST_ID')=='15'){
        $attendee_age = $ans->get('ANS_value');
    }elseif($ans->get('QST_ID')=='12'){
        $anything_else = $ans->get('ANS_value');
    }       
}

?>
<tr>
    <td><?php echo '<span class="hidden-xs">' . $gravatar . '&nbsp;</span>' . $contact->full_name(); ?></td>    
    <td><?php echo '<a href = "mailto:'. $contact->email().'">'.$contact->email().'</a>';?></td>
    <td><?php echo $registration->get('REG_date');?></td>            
    <td><?php echo $attendee_age;?></td>
    <td><?php echo $anything_else;?></td>
</tr>
<?php do_action( 'AHEE__content-espresso_event_attendees__after', $contact, $show_gravatar ); ?>