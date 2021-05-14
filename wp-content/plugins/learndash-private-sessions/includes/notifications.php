<?php
function ldms_send_email_notification( $send_to_user, $comment, $post_url, $sender_user, $reply = false){

	if( ldms_get_option( 'ldms_send_email' ) == 'no' ) {
		return;
	}

	$sender = apply_filters( 'ldms_notification_sender', array(
		'name'	=>	$sender_user->display_name,
		'email'	=>	$sender_user->user_email
	) );

	if( ldms_get_option( 'ldms_email_style' ) == 'text' ) {
		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: '.$sender['name'].' <'.$sender['email'].'>'
		);
	} else {
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: '.$sender['name'].' <'.$sender['email'].'>'
		);
	}

	$subject = $sender_user->display_name.__(' started a conversation','ldmessenger');

	if($reply){
		$subject = $sender_user->display_name.__(' left a reply in your conversation','ldmessenger');
	}

	$message = ldms_generate_email($comment, $sender_user->display_name, $send_to_user->display_name, $post_url, $reply);

	$return = wp_mail($send_to_user->user_email, $subject , $message , $headers );

	return $return;
}

function ldms_generate_email($comment, $sender_name, $recipents_name, $post_url, $reply = false){

	$message = ldms_generate_message($comment, $sender_name, $recipents_name, $post_url, $reply = false);

	if( ldms_get_option( 'ldms_email_style' ) == 'text' ) {

		$email_body = stripslashes( strip_tags( ldms_convert_breaks($message) ) );

	} else {

	    ob_start(); ?>
	    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	    <html>
	        <head>
	            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	            <title><?php echo $sender_name;?> <?php _e('has started a conversation on', 'ldmessenger'); ?> <?php echo get_bloginfo( 'name' );?></title>
	    		<style type="text/css">
	    			/* Client-specific Styles */
	    			#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
	    			body{width:100% !important;} .ReadMsgBody{width:100%;} .ExternalClass{width:100%;} /* Force Hotmail to display emails at full width */
	    			body{-webkit-text-size-adjust:none;} /* Prevent Webkit platforms from changing default text sizes. */

	    			/* Reset Styles */
	    			body{margin:0; padding:0;}
	    			img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}
	    			table td{border-collapse:collapse;}
	    			#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}

	    			/* Template Styles */

	    			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: COMMON PAGE ELEMENTS /\/\/\/\/\/\/\/\/\/\ */

	    			/**
	    			* @tab Page
	    			* @section background color
	    			* @tip Set the background color for your email. You may want to choose one that matches your company's branding.
	    			* @theme page
	    			*/
	    			body, #backgroundTable{
	    				/*@editable*/ background-color:#FAFAFA;
	    			}

	    			/**
	    			* @tab Page
	    			* @section email border
	    			* @tip Set the border for your email.
	    			*/
	    			#templateContainer{
	    				/*@editable*/ border:0;
	    			}

	    			/**
	    			* @tab Page
	    			* @section heading 1
	    			* @tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
	    			* @style heading 1
	    			*/
	    			h1, .h1{
	    				/*@editable*/ color:#202020;
	    				display:block;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:40px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				margin-top:2%;
	    				margin-right:0;
	    				margin-bottom:1%;
	    				margin-left:0;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Page
	    			* @section heading 2
	    			* @tip Set the styling for all second-level headings in your emails.
	    			* @style heading 2
	    			*/
	    			h2, .h2{
	    				/*@editable*/ color:#404040;
	    				display:block;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:18px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				margin-top:2%;
	    				margin-right:0;
	    				margin-bottom:1%;
	    				margin-left:0;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Page
	    			* @section heading 3
	    			* @tip Set the styling for all third-level headings in your emails.
	    			* @style heading 3
	    			*/
	    			h3, .h3{
	    				/*@editable*/ color:#606060;
	    				display:block;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:16px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				margin-top:2%;
	    				margin-right:0;
	    				margin-bottom:1%;
	    				margin-left:0;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Page
	    			* @section heading 4
	    			* @tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
	    			* @style heading 4
	    			*/
	    			h4, .h4{
	    				/*@editable*/ color:#808080;
	    				display:block;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:14px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				margin-top:2%;
	    				margin-right:0;
	    				margin-bottom:1%;
	    				margin-left:0;
	    				/*@editable*/ text-align:left;
	    			}

	    			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: PREHEADER /\/\/\/\/\/\/\/\/\/\ */

	    			/**
	    			* @tab Header
	    			* @section preheader style
	    			* @tip Set the background color for your email's preheader area.
	    			* @theme page
	    			*/
	    			#templatePreheader{
	    				/*@editable*/ background-color:#FAFAFA;
	    			}

	    			/**
	    			* @tab Header
	    			* @section preheader text
	    			* @tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
	    			*/
	    			.preheaderContent div{
	    				/*@editable*/ color:#707070;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:10px;
	    				/*@editable*/ line-height:100%;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Header
	    			* @section preheader link
	    			* @tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
	    			*/
	    			.preheaderContent div a:link, .preheaderContent div a:visited, /* Yahoo! Mail Override */ .preheaderContent div a .yshortcuts /* Yahoo! Mail Override */{
	    				/*@editable*/ color:#336699;
	    				/*@editable*/ font-weight:normal;
	    				/*@editable*/ text-decoration:underline;
	    			}

	    			/**
	    			* @tab Header
	    			* @section social bar style
	    			* @tip Set the background color and border for your email's footer social bar.
	    			*/
	    			#social div{
	    				/*@editable*/ text-align:right;
	    			}

	    			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: HEADER /\/\/\/\/\/\/\/\/\/\ */

	    			/**
	    			* @tab Header
	    			* @section header style
	    			* @tip Set the background color and border for your email's header area.
	    			* @theme header
	    			*/
	    			#templateHeader{
	    				/*@editable*/ background-color:#FFFFFF;
	    				/*@editable*/ border-bottom:5px solid #505050;
	    			}

	    			/**
	    			* @tab Header
	    			* @section left header text
	    			* @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	    			*/
	    			.leftHeaderContent div{
	    				/*@editable*/ color:#202020;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:32px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				/*@editable*/ text-align:right;
	    				/*@editable*/ vertical-align:middle;
	    			}

	    			/**
	    			* @tab Header
	    			* @section right header text
	    			* @tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	    			*/
	    			.rightHeaderContent div{
	    				/*@editable*/ color:#202020;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:32px;
	    				/*@editable*/ font-weight:bold;
	    				/*@editable*/ line-height:100%;
	    				/*@editable*/ text-align:left;
	    				/*@editable*/ vertical-align:middle;
	    			}

	    			/**
	    			* @tab Header
	    			* @section header link
	    			* @tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
	    			*/
	    			.leftHeaderContent div a:link, .leftHeaderContent div a:visited, .rightHeaderContent div a:link, .rightHeaderContent div a:visited{
	    				/*@editable*/ color:#336699;
	    				/*@editable*/ font-weight:normal;
	    				/*@editable*/ text-decoration:underline;
	    			}

	    			#headerImage{
	    				height:auto;
	    				max-width:180px !important;
	    			}

	    			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: MAIN BODY /\/\/\/\/\/\/\/\/\/\ */

	    			/**
	    			* @tab Body
	    			* @section body style
	    			* @tip Set the background color for your email's body area.
	    			*/
	    			#templateContainer, .bodyContent{
	    				/*@editable*/ background-color:#FDFDFD;
	    			}

	    			/**
	    			* @tab Body
	    			* @section body text
	    			* @tip Set the styling for your email's main content text. Choose a size and color that is easy to read.
	    			* @theme main
	    			*/
	    			.bodyContent div{
	    				/*@editable*/ color:#505050;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:14px;
	    				/*@editable*/ line-height:150%;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Body
	    			* @section body link
	    			* @tip Set the styling for your email's main content links. Choose a color that helps them stand out from your text.
	    			*/
	    			.bodyContent div a:link, .bodyContent div a:visited, /* Yahoo! Mail Override */ .bodyContent div a .yshortcuts /* Yahoo! Mail Override */{
	    				/*@editable*/ color:#336699;
	    				/*@editable*/ font-weight:normal;
	    				/*@editable*/ text-decoration:underline;
	    			}

	    			.bodyContent img{
	    				display:inline;
	    				height:auto;
	    			}

	    			/* /\/\/\/\/\/\/\/\/\/\ STANDARD STYLING: FOOTER /\/\/\/\/\/\/\/\/\/\ */

	    			/**
	    			* @tab Footer
	    			* @section footer style
	    			* @tip Set the background color and top border for your email's footer area.
	    			* @theme footer
	    			*/
	    			#templateFooter{
	    				/*@editable*/ background-color:#FAFAFA;
	    				/*@editable*/ border-top:3px solid #909090;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section footer text
	    			* @tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
	    			* @theme footer
	    			*/
	    			.footerContent div{
	    				/*@editable*/ color:#707070;
	    				/*@editable*/ font-family:Arial;
	    				/*@editable*/ font-size:11px;
	    				/*@editable*/ line-height:125%;
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section footer link
	    			* @tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
	    			*/
	    			.footerContent div a:link, .footerContent div a:visited, /* Yahoo! Mail Override */ .footerContent div a .yshortcuts /* Yahoo! Mail Override */{
	    				/*@editable*/ color:#336699;
	    				/*@editable*/ font-weight:normal;
	    				/*@editable*/ text-decoration:underline;
	    			}

	    			.footerContent img{
	    				display:inline;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section social bar style
	    			* @tip Set the background color and border for your email's footer social bar.
	    			* @theme footer
	    			*/
	    			#social{
	    				/*@editable*/ background-color:#FFFFFF;
	    				/*@editable*/ border:0;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section social bar style
	    			* @tip Set the background color and border for your email's footer social bar.
	    			*/
	    			#social div{
	    				/*@editable*/ text-align:left;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section utility bar style
	    			* @tip Set the background color and border for your email's footer utility bar.
	    			* @theme footer
	    			*/
	    			#utility{
	    				/*@editable*/ background-color:#FAFAFA;
	    				/*@editable*/ border-top:0;
	    			}

	    			/**
	    			* @tab Footer
	    			* @section utility bar style
	    			* @tip Set the background color and border for your email's footer utility bar.
	    			*/
	    			#utility div{
	    				/*@editable*/ text-align:left;
	    			}

	    		</style>
	    	</head>
	        <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	        	<center>
	            	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
	                	<tr>
	                    	<td align="center" valign="top">
	                            <!-- // Begin Template Preheader \\ -->
	                            <table border="0" cellpadding="10" cellspacing="0" width="600" id="templatePreheader">
	                                <tr>
	                                    <td valign="top" class="preheaderContent">

	                                    	<!-- // Begin Module: Standard Preheader \ -->
	                                        <table border="0" cellpadding="10" cellspacing="0" width="100%">
	                                        	<tr>
	                                            	<td valign="top">
	                                                	<div>
	                                                    	 <?php echo $sender_name;?> <?php _e('has started a conversation on', 'ldmessenger'); ?> <?php echo get_bloginfo( 'name' );?>
	                                                    </div>
	                                                </td>
	                                                <!-- *|IFNOT:ARCHIVE_PAGE|* -->
	    											<td valign="top" width="170">
	                                                	<div>
	                                                    	<?php _e('Email not displaying correctly?', 'ldmessenger'); ?><br /><a href="<?php echo $post_url;?>" target="_blank"><?php _e('View the new message on the site', 'ldmessenger'); ?></a>.
	                                                    </div>
	                                                </td>
	    											<!-- *|END:IF|* -->
	                                            </tr>
	                                        </table>
	                                    	<!-- // End Module: Standard Preheader \ -->

	                                    </td>
	                                </tr>
	                            </table>
	                            <!-- // End Template Preheader \\ -->
	                        	<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateContainer">
	                            	<tr>
	                                	<td align="center" valign="top">
	                                        <!-- // Begin Template Header \\ -->
	                                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="templateHeader">
	                                            <tr>
	                                                <td class="headerContent">

	                                                    <!-- // Begin Module: Letterhead, Center Header Image \\ -->
	                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
	                                                        <tr>
	                                                        	<h1><?php echo $reply ? esc_html__('Reply From: ', 'ldmessenger').$sender_name : esc_html__('Conversation Started','ldmessenger'); ?></h1>
	                                                        </tr>
	                                                    </table>
	                                                    <!-- // End Module: Letterhead, Center Header Image \\ -->

	                                                </td>
	                                            </tr>
	                                        </table>
	                                        <!-- // End Template Header \\ -->
	                                    </td>
	                                </tr>
	                            	<tr>
	                                	<td align="center" valign="top">
	                                        <!-- // Begin Template Body \\ -->
	                                    	<table border="0" cellpadding="10" cellspacing="0" width="600" id="templateBody">
	                                        	<tr>
	                                            	<td valign="top" class="bodyContent">

	                                                    <!-- // Begin Module: Standard Content \\ -->
	                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
	                                                        <tr>
	                                                            <td valign="top">
	                                                                <div>
	                                                                	<?php echo wpautop($message); ?>
	                                                                </div>
	    														</td>
	                                                        </tr>
	                                                    </table>
	                                                    <!-- // End Module: Standard Content \\ -->

	                                                </td>
	                                            </tr>
	                                        </table>
	                                        <!-- // End Template Body \\ -->
	                                    </td>
	                                </tr>
	                            </table>
	                            <br />
	                        </td>
	                    </tr>
	                </table>
	            </center>
	        </body>
	    </html>
	    <?php
	    $email_body = ob_get_clean();

	}

    $return = apply_filters( 'ldms_email_output', $email_body, $message, $sender_name, $recipents_name, $post_url );

    return $return;

}

