<?php
/**
 * Template Name: Youzer Profile Template
 */

$uid = bp_displayed_user_id();
$user_meta = get_user_meta($uid);
$user_levels = explode(",",$user_meta['ihc_user_levels'][0]);
$member_type = bp_get_member_type($uid);
$expired_user = false;

foreach ($user_levels as &$user_level) {
	$time_data = ihc_get_start_expire_date_for_user_level($uid, $user_level);
	if(strtotime($time_data['expire_time']) > time()) {
		$expired_user = false;
		break;
	} else {
		$expired_user = true;
	}
}

if ( !empty($user_meta['ihc_user_levels']) && $expired_user != true || $member_type == "maker_space"  || $member_type == "makers" || user_can( $uid, 'administrator' )) {
	
?>

<div id="youzer">

<?php do_action( 'youzer_profile_before_profile' ); ?>
	
<div id="<?php echo apply_filters( 'yz_profile_template_id', 'yz-bp' ); ?>" class="youzer noLightbox yz-page yz-profile <?php echo yz_get_profile_class(); ?>">
	
	<?php do_action( 'youzer_profile_before_content' ); ?>

	<div class="yz-content">

		<?php do_action( 'youzer_profile_before_header' ); ?>

		<header id="yz-profile-header" class="<?php echo yz_headers()->get_class( 'user' ); ?>" <?php echo yz_widgets()->get_loading_effect( yz_option( 'yz_hdr_load_effect', 'fadeIn' ) ); ?>>

			<?php do_action( 'youzer_profile_header' ); ?>
			<?php // if user is owner of this profile, give them a link to change their background image
			   $user = wp_get_current_user();
				$profile_name = bp_core_get_username($user->ID);
			   if($user->ID == bp_displayed_user_id()){
					echo('<div class="edit-btn-wrap">');
						echo('<a id="edit-cover-btn" class="btn universal-btn" href="/members/'.$profile_name.'/profile/change-cover-image/">Change Profile Cover</a>');
						echo('<a id="edit-photo-btn" class="btn universal-btn" href="/members/'.$profile_name.'/profile/change-avatar">Change Profile Photo</a>');
					echo('</div>');
				}
			?>
		</header>

		<div class="yz-profile-content">

			<div class="yz-inner-content">

				<?php do_action( 'youzer_profile_navbar' ); ?>

				<main class="yz-page-main-content">

					<?php

					/**
					 * Fires before the display of member home content.
					 *
					 * @since 1.2.0
					 */
					do_action( 'bp_before_member_home_content' ); ?>

					<?php do_action( 'yz_profile_main_content' ); ?>

					<?php 

						/**
						 * Fires after the display of member home content.
						 *
						 * @since 1.2.0
						 */
						do_action( 'bp_after_member_home_content' );
						
					?>

				</main>

			</div>

		</div>

		<?php do_action( 'youzer_profile_sidebar' ); ?>

	</div>

	<?php do_action( 'youzer_profile_after_content' ); ?>

</div>

<?php do_action( 'youzer_profile_after_profile' ); ?>

</div>

<?php
																															
} else { // SHOW THE 404 PAGE ?>
<div class="main-content container" style="margin-top: 0px;">
	<div id="youzer">
		<div id="yz-bp" class="youzer noLightbox yz-page yz-profile yz-horizontal-layout yz-wild-content yz-tabs-list-gradient yz-wg-border-radius yz-page-btns-border-flat  yz-404-profile">
			<div class="yz-content">
				<header id="yz-profile-header" class="yz-profile-header yz-hdr-v1 yz-hdr-v8 yz-header-overlay yz-header-pattern yz_effect" data-effect="fadeIn">
					<div class="yz-header-cover" style="background-image:url( http://makehub.local/wp-content/plugins/youzer/includes/public/assets/images/geopattern.png ); background-size: auto;">
						<div class="yz-cover-content">
							<div class="yz-inner-content">
								<div class="yz-profile-photo yz-photo-circle yz-photo-border yz-profile-photo-effect">
									<div class="yz-profile-img"><img src="http://makehub.local/wp-content/themes/make-co/images/default-avatars/custom-avatar0.jpg" alt="User Avatar"></div>
								</div>					
								<div class="yz-head-content">
									<div class="yz-name">
										<h2>404 Profile<span class="yz-user-status yz-user-offline">offline</span></h2>
									</div>
									<div class="yz-usermeta">
										<ul>
											<li><i class="fas fa-map-marker-alt"></i><span>404 city</span></li>
											<li><i class="fas fa-link"></i><span>www.page.404</span></li>
										</ul>
									</div>											
								</div>
							</div>
						</div>
					</div>
					<div class="yz-header-content">
						<div class="yz-header-head"></div>
					</div>
				</header>
				<div class="yz-profile-content">
					<div class="yz-inner-content">
						<main class="yz-page-main-content">
							<div id="template-notices" role="alert" aria-atomic="true"></div>
							<div class="yz-box-404">
								<h2>404</h2>
								<p>We're sorry, the profile you are looking for can't be found.</p>
								<a class="yz-box-button" href="http://makehub.local">Go Back Home</a>
							</div>
						</main>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>