<?php 

/****************************************************
LOGIN FUNCTIONS 
****************************************************/

// customize the wp-login page
function custom_login_stylesheets() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/css/style-login.css' );
	 wp_enqueue_style( 'custom-login', '/wp-content/universal-assets/v1/css/universal.css' );
}
//This loads the function above on the login page
add_action( 'login_enqueue_scripts', 'custom_login_stylesheets' );

add_action( 'login_header', function() {
    get_header();
});
add_action( 'login_footer', function() {
    get_footer();
});

// handle users without access to certain pages
function no_access_modal() {
	// if user is logged in, but they still got the access denied flag, they are an expired user.
	$error_message = "You don't have access to this page, but we want you to be a part of our community.<br /><a href='javascript:jQuery.fancybox.close();'><b>Please Join!</b></a>";
	if(is_user_logged_in() == "true") {
    	$error_message = "Your membership has expired! You can check your membership <a href='/account/?ihc_ap_menu=subscription'>here</a>, or purchase a new membership on this page to continue.";
	}
   $return = '<div class="fancybox-access-denied" style="display:none;">
					  <div style="padding:15px;">
						 <div class="col-sm-4 col-xs-4" style="padding:0px;">
							<span class="fa-stack fa-4x" style="margin-left:20px;">
							  <i class="far fa-sad-cry fa-stack-2x"></i>
							</span>
						 </div>
						 <div class="col-sm-8 col-xs-8" style="padding:0px;">
							<h3 style="margin-top:20%;margin-left:39%;font-weight:bold;">Uh Oh!</h3>
						 </div>
						 <div class="clearfix"></div>
						 <div class="col-sm-12"><p style="color:#333;text-align:center;margin-top:20px;padding-bottom:10px;">'.$error_message.'</p></div>
					  </div>
					</div>
					<script type="text/javascript">
						jQuery(document).ready(function(jQuery){
						  // activate the fancybox message
						  jQuery(".fancybox-access-denied").fancybox({
							 autoSize : false,
							 width  : 400,
							 autoHeight : true,
							 padding : 0,
							 afterLoad   : function() {
								this.content = this.content.html();
							 }
						  });
						  jQuery(".fancybox-access-denied").trigger("click");
						});
					</script>';
	echo $return;
}
if(isset($_GET['access']) && $_GET['access'] === "denied") {
	add_action( 'wp_footer', 'no_access_modal' );
}

/* Old auth0 scripts
// redirect wp-login.php to the auth0 login page 

function load_auth0_js() {
    //auth0
    wp_enqueue_script('auth0', 'https://cdn.auth0.com/js/auth0/9.6.1/auth0.min.js', array(), false);
    wp_enqueue_script('auth0Login', get_stylesheet_directory_uri() . '/auth0/js/auth0login.js', array(), false);
}

add_action('login_enqueue_scripts', 'load_auth0_js', 10);

// Set up the Ajax Logout 
add_action('wp_ajax_mm_wplogout', 'MM_wordpress_logout');
add_action('wp_ajax_nopriv_mm_wplogout', 'MM_wordpress_logout');

function MM_wordpress_logout() {
    //check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}

add_action('wp_ajax_mm_wplogin', 'MM_WPlogin');
add_action('wp_ajax_nopriv_mm_wplogin', 'MM_WPlogin');

// allow capital letters in usernames
remove_action( 'sanitize_user', 'strtolower' );

// Set up the Ajax WP Login 
function MM_WPlogin() {
    //check_ajax_referer( 'ajax-login-nonce', 'ajaxsecurity' );
    global $wpdb; // access to the database
    //use auth0 plugin to log people into wp
    $a0_plugin = new WP_Auth0();
    $a0_options = WP_Auth0_Options::Instance();
    $users_repo = new WP_Auth0_UsersRepo($a0_options);
    $users_repo->init();

    $login_manager = new WP_Auth0_LoginManager($users_repo, $a0_options);
    $login_manager->init();

    //get the user information passed from auth0
    $userinput = filter_input_array(INPUT_POST);
    $userinfo = (object) $userinput['auth0_userProfile'];
    $userinfo->email_verified = true;
    $access_token = filter_input(INPUT_POST, 'auth0_access_token', FILTER_SANITIZE_STRING);
    $id_token = filter_input(INPUT_POST, 'auth0_id_token', FILTER_SANITIZE_STRING);

    if ($login_manager->login_user($userinfo, $id_token, $access_token)) {
        wp_send_json_success();
    } else {
        error_log('Failed login');
        error_log(print_r($userinput, TRUE));
        wp_send_json_error();
    }
} */


/**
 * These Functions Add and Verify the Invisible Google reCAPTCHA on Login
 * Normal users never see the wp-login.php page as they are forwarded to Auth0. 
 * this will stop spam bots from signing up
 */
/*

/*
function cookie_login_warning() { ?>
    <style type="text/css">
        .wp-core-ui #login { width: 80%; }
        #login::before {
            content: "We are unable to process your login as we have detected that you have cookies blocked. Please make sure cookies are enabled in your browser and try again.";
            text-align: center;
            font-size:42px;
            line-height: 46px;
        }
        #form-signin-wrapper {
            display: none;
        }
    </style>
    <?php

}

add_action('login_enqueue_scripts', 'cookie_login_warning');

add_action('login_enqueue_scripts', 'login_recaptcha_script');

function login_recaptcha_script() {
    wp_register_script('recaptcha_login', 'https://www.google.com/recaptcha/api.js');
    wp_enqueue_script('recaptcha_login');
}

add_action('login_form', 'display_recaptcha_on_login');

function display_recaptcha_on_login() {
    echo "<script>
            function onSubmit(token) {
                document.getElementById('loginform').submit();
            }
          </script>
            <button class='g-recaptcha' data-sitekey='6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP' data-callback='onSubmit' data-size='invisible' style='display:none;'>Submit</button>";
}

add_filter('wp_authenticate_user', 'verify_recaptcha_on_login', 10, 2);

function verify_recaptcha_on_login($user, $password) {
    if (isset($_POST['g-recaptcha-response'])) {
        $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP&response=' . $_POST['g-recaptcha-response']);
        $response = json_decode($response['body'], true);

        if (true == $response['success']) {
            return $user;
        } else {
            // FIXME: This one fires if your password is incorrect... Check if password was incorrect before returning this error...
            return new WP_Error( 'Captcha Invalid', __('<strong>ERROR</strong>: You are a bot') );
        }
    } else {
        return new WP_Error('Captcha Invalid', __('<strong>ERROR</strong>: You are a bot. If not then enable JavaScript.'));
    }
}

add_action( 'login_form_lostpassword', 'wpse45134_filter_option' );
add_action( 'login_form_retrievepassword', 'wpse45134_filter_option' );
add_action( 'login_form_register', 'wpse45134_filter_option' );

*/

/**
 * Simple wrapper around a call to add_filter to make sure we only
 * filter an option on the login page.
 */
 /*
function wpse45134_filter_option()
{
    // use __return_zero because pre_option_{$opt} checks
    // against `false`
    add_filter( 'pre_option_users_can_register', '__return_zero' );
} 
*/