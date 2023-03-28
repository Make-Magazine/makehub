<?php
function onecommunity_activity($atts, $content = null) {
	extract(shortcode_atts(array(
		"max" => '12',
		"col" => '3'
	), $atts));
ob_start();
?>

<div id="tabs-activity" class="shortcode-activity col-<?php echo $col ?>">

<ul class="tabs-activity-nav">
<li class="nav-1 current" data-tab-type="1" data-tab-page="1"><?php _e('Groups', 'onecommunity-shortcodes'); ?></li><li class="nav-2" data-tab-type="2" data-tab-page="1"><?php _e('Profiles', 'onecommunity-shortcodes'); ?></li><li class="nav-3" data-tab-type="3" data-tab-page="1"><?php _e('Friends', 'onecommunity-shortcodes'); ?></li><li class="nav-4" data-tab-type="4" data-tab-page="1"><?php _e('Forum topics', 'onecommunity-shortcodes'); ?></li><li class="nav-5" data-tab-type="5" data-tab-page="1"><?php _e('Forum posts', 'onecommunity-shortcodes'); ?></li><li class="nav-6" data-tab-type="6" data-tab-page="1"><?php _e('Blog comments', 'onecommunity-shortcodes'); ?></li>
</ul>

<div class="list-wrap">

<div class="tab-content shortcode-activity-list">

<?php
$transient = get_transient( 'onecommunity_bp_activity' );
if ( false === $transient || !get_theme_mod( 'onecommunity_transient_activity_enable', 0 ) == 1 ) {
ob_start(); ?>

<ul class="tab-content-list">

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&page=1&object=groups&per_page=' . $max . '' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
endif; ?>

</ul>

<?php
$activity = ob_get_clean();
echo $activity;

	if ( get_theme_mod( 'onecommunity_transient_activity_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_bp_activity', $activity, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_activity_expiration', 20) );
	}

} else {
	echo '<!-- Transient onecommunity_bp_activity ('.get_theme_mod( 'onecommunity_transient_activity_expiration', 20).' minutes) -->';
	echo $transient;
}
?>

<div class="clear"></div>

</div>

</div><!-- list-wrap -->
</div><!-- shortcode-activity -->
<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;

}
add_shortcode("onecommunity-activity", "onecommunity_activity");

///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_activity_load() {
$tab_activity_type = esc_html( $_POST['tab_activity_type'] );
$tab_activity_page = esc_html( $_POST['tab_activity_page'] );

if ( $tab_activity_type == 1 ) {

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&per_page=8&page=' . $tab_activity_page . '&object=groups' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
	endif;

} elseif ($tab_activity_type == 2) {

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&per_page=8&page=' . $tab_activity_page . '&object=profile' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
	endif;


} elseif ($tab_activity_type == 3) {


	if ( is_user_logged_in() ) {

	$friends = friends_get_friend_user_ids( bp_loggedin_user_id() );
	$friends_ids = implode( ',', (array) $friends );
	$friends_ids =  $friends_ids;

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&per_page=8&page=' . $tab_activity_page . '&user_id=' . $friends_ids ) ) :
 	   while ( bp_activities() ) : bp_the_activity();
        	include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
	    endwhile;
	endif;

 	} else {
	  echo "<span class='shortcode-activity-info'>";
	   echo  __('You must login first to see activity of your friends.', 'onecommunity-shortcodes');
	  echo "</span>";
	}


} elseif ($tab_activity_type == 4) {

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&per_page=8&page=' . $tab_activity_page . '&action=new_forum_topic' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
	endif;

} elseif ($tab_activity_type == 5) {

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . 'per_page=8&page=' . $tab_activity_page . '&action=new_forum_post' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
	endif;

} elseif ($tab_activity_type == 6) {

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&per_page=8&page=' . $tab_activity_page . '&action=new_blog_comment' ) ) :
    while ( bp_activities() ) : bp_the_activity();
        include( ONECOMMUNITY_SHORTCODES_PATH . 'inc/bp-activity-entry.php' );
    endwhile;
	endif;

}

wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_activity_load', 'onecommunity_activity_load' );
add_action( 'wp_ajax_onecommunity_activity_load', 'onecommunity_activity_load' );

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_bp_groups_listing($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_groups" => '8',
		"col" => '4',
		"title" => '',
		"type" => ''
	), $atts));

