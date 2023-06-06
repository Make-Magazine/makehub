<?php
function onecommunity_gamipress_leaderboard($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '10',
		"type" => '_gamipress_points_points',
		"page" => '1',
		"layout" => 'small',
		"name" => 'points'
	), $atts));

ob_start();

global $wpdb;

$query = "
    SELECT user_id, meta_value
    FROM {$wpdb->prefix}usermeta
    WHERE meta_key = '$type'
    ORDER BY CAST(meta_value AS DECIMAL) DESC
    LIMIT $limit
    ";

$gamipress_points = $wpdb->get_results($query, OBJECT);

echo '<div class="' . $layout . '-leaderboard">';

$i = 0;
foreach ( $gamipress_points as $user_IDs )
{
	$i++;
	echo '<div class="' . $layout . '-leaderboard-row">';
	echo '<div class="leaderboard-position">' . $i . '.</div>';
	$user_info = get_userdata($user_IDs->user_id);
	echo '<div class="leaderboard-user"><a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="avatar">' . get_avatar( $user_IDs->user_id, 50, '', 'avatar', array('class' => 'leaderboard-avatar') ) . '</a></div>';
	echo '<a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="name">' . $user_nicename = $user_info->display_name . '</a>';
	echo '<div class="leaderboard-points">' . $user_IDs->meta_value . ' ' . $name . '</div>';

	echo '</div>';
}

echo '</div><!-- small-leaderboard -->';

$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-gamipress-leaderboard", "onecommunity_gamipress_leaderboard");


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_gamipress_leaderboard_small($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '10',
		"type" => '_gamipress_points_points',
		"page" => '1',
		"name" => 'points'
	), $atts));

ob_start();

$transient = get_transient( 'onecommunity_leaderboard_small' );
if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_leaderboard_small_enable', 0 ) == 1 ) {

	global $wpdb;

	$query = "
    	SELECT user_id, meta_value
    	FROM {$wpdb->prefix}usermeta
    	WHERE meta_key = '$type'
    	ORDER BY CAST(meta_value AS DECIMAL) DESC
    	LIMIT $limit
    	";

	$gamipress_points = $wpdb->get_results($query, OBJECT);

	echo '<div class="small-leaderboard">';

	$i = 0;
	$leaderboard = '';
	foreach ( $gamipress_points as $user_IDs )
	{
		$i++;
		$leaderboard .= '<div class="small-leaderboard-row">';
		$leaderboard .= '<div class="leaderboard-position">' . $i . '.</div>';
		$user_info = get_userdata($user_IDs->user_id);
		$leaderboard .= '<div class="leaderboard-user"><a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="avatar">' . get_avatar( $user_IDs->user_id, 50 ) . '</a></div>';
		$leaderboard .= '<a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="name">' . $user_nicename = $user_info->display_name . '</a>';
		$leaderboard .= '<div class="leaderboard-points">' . $user_IDs->meta_value . ' ' . $name . '</div>';

		$leaderboard .= '</div>';
	}

	echo $leaderboard;

	echo '</div>';

	if ( get_theme_mod( 'onecommunity_transient_leaderboard_small_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_leaderboard_small', $leaderboard, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_leaderboard_small_expiration', 1440 ) );
	}

} else {

	echo '<div class="small-leaderboard">';
	echo '<!-- Transient onecommunity_leaderboard_small ('.get_theme_mod( 'onecommunity_transient_leaderboard_small_expiration', 1440 ).' minutes) -->';
	echo $transient;
	echo '</div>';

}

$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-gamipress-leaderboard-small", "onecommunity_gamipress_leaderboard_small");


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_gamipress_leaderboard_big($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '20',
		"type" => '_gamipress_points_points',
		"page" => '1',
		"name" => 'points'
	), $atts));

ob_start();

$transient = get_transient( 'onecommunity_leaderboard_big' );
if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_leaderboard_big_enable', 0 ) == 1 ) {

	global $wpdb;

	$query = "
    	SELECT user_id, meta_value
    	FROM {$wpdb->prefix}usermeta
    	WHERE meta_key = '$type'
    	ORDER BY CAST(meta_value AS DECIMAL) DESC
    	LIMIT $limit
    	";

	$gamipress_points = $wpdb->get_results($query, OBJECT);

	echo '<div class="big-leaderboard">';

	$i = 0;
	$leaderboard = '';
	foreach ( $gamipress_points as $user_IDs )
	{
		$i++;
		$leaderboard .= '<div class="big-leaderboard-row">';
		$leaderboard .= '<div class="leaderboard-position">' . $i . '.</div>';
		$user_info = get_userdata($user_IDs->user_id);
		$leaderboard .= '<div class="leaderboard-user"><a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="avatar">' . get_avatar( $user_IDs->user_id, 50 ) . '</a></div>';
		$leaderboard .= '<a href="' . esc_url( home_url( '/' ) ) . 'members/' . $user_info->user_nicename . '" class="name">' . $user_nicename = $user_info->display_name . '</a>';
		$leaderboard .= '<div class="leaderboard-points">' . $user_IDs->meta_value . ' ' . $name . '</div>';

		$leaderboard .= '</div>';
	}

	echo $leaderboard;

	echo '</div>';

	if ( get_theme_mod( 'onecommunity_transient_leaderboard_big_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_leaderboard_big', $leaderboard, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_leaderboard_big_expiration', 1440 ) );
	}

} else {

	echo '<div class="big-leaderboard">';
	echo '<!-- Transient onecommunity_leaderboard_big ('.get_theme_mod( 'onecommunity_transient_leaderboard_big_expiration', 1440 ).' min) -->';
	echo $transient;
	echo '</div>';

}

$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-gamipress-leaderboard-big", "onecommunity_gamipress_leaderboard_big");