function ldms_get_default_email_message( $type = null ) {


		$output = '<h4>'.esc_html__( 'Hello, %recipient_name%', 'ldmessenger' ).'</h4>';
		$output .= '<p>'.esc_html__( 'You\'ve received a message from %sender_name% on %site_name%.', 'ldmessenger' ).'</p>';
		$output .= '<p><strong>'.esc_html__( '%sender_name% wrote:', 'ldmessenger').'</strong></p>';
		$output .= '%message%';
		$output .= '<hr>';
		$output .= '<p>'.esc_html__( 'To respond click here: ', 'ldmessenger').'<a href="%session_url%">%session_url%</a></p>';

		if ( $type == 'reply' ){
			$output = '<h4>'.esc_html__( 'Hello, %recipient_name%', 'ldmessenger' ).'</h4>';
			$output .= '<p>'.esc_html__( 'You\'ve received a reply from %sender_name% on %site_name%.', 'ldmessenger' ).'</p>';
			$output .= '<p><strong>'.esc_html__( '%sender_name% wrote:', 'ldmessenger').'</strong></p>';
			$output .= '%message%';
			$output .= '<hr>';
			$output .= '<p>'.esc_html__( 'To respond click here: ', 'ldmessenger').'<a href="%session_url%">%session_url%</a></p>';
		}

		return $output;


}