if ( function_exists( 'bp_is_active' ) ) {
ob_start();
?>


<div class="shortcode-bp-groups-tabs-container col-<?php echo $col; ?>">

<h4><?php echo $title; ?></h4>

<div class="object-nav-container">
<div id="object-nav">
    <ul class="tabs-nav">
        <li class="nav-four" data-tab="newest" data-tab-per-page="<?php echo $number_of_groups; ?>" data-tab-page="1" data-tab-type="<?php echo $type; ?>"><?php _e('Newest', 'onecommunity-shortcodes'); ?></li>
        <li class="nav-three" data-tab="alphabetical" data-tab-per-page="<?php echo $number_of_groups; ?>" data-tab-page="1" data-tab-type="<?php echo $type; ?>"><?php _e('Alphabetical', 'onecommunity-shortcodes'); ?></li>
        <li class="nav-two" data-tab="active" data-tab-per-page="<?php echo $number_of_groups; ?>" data-tab-page="1" data-tab-type="<?php echo $type; ?>"><?php _e('Active', 'onecommunity-shortcodes'); ?></li>
        <li class="nav-one current" data-tab="popular" data-tab-per-page="<?php echo $number_of_groups; ?>" data-tab-page="1" data-tab-type="<?php echo $type; ?>"><?php _e('Popular', 'onecommunity-shortcodes'); ?></li>
    </ul>
</div>
</div>

<div class="clear"></div>

<div class="list-wrap">

<!-- GROUPS LOOP -->
	<?php 
	$transient = get_transient( 'onecommunity_bp_groups' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_bp_groups_enable', 0 ) == 1 ) {
	ob_start();
		if ( bp_has_groups( 'type=popular&page=1&group_type=' . $type . '&per_page=' . $number_of_groups . '' ) ) : ?>

		<ul>
     		<?php while ( bp_groups() ) : bp_the_group(); ?>
			<li <?php bp_group_class(); ?>>
    			<div class="group-box">
				<div class="group-box-image-container">
					<a class="group-box-image" href="<?php bp_group_permalink() ?>"><?php bp_group_avatar( 'type=full' ) ?></a>
				</div>

				<div class="group-box-bottom">
				<h6 class="group-box-title"><a href="<?php bp_group_permalink() ?>"><?php $grouptitle = bp_get_group_name(); $getlength = strlen($grouptitle); $thelength = 20; echo mb_substr($grouptitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></h6>
				<div class="group-box-details">

					<span class="activity">
							<?php
								printf(
									/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
									esc_html__( 'Active %s', 'buddypress' ),
									sprintf(
										'<span data-livestamp="%1$s">%2$s</span>',
										bp_core_get_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ),
										esc_html( bp_get_group_last_active() )
									)
								);
							?>
					</span>

					<span class="group-box-details-members"><?php bp_group_member_count(); ?></span>
				</div>
				</div><!--group-box-bottom-->
    			</div><!--group-box ends-->
			</li>
      		<?php endwhile; ?>
		</ul>

		<div class="clear"></div>

		<div class="load-more-groups" data-tab="popular" data-tab-per-page="<?php echo $number_of_groups; ?>" data-tab-page="1" data-tab-type="<?php echo $type; ?>"><span><?php _e('Load More', 'onecommunity-shortcodes'); ?></span></div>

		<?php do_action( 'bp_after_groups_loop' ) ?>

		<?php else: ?>

   			<div id="message" class="info" style="width:50%">
        		<p><?php _e( 'There were no groups found.', 'buddypress' ) ?></p>
    		</div>

    		<style type="text/css">
				.load-more-groups { display: none; }
    		</style>

	<?php endif;

	$groups = ob_get_clean();

		if ( get_theme_mod( 'onecommunity_transient_bp_groups_enable', 0 ) == 1 ) {
			set_transient( 'onecommunity_bp_groups', $groups, HOUR_IN_SECONDS * 24 );
		}

		echo $groups;

	} else {
		echo '<!-- Transient onecommunity_bp_groups (24H) -->';
		echo $transient;
	}
?>
<!-- GROUPS LOOP END -->

</div> <!-- List Wrap -->
</div> <!-- shortcode-bp-groups-tabs-container -->

<div class="clear"></div>
<?php } else { echo "Buddypress plugin is inactive"; }

$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-bp-groups-listing", "onecommunity_bp_groups_listing");




function onecommunity_bp_groups_listing_load() {

$groups_type = esc_html( $_POST['groups_type'] );
$per_page = esc_html( $_POST['per_page'] );
$page = esc_html( $_POST['page'] );
$type = esc_html( $_POST['type'] );

if ( bp_has_groups( 'group_type=' . $type . '&type=' . $groups_type . '&page=' . $page . '&per_page=' . $per_page . '' ) ) : ?>

     <?php while ( bp_groups() ) : bp_the_group(); ?>
<li <?php bp_group_class(); ?>>
       <div class="group-box">
	<div class="group-box-image-container">
		<a class="group-box-image" href="<?php bp_group_permalink() ?>"><?php bp_group_avatar( 'type=full' ) ?></a>
	</div>
	<div class="group-box-bottom">
	<h6 class="group-box-title"><a href="<?php bp_group_permalink() ?>"><?php $grouptitle = bp_get_group_name(); $getlength = strlen($grouptitle); $thelength = 20; echo mb_substr($grouptitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></h6>
	<div class="group-box-details"><div class="group-box-details">
		<span class="activity">
							<?php
								printf(
									/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
									esc_html__( 'Active %s', 'buddypress' ),
									sprintf(
										'<span data-livestamp="%1$s">%2$s</span>',
										bp_core_get_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ),
										esc_html( bp_get_group_last_active() )
									)
								);
							?>
	</span>
	<span class="group-box-details-members"><?php bp_group_member_count(); ?></span></div>
	</div>
        </div><!--group-box ends-->
</li>
      <?php endwhile; ?>

    <?php do_action( 'bp_after_groups_loop' ) ?>

<?php else: ?>
    <div id="message" class="info" style="width:50%">
        <p><?php _e( 'There were no groups found.', 'buddypress' ) ?></p>
    </div>
    <style type="text/css">
	.load-more-groups { display: none; }
    </style>
<?php endif;

wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_bp_groups_listing_load', 'onecommunity_bp_groups_listing_load' );
add_action( 'wp_ajax_onecommunity_bp_groups_listing_load', 'onecommunity_bp_groups_listing_load' );

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_bp_groups_listing_2($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_groups" => '6',
		"sort_type" => 'active',		
		"type" => ''
	), $atts));

