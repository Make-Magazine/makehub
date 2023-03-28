<?php
/*
Template Name: Blog - Masonry (No Sidebar)
*/
?>

<?php get_header(); ?>

<div id="page-header">

<section class="wrapper">

	<div class="breadcrumbs">
	<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title half"><?php the_title(); ?></h1>

	<?php $nonce = wp_create_nonce("onecommunity_blog_1"); ?>

	<div id="item-nav">

	<div id="object-nav" class="item-list-tabs" role="navigation">
			<ul>
				<?php if (function_exists('wp_ulike_get_post_likes')) { ?><li data-posts-type="2" data-tab-page="1"><?php esc_attr_e('Most liked', 'onecommunity'); ?></li><?php } ?>
				<li data-posts-type="3" data-tab-page="1"><?php esc_attr_e('Most commented', 'onecommunity'); ?></li>
				<li class="current" data-posts-type="1" data-tab-page="1"><?php esc_attr_e('Recent', 'onecommunity'); ?></li>
			</ul>
	</div><!-- .item-list-tabs -->

	</div><!-- item-nav -->

	<div class="clear"></div>

</section>

</div>

<section class="wrapper">

<main id="content">

<ul class="blog-1 blog-1-full-width col-3 list-unstyled">

<?php
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=6&post_status=publish'.'&paged=1');
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
?>

</ul>
<div class="clear"></div>

<?php $wp_query = null; $wp_query = $temp;?>

<div class="load-more-container">
<span id="load-more-posts-1" class="load-more show" data-posts-type="1" data-tab-page="1"><?php esc_attr_e('Load More', 'onecommunity'); ?></span>
</div>

</main><!-- content -->

</section><!-- .wrapper -->
<?php get_footer(); ?>