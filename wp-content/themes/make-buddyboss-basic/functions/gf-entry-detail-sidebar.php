<?php
add_action( 'gform_entry_info', 'make_entry_info', 10, 2 );
function make_entry_info( $form_id, $entry ) {
	$entry_status = gform_get_meta( $entry['id'], 'is_approved' );	
	if (\GV\Utils::_POST('screen_mode') !== 'edit') {
		echo 'Entry Status: ';	
		switch ($entry_status) {
			case 1:
				echo 'Approved';
				break;
			case 2: 
				echo 'Rejected';
				break;
			case 3: 
				echo 'Proposed';
				break;
			default:
				echo 'Unknown';
		}
	}else{
		echo 'Change Entry Status: ';	
		$output = '<select style="width:250px" name="is_approved" id="is_approved">';
		$output .= '<option '.($entry_status==1?'selected':'').' value="1">Approved</option>';
		$output .= '<option '.($entry_status==2?'selected':'').' value="2">Rejected</option>';
		$output .= '<option '.($entry_status==3?'selected':'').' value="3">Proposed</option>';	
		$output .= '</select>';		
		echo $output;
	}
	
}

// Update the GV status
add_filter( 'gform_after_update_entry', 'update_gv_status', 10, 3 );

function update_gv_status($form, $entry_id, $orig_entry = array()) {
	error_log('update_gv_status');
	$entry = GFAPI::get_entry($entry_id);
	error_log(print_r($entry,true));	
}