function ldms_generate_message($comment, $sender_name, $recipents_name, $post_url, $reply = false){
	$settings = get_option( 'ldms_private_sessions_settings', array() );

	$message = isset( $settings['ldms_email_message'] ) ? $settings['ldms_email_message'] : ldms_get_default_email_message(true);

	if ( $reply ){
		$message = isset( $settings['ldms_email_rely_message'] ) ? $settings['ldms_email_rely_message'] : ldms_get_default_email_message(true);
	}
	$message = str_replace("%recipient_name%", $recipents_name, $message);
	$message = str_replace("%site_name%", get_bloginfo( 'name' ), $message);
	$message = str_replace("%sender_name%", $sender_name, $message);
	$message = str_replace("%session_url%", $post_url, $message);
	$message = str_replace("%message%", $comment, $message);
	return $message;
}

function ldms_convert_breaks( $input ) {

	$out = str_replace( "<br>", "\r\n", $input );
	$out = str_replace( "<br/>", "\r\n", $out );
	$out = str_replace( "<br />", "\r\n", $out );
	$out = str_replace( "<BR>", "\r\n", $out );
	$out = str_replace( "<BR/>", "\r\n", $out );
	$out = str_replace( "<BR />", "\r\n", $out );

	return "\r\n\r\n" . $out . "\r\n\r\n";

}
