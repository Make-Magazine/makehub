<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Email_Controller')){
	class Wpvc_Email_Controller{	
		
		public function __construct(){
			add_action('transition_post_status',array($this,'wpvc_send_publish_email_from_admin'),10,3);	
		} 
	
		
		public function wpvc_contestant_check_email($post_id,$userdata = "",$insertdata = "",$gateway=""){
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if(is_array($vote_opt)){
				$email = $vote_opt['email'];
				$contest = $vote_opt['contest'];
				//Check ADMIN EMAIL NOTIFICATION is set to on
				if($email['vote_notify_mail'] === 'on'){					
					Wpvc_Email_Controller::wpvc_sending_admin($post_id,$insertdata,$userdata,$vote_opt);
				}

				//Payment Email
				if($gateway != null){
					Wpvc_Email_Controller::wpvc_contestant_send_payment($post_id,$insertdata,$userdata,$gateway,$vote_opt);
				}

				//Check CONTESTANT SUCCESS EMAIL NOTIFICATION is set to on and Auto Approve to off
				if($email['vote_notify_contestant'] === 'on' && $contest['vote_publishing_type'] === 'off'){
					Wpvc_Email_Controller::wpvc_insert_contestants_mail($post_id,$insertdata,$userdata,$vote_opt);
				}//Check CONTESTANT PUBLISHED EMAIL NOTIFICATION is set to on and Auto Approve to on
				else if($email['vote_notify_approved'] === 'on' && $contest['vote_publishing_type'] === 'on'){
					Wpvc_Email_Controller::wpvc_send_published_mail($post_id,$insertdata,$userdata,$vote_opt);
				}//Check CONTESTANT SUCCESS EMAIL NOTIFICATION is set to on and Auto Approve to on and CONTESTANT PUBLISHED EMAIL NOTIFICATION is set to off
				else if($email['vote_notify_contestant'] === 'on' && $contest['vote_publishing_type'] === 'on' && $email['vote_notify_approved'] === 'off'){
					Wpvc_Email_Controller::wpvc_insert_contestants_mail($post_id,$insertdata,$userdata,$vote_opt);
				}
				else{
					return;
				}
			}
			else{
				return ;
			}
		}

		//Sending Payment Email
		public function wpvc_contestant_send_payment($post_id,$insertdata,$userdata,$gateway,$vote_opt=""){
			$email = $vote_opt['paidentry'][$gateway.'_settings'];
			
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/email.php');		
			$variables['thanks'] = 'Thanks for Adding Contestants';
			$variables['subject'] = 'Contestant Submission - '.strtoupper($gateway);
			$variables['content'] = 'Contestant Title : '. $insertdata['contestant-title']."<br/>"; 		
			$variables['content'] .=  $email['payment_'.$gateway.'_email_content']; 		
			
			if($insertdata['contestant-email_address'] != null){
				Wpvc_Email_Controller::wpvc_send_mail($insertdata['contestant-email_address'], $variables['subject'], $template,$variables);
			}
			else if($insertdata['contestant-email_address'] == null){
				$where = " where post_id='".$post_id."'";
				$track = Wpvc_Shortcode_Model::wpvc_get_post_entry_track_by_where($where);
				if(isset($track)){
					$user = get_user_by( 'id', $track->user_id_map );
					Wpvc_Email_Controller::wpvc_send_mail($user->user_email, $variables['subject'], $template,$variables);
				}
				else{
					return;
				}
			}
		}

		//Send Email When Contestant Published
		public function wpvc_send_publish_email_from_admin($new_status, $old_status, $post){ 
			if ( ( 'publish' === $new_status && 'publish' !== $old_status )	&& 'contestants' === $post->post_type) {
				if ( !(defined( 'REST_REQUEST' ) && REST_REQUEST )) { 
					$insertdata = get_post_meta($post->ID, WPVC_VOTES_POST,TRUE);			
					$vote_opt = get_option(WPVC_VOTES_SETTINGS);
					$email = $vote_opt['email'];			
					if($email['vote_notify_approved'] === 'on'){
						$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/emailbutton.php');	
						$variables['thanks'] = 'Your Submission is Approved';
						$variables['subject'] = $email['vote_approve_subject'];

						$variables['content'] =	Wpvc_Email_Controller::wpvc_replace_custom_fields($post->ID,$email['vote_contestant_approved_content'],$insertdata);

						//contestant_link
						$variables['contestant_link'] = get_the_permalink($post->ID);

						if($insertdata['contestant-email_address'] != null){
							Wpvc_Email_Controller::wpvc_send_mail($insertdata['contestant-email_address'], $variables['subject'], $template,$variables);
						}
						else if($insertdata['contestant-email_address'] == null){
							$where = " where post_id='".$post->ID."'";
							$track = Wpvc_Shortcode_Model::wpvc_get_post_entry_track_by_where($where);
							if(isset($track)){
								$user = get_user_by( 'id', $track->user_id_map );
								Wpvc_Email_Controller::wpvc_send_mail($user->user_email, $variables['subject'], $template,$variables);
							}
							else{
								return;
							}
						}
						else{
							return;
						}
					}
				}
				else {
					return;
				}			
			}
			else {
				return;
			}
		
		}

		//Sending Mail to the Contestants who posted
		public function wpvc_insert_contestants_mail($post_id,$insertdata,$userdata = "",$vote_opt=NULL){			
			$email = $vote_opt['email'];						
			
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/email.php');			

			$variables['thanks'] = 'Thank you for your submission';
			$variables['subject'] = $email['vote_notify_subject'];

			$variables['content'] =	Wpvc_Email_Controller::wpvc_replace_custom_fields($post_id,$email['vote_contestant_submit_content'],$insertdata);
			
			//Check if the user is logged in 
			if($userdata['user_logged'] === true){
				$current_user = get_user_by('id', $userdata['user_id_profile']);
				$to = $current_user->user_email;
				Wpvc_Email_Controller::wpvc_send_mail($to, $variables['subject'], $template,$variables);
			}
			else if($insertdata['contestant-email_address'] != null){
				Wpvc_Email_Controller::wpvc_send_mail($insertdata['contestant-email_address'], $variables['subject'], $template,$variables);
			}
			else{
				return;
			}								
		}

		//Sending Mail to the Contestants when published
		public function wpvc_send_published_mail($post_id,$insertdata,$userdata = "",$vote_opt=NULL){		
			$email = $vote_opt['email'];			
			//Check CONTESTANT PUBLISHED EMAIL NOTIFICATION is set to on
			if($email['vote_notify_approved'] === 'on'){
				$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/emailbutton.php');			

				$variables['thanks'] = 'Your Submission is Approved';
				$variables['subject'] = $email['vote_approve_subject'];
	
				$variables['content'] =	Wpvc_Email_Controller::wpvc_replace_custom_fields($post_id,$email['vote_contestant_approved_content'],$insertdata);

				//contestant_link
				$variables['contestant_link'] = get_the_permalink($post_id);

				//Check if the user is logged in 
				if($userdata['user_logged'] === true){
					$current_user = get_user_by('id', $userdata['user_id_profile']);
					$to = $current_user->user_email;
					Wpvc_Email_Controller::wpvc_send_mail($to, $variables['subject'], $template,$variables);
				}
				else if($insertdata['contestant-email_address'] != null){
					Wpvc_Email_Controller::wpvc_send_mail($insertdata['contestant-email_address'], $variables['subject'], $template,$variables);
				}
				else{
					return;
				}
			}			
		}

		//Sending Email to admin
		public function wpvc_sending_admin($post_id,$insertdata,$userdata=NULL,$vote_opt=NULL){	
			$email = $vote_opt['email'];			
			
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/emailbutton.php');			

			$variables['thanks'] = 'A user has added a contestant';
			$variables['subject'] = 'CONTESTANT ADDED';

			$variables['content'] =	Wpvc_Email_Controller::wpvc_replace_custom_fields($post_id,$email['vote_admin_mail_content'],$insertdata);

			//contestant_link
			$variables['contestant_link'] = get_bloginfo( 'url' ).'/wp-admin/post.php?post='.$post_id.'&action=edit';
			
			
			//Check if the user is logged in 
			if($email['vote_admin_mail'] === ''){				
				$to = get_option( 'admin_email' );
				Wpvc_Email_Controller::wpvc_send_mail($to, $variables['subject'], $template,$variables);
			}
			else if($email['vote_admin_mail'] != null){
				Wpvc_Email_Controller::wpvc_send_mail($email['vote_admin_mail'], $variables['subject'], $template,$variables);
			}
			else{
				return;
			}								
		}

		//Replace the Custom Fields data in the Mail Content
		public function wpvc_replace_custom_fields($post_id,$content,$insertdata){

			//Fetch Custom Fields
			$custom_fields = Wpvc_Settings_Model::wpvc_custom_fields_json();

			//Old Version - replacing the existing string
			$content = str_replace('customfield_','',$content);

			//Feature Image
			if(strpos($content, '{contestant-image}') !== false){
				$featured_img_url = get_the_post_thumbnail_url($post_id,'full'); 
				$replace_content = '<a target="_blank" href="'.$featured_img_url.'">'.__("Link","voting-contest").'</a>';
				$content = str_replace('{contestant-image}',$replace_content."<br/>",$content);
			}

			//Contestant Link
			if (strpos($content, '{contestant-link}') !== false) {
				$replace_content = get_permalink($post_id,FALSE);
				$content = str_replace('{contestant-link}',$replace_content."<br/>",$content);
			}

			//Contestant Email Address
			if(strpos($mail_content, '{contestant-email_address}') !== false){
				$email_address = get_post_meta($post_id,'contestant-email_address',true); 
				$replace_content = '<a href="mailto:'.$email_address.'">'.$email_address.'</a>';
				$content = str_replace('{contestant-email_address}',$replace_content."<br/>",$content);
			}


			foreach($custom_fields as $custom_field){
				//DATE Field Type
				if($custom_field['question_type'] == 'DATE'){
					$date_field 	 = $insertdata[$custom_field['system_name']];
					if($date_field != null){
						$replace_content = date('Y-m-d',strtotime($date_field));
						$content = str_replace('{'.$custom_field['system_name'].'}',$replace_content."<br/>",$content);
					}
					else{
						$content = str_replace('{'.$custom_field['system_name'].'}',''."<br/>",$content);
					}
				}
				//FILE Field Type
				else if($custom_field['question_type'] == 'FILE'){
					$uploaded_file = get_post_meta($post_id,'ow_custom_attachment_'.$custom_field['system_name'],true);
					if(!empty($uploaded_file)){
						$file_url = $uploaded_file['url'];
						$replace_content = '<a target="_blank" href="'.$file_url.'">'.__("Link","voting-contest").'</a>';
						$content = str_replace('{'.$custom_field['system_name'].'}',$replace_content."<br/>",$content);
					}	
					else{
						$content = str_replace('{'.$custom_field['system_name'].'}',''."<br/>",$content);
					}
				}				
				else if (strpos($content, '{'.$custom_field['system_name'].'}') !== false) {
					$content = str_replace('{'.$custom_field['system_name'].'}',$insertdata[$custom_field['system_name']]."<br/>",$content);
				}

				
			}	
			
			return $content;
		}


		//Coinbase Emails
		public function wpvc_send_coinbase($status,$email,$contestant_id){					
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/emailbutton.php');				
			switch($status){

				case "confirmed" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_EmailThanks_confirmed', 'Thanks for Voting');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_EmailSubject_confirmed', 'Vote Confirmed');
					$variables['content'] = apply_filters( 'wpvc_coinbase_EmailContent_confirmed', 'Vote Confirmed for the Contestant - '.get_the_title($contestant_id));				
					break;

				case "overpaid" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_EmailThanks_overpaid', 'Thanks for Voting');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_EmailSubject_overpaid', 'Vote Confirmed - OverPaid');
					$variables['content'] = apply_filters( 'wpvc_coinbase_EmailContent_overpaid', 'Vote Confirmed for the Contestant - '.get_the_title($contestant_id).'<br/>But the Payment is OverPaid. Please contact the site administrator.');					
					break;

				case "underpaid" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_EmailThanks_underpaid', 'Voting Status');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_EmailSubject_underpaid', 'Vote Failed - UnderPaid');
					$variables['content'] = apply_filters( 'wpvc_coinbase_EmailContent_underpaid', 'Vote Failed for the Contestant - '.get_the_title($contestant_id).'<br/>Because the Payment is UnderPaid. Please contact the site administrator.');					
					break;
			}
			
			$variables['contestant_link'] = get_the_permalink($contestant_id);
			
			Wpvc_Email_Controller::wpvc_send_mail($email, $variables['subject'], $template,$variables);
		}

		public function wpvc_send_coinbase_paidentry($status,$email,$contestant_id){	
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);	
			$emailsettings = $vote_opt['paidentry']['coinbase_settings'];			
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/emailbutton.php');				
			switch($status){

				case "confirmed" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_paidEntry_EmailThanks_confirmed', 'Thanks for Adding Contestants');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailSubject_confirmed', 'Contestants Added');
					$variables['content'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailContent_confirmed', 'Contestants Added Successfully - '.get_the_title($contestant_id));
								
					$variables['content'] .=  $emailsettings['payment_coinbase_email_content']; 	
			
					break;

				case "overpaid" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_paidEntry_EmailThanks_overpaid', 'Thanks for Adding Contestants');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailSubject_overpaid', 'Contestants Added - OverPaid');
					$variables['content'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailContent_overpaid', 'Contestants Added Successfully - '.get_the_title($contestant_id).'<br/>But the Payment is OverPaid. Please contact the site administrator.');		
					
					$variables['content'] .=  $emailsettings['payment_coinbase_email_content']; 				
					break;

				case "underpaid" :
					$variables['thanks']  = apply_filters( 'wpvc_coinbase_paidEntry_EmailThanks_underpaid', 'Contestant Status');
					$variables['subject'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailSubject_underpaid', 'Contestants Failed to Added - UnderPaid');
					$variables['content'] = apply_filters( 'wpvc_coinbase_paidEntry_EmailContent_underpaid', 'Contestants Failed to Added - '.get_the_title($contestant_id).'<br/>Because the Payment is UnderPaid. Please contact the site administrator.');					
					break;
			}
			
			$variables['contestant_link'] = get_the_permalink($contestant_id);
			
			Wpvc_Email_Controller::wpvc_send_mail($email, $variables['subject'], $template,$variables);
		}

		//Email Verification Send
		public function wpvc_send_email_verification($code,$email){					
			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/email.php');		
			$variables['thanks'] = 'Thanks for Voting';
			$variables['subject'] = 'Email Verification Code - '.$code;
			$variables['content'] =	 '<br/><br/>'; 			
			Wpvc_Email_Controller::wpvc_send_mail($email, $variables['subject'], $template,$variables);
		}

		//Reset Password Link 
		public function wpvc_send_reset_password($user_data,$key){	
			$user_login = $user_data->user_login;
    		$user_email = $user_data->user_email;

			$message = __('Someone requested that the password be reset for the following account:') . "<br/>";
			$message .= network_home_url( '/' ) . "<br/>";
			$message .= sprintf(__('Username: %s'), $user_login) . "<br/>";
			$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "<br/>";
			$message .= __('To reset your password, visit the following address:') . "<br/>";
			$message .= '<a href=' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">Link</a><br/>";
			                
			if ( is_multisite() )
				$blogname = $GLOBALS['current_site']->site_name;
			else
				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			$title = sprintf( __('[%s] Password Reset'), $blogname );
			
			$title = apply_filters('retrieve_password_title', $title);
			$message = apply_filters('retrieve_password_message', $message, $key);

			$template = file_get_contents(WPVC_VOTES_ABSPATH. 'wpvc_email/email.php');		
			$variables['thanks'] = 	$title;
			$variables['subject'] = $title;
			$variables['content'] =	$message; 
			
			$variables['sitename'] = get_bloginfo( 'name' );
			foreach($variables as $key => $value){
				$template = str_replace('{{ '.$key.' }}', $value, $template);
			}
			$email_content = $template;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			
			if(!wp_mail($user_email, $title, $email_content, $headers))
				return false;
			else
				return true;
		
		}

		//Common Mail Sending function
		public function wpvc_send_mail($to,$subject, $template,$variables){		
			$variables['sitename'] = get_bloginfo( 'name' );
			foreach($variables as $key => $value){
				$template = str_replace('{{ '.$key.' }}', $value, $template);
			}
			$email_content = $template;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$vote_opt = get_option(WPVC_VOTES_SETTINGS);
			if(!empty($vote_opt)){
				$email = $vote_opt['email'];
				if($email['vote_from_mail'] != ''){					
					$headers[] = 'From: '.get_bloginfo( 'name' ).' <'.$email['vote_from_mail'].'>';
				}
			}	
			wp_mail($to, $subject, $email_content, $headers);
		}
	}


}
else
die("<h2>".__('Failed to load the Voting Email Controller','voting-contest')."</h2>");

return new Wpvc_Email_Controller();
