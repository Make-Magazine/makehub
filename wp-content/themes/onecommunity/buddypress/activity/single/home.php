<?php
/**
 * BuddyPress - Home
 *
 * @version 3.0.0
 */

?>

<section class="wrapper">

	<div class="breadcrumbs">
	<a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>


	<?php bp_nouveau_template_notices(); ?>

	<div class="activity" data-bp-single="<?php echo esc_attr( bp_current_action() ); ?>">

		<ul id="activity-stream" class="activity-list item-list bp-list" data-bp-list="activity">

			<li id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'single-activity-loading' ); ?></li>

		</ul>

	</div>

</section><!-- .wrapper -->