if ( function_exists( 'bp_is_active' ) ) {
ob_start();
?>

<div class="shortcode-active-groups">

	<?php if ( bp_has_groups('type=' . $sort_type . '&max=' . $number_of_groups . 'group_type=' . $type . '') ) : ?>
 
    <ul>
    <?php while ( bp_groups() ) : bp_the_group(); ?>
 
        <li class="item">
 
 			<div class="left">
                <div class="title"><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></div>
                <span class="group-type"><?php bp_group_type() ?></span>
  			</div>

 			<div class="right">
                <span class="activity">						
                	<?php
							printf(
								/* translators: %s: last activity timestamp (e.g. "Active 1 hour ago") */
								esc_html__( 'Active %s', 'buddypress' ),
								sprintf(
									'<span data-livestamp="%1$s">%2$s</span>',
									bp_core_get_iso8601_date( bp_get_group_last_active( 0, array( 'relative' => false ) ) ),
									esc_html( bp_get_group_last_active() )
								)
							);
						?></span>
                <span class="members-count"><?php bp_group_member_count() ?></span>
            </div>
 
            <div class="clear"></div>

        </li>
 
    <?php endwhile; ?>
    </ul>
  
<?php else: ?>
 
    <div id="message" class="info">
        <p><?php _e( 'There were no groups found.', 'onecommunity-shortcodes' ) ?></p>
    </div>
 
<?php endif; ?>

</div><!-- shortcode-active-groups -->


<div class="clear"></div>
<?php } else { echo "Buddypress plugin is inactive"; }

$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-bp-groups-listing-2", "onecommunity_bp_groups_listing_2");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_sidenav($atts, $content = null) {
	extract(shortcode_atts(array(
	), $atts));
ob_start();
?>

<div id="sidenav">

<a id="sidenav-button" href="javascript: void(0)"></a>

<a id="sidenav-drop-down-menu" href="javascript: void(0)"></a>


<div class="clear"></div>

<div class="sidenav-tabs">

		<ul id="sidenav-ul">
			<span class="group"><?php _e('Members', 'onecommunity-shortcodes'); ?></span>
			<li data-tab="popular" class="tab-popular current"><?php _e('Popular', 'onecommunity-shortcodes'); ?></li>
			<li data-tab="active" class="tab-active"><?php _e('Active', 'onecommunity-shortcodes'); ?></li>
			<?php if ( is_user_logged_in() ) { ?><li data-tab="active_friends" class="tab-active-friends"><?php _e('Friends', 'onecommunity-shortcodes'); ?></li><?php } ?>
			<li data-tab="newest" class="tab-newest"><?php _e('New', 'onecommunity-shortcodes'); ?></li>
			<li data-tab="online" class="tab-online"><?php _e('Online', 'onecommunity-shortcodes'); ?></li>
			<span class="group"><?php _e('Groups', 'onecommunity-shortcodes'); ?></span>
			<?php if ( is_user_logged_in() ) { ?><li data-tab="groups-my-active" class="tab-groups-my-active"><?php _e('My Active', 'onecommunity-shortcodes'); ?></li><?php } ?>
			<li data-tab="groups-popular" class="tab-groups-popular"><?php _e('Popular', 'onecommunity-shortcodes'); ?></li>
			<li data-tab="groups-active" class="tab-groups-active"><?php _e('Active', 'onecommunity-shortcodes'); ?></li>
			<li data-tab="groups-newest" class="tab-groups-newest"><?php _e('Newest', 'onecommunity-shortcodes'); ?></li>
		</ul>

		<div class="tab-content fadein">
		<?php
		$transient = get_transient('onecommunity_sidenav');
		if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidenav_enable', 0 ) == 1 ) {
		ob_start();

		if ( bp_has_members( 'type=popular&max=14' ) ) : ?>
			<?php while ( bp_members() ) : bp_the_member(); ?>
				<a href="<?php bp_member_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" class="item">
					<?php bp_member_avatar('type=thumb&width=60&height=60'); ?>
					<span class="online"></span>
					<span class="name"><?php bp_member_name(); ?></span>
					<span class="activity"><?php bp_member_last_active(); ?></span>
				</a>
			<?php endwhile;
		endif;

		$sidenav_members = ob_get_clean();
		echo $sidenav_members;
		if ( get_theme_mod( 'onecommunity_transient_sidenav_enable', 0 ) == 1 ) {
			set_transient( 'onecommunity_sidenav', $sidenav_members, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidenav_expiration', 1440 ) );
		}
		} else {
			echo '<!-- Transient onecommunity_transient_sidenav ('.get_theme_mod( 'onecommunity_transient_sidenav_expiration', 1440 ).' min) -->';
			echo $transient;
		}
		?>
		</div>

</div><!-- sidenav-tabs -->

<div class="sidenav-bottom">

<?php 
if ( get_theme_mod( 'onecommunity_sidenav_featured_member_enable', true ) == true ) {

	if ( shortcode_exists( 'onecommunity-gamipress-leaderboard' ) ) {
		echo '<div class="best-user">';
		
		if ( false === ( get_transient( 'onecommunity_best_user' ) ) ) {

			$best_user = do_shortcode( '[onecommunity-gamipress-leaderboard limit="1" name="points" layout="sidenav" type="_gamipress_points_points"]' );
			print_r( $best_user );
			set_transient( 'onecommunity_best_user', $best_user, HOUR_IN_SECONDS );
			
		} else {

			echo '<!-- Transient onecommunity_best_user (1h) -->';
			echo get_transient( 'onecommunity_best_user' );

		}

		echo '</div>';
	}

} 
?>

