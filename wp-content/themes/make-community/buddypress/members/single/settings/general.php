<?php
/**
 * BuddyPress - Members Settings ( General )
 *
 * @since 3.0.0
 * @version 8.0.0
 */

bp_nouveau_member_hook( 'before', 'settings_template' ); 
?>

<h2 class="screen-heading general-settings-screen">
	<?php esc_html_e( 'Email', 'buddypress' ); ?>
</h2>

<p class="info email-pwd-info">
	<?php esc_html_e( 'Update your email.', 'buddypress' ); ?>
</p>

<form action="<?php echo esc_url( bp_displayed_user_domain() . bp_nouveau_get_component_slug( 'settings' ) . '/general' ); ?>" method="post" class="standard-form" id="your-profile">


	<label for="email"><?php esc_html_e( 'Account Email', 'buddypress' ); ?></label>
	<input type="email" name="email" id="email" value="<?php echo esc_attr( bp_get_displayed_user_email() ); ?>" class="settings-input" <?php bp_form_field_attributes( 'email' ); ?>/>
	<input type="password" name="pwd" id="pwd" style="display:none" value="Auth0pass" / >	
	<?php bp_nouveau_submit_button( 'members-general-settings' ); ?>	
</form>

<input id="submit" class="change_password" type="submit" value="Change Password">

<div id="responseText"></div>

<?php
bp_nouveau_member_hook( 'after', 'settings_template' );
