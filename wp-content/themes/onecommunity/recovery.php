<?php
/*
Template Name: Password Recovery
*/
?>

<?php get_header(); ?>

<section class="wrapper">

<main id="content">

	<div class="breadcrumbs">
	<a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title"><?php
		$thetitle = get_the_title();
		$getlength = strlen($thetitle);
		$thelength = 35;
		echo substr($thetitle, 0, $thelength);
		if ($getlength > $thelength) echo "...";
	?></h1>

	<?php if ( is_user_logged_in() ) : ?>

		<center><h3><?php esc_attr_e('You are logged in! Redirecting to your profile.', 'onecommunity'); ?></h3></center><br /><br /><br />

		<script type="text/javascript">
		<!--
		window.location = "<?php echo bp_loggedin_user_domain() ?>"
		//-->
		</script>

	<?php else : ?>

			<form method="post" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" id="page-login-form" class="standard-form">
			<p><?php esc_attr_e( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'onecommunity' ); ?></p>
			<div class="username">
				<label for="user_login"><?php esc_attr_e('Username or Email', 'onecommunity' ); ?>: </label>
				<input type="text" name="user_login" class="input" value="" size="20" id="page-user-pass" tabindex="1001" />
			</div>
			<div class="login_fields">
				<?php do_action('login_form', 'resetpass'); ?>
				<input type="submit" name="user-submit" value="<?php esc_attr_e('Reset my password', 'onecommunity' ); ?>" id="wp-submit" tabindex="1002" />
				<?php $reset = ''; $reset = $_GET['reset']; if($reset == true) { echo '<br /><br /><br /><br /><br /><p style="font-size:18px; color:#d70a00;">' . esc_attr_e('A message will be send to your e-mail address.', 'onecommunity' ) . '</p>'; } ?>
				<input type="hidden" name="redirect_to" value="<?php global $wp; echo esc_url( home_url( $wp->request ) ); ?>?reset=true" />
				<input type="hidden" name="user-cookie" value="1" />
			</div>
			</form>
<br /><br /><br /><br />
	<?php endif; ?>




</main><!-- content -->

<div class="clear"></div>

</section><!-- wrapper -->

<?php get_footer(); ?>
