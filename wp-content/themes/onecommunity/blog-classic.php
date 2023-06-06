<?php
/*
Template Name: Blog Classic
*/
?>

<?php get_header(); ?>

<section class="wrapper">

<main id="content">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title half"><?php the_title(); ?></h1>

	<?php $nonce = wp_create_nonce("onecommunity_blog_classic"); ?>


	<div id="item-nav">

	<div id="object-nav" class="item-list-tabs" role="navigation">
			<ul>
				<li data-posts-type="3" data-tab-page="1"><?php esc_attr_e('Most commented', 'onecommunity'); ?></li>
				<?php if (function_exists('wp_ulike_get_post_likes')) { ?><li data-posts-type="2" data-tab-page="1"><?php esc_attr_e('Most liked', 'onecommunity'); ?></li><?php } ?>
				<li class="current" data-posts-type="1" data-tab-page="1"><?php esc_attr_e('Recent', 'onecommunity'); ?></li>
			</ul>
	</div><!-- .item-list-tabs -->

	</div><!-- item-nav -->


	<div class="clear"></div>

<div class="blog-classic">

<?php
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=3&post_status=publish'.'&paged='.$paged);
while ($wp_query->have_posts()) : $wp_query->the_post();

	get_template_part( 'template-parts/blog', 'classic' );

endwhile; ?>
<div class="clear"></div>

<?php $wp_query = null; $wp_query = $temp;?>

</div><!-- blog-classic -->

<div class="load-more-container">
<span id="load-more-posts-classic" class="load-more show" data-posts-type="1" data-tab-page="1"><?php esc_attr_e('Load More', 'onecommunity'); ?></span>
</div>

</main><!-- content -->

<div id="sidebar-spacer"></div>

<aside id="sidebar" class="sidebar">

	<?php 
	$transient = get_transient( 'onecommunity_sidebar_blog' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidebar_blog_enable', 0 ) == 1 ) {
	ob_start();

	if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-blog')) : endif;

	$sidebar = ob_get_clean();
	print_r( $sidebar );
	
	if ( get_theme_mod( 'onecommunity_transient_sidebar_blog_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_blog', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_blog_expiration', 20 ) );
	}

	} else {
		echo '<!-- Transient onecommunity_sidebar_blog ('.get_theme_mod( 'onecommunity_transient_sidebar_blog_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside><!--sidebar ends-->
</section><!-- wrapper -->
<?php get_footer(); ?>