<?php

function myGo($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => 'http://'
	), $atts));
	return '<div class="shortcode_go"><a href="'.$href.'">'.$content.'</a></div>';
}

add_shortcode("go", "myGo");


function myQuoteBy($atts, $content = null) {
	extract(shortcode_atts(array(
		"by" => ''
	), $atts));
	return '<div class="shortcode_quoteby"><div class="shortcode_quotebyauthor">'.$by.'</div>'.$content.'</div>';
}

add_shortcode("quoteby", "myQuoteBy");

function myClear() {return '<div class="clear"></div>';}
add_shortcode('clear', 'myClear');


function highlighttext($atts, $content=null, $code="") {
	$return = '<span class="shortcode_highlight">';
	$return .= $content;
	$return .= '</span>';
	return $return;
}

add_shortcode('highlight' , 'highlighttext' );


function noticetext($atts, $content=null, $code="") {
	$return = '<div class="shortcode_notice">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}

add_shortcode('notice' , 'noticetext' );


function quotetext($atts, $content=null, $code="") {
	$return = '<div class="shortcode_quote">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}

add_shortcode('quote' , 'quotetext' );


function leftpullquotes($atts, $content=null, $code="") {
	$return = '<div class="leftpullquote">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}

add_shortcode('leftpullquote' , 'leftpullquotes' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function rightpullquotes($atts, $content=null, $code="") {
	$return = '<div class="rightpullquote">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}

add_shortcode('rightpullquote' , 'rightpullquotes' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function member_check_shortcode( $atts, $content = null ) {
    if ( is_user_logged_in() && !is_null( $content ) && !is_feed() ) {
	return '<div class="shortcode_member">' . $content . '</div>';
	} else {
	return '<div class="shortcode_no-member">' . __("This content is visible for members only", "onecommunity-shortcodes") . '</div>';
	}
    return '';
}

add_shortcode( 'member', 'member_check_shortcode' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_login($atts, $content = null) {

ob_start();

if ( is_user_logged_in() ) : ?>
<?php else : ?>

<div class="shortcode-login" id="login-jump">

		<form name="login-form" class="shortcode-login-form" action="login" method="post">

			<h4><?php _e( 'Log In', 'onecommunity-shortcodes' ); ?></h4>

			<span class="shortcode-login-desc"><?php _e( 'Login to your account and check out the new messages.', 'onecommunity-shortcodes' ); ?></span>

			<span class="status"></span>

			<label><input type="text" name="username" id="shortcode-user-login" placeholder="<?php _e( 'Username', 'onecommunity-shortcodes' ) ?>" value="<?php if ( isset( $user_login) ) echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><input type="password" name="password" id="shortcode-user-pass" placeholder="<?php _e( 'Password', 'onecommunity-shortcodes' ) ?>" value="" tabindex="98" /><span class="password-show hidden"></span></label>

			<div class="forgetmenot">
				<div><label><input name="rememberme" type="checkbox" class="shortcode-rememberme" value="forever" tabindex="99" checked="checked" /><span class="checkmark"></span><?php _e( 'Remember Me', 'onecommunity-shortcodes' ) ?></label></div>
			</div>

			<input type="submit" name="wp-submit" id="shortcode-login-submit" value="<?php _e( 'Log In', 'onecommunity-shortcodes' ); ?>" tabindex="100" />

		</form>

		<div class="clear"></div>

		<div class="shortcode-login-bottom">
		<a href="<?php echo home_url(); ?>/recovery" class="shortcode-password-recovery"><?php _e( 'Forgot your password?', 'onecommunity-shortcodes' ); ?></a> <a href="<?php echo home_url(); ?>/register" class="shortcode-login-register"><?php _e("Create New Account!", "onecommunity-shortcodes"); ?></a>
		</div>

</div><!-- shortcode-login -->

<?php endif;

$shortcode_content = ob_get_clean();
return $shortcode_content;
}

add_shortcode("onecommunity-login", "onecommunity_login");


///////////////////////////////// ANOTHER PART OF THE LOGIN CODE IS INSIDE THEME'S FUNCTIONS.PHP //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



function my_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_third', 'my_one_third');

function my_one_third_last( $atts, $content = null ) {
   return '<div class="one_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_third_last', 'my_one_third_last');

function my_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_third', 'my_two_third');

function my_two_third_last( $atts, $content = null ) {
   return '<div class="two_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_third_last', 'my_two_third_last');

function my_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_half', 'my_one_half');

function my_one_half_last( $atts, $content = null ) {
   return '<div class="one_half last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_half_last', 'my_one_half_last');

function my_one_fourth( $atts, $content = null ) {
   return '<div class="one_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fourth', 'my_one_fourth');

function my_one_fourth_last( $atts, $content = null ) {
   return '<div class="one_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fourth_last', 'my_one_fourth_last');

function my_three_fourth( $atts, $content = null ) {
   return '<div class="three_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fourth', 'my_three_fourth');

function my_three_fourth_last( $atts, $content = null ) {
   return '<div class="three_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fourth_last', 'my_three_fourth_last');

function my_one_fifth( $atts, $content = null ) {
   return '<div class="one_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fifth', 'my_one_fifth');

function my_one_fifth_last( $atts, $content = null ) {
   return '<div class="one_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fifth_last', 'my_one_fifth_last');

function my_two_fifth( $atts, $content = null ) {
   return '<div class="two_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_fifth', 'my_two_fifth');

function my_two_fifth_last( $atts, $content = null ) {
   return '<div class="two_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_fifth_last', 'my_two_fifth_last');

function my_three_fifth( $atts, $content = null ) {
   return '<div class="three_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fifth', 'my_three_fifth');

function my_three_fifth_last( $atts, $content = null ) {
   return '<div class="three_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fifth_last', 'my_three_fifth_last');

function my_four_fifth( $atts, $content = null ) {
   return '<div class="four_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('four_fifth', 'my_four_fifth');

function my_four_fifth_last( $atts, $content = null ) {
   return '<div class="four_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('four_fifth_last', 'my_four_fifth_last');

function my_one_sixth( $atts, $content = null ) {
   return '<div class="one_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_sixth', 'my_one_sixth');

function my_one_sixth_last( $atts, $content = null ) {
   return '<div class="one_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_sixth_last', 'my_one_sixth_last');

function my_five_sixth( $atts, $content = null ) {
   return '<div class="five_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('five_sixth', 'my_five_sixth');

function my_five_sixth_last( $atts, $content = null ) {
   return '<div class="five_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('five_sixth_last', 'my_five_sixth_last');


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_title( $atts, $content = null ) {
	return '<div class="shortcode-box-title">' . do_shortcode($content) . '</div><div class="shortcode-box-title-border"></div>';
}
add_shortcode( 'onecommunity-title', 'onecommunity_title' );



function onecommunity_title_2( $atts, $content = null ) {
	return '<div class="shortcode-box-title-2">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'onecommunity-title-2', 'onecommunity_title_2' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