<?php if ( get_theme_mod( 'onecommunity_dark_mode_enable', true ) == true ) { ?>

<div id="dark-mode-container">
<span id="dark-mode"><svg height="448pt" viewBox="-12 0 448 448.04455" width="448pt" xmlns="http://www.w3.org/2000/svg"><title><?php esc_attr_e('Dark mode', 'onecommunity-shortcodes'); ?></title><path d="m224.023438 448.03125c85.714843.902344 164.011718-48.488281 200.117187-126.230469-22.722656 9.914063-47.332031 14.769531-72.117187 14.230469-97.15625-.109375-175.890626-78.84375-176-176 .972656-65.71875 37.234374-125.832031 94.910156-157.351562-15.554688-1.980469-31.230469-2.867188-46.910156-2.648438-123.714844 0-224.0000005 100.289062-224.0000005 224 0 123.714844 100.2851565 224 224.0000005 224zm0 0"/></svg></span>
</div>

<?php } ?>

</div><!-- sidenav-bottom -->

</div><!-- sidenav-members -->

<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;

}
add_shortcode("onecommunity-sidenav", "onecommunity_sidenav");




function onecommunity_sidenav_load() {
$loop_type = esc_html( $_POST['members_loop_type'] );

if ( $loop_type == 'active' ) {

	if ( bp_has_members( 'type=active&max=12' ) ) :
			while ( bp_members() ) : bp_the_member(); ?>
			<a href="<?php bp_member_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" class="item">
			<?php bp_member_avatar('type=thumb&width=60&height=60') ?>
			<span class="online"></span>
			<span class="name"><?php bp_member_name(); ?></span>
			<span class="activity"><?php bp_member_last_active(); ?></span>
			</a>
			<?php endwhile;

endif;

} elseif ($loop_type == 'popular') {

	if ( bp_has_members( 'type=popular&max=12' ) ) :
		while ( bp_members() ) : bp_the_member(); ?>
		<a href="<?php bp_member_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" class="item">
		<?php bp_member_avatar('type=thumb&width=60&height=60') ?>
		<span class="online"></span>
		<span class="name"><?php bp_member_name(); ?></span>
		<span class="activity"><?php bp_member_last_active(); ?></span>
		</a>
		<?php endwhile;

endif;

} elseif ($loop_type == 'newest') {

	if ( bp_has_members( 'type=newest&max=12' ) ) :
			while ( bp_members() ) : bp_the_member(); ?>
			<a href="<?php bp_member_permalink() ?>" class="item">
			<?php bp_member_avatar('type=thumb&width=60&height=60') ?>
			<span class="online"></span>
			<span class="name"><?php bp_member_name(); ?></span>
			<span class="activity"><?php bp_member_last_active(); ?></span>
			</a>
			<?php endwhile;

endif;


} elseif ($loop_type == 'online') {

	if ( bp_has_members( 'type=online&max=12' ) ) :
			while ( bp_members() ) : bp_the_member(); ?>
			<a href="<?php bp_member_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" class="item">
			<?php bp_member_avatar('type=thumb&width=60&height=60') ?>
			<span class="online"></span>
			<span class="name"><?php bp_member_name(); ?></span>
			<span class="activity"><?php bp_member_last_active(); ?></span>
			</a>
			<?php endwhile;

endif;

} elseif ($loop_type == 'active_friends') {

	if ( bp_has_members( 'type=active&per_page=12&user_id=' . bp_loggedin_user_id() ) ) :
			while ( bp_members() ) : bp_the_member(); ?>
			<a href="<?php bp_member_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" class="item">
				<?php bp_member_avatar('type=thumb&width=60&height=60') ?>
				<span class="online"></span>
				<span class="name"><?php bp_member_name(); ?></span>
				<span class="activity"><?php bp_member_last_active(); ?></span>
			</a>
			<?php endwhile;

endif;

} elseif ($loop_type == 'groups-my-active') {

	if ( bp_has_groups( 'type=active&per_page=12&user_id=' . bp_loggedin_user_id() ) ) :
    	while ( bp_groups() ) : bp_the_group(); ?>

			<a href="<?php bp_group_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" title="<?php bp_group_name() ?>" class="item">
				<?php bp_group_avatar('type=thumb&width=60&height=60') ?>
				<span class="name"><?php bp_group_name() ?></span>
				<span class="activity"><?php bp_group_last_active(); ?></span>
			</a>

		<?php endwhile;

endif;

} elseif ($loop_type == 'groups-popular') {

	if ( bp_has_groups( 'type=popular&per_page=12' ) ) :
    	while ( bp_groups() ) : bp_the_group(); ?>

			<a href="<?php bp_group_permalink() ?>" title="<?php echo strip_tags( html_entity_decode( bp_get_member_name() ) ); ?>" title="<?php bp_group_name() ?>" class="item">
				<?php bp_group_avatar('type=thumb&width=60&height=60') ?>
				<span class="name"><?php bp_group_name() ?></span>
				<span class="activity"><?php bp_group_member_count(); ?></span>
			</a>

		<?php endwhile;

endif;

} elseif ($loop_type == 'groups-active') {

	if ( bp_has_groups( 'type=active&per_page=12' ) ) :
    	while ( bp_groups() ) : bp_the_group(); ?>

			<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>" class="item">
				<?php bp_group_avatar('type=thumb&width=60&height=60') ?>
				<span class="name"><?php bp_group_name() ?></span>
				<span class="activity"><?php bp_group_last_active(); ?></span>
			</a>

		<?php endwhile;

endif;

} elseif ($loop_type == 'groups-newest') {

	if ( bp_has_groups( 'type=newest&per_page=12' ) ) :
    	while ( bp_groups() ) : bp_the_group(); ?>

			<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>" class="item">
				<?php bp_group_avatar('type=thumb&width=60&height=60') ?>
				<span class="name"><?php bp_group_name() ?></span>
				<span class="activity"><?php bp_group_last_active(); ?></span>
			</a>

		<?php endwhile;

endif;

}

wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_sidenav_load', 'onecommunity_sidenav_load' );
add_action( 'wp_ajax_onecommunity_sidenav_load', 'onecommunity_sidenav_load' );




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// GROUP GALLERY WIDGET ///////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_group_gallery() {
    register_widget( 'onecommunity_widget_group_gallery' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_group_gallery' );
 
// Creating the widget 
class onecommunity_widget_group_gallery extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'onecommunity_widget_group_gallery', 
 
// Widget name will appear in UI
__('Group Gallery', 'onecommunity-shortcodes'), 
 
// Widget description
array( 'description' => __( 'Displays group`s photos. It can be used on the single group page only.', 'onecommunity-shortcodes' ), ) 
);
}

// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
$number = apply_filters( 'widget_number', $instance['number'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];

if ( ! empty( $title ) )
	echo $args['before_title'] . $title . $args['after_title'];

if ( empty( $number ) )
	$number = 6;

// This is where you run the code and display the output

	if ( bp_group_is_visible() ) :
		$group_id = bp_get_current_group_id();
		echo do_shortcode( '[rtmedia_gallery context="group" per_page="6" context_id="' . $group_id . '"]' );
	endif;

echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
}
else {
	$title = __( 'Photos', 'onecommunity-shortcodes' );
}

if ( isset( $instance[ 'number' ] ) ) {
	$number = $instance[ 'number' ];
}
else {
	$number = 6;
}

// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'onecommunity-shortcodes' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of photos:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_group_gallery ends here


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// USER GALLERY WIDGET ///////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_user_gallery() {
    register_widget( 'onecommunity_widget_user_gallery' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_user_gallery' );
 
// Creating the widget 
class onecommunity_widget_user_gallery extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'onecommunity_widget_user_gallery', 
 
// Widget name will appear in UI
__('User Gallery', 'onecommunity-shortcodes'), 
 
// Widget description
array( 'description' => __( 'Displays user`s photos. It can be displayed on the user profile page only.', 'onecommunity-shortcodes' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
$number = apply_filters( 'widget_number', $instance['number'] );

// before and after widget arguments are defined by themes

$classes = get_body_class();
if (!in_array('album',$classes)) {

	echo $args['before_widget'];

	if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];

	if ( empty( $number ) )
		$number = 6;

		// This is where you run the code and display the output
    
		$user_id = bp_displayed_user_id();
		echo do_shortcode( '[rtmedia_gallery global="false" context="profile" per_page="' . $number . '" media_author="' . $user_id . '"]' );

	echo $args['after_widget'];

}

}
         
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
}
else {
	$title = __( 'Photos', 'onecommunity-shortcodes' );
}

if ( isset( $instance[ 'number' ] ) ) {
	$number = $instance[ 'number' ];
}
else {
	$number = 6;
}

// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'onecommunity-shortcodes' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of photos:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_user_gallery ends here


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// USER GROUPS WIDGET ///////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_user_groups() {
    register_widget( 'onecommunity_widget_user_groups' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_user_groups' );
 
// Creating the widget 
class onecommunity_widget_user_groups extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'onecommunity_widget_user_groups', 
 
// Widget name will appear in UI
__('User Groups', 'onecommunity-shortcodes'), 
 
// Widget description
array( 'description' => __( 'Displays user groups. It can be displayed on the user profile page only.', 'onecommunity-shortcodes' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
$number = apply_filters( 'widget_number', $instance['number'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

if ( empty( $number ) )
$number = 8;

// This is where you run the code and display the output

	$user_id = bp_displayed_user_id();
	if ( bp_has_groups( 'max=' . $number . '&user_id=' . $user_id ) ) : ?>

    <ul class="bp-user-groups">
    <?php while ( bp_groups() ) : bp_the_group(); ?>

        <li>
              <a href="<?php bp_group_permalink() ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ) ?></a>
        </li>

    <?php endwhile; ?>
    </ul>

<?php else: ?>
	<div class="no-results">
        <p><?php _e( 'There were no groups found.', 'onecommunity-shortcodes' ) ?></p>
    </div>
<?php endif;

echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'Groups', 'onecommunity-shortcodes' );
}

if ( isset( $instance[ 'number' ] ) ) {
$number = $instance[ 'number' ];
}
else {
$number = 8;
}

// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'onecommunity-shortcodes' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of groups:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_user_groups ends here


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// USER FRIENDS WIDGET ///////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_user_friends() {
    register_widget( 'onecommunity_widget_user_friends' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_user_friends' );
 
// Creating the widget 
class onecommunity_widget_user_friends extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'onecommunity_widget_user_friends', 
 
// Widget name will appear in UI
__('User Friends', 'onecommunity-shortcodes'), 
 
// Widget description
array( 'description' => __( 'Displays user friends. It can be displayed on the user profile page only.', 'onecommunity-shortcodes' ), ) 
);
}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
$number = apply_filters( 'widget_number', $instance['number'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

if ( empty( $number ) )
$number = 8;

 
// This is where you run the code and display the output

if ( bp_has_members( 'max=' . $number . '&user_id=' . bp_displayed_user_id() ) ) : ?>

  		<ul class="bp-user-friends">

  		<?php while ( bp_members() ) : bp_the_member(); ?>
   			<li>
         		<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
   			</li>
 		<?php endwhile; ?>

 		</ul>

<?php else: ?>

   <div class="no-results">
      <p><?php _e( "Sorry, no members were found.", 'onecommunity-shortcodes' ); ?></p>
   </div>

<?php endif;

echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
}
else {
	$title = __( 'Friends', 'onecommunity-shortcodes' );
}

if ( isset( $instance[ 'number' ] ) ) {
	$number = $instance[ 'number' ];
}
else {
	$number = 8;
}

// Widget admin form
?>

<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of friends:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_user_friends ends here


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// GROUP ADMINS & MODERATORS WIDGET ////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_group_admins() {
    register_widget( 'onecommunity_widget_group_admins' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_group_admins' );
 
// Creating the widget 
class onecommunity_widget_group_admins extends WP_Widget {
 
function __construct() {

	parent::__construct(
 
		// Base ID of your widget
		'onecommunity_widget_group_admins_mods', 
 
		// Widget name will appear in UI
		__('Group Admins & Moderators', 'onecommunity-shortcodes'), 
 
		// Widget description
		array( 'description' => __( 'Displays group admins and moderators.', 'onecommunity-shortcodes' ), ) 
	);

}
 
// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];

// This is where you run the code and display the output
if ( bp_group_is_visible() ) : ?>

	<div class="admins-moderators">

		<div class="admins">

			<div class="mods-title bp-sidebar-details-category"><?php _e( 'Admins', 'onecommunity-shortcodes' ); ?></div>

			<?php bp_group_list_admins();

			do_action( 'bp_after_group_menu_admins' ); ?>
		</div>

	<?php if ( bp_group_has_moderators() ) : ?>

		<div class="moderators">
			<?php do_action( 'bp_before_group_menu_mods' ); ?>

			<div class="mods-title bp-sidebar-details-category"><?php _e( 'Mods' , 'onecommunity-shortcodes' ); ?></div>

			<?php bp_group_list_mods(); ?>

			<?php do_action( 'bp_after_group_menu_mods' ); ?>
		</div>

	<?php endif; ?>
	
	</div>
			
<?php endif;

echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {

// Widget admin form
?>

<p>
<?php _e( 'This widget displays group administrators and moderators.' , 'onecommunity-shortcodes' ); ?>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_group_admins ends here




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// USER PROFILE FIELDS WIDGET ///////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_user_profile_fields() {
    register_widget( 'onecommunity_widget_user_profile_fields' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_user_profile_fields' );
 
// Creating the widget 
class onecommunity_widget_user_profile_fields extends WP_Widget {
 
function __construct() {
	parent::__construct(
 
		// Base ID of your widget
		'onecommunity_widget_user_profile_fields', 
 
		// Widget name will appear in UI
		__('User Profile Fields', 'onecommunity-shortcodes'), 
 
		// Widget description
		array( 'description' => __( 'Displays user`s profile fields.', 'onecommunity-shortcodes' ), ) 

	);
}

// Creating widget front-end
 
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );

// before and after widget arguments are defined by themes
echo $args['before_widget'];

if ( ! empty( $title ) )
	echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output

	require_once dirname( __FILE__ ) . '/profile-fields.php';

echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
}
else {
	$title = __( 'Informations', 'onecommunity-shortcodes' );
}

// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'onecommunity-shortcodes' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
<?php _e( 'It can be customized under Users -> Profile Fields and in the plugins/onecommunity-shortcodes/inc/profile-fields.php file.', 'onecommunity-shortcodes' ); ?>
</p>

<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
}

} // Class onecommunity_widget_user_profile_fields ends here



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// CREATE BP GROUP BLOG POSTS TAXONOMY /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//hook into the init action and call create_bp_group_post_taxonomy when it fires

if ( get_theme_mod( 'onecommunity_bp_groups_blog_enable', false ) == true ) {
	add_action( 'init', 'create_bp_group_post_taxonomy', 0 );
}
 
function create_bp_group_post_taxonomy() {
 
// Labels part for the GUI
 
  $labels = array(
    'name' => _x( 'BP Group Post', 'onecommunity_shortcodes' ),
    'singular_name' => _x( 'BP Group Post', 'onecommunity_shortcodes' ),
    'search_items' =>  __( 'Search BP Group Posts', 'onecommunity_shortcodes' ),
    'popular_items' => __( 'Popular BP Group Posts', 'onecommunity_shortcodes' ),
    'all_items' => __( 'All BP Group Posts', 'onecommunity_shortcodes' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit BP Group Post', 'onecommunity_shortcodes' ), 
    'update_item' => __( 'Update BP Group Post', 'onecommunity_shortcodes' ),
    'add_new_item' => __( 'Add New BP Group', 'onecommunity_shortcodes' ),
    'new_item_name' => __( 'New BP Group Name', 'onecommunity_shortcodes' ),
    'separate_items_with_commas' => __( 'Separate BP Groups with commas', 'onecommunity_shortcodes' ),
    'add_or_remove_items' => __( 'Add or remove BP Group', 'onecommunity_shortcodes' ),
    'choose_from_most_used' => __( 'Choose from the most used BP Group Posts', 'onecommunity_shortcodes' ),
    'menu_name' => __( 'BP Group Posts', 'onecommunity_shortcodes' ),
  ); 
 
// Now register the non-hierarchical taxonomy like tag
 
  register_taxonomy('bp-group-post','post',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'bp-group-post' ),
  ));
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// BP GROUP BLOG POSTS WIDGET /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_bp_group_blog_posts() {
    register_widget( 'onecommunity_widget_bp_group_blog_posts' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_bp_group_blog_posts' );
 
// Creating the widget 
class onecommunity_widget_bp_group_blog_posts extends WP_Widget {
 
	function __construct() {
	parent::__construct(
 
	// Base ID of your widget
	'onecommunity_widget_bp_group_blog_posts', 
 
	// Widget name will appear in UI
	__('Group Blog Posts', 'onecommunity-shortcodes'), 
 
		// Widget description
	array( 'description' => __( 'Displays group blog posts. It can be displayed on the group page only.', 'onecommunity-shortcodes' ), )
	);
	}
 
	// Creating widget front-end
 
	public function widget( $args, $instance ) {
	$title = apply_filters( 'widget_title', $instance['title'] );
	$number = apply_filters( 'widget_number', $instance['number'] );

	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )
		$name = bp_get_group_name();
		echo $args['before_title'] . sprintf( __( '' . $title . '', 'onecommunity-shortcodes'), $name) . $args['after_title'];

	if ( empty( $number ) )
		$number = 3;

	// This is where you run the code and display the output

	echo '<ul class="shortcode-small-recent-posts shortcode-general-list col-1">';

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('bp-group-post=' . bp_get_group_id() . '&posts_per_page=' . $number . ''.'&paged=1');
	if ($wp_query->have_posts()) {
		while ($wp_query->have_posts()) : $wp_query->the_post();

			include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

		endwhile; // end of loop

	} else { 
	echo '<p>' . __( 'No posts found.', 'onecommunity-shortcodes') . '</p>';
	}

	echo '</ul>';

	wp_reset_postdata();

	echo $args['after_widget'];

	}
         
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( '%s Posts', 'onecommunity-shortcodes');
		}

		if ( isset( $instance[ 'number' ] ) ) {
			$number = $instance[ 'number' ];
		}
		else { 
			$number = 3;
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
		</p>

		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
		return $instance;
	}

} // Class onecommunity_widget_bp_group_blog_posts ends here



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// BP USER BLOG POSTS WIDGET //////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Register and load the widget
function onecommunity_load_widget_bp_user_blog_posts() {
    register_widget( 'onecommunity_widget_bp_user_blog_posts' );
}
add_action( 'widgets_init', 'onecommunity_load_widget_bp_user_blog_posts' );
 
// Creating the widget 
class onecommunity_widget_bp_user_blog_posts extends WP_Widget {
 
	function __construct() {
	parent::__construct(
 
	// Base ID of your widget
	'onecommunity_widget_bp_user_blog_posts', 
 
	// Widget name will appear in UI
	__('User Blog Posts', 'onecommunity-shortcodes'),
 
		// Widget description
	array( 'description' => __( 'Displays user blog posts. It can be displayed on the user profile only.', 'onecommunity-shortcodes' ), )
	);
	}
 
	// Creating widget front-end
 
	public function widget( $args, $instance ) {
	$title = apply_filters( 'widget_title', $instance['title'] );
	$number = apply_filters( 'widget_number', $instance['number'] );

	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )
		$name = bp_core_get_user_displayname( bp_displayed_user_id() );
		echo $args['before_title'] . sprintf( __( '' . $title . '', 'onecommunity-shortcodes'), $name) . $args['after_title'];

	if ( empty( $number ) )
		$number = 3;


	// This is where you run the code and display the output

	echo '<ul class="shortcode-small-recent-posts shortcode-general-list col-1">';

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('author=' . bp_displayed_user_id() . '&posts_per_page=' . $number . ''.'&paged='.$paged);
	if ($wp_query->have_posts()) {
		while ($wp_query->have_posts()) : $wp_query->the_post();

			include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

		endwhile; // end of loop

	} else { 
	echo '<p>' . __( 'No posts found.', 'onecommunity-shortcodes') . '</p>';
	}

	echo '</ul>';

	wp_reset_postdata();

	echo $args['after_widget'];

	}
         
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( '%s Posts', 'onecommunity-shortcodes');
		}

		if ( isset( $instance[ 'number' ] ) ) {
			$number = $instance[ 'number' ];
		}
		else { 
			$number = 3;
		}

		// Widget admin form
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts:', 'onecommunity-shortcodes' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $number ); ?>" style="width: 30%"></label>
		</p>

		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
		return $instance;
	}

} // Class onecommunity_widget_bp_user_blog_posts ends here


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////// CREATE BP GROUP BLOG POSTS TAB ///////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ( get_theme_mod( 'onecommunity_bp_groups_blog_enable', false ) == true ) {

