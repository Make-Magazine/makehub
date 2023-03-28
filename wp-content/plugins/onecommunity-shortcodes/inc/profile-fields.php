<div class="bp-profile-fields">

<!-- SINGLE PROFILE FIELD -->
	<?php
	$profile_field = bp_get_member_profile_data( 'field=Biography' );
	if( $profile_field ) { ?>
   	<div class="item biography">
		<?php echo esc_attr( $profile_field ) ?>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->



<!-- SINGLE PROFILE FIELD -->

	<div class="item bg">
		<div class="left"><?php _e( 'Joined', 'onecommunity-shortcodes' ); ?>:</div>
		<div class="right">
		<?php
		global $bp;
		$currentuser = get_userdata( $bp->displayed_user->id );
		$joined = date_i18n("M, Y", strtotime($currentuser ->user_registered));
		echo '' . $joined . '';
		?>
		</div>
	</div><!-- item -->

<!-- SINGLE PROFILE FIELD -->



<!-- SINGLE PROFILE FIELD -->

	<?php
	$profile_field = bp_get_member_profile_data( 'field=Age' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Birthdate', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->


<!-- SINGLE PROFILE FIELD -->


	<?php
	$profile_field = bp_get_member_profile_data( 'field=Marital Status' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Status', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->



<!-- SINGLE PROFILE FIELD -->

	<?php
	$profile_field = bp_get_member_profile_data( 'field=Location' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Location', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->



<!-- SINGLE PROFILE FIELD -->

	<?php
	$profile_field = bp_get_member_profile_data( 'field=Hair Colour' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Hair colour', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->


<!-- SINGLE PROFILE FIELD -->

	<?php
	$profile_field = bp_get_member_profile_data( 'field=Height' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Height', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->


<!-- SINGLE PROFILE FIELD -->

	<?php
	$profile_field = bp_get_member_profile_data( 'field=Lucky Number' );
	if( $profile_field ) { ?>
	<div class="item bg">
		<div class="left"><?php _e( 'Lucky number', 'onecommunity-shortcodes' ); ?>:</div>
   		<div class="right"><?php echo esc_attr( $profile_field ) ?></div>
	</div>
	<?php } ?>

<!-- SINGLE PROFILE FIELD -->

</div><!-- bp-profile-fields -->