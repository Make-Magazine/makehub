<?php get_header(); ?>

<section class="wrapper">

<main id="content" class="<?php if( !is_active_sidebar('sidebar-pages') ) { ?>no-sidebar<?php } ?>">

	<div class="breadcrumbs">

	    <?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php esc_url($home); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>

	</div>

	<h1 class="page-title"><span><?php printf( esc_attr__( 'Search Results for: %s', 'onecommunity' ), '  ' . esc_html( get_search_query() ) ); ?></span></h1>

	<div class="clear"></div>

	<div class="blog-classic">

	<?php
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	$wp_query->query('posts_per_page=6'.'&paged='.$paged);
	while ($wp_query->have_posts()) : $wp_query->the_post();

		get_template_part( 'template-parts/blog', 'classic' );

	endwhile; // end of loop
	?>

	</ul>

	<div class="clear"></div>

	<div class="pagination-blog">
	<?php
	$big = 999999999; // need an unlikely integer
	echo paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'prev_text' => esc_attr__('&laquo;','onecommunity'),
		'next_text' => esc_attr__('&raquo;','onecommunity'),
		'total' => $wp_query->max_num_pages
	) );
	?>
	</div>

	<?php $wp_query = null; $wp_query = $temp;?>

	</div><!-- blog-classic -->
</main><!-- content -->

<div id="sidebar-spacer"></div>

<aside id="sidebar" class="sidebar">
	<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-blog')) : ?><?php endif; ?>
</aside><!--sidebar ends-->

</section><!-- .wrapper -->



<?php get_footer(); ?>
