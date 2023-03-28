<?php
/*
Template Name: Login Page
*/
?>

<?php get_header(); ?>

<div class="header-title">
<div class="wrapper">

	<div class="page-title"><span><?php the_title(); ?></span></div>

</div><!-- wrapper -->
</div><!-- #header-title -->

<div class="wrapper">

	<div class="content">

	<?php if ( is_user_logged_in() ) : ?>

		<center><h3><?php esc_attr_e('You are logged in! Redirecting to your profile.', 'onecommunity'); ?></h3></center><br /><br /><br />

		<script type="text/javascript">
		<!--
		window.location = "<?php echo bp_loggedin_user_domain() ?>"
		//-->
		</script>

	<?php else : ?>

        <div class="ajax_login">
            <form id="login" action="login" method="post">
                <p class="status"></p>
                <input id="username" type="text" name="username" placeholder="<?php esc_attr_e('Username','onecommunity') ?>">
                <input id="password" type="password" name="password" placeholder="<?php esc_attr_e('Password','onecommunity') ?>">
                <div class="forgotten_box">
                    <a class="lost" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_attr_e('Lost your password?','onecommunity') ?></a>
                </div>
                <input class="submit_button" type="submit" value="Login" name="submit">
                <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
            </form>
        </div>

	<?php endif; ?>


</div><!-- content -->

</div><!-- wrapper -->

<?php get_footer(); ?>
