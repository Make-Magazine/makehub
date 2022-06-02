<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Wpvc_Front_Rest_Actions_Controller')){
    class Wpvc_Front_Rest_Actions_Controller{

        public function __construct(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']!='xmlhttprequest'){
                $create_random_hash="wpvcvotingcontestadmin".rand();
                $hash = wp_hash($create_random_hash);
                unset($_COOKIE['wpvc_voting_authorize']);
                setcookie('wpvc_voting_authorize', $hash, (time()+86400), "/");
            }
        }

        public static function wpvc_callback_save_votes($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $post_id = $param['postid'];
                $category_id = $param['category_id'];
                $votesetting = $param['votesetting'];
                $post_index = $param['postind'];
                $email = $param['email'];
                if($post_id !='' && $category_id != ''){
                    $response = Wpvc_Voting_Model::wpvc_save_votes($post_id,$category_id,$votesetting,$post_index,null,false,'',$email);
                }
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_send_email_verification($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $email = $param['email'];
                $six_digit_random_number = random_int(100000, 999999);     
                $email_ctrl = new Wpvc_Email_Controller();      
                $email_ctrl->wpvc_send_email_verification($six_digit_random_number,$email);
                return new WP_REST_Response(array('code' => $six_digit_random_number * 999),200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }


        public static function wpvc_callback_send_email($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $post_id = $param['post_id'];
                $userdata = $param['userdata'];
                $insertdata = $param['insertdata'];
                $gateway = $param['gateway'];
                $email_ctrl = new Wpvc_Email_Controller();       
                $email_ctrl->wpvc_contestant_check_email($post_id,$userdata,$insertdata,$gateway);
                return new WP_REST_Response(array('new_post_id' => $post_id),200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        //Check recaptcha Enable or not 
        public static function wpvc_validate_recaptcha($recapatcha, $secret){
            
            $zn_error_message = array();
        
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }

            $post_data = http_build_query(
                array(
                    'secret' => $secret,
                    'response' => $recapatcha,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                )
            );
            
            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $post_data
                )
            );
            $context  = stream_context_create($opts);
            $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            $result = json_decode($response);					

            if (!$result->success) {
                $zn_error_message['error'] = __("Captcha Error - Reload the page and try again","voting-contest");
            }
        
            return $zn_error_message;
        }
        
        public static function wpvc_callback_captcha_verify($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $recaptcha = $param['captcha'];
                
                if(!empty($recaptcha)){
                    $votes_settings = get_option(WPVC_VOTES_SETTINGS);                
                    $captcha = Wpvc_Front_Rest_Actions_Controller::wpvc_validate_recaptcha($recaptcha, $votes_settings['common']['vote_recapatcha_secret']);
                    if(!empty($captacha)){
                        $captcha['verified'] = false;
                        return new WP_REST_Response($response,200);
                    }
                    $response['verified'] = true;
                    return new WP_REST_Response($response,200);
                }
                $response['verified'] = false;
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 

        }

        public static function wpvc_callback_user_logon($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $logon = $param['login'];
    
                if(!empty($logon)){
                    $votes_settings = get_option(WPVC_VOTES_SETTINGS);
                    if($votes_settings['common']['vote_enable_recaptcha'] == 'on')
                    {
                        $captacha = Wpvc_Front_Rest_Actions_Controller::wpvc_validate_recaptcha($logon['recaptcha'], $votes_settings['common']['vote_recapatcha_secret']);
                        if(!empty($captacha)){
                            $response = array('user_login'=>$captacha);
                            return new WP_REST_Response($response,200);
                        }
                    }
                    $creds['user_login'] = $logon['username'];
                    $creds['user_password'] = $logon['password'];
                    $creds['remember'] = ($logon['remember_me']=='on')?true:false;
                    $user = wp_signon($creds, false);
                    $response = array('user_login'=>$user);
                    return new WP_REST_Response($response,200);
                }
                return;
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_reset_password($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $user_login = sanitize_text_field($param['login']);
    
                if(!empty($user_login)){
                    global $wpdb, $current_site;

                    if ( empty( $user_login) ) {
                        return false;
                    } else if ( strpos( $user_login, '@' ) ) {
                        $user_data = get_user_by( 'email', trim( $user_login ) );
                        if ( empty( $user_data ) )
                        return false;
                    } else {
                        $login = trim($user_login);
                        $user_data = get_user_by('login', $login);
                    }

                    if ( !$user_data ) {
                        $response = array('forgot_error' => 'User Does not exists');
                        return new WP_REST_Response($response,200);
                    }
                    else{
                        $user_login = $user_data->user_login;
                        do_action('retrieve_password', $user_login);                                   
                        $key = wp_generate_password( 20, false );
                        do_action( 'retrieve_password_key', $user_login, $key );    
                        if ( empty( $wp_hasher ) ) {
                            require_once ABSPATH . 'wp-includes/class-phpass.php';
                            $wp_hasher = new PasswordHash( 8, true );
                        }
                        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
                        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
                        $email_ctrl = new Wpvc_Email_Controller();      
                        $email_res = $email_ctrl->wpvc_send_reset_password($user_data,$key);                                          
                        if($email_res == true){
                            $response = array('forgot_success' => 'Link for password reset has been emailed to you. Please check your email');                                                                  
                        }
                        else{
                            $response = array('forgot_error' => 'The e-mail could not be sent');  
                        }
                        
                        return new WP_REST_Response($response,200);
                    }

                    
                }
                return;
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_user_get_register(){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $reg_form = Wpvc_Shortcode_Model::wpvc_reg_custom_fields();
                if(!empty($reg_form)){
                    $new_reg_array = array();
                    $i=0;
                    foreach($reg_form as $registerform){
                        $react_values = json_decode($registerform['react_val']);
                        $comma_sep_values = explode(',',$registerform['response']);
                        $registerform = array_merge($registerform, array('react_values' =>$react_values,'drop_values'=>$comma_sep_values));
                        $new_reg_array[$i] = maybe_unserialize($registerform);
                        $i++;
                    }
                    $response = array('register_form'=>$new_reg_array);
                    return new WP_REST_Response($response,200);
                }else{
                    return new WP_REST_Response('',200);
                }   
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_user_register_facebook($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $register = $param['register'];
                $zn_error_message = array();
                if(!empty($register)){
                    $username = $register['first_name'].$register['last_name'];
                    $user_login = sanitize_user($username, true);

                    if (($user_id_tmp = email_exists ($register['email'])) !== false) {
                        $user_data = get_userdata ($user_id_tmp);
                        if ($user_data !== false) {
                        $user_id = $user_data->ID;
                        $user_login = $user_data->user_login;
                        wp_clear_auth_cookie ();
                        wp_set_current_user ( $user_data->ID );
                        wp_set_auth_cookie ($user_data->ID, true);
                        do_action ('wp_login', $user_data->user_login, $user_data);
                        $user = array('ID'=>$user_data->ID);
                        $response = array('user_login'=>$user);
                        return new WP_REST_Response($response,200);
                        }
                    }
                    else{
                        $new_user = true;
                        $user_login = SELF::wpvc_voting_usernameexists($user_login);
                        $user_password = wp_generate_password();
                        $user_role = get_option('default_role');
                        $user_data = array (
                                            'user_login' => $user_login,
                                            'display_name' => (!empty ($register['name']) ? $register['name'] : $user_login),
                                            'user_email' => $register['email'],
                                            'first_name' => $register['first_name'],
                                            'last_name' => $register['last_name'],
                                            'user_url' => $register['website'],
                                            'user_pass' => $user_password,
                                            'description' => $register['aboutme'],
                                            'role' => $user_role
                                        );
            
                        $user_id = wp_insert_user($user_data);
                        update_user_meta($user_id,WPVC_VOTES_USER_META,$register);

                        wp_clear_auth_cookie ();
                        wp_set_current_user ( $user_id );
                        wp_set_auth_cookie ($user_id, true);
                        do_action( 'wp_login', $user_data->user_login, $user_data );
                        $user = array('ID'=>$user_id);
                        $response = array('user_login'=>$user);
                        return new WP_REST_Response($response,200);
                    }
                    exit;
                }
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_user_register_twitter($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $register = $param['register'];

                if(!empty($register)){

                    $votes_settings = get_option(WPVC_VOTES_SETTINGS);
                    
                    // everything looks good, request access token
                    //successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
                    include_once WPVC_CONTROLLER_PATH.'wpvc_twitter_controller.php';
                    $connection = new Wpvc_Twitter_Controller($votes_settings["share"]['vote_tw_appid'] , $votes_settings["share"]['vote_tw_secret'],$register['oauth_token'] , $register['token_secret'] );
                    $access_token = $connection->getAccessToken($register['oauth_verifier']);

                    if($connection->http_code=='200'){
                        $params = array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');

                        $content = $connection->get('account/verify_credentials', $params); // get the data
                    }else{
                        $error = str_replace(['=', '+'], ' ', http_build_query($access_token, null, ','));
                        $response = array('user_login'=> array('error' => $error));
                        return new WP_REST_Response($response,200);
                    }

                    if($connection->http_code=='200'){
                        
                        // getting twitter user profile details            
                        $twt_email = $content->email;
                        
                        $screenname = $content->screen_name;
                        // $content = $connection->get('users/show', array('screen_name' => $screenname));
        
                        $username   = $content->name;
                        $user_login = sanitize_user($screenname, true);
                        
                        if (($user_id_tmp = email_exists ($twt_email)) !== false) {
                            
                            $user_data = get_userdata ($user_id_tmp);
                            if ($user_data !== false) {
                            $user_id = $user_data->ID;
                            $user_login = $user_data->user_login;
                            wp_clear_auth_cookie ();
                            wp_set_current_user ( $user_data->ID );
                            wp_set_auth_cookie ($user_data->ID, true);
                            do_action ('wp_login', $user_data->user_login, $user_data);
                            $user = array('ID'=>$user_data->ID);
                            $response = array('user_login'=>$user);
                            return new WP_REST_Response($response,200);
                            }
                        }
                        else{
                            $new_user = true;
                            
                            $user_login = SELF::wpvc_voting_usernameexists($user_login);
                            $user_password = wp_generate_password();
                            $user_role = get_option('default_role');
                            $user_data = array (
                                                'user_login' => $user_login,
                                                'display_name' => (!empty ($username) ? $username : $user_login),
                                                'user_email' => $twt_email,
                                                'first_name' => $content->name,
                                                'user_url' => $content->url,
                                                'user_pass' => $user_password,
                                                'description' => $content->description,
                                                'role' => $user_role
                                            );
                            $register['email'] = $twt_email;
                            $register['first_name'] = $content->name;
                            $user_id = wp_insert_user($user_data);
                            update_user_meta($user_id,WPVC_VOTES_USER_META,$register);

                            wp_clear_auth_cookie ();
                            wp_set_current_user ( $user_id );
                            wp_set_auth_cookie ($user_id, true);
                            do_action( 'wp_login', $user_data->user_login, $user_data );
                            $user = array('ID'=>$user_id);
                            $response = array('user_login'=>$user);
                            
                            return new WP_REST_Response($response,200);
                        }
                        exit;
                    }else{
                        $response = array('user_login'=> array('error' => 'Error, Try again later!'));
                        return new WP_REST_Response($response,200);
                    }		
                }
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_user_register_google($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $register = $param['register']['profileObj'];
                $zn_error_message = array();
                if(!empty($register)){
                    $username = $register['name'];
                    $user_login = sanitize_user($username, true);

                    if (($user_id_tmp = email_exists ($register['email'])) !== false) {
                        $user_data = get_userdata ($user_id_tmp);
                        if ($user_data !== false) {
                        $user_id = $user_data->ID;
                        $user_login = $user_data->user_login;
                        wp_clear_auth_cookie ();
                        wp_set_current_user ( $user_data->ID );
                        wp_set_auth_cookie ($user_data->ID, true);
                        do_action ('wp_login', $user_data->user_login, $user_data);
                        $user = array('ID'=>$user_data->ID);
                        $response = array('user_login'=>$user);
                        return new WP_REST_Response($response,200);
                        }
                    }
                    else{
                        $new_user = true;
                        $user_login = SELF::wpvc_voting_usernameexists($user_login);
                        $user_password = wp_generate_password();
                        $user_role = get_option('default_role');
                        $user_data = array (
                                            'user_login' => $user_login,
                                            'display_name' => (!empty ($register['name']) ? $register['name'] : $user_login),
                                            'user_email' => $register['email'],
                                            'first_name' => $register['givenName'],
                                            'last_name' => $register['familyName'],
                                            'user_url' => '',
                                            'user_pass' => $user_password,
                                            'description' => '',
                                            'role' => $user_role
                                        );
            
                        $user_id = wp_insert_user($user_data);
                        update_user_meta($user_id,WPVC_VOTES_USER_META,$register);

                        wp_clear_auth_cookie ();
                        wp_set_current_user ( $user_id );
                        wp_set_auth_cookie ($user_id, true);
                        do_action( 'wp_login', $user_data->user_login, $user_data );
                        $user = array('ID'=>$user_id);
                        $response = array('user_login'=>$user);
                        return new WP_REST_Response($response,200);
                    }
                    exit;
                }
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_voting_usernameexists($username) {
			$nameexists = true;
			$index = 0;
			$userName = $username;
			while($nameexists == true){
			  if (username_exists($userName) != 0) {
				$index++;
				$userName = $username.$index;
			  }
			  else {
				$nameexists = false;
			  }
			}
			return $userName;
		}

        public static function wpvc_callback_user_register($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $register = $param['register'];
                $zn_error_message = array();
                if(!empty($register)){
                    
                    if ( username_exists( $register['username'] ) ){
                        $zn_error_message['error'] = __('The username already exists','voting-contest');
                    }
                    if( email_exists( $register['email'] )) {
                        $zn_error_message['error'] = __('This email address has already been used','voting-contest');
                    }

                    $votes_settings = get_option(WPVC_VOTES_SETTINGS);
                    if($votes_settings['common']['vote_enable_recaptcha'] == 'on')
                    {
                        $zn_error_message = Wpvc_Front_Rest_Actions_Controller::wpvc_validate_recaptcha($register['recaptcha'], $votes_settings['common']['vote_recapatcha_secret']);
                    }
                    
                    if(empty($zn_error_message)){
                        $user_data = array(
                            'ID' => '',
                            'user_pass' =>$register['password'],
                            'user_login' => $register['username'],
                            'display_name' => $register['username'],
                            'user_email' => $register['email'],
                            'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
                        );
                        $user_id = wp_insert_user( $user_data );
    
                        update_user_meta($user_id,WPVC_VOTES_USER_META,$register);

                        $creds['user_login'] = $register['username'];
                        $creds['user_password'] = $register['password'];
                        $user = wp_signon($creds, false);
                        $response = array('user_registration'=>$user);
                        return new WP_REST_Response($response,200);
                    }else{
                        $response = array('user_registration'=>$zn_error_message);
                        return new WP_REST_Response($response,200);
                    }

                }
                return;
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_submit_entry($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $category_id = $param['category_id'];
                $insertdata = $param['insertData'];
                $userdata = $param['userdata'];
                $post_status = $param['post_status'];
                $coinbaseCharge = $param['coinbaseCharge'];
                $get_file_sys_name = array();
                if(!empty($insertdata)){
                    $getkey_values= array();
                    foreach($insertdata as $data=>$key){
                        array_push($getkey_values,$data);
                    }
                    $get_file_sys_name = Wpvc_Shortcode_Model::wpvc_custom_fields_files($getkey_values);
                }
                
                if($category_id!=''){
                    $post_id = Wpvc_Shortcode_Model::wpvc_insert_contestants($category_id,$insertdata,$userdata,$post_status);

                    //Update Coinbase Charge ID for verifying it Webhook
                    if($coinbaseCharge != "")
                        update_post_meta($post_id,"wpvc_coinbase_charge",$coinbaseCharge);
                }
                else{
                    $post_id = '';
                }
                                
                $response = array('new_post_id'=>$post_id,'file_sys_name'=>$get_file_sys_name);
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
            
        }

        public static function wpvc_callback_update_submit_entry($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $post_id = $param['post_id'];
                $insertdata = $param['insertData'];
                $get_file_sys_name = array();
                if(!empty($insertdata)){
                    $getkey_values= array();
                    foreach($insertdata as $data=>$key){
                        array_push($getkey_values,$data);
                    }
                    $get_file_sys_name = Wpvc_Shortcode_Model::wpvc_custom_fields_files($getkey_values);
                }

                if($post_id!='')
                    $uppost_id = Wpvc_Shortcode_Model::wpvc_update_contestants($post_id,$insertdata);
                else
                    $uppost_id = '';
                                
                $response = array('new_post_id'=>$uppost_id,'file_sys_name'=>$get_file_sys_name);
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
            
        }

        public static function wpvc_get_sucess_msg_submit_entry(){
            $settings = get_option(WPVC_VOTES_SETTINGS);
            if(is_array($settings)){
                $auto_arrove=$settings['contest']['vote_publishing_type'];
                if($auto_arrove=='on'){
                    return 'approve_entry';
                }else{
                    return 'not_approved';
                }
            }

        }

        public static function wpvc_callback_showallcontestants($request_data){
            /*if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { */
                $param = $request_data->get_params();	
                $category_id = $param['id'];
                $return_alert = SELF::wpvc_get_sucess_msg_submit_entry();
                if($category_id != ''){
                    $postID = $param['postID']; 
                    //Admin end EditContestant  Send postID 
                    $custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields($category_id,$postID); 		
                    $get_settings = Wpvc_Shortcode_Model::wpvc_settings_page_json($category_id);
                
                    $selcategory_options = get_term_meta($category_id,'',true); 
                    $align_category = array();
                    if(is_array($selcategory_options)){
                        foreach($selcategory_options as $key=>$val){
                            if($key=='contest_rules'){
                                $align_category[$key] = format_to_edit($val[0],TRUE);
                            }else
                                $align_category[$key] = maybe_unserialize($val[0]);
                        }
                        $align_category['id'] = $category_id;
                    }

                    $entrycount = apply_filters('wpvc_post_entry_hook',Wpvc_Shortcode_Model::wpvc_get_post_entry_track($GLOBALS['wpvc_user_id'],$category_id),$category_id);
                    $respon = array('settings'=>$get_settings,'entrycount' => $entrycount,'search_text'=>'','paginate_value'=>'','selcategoryoptions' => apply_filters('cat_extension_update_values',$align_category,$category_id),'ret_alert'=>$return_alert);
                    $response = array_merge($respon,$custom_fields); 
                    return new WP_REST_Response($response,200);
                }else{
                    $custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields(); 		
                    $get_settings = Wpvc_Shortcode_Model::wpvc_settings_page_json();

                    $new_taxonomy = Wpvc_Common_State_Controller::wpvc_new_taxonomy_state();  
                    $terms = Wpvc_Settings_Model::wpvc_category_get_allTerms($new_taxonomy);                   

                    $respon = array('settings'=>$get_settings,'taxonomy' => $terms,'new_taxonomy' => $new_taxonomy,'category_term'=>0,'sort_order'=>0,'search_text'=>'','paginate_value'=>'','allpaginateval'=>'','selcategoryoptions' => null,'ret_alert'=>$return_alert);
                    $response = array_merge($respon,$custom_fields);
                    return new WP_REST_Response($response,200);
                }
            /*}else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } */    
        }

        public static function wpvc_get_custom_fields($category_id=null,$postID = null,$admin = false){
            $wpvc_video_extension = get_option('_ow_video_extension');
            $imgcontest = '';
            
            if($category_id !=''){
                $category_options = get_term_meta($category_id,'contest_category_assign_custom',true);
                $imgcontest = get_term_meta($category_id,'imgcontest',true);
                $musicfileenable = get_term_meta($category_id,'musicfileenable',true);

                $custom_fields = maybe_unserialize($category_options);
                $create_custom = array();
                $contestant_form = array();

                $settings = Wpvc_Shortcode_Model::wpvc_settings_page_json($category_id);
                // var_dump($custom_fields);
                if(is_array($custom_fields)){
                    $i=0;
                    foreach($custom_fields as $custom){
                        $submitentry_form = $custom['system_name'];

                        if($wpvc_video_extension == 1){
                            if($custom['system_name'] == 'contestant-ow_video_url'){
                                $admin_edit = $settings['wpvc_videoextension_settings']['ow_enable_video_url'] == 'on' ? true : false;
                                $current_url = wp_get_referer();
                                if (strpos($current_url, 'action=edit') !== false){
                                    if(($postID != null && !$admin_edit) || ($postID == null && !$admin && !$admin_edit)){
                                        continue;
                                    }
                                }
                            }
                        }
                        
                        if($custom['system_name'] == 'contestant-ow_video_upload_url' && $wpvc_video_extension != 1){
                            continue;
                        }
                        

                        //get original custom fields from the dragged one
                        $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_id($custom['id']);
                        if(!empty($getcustom)){
                            //Sending the original form
                            $react_values = json_decode($getcustom['react_val']);
                            $comma_sep_values = explode(',',$getcustom['response']);
                            $getcustom = array_merge($getcustom, array('react_values' =>$react_values,'drop_values'=>$comma_sep_values));
                            $create_custom[$i] = maybe_unserialize($getcustom);
                            //To save the values of form on redux
                            $contestant_form[$submitentry_form] = '';                            
                            
                        }
                        $i++; 
                    }
                }else{
                    
                    //If no fields assigned get default fields
                    $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest($imgcontest,$musicfileenable);
                    $create_custom = maybe_unserialize($getcustom);
                    // var_dump($create_custom);
                    if(is_array($create_custom)){
                        $keys = [];
                        foreach($create_custom as $key => $custom){

                            if($custom['system_name'] == 'contestant-ow_video_upload_url'){
                                if($wpvc_video_extension != 1){
                                    $keys[] = $key;
                                    continue;
                                }
                            }

                            //hide url from user and admin edit page
                            if($custom['system_name'] == 'contestant-ow_video_url' && $wpvc_video_extension == 1){
                                $admin_edit = $settings['wpvc_videoextension_settings']['ow_enable_video_url'] == 'on' ? true : false;
                                $current_url = wp_get_referer();
                                if (strpos($current_url, 'action=edit') !== false){
                                    if(($postID != null && !$admin_edit) || ($postID == null && !$admin && !$admin_edit)){
                                        $keys[] = $key;
                                        continue;
                                    }
                                }
                            }

                            $submitentry_form = $custom['system_name'];
                            $contestant_form[$submitentry_form] = '';
                        }

                        foreach($keys as $key){
                            array_splice($create_custom, $key, 1);
                        }
                    }
                }
            }else{
                $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_contest('contest',$musicfileenable);
                $create_custom = maybe_unserialize($getcustom);
                
                if(is_array($create_custom)){
                    foreach($create_custom as $custom){

                        if($custom['system_name'] == 'contestant-ow_video_upload_url'){
                            if($wpvc_video_extension != 1){
                                continue;
                            }
                        }
            
                            $submitentry_form = $custom['system_name'];
                            $contestant_form[$submitentry_form] = '';
                    }
                }
            }
            
            //Add file upload field in frontend
            if($imgcontest == 'video' && $wpvc_video_extension == 1 && !array_key_exists('contestant-ow_video_upload_url',$contestant_form) && $postID == null){
                $contestant_form['contestant-ow_video_upload_url'] = '';
                $getcustom = Wpvc_Shortcode_Model::wpvc_custom_fields_by_name('contestant-ow_video_upload_url');
                if(!empty($getcustom)){
                    $react_values = json_decode($getcustom['react_val']);
                    $comma_sep_values = explode(',',$getcustom['response']);
                    $getcustom = array_merge($getcustom, array('react_values' =>$react_values,'drop_values'=>$comma_sep_values));
                    $custom = maybe_unserialize($getcustom);
                    
                    if(count($create_custom) > 0){
                        array_push($create_custom, $custom);
                    }else{
                        $create_custom = $custom;
                    }
                }
            }
            
            if(!empty($create_custom))
            $create_custom = array_values($create_custom);

            //Admin end EditContestant - Get Values assigned for the custom fields
            if($postID != null){
                //Splitting the file array and all array values
                $allValues =  Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields_values($category_id,$postID);
                wp_set_post_terms( $postID, $category_id , WPVC_VOTES_TAXONOMY);
                $contestant_form = $allValues['custom_values'];
                $newUploadedFiles = $allValues['newUploadedFiles'];
                
                $return_array = array('contestant_form'=>$contestant_form,'custom_field'=>$create_custom,'newUploadedFiles' => $newUploadedFiles);
            }
            else{
                $return_array = array('contestant_form'=>$contestant_form,'custom_field'=>$create_custom);
                $return_array = apply_filters('cat_extension_payment_entry',$return_array);
            }
            return $return_array;
            
        }

        public static function wpvc_get_custom_fields_values($termid,$postID){
            //Get Custom_fields
            $get_custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields($termid, null, true);
            $get_post_metas = get_post_meta($postID, WPVC_VOTES_POST,TRUE);
            if(!empty($get_custom_fields)){
                $custom_values = array();
                $newUploadedFiles = array();
                $custom_fields = $get_custom_fields['custom_field'];

                if(is_array($custom_fields)){
                    foreach($custom_fields as $cus_key => $fields){
                        $system_name = $fields['system_name'];
                        if($fields['question_type']=='FILE'){
                            $post_image = get_post_meta($postID, 'ow_custom_attachment_'.$system_name, TRUE);
                            $custom_values[$system_name]= empty($post_image)?'':$post_image['url'];
                            $newUploadedFiles[$system_name] = '';
                        }
                        else{
                            $custom_values[$system_name] = (is_array($get_post_metas))?$get_post_metas[$system_name]:'';
                            if($custom_values[$system_name] == ''){
                                $custom_values[$system_name] = get_post_meta($postID,$system_name,TRUE);
                            }
                            if(array_key_exists('contestant-ow_video_url',$custom_values) && $custom_values[$system_name]==''){
                                $get_post_url = get_post_meta($postID, 'contestant-ow_video_url',TRUE);
                                $custom_values[$system_name] = $get_post_url;
                            }
                        }
                    }                    
                }
            }
            return array('custom_values' => $custom_values,'newUploadedFiles' => $newUploadedFiles);
        }

        public static function wpvc_callback_delete_contestant($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $postID = $param['id'];
                $delete_post='';
                if($postID!=''){
                $delete_post = wp_delete_post($postID);
                }
                if($delete_post!=''){
                    $respon = array('deleted'=>$delete_post->ID);
                }
                $respon = array('deleted'=>$delete_post->ID);
                return new WP_REST_Response($respon,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_callback_edit_contestant($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();	
                $termid = $param['termid'];
                $post_id = $param['postid'];
                if($termid!='' && $post_id!=''){
                    $custom_values = SELF::wpvc_get_custom_fields_values_frontend($termid,$post_id);
                    return new WP_REST_Response($custom_values,200);
                }
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }

        public static function wpvc_get_custom_fields_values_frontend($termid,$postID){
            //Get Custom_fields
            $get_custom_fields = Wpvc_Front_Rest_Actions_Controller::wpvc_get_custom_fields($termid);
            $get_post_metas = get_post_meta($postID, WPVC_VOTES_POST,TRUE);
            $post_val = get_post($postID);
            if(!empty($get_custom_fields)){
                $custom_values = array();
                $custom_fields = $get_custom_fields['custom_field'];
                if(is_array($custom_fields)){
                    foreach($custom_fields as $cus_key => $fields){

                        // Assign values based on name and file types
                        $system_name = $fields['system_name'];
                        if($system_name == 'contestant-image'){
                            $get_image = get_the_post_thumbnail_url($postID);
                            $custom_values[$system_name]= $get_image;
                        }
                        else if($system_name == 'contestant-title'){
                            $custom_values[$system_name]= $post_val->post_title;
                        }
                        else if($system_name == 'contestant-desc'){
                            $custom_values[$system_name]= $post_val->post_content;
                        }
                        else if($fields['question_type']=='FILE'){
                            $post_image = get_post_meta($postID, 'ow_custom_attachment_'.$system_name, TRUE);
                            $custom_values[$system_name]= empty($post_image)?'':SELF::normalizeUrl($post_image['url']);
                        }
                        else{
                            if($system_name != 'contestant-title' && $system_name != 'contestant-desc')
                                if(isset($get_post_metas[$system_name]))
                                    $custom_values[$system_name] = $get_post_metas[$system_name];
                        }

                    }                    
                }
            }
            return $custom_values;
        }
        
        public static function normalizeUrl(string $url) {
            $parts = parse_url($url);
            return $parts['scheme'] .
                '://' .
                $parts['host'] .
                implode('/', array_map('rawurlencode', explode('/', $parts['path'])));
    
        }
       
        public static function wpvc_buyvotes_stripe_secrect($request_data){
                $param = $request_data->get_params();
                $settings = get_option(WPVC_VOTES_SETTINGS);
                $payment_option = $settings['wpvc_buyvotes_settings'];
                $currency = ($payment_option['ow_buyvotes_currency'] == null)?'usd':$payment_option['ow_buyvotes_currency'];

                require_once(WP_PLUGIN_DIR.'/wp-voting-contest-buyvotes/lib/stripe-php/init.php');
                    
                $stripe = array(
                    'secret_key'      => $payment_option['ow_buy_stripe_secretkey'],
                    'publishable_key' => $payment_option['ow_buy_stripe_publishkey']
                );
                \Stripe\Stripe::setApiKey($stripe['secret_key']);

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $param['cost'] * 100,
                    'currency' => $currency,
                ]);
                $response = [
                    'clientSecret' => $paymentIntent->client_secret,
                ];
                return new WP_REST_Response($response,200);
        }

        public static function wpvc_callback_buyvotesentry($request_data){
            if($_SERVER['HTTP_AUTHORIZE_WPVC']!='' && strtolower($_SERVER['HTTP_AUTHORIZE_WPVC']) == $_COOKIE['wpvc_voting_authorize'] && $_SERVER['HTTP_AUTHORIZE_WPVC_REQUEST']=='xmlhttprequest')
            { 
                $param = $request_data->get_params();
                $category_id = $param["insertData"]['term'];
                $contestant_id = $param['category_id'];
                $vote_count	= $param["insertData"]['vote_count'];
                $data_amount = $param["insertData"]['cost'];
                $gateway = $param["insertData"]['gateway'];
                $email_address = $param["insertData"]['email_address'];
                if($GLOBALS['wpvc_user_id'] != 0 && $email_address == ""){                
                    $currentuser = get_user_by( 'ID', $GLOBALS['wpvc_user_id'] );
                    $email_address = $currentuser->user_email;
                } 

                $settings = get_option(WPVC_VOTES_SETTINGS);            
                $payment_option = $settings['wpvc_buyvotes_settings'];
                $currency = ($payment_option['ow_buyvotes_currency'] == null)?'usd':$payment_option['ow_buyvotes_currency'];
                
                $response =  (object)$param["insertData"]['response'];
                if($gateway == "stripe"){
                    
                    $payment_status = $response->status;
                    $transaction_id = $response->id;
                    $customer = $response->source;
                    $paid = $response->status;
                    $receipt_email = $response->receipt_email;
                }elseif($gateway == "paypal"){
                    $payment_status = $response->status;
                    $transaction_id = $response->id;
                    $customer = $response->payer;
                    $paid = $response->paid;
                    $receipt_email = $response->purchase_units->payee->email_address;
                    $email_address = $customer['email_address'];

                }elseif($gateway == "paystack"){
                    $payment_status = $response->status;
                    $transaction_id = $response->reference;
                    $paid = $response->message;
                }

                $post_index = $param["insertData"]['index'];
                $votesetting = $param["insertData"]['settings'];
                if($contestant_id !='' && $category_id != ''){
                    $response = Wpvc_Voting_Model::wpvc_save_votes($contestant_id,$category_id,$votesetting,$post_index, null, true, $vote_count,$email_address);
                }
                
                if($payment_status == 'succeeded' || $payment_status == 'COMPLETED'){
                    $payment_status = 'success';
                }

                //add the transaction to custom post type
                $transactions = array(
                    'id'	 => $transaction_id,
                    'amount' => $data_amount,
                    'currency' => $currency,
                    'customer' => $customer,
                    'paid' => $paid,
                    'receipt_email' => $receipt_email,
                    'status' => $payment_status,
                    'vote_count'=>$vote_count                
                );

                $args = array(
                    'post_author'   => 1,
                    'post_status'   => 'publish',
                    'post_type' => 'ow_buy_payment',
                    'post_title' => $transaction_id
                );
                
                $post_id = wp_insert_post( $args );
                
                update_post_meta($post_id,'ow_buy_payment_postid',$contestant_id);
                update_post_meta($post_id,'ow_buy_payment_gateway', $gateway);
                update_post_meta($post_id,'ow_buy_votes_transactions',$transactions);
                update_post_meta($post_id,'ow_buy_payment_status', $payment_status);
                update_post_meta($post_id,'ow_buy_payment_vote_cnt', $vote_count);
                update_post_meta($post_id,'ow_buy_user_email', $email_address);
                
                return new WP_REST_Response($response,200);
            }else{
                die(json_encode(array('no_cheating' => "You have no permission to access Voting contest")));
            } 
        }
    }
}else
die("<h2>".__('Failed to load Voting Front Rest Actions Controller','voting-contest')."</h2>");

return new Wpvc_Front_Rest_Actions_Controller();

