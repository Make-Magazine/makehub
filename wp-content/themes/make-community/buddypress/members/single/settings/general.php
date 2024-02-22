<?php
/**
 * BuddyPress - Members Settings ( General )
 *
 * @since 3.0.0
 * @version 8.0.0
 */

bp_nouveau_member_hook( 'before', 'settings_template' ); 
?>
<div class="settings-wrapper">
	<h2 class="screen-heading email-settings-screen">
		<?php _e( 'Change Password', 'buddypress' ); ?>
	</h2>
	<p>By clicking the below button, a link will be emailed to you to reset your password</p>
	<form action="<?php echo esc_url( bp_displayed_user_domain() . bp_nouveau_get_component_slug( 'settings' ) . '/general' ); ?>" method="post" class="standard-form" id="your-profile">
		<input type="hidden" name="email" id="email" value="<?php echo esc_attr( bp_get_displayed_user_email() ); ?>" class="settings-input" <?php bp_form_field_attributes( 'email' ); ?>/>
		<input type="password" name="pwd" id="pwd" style="display:none" value="Auth0pass" / >	
		<?php bp_nouveau_submit_button( 'members-general-settings' ); ?>	
	</form>

	<input id="submit" class="change_password" type="submit" value="Change Password">

	<div id="responseText"></div>
</div>

<?php
bp_nouveau_member_hook( 'after', 'settings_template' );
