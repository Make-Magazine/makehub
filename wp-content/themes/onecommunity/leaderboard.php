<?php
/*
Template Name: Leaderboard
*/

get_header(); ?>

<section class="wrapper">

<main id="content">

	<div class="breadcrumbs">
		<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title"><span><?php the_title(); ?></span></h1>

<div class="leaderboard">
<div class="big-leaderboard-row"><h3><?php esc_attr_e('General', 'onecommunity'); ?></h3></div>


<?php
if ( shortcode_exists( 'onecommunity-gamipress-leaderboard-big' ) ) {
echo do_shortcode( '[onecommunity-gamipress-leaderboard-big limit="20" name="points" type="_gamipress_points_points"]' );
}
?>

</div>

</main>

<?php get_sidebar(); ?>

<div id="sidebar-spacer"></div>

</section><!-- .wrapper -->

<?php get_footer(); ?>
