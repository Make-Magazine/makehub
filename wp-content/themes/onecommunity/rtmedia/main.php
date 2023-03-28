<?php
/* * **************************************
 * Main.php
 *
 * The main template file, that loads the header, footer and sidebar
 * apart from loading the appropriate rtMedia template
 * *************************************** */
// by default it is not an ajax request
global $rt_ajax_request;
$rt_ajax_request = false;

//todo sanitize and fix $_SERVER variable usage
// check if it is an ajax request

$_rt_ajax_request = rtm_get_server_var( 'HTTP_X_REQUESTED_WITH', 'FILTER_SANITIZE_STRING' );
if ( 'xmlhttprequest' === strtolower( $_rt_ajax_request ) ) {
	$rt_ajax_request = true;
}
?>

<div id="buddypress" class="buddypress-wrap bp-dir-hori-nav">
<div id="buddypress-rtmedia">

<section class="wrapper">

<?php
//if it's not an ajax request, load headers
if ( ! $rt_ajax_request ) {
	// if this is a BuddyPress page, set template type to
	// buddypress to load appropriate headers
	if ( class_exists ( 'BuddyPress' ) && ! bp_is_blog_page () && apply_filters( 'rtm_main_template_buddypress_enable', true ) ) {
		$template_type = 'buddypress' ;
	} else {
		$template_type = '' ;
	}
	//get_header( $template_type );

	if ( $template_type == 'buddypress' ) {
		//load buddypress markup
		if ( bp_displayed_user_id () ) {

			//if it is a buddypress member profile
?>


<main id="content">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <a href="<?php echo home_url(); ?>/members"><?php esc_attr_e('Members', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<?php bp_nouveau_member_hook( 'before', 'home_content' ); ?>

	<div id="item-header" role="complementary" data-bp-item-id="<?php echo esc_attr( bp_displayed_user_id() ); ?>" data-bp-item-component="members" class="users-header single-headers">

		<?php bp_nouveau_member_header_template_part(); ?>

	</div><!-- #item-header -->

	<div class="clear"></div>

	<div class="bp-wrap">
		<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

				<?php bp_get_template_part( 'members/single/parts/item-nav' ); ?>

		<?php endif; ?>

		<div id="item-body" class="item-body">

		<nav id="subnav" class="bp-navs bp-subnavs no-ajax user-subnav user-rtmedia">
			<ul class="subnav">
				<?php rtmedia_sub_nav(); ?>
			</ul>
		</nav>

			<?php rtmedia_load_template(); ?>

		</div><!-- #item-body -->
	</div><!-- // .bp-wrap -->

	<?php bp_nouveau_member_hook( 'after', 'home_content' ); ?>

</main><!-- content -->

<?php bp_get_template_part( 'members/single/profile-sidebar' ); ?>


	<?php } else if ( bp_is_group () ) {

	//not a member profile, but a group
	?>

<main id="content">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <a href="<?php echo home_url(); ?>/groups"><?php esc_attr_e('Groups', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<?php if (bp_has_groups()) : while (bp_groups()) : bp_the_group(); ?>

		<?php bp_nouveau_group_hook( 'before', 'home_content' ); ?>

		<div id="item-header" role="complementary" data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups" class="groups-header single-headers">

			<?php bp_nouveau_group_header_template_part(); ?>

		</div><!-- #item-header -->

		<div class="bp-wrap">

			<?php if ( ! bp_nouveau_is_object_nav_in_sidebar() ) : ?>

				<?php bp_get_template_part( 'groups/single/parts/item-nav' ); ?>

			<?php endif; ?>

			<div id="item-body" class="item-body">

			<nav id="subnav" class="bp-navs bp-subnavs no-ajax user-rtmedia">
			<ul class="subnav">
				<?php rtmedia_sub_nav(); ?>
			</ul>
			</nav>

				<?php rtmedia_load_template(); ?>

			</div><!-- #item-body -->

		</div><!-- // .bp-wrap -->

		<?php bp_nouveau_group_hook( 'after', 'home_content' ); ?>

</main><!-- content -->


<?php bp_get_template_part( 'groups/single/group-sidebar' ); ?>

	<?php endwhile; endif; // group/profile if/else
	}
	} else { ////if BuddyPress



	}
} // if ajax

        // include the right rtMedia template
        rtmedia_load_template();

        if ( ! $rt_ajax_request ) {
			if ( function_exists ( "bp_displayed_user_id" ) && $template_type == 'buddypress' && (bp_displayed_user_id () || bp_is_group ()) ) {
				if ( bp_is_group () ) {
					do_action( 'bp_after_group_media' );
					do_action( 'bp_after_group_body' );
				}
				if ( bp_displayed_user_id () ) {
					do_action( 'bp_after_member_media' );
					do_action( 'bp_after_member_body' );
				}
			}
			?>

			<?php
			if ( function_exists ( "bp_displayed_user_id" ) && $template_type == 'buddypress' && (bp_displayed_user_id () || bp_is_group ()) ) {
				if ( bp_is_group() ) {
					do_action( 'bp_after_group_home_content' );
				}
				if ( bp_displayed_user_id() ) {
					do_action( 'bp_after_member_home_content' );
				}
			}
        }
        //close all markup
        ?>

</section><!-- wrapper -->
</div><!--#buddypress-rtmedia-->
</div><!-- buddypress -->
        <?php
        //get_sidebar($template_type);
        //get_footer($template_type);
        // if ajax

