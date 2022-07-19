<?php

//Create metabox for the Judging Panel 
add_action( 'add_meta_boxes', 'judgingpanel_add_meta_box' );

function judgingpanel_add_meta_box(){
	add_meta_box(
		'judgingpanel',
		__( 'Judging Panel', 'voting-contest' ),
		'judgingpanel_html',
		'owjudge',
		'normal',
		'high'
	);	

	add_meta_box(
		'judgingbuttons',
		__( 'Judging Panel Actions', 'voting-contest' ),
		'judgingpanel_buttons',
		'owjudge',
		'side',
		'high'
	);	

}

add_action('admin_menu', 'ow_judging_menu'); 

function ow_judging_menu() { 
	add_submenu_page('edit.php?post_type=owjudge', __("Dashboard",'voting-contest'), __("Dashboard",'voting-contest'), 'manage_options', 'judging-dashboard','judging_dashboard');
}  

function judging_dashboard(){
	echo '<div class="wrap"><input type="hidden" id="owdataURL" value='.site_url("/").' /><h2>'.__("Dashboard",'voting-contest').'</h2><div id="judging-dashoboard"></div></div>';
}


//Judging Panel HTML for the admin for the judging post type
function judgingpanel_html($post, $data){	

	//Include the Google font alobe for this view file
	wp_enqueue_style('google_fonts');	
	wp_nonce_field( 'judgingpanel_nonce', 'judgingpanel_meta_box_nonce' );
	?>
		<div id="judging-app" class="judgingcontainer"></div>
		<input type="hidden" id="owdataURL" value="<?php echo site_url('/'); ?>" />
	<?php	
}


//Judging Panel Buttons for the Judging Post type
function judgingpanel_buttons($post, $data){	

	//Include the Google font alobe for this view file
	wp_enqueue_style('google_fonts');	
	?>
		<div id="judging-actions"></div>
		<input type="hidden" id="owdataURL" value="<?php echo site_url('/'); ?>" />
	<?php	
}