function dd_setup_group_blog_nav(){
    global $bp;
    /* Add some group subnav items */
    $user_access = false;
    $group_link = '';
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ) {
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        $user_access = $bp->groups->current_group->user_has_access;
        bp_core_new_subnav_item( array(
            'name' => __('Blog', 'onecommunity-shortcodes'),
            'slug' => __('blog', 'onecommunity-shortcodes'),
            'parent_url' => $group_link,
            'parent_slug' => $bp->groups->current_group->slug,
            'screen_function' => 'bp_group_custom',
            'position' => get_theme_mod( 'onecommunity_bp_groups_blog_position_hierarchy', 10 ),
            'user_has_access' => $user_access,
            'item_css_id' => 'blog'
        ));
    }
}
add_action( 'bp_init', 'dd_setup_group_blog_nav' );

function bp_group_custom() {
    add_action('bp_template_title', 'dd_bp_group_blog_show_screen_title');
    add_action('bp_template_content', 'dd_bp_group_blog_show_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }

}

function dd_bp_group_blog_show_screen_title() {
    _e( 'Blog', 'onecommunity-shortcodes');
}

function dd_bp_group_blog_show_screen_content() {


echo '<div id="bp-group-blog">';

	$name = bp_get_group_name();
	echo '<h4 class="bp-screen-title">'.sprintf( __( '%s Posts', 'onecommunity-shortcodes'), $name).'</h4>';

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('bp-group-post=' . bp_get_group_id() . '&posts_per_page=10'.'&paged='.$paged);
	if ($wp_query->have_posts()) {
	echo '<ul class="shortcode-small-recent-posts shortcode-general-list col-1">';
	while ($wp_query->have_posts()) : $wp_query->the_post();

		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

	endwhile; // end of loop

	echo '</ul>';

	echo '<div class="load-more-container">';
	echo '<span id="bp-group-blog-load-more" class="load-more show" data-tab-page="1" data-group-id="' . bp_get_group_id() . '">'.__( "Load More", "onecommunity-shortcodes").'</span>';
	echo '</div>';

	} else { 
		echo '<p>' . __( 'No posts found.', 'onecommunity-shortcodes') . '</p>';
	}


echo '</div>';

}

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_bp_group_blog_more() {
$page = esc_html( $_POST['page'] );
$group_id = esc_html( $_POST['group_id'] );

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('bp-group-post=' . $group_id . '&posts_per_page=10'.'&paged='.$page);
	if ($wp_query->have_posts()) {
	while ($wp_query->have_posts()) : $wp_query->the_post();

		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

	endwhile; // end of loop
	}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_bp_group_blog_more', 'onecommunity_bp_group_blog_more' );
add_action( 'wp_ajax_onecommunity_bp_group_blog_more', 'onecommunity_bp_group_blog_more' );


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////// CREATE USER BLOG POSTS TAB /////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ( get_theme_mod( 'onecommunity_bp_user_blog_enable', false ) == true ) {

function dd_setup_user_blog_nav(){
    global $bp; 

    bp_core_new_nav_item( array(
        'name' => __( 'Blog', 'onecommunity-shortcodes'),
        'slug' => __('blog', 'onecommunity-shortcodes'),
        'parent_url' => $bp->profile->slug,
        'parent_slug' => $bp->profile->slug,
        'screen_function' => 'bp_user_blog',
        'position' => get_theme_mod( 'onecommunity_bp_user_blog_position_hierarchy', 10 ),
        'user_has_access' => 1, 
        'item_css_id' => 'blog'
    ));
   
}
add_action( 'bp_setup_nav', 'dd_setup_user_blog_nav' );

function bp_user_blog() {

    add_action('bp_template_content', 'dd_bp_user_blog_show_screen_content');

    $templates = array('members/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'members/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }

}


function dd_bp_user_blog_show_screen_content() {

echo '<div id="bp-user-blog">';

	$name = bp_core_get_user_displayname( bp_displayed_user_id() );
	echo '<h4 class="bp-screen-title">'.sprintf( __( "%s's Posts", "onecommunity-shortcodes"), $name).'</h4>';

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('author=' . bp_displayed_user_id() . '&posts_per_page=10'.'&paged='.$paged);
	if ($wp_query->have_posts()) {
	echo '<ul class="shortcode-small-recent-posts shortcode-general-list col-1">';
	while ($wp_query->have_posts()) : $wp_query->the_post();

		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

	endwhile; // end of loop

	echo '</ul>';

	echo '<div class="load-more-container">';
	echo '<span id="bp-user-blog-load-more" class="load-more show" data-tab-page="1" data-user-id="' . bp_displayed_user_id() . '">'.__( "Load More", "onecommunity-shortcodes").'</span>';
	echo '</div>';

	} else { 
		echo '<p>' . __( 'No posts found.', 'onecommunity-shortcodes') . '</p>';
	}

	echo '</ul>';

echo '</div>';

}

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_bp_user_blog_more() {
$page = esc_html( $_POST['page'] );
$user_id = esc_html( $_POST['user_id'] );

	$wp_query = '';
	$paged = '';
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('author=' . $user_id . '&posts_per_page=10'.'&paged='.$page);
	if ($wp_query->have_posts()) {
	while ($wp_query->have_posts()) : $wp_query->the_post();

		include ONECOMMUNITY_SHORTCODES_PATH . '/inc/template-parts/small-recent-post.php';

	endwhile; // end of loop
	}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_bp_user_blog_more', 'onecommunity_bp_user_blog_more' );
add_action( 'wp_ajax_onecommunity_bp_user_blog_more', 'onecommunity_bp_user_blog_more' );


/* Disable lazy loading for buddypress activity */
function dd_bp_lazy_loading_attribute_remove_actions() {
	remove_filter( 'bp_get_activity_content_body', 'bp_core_add_loading_lazy_attribute' );
	remove_filter( 'bp_activity_comment_content',  'bp_core_add_loading_lazy_attribute' );
}
add_action( 'bp_init', 'dd_bp_lazy_loading_attribute_remove_actions' );


/* Add livestamp on all pages */
function dd_bp_core_add_livestamp() {
	bp_core_enqueue_livestamp();
}
add_action( 'bp_enqueue_scripts', 'dd_bp_core_add_livestamp' );