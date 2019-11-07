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

/**
* Remove the register link from the wp-login.php script
* https://wphelper.site/remove-register-link-wordpress-login/
*/
add_filter('option_users_can_register', function($value) {
    $script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
 
    if ($script == 'wp-login.php') {
        $value = false;
    }
    return $value;
});