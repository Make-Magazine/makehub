<?php get_header(); ?>

<div id="page-header">

<section class="wrapper">

	<div class="breadcrumbs">
	<a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php esc_attr_e( 'Not Found!', 'onecommunity' ); ?></span>
	</div>

	<h1 class="page-title"><?php esc_attr_e( 'Not Found!', 'onecommunity' ); ?></h1>

</section><!-- .wrapper -->

</div>

<section class="wrapper">

<h2><?php esc_attr_e( 'It looks like nothing was found at this location.', 'onecommunity' ); ?></h2>

<br><br><br><br><br><br><br><br>

</section><!-- .wrapper -->

<?php get_footer(); ?>