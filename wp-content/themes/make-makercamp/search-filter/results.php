<?php
/**
 * Search & Filter Pro
 *
 * Sample Results Template
 *
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 *
 * Note: these templates are not full page templates, rather
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think
 * of it as a template part
 *
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs
 * and using template tags -
 *
 * http://codex.wordpress.org/Template_Tags
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $query->have_posts() ) {
	?>

	<div class="results-info">
		<span>Found <?php echo $query->found_posts; ?> Results</span>
		<span>Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?></span>
	</div>

	<div class="pagination">

		<div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
		<div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>
		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>

	<div class="result-items">
	<?php
		while ($query->have_posts()) {
			$post = $query->the_post();
			$postType = get_post_type_object(get_post_type($post));

			$author_id = get_the_author_meta( 'ID' );
			$user_link = str_replace("/author/", "/members/", get_author_posts_url($author_id));
			?>
			<div class="result-item">
				<?php if ( has_post_thumbnail() ) { ?>
						<div class="result-image"><a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail("small"); ?>
						</a></div>
				<?php } ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php
					if(get_field('what_will_you_learn', $post->ID)) {
						echo "<p>" . first_sentence(get_field('what_will_you_learn', $post->ID)) . "</p>"; 
					}
				?>
				<div class="results-meta">
					<p><?php the_category(); ?></p>
					<p><?php the_tags(); ?></p>
					<p><?php echo $postType->labels->singular_name;  ?></p>
					<p><a href="<?php echo $user_link; ?>"><?php the_author(); ?></a></p>
				</div>
			</div>

			<hr />
	<?php } ?>
	</div>
	Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br />

	<div class="pagination">

		<div class="nav-previous"><?php next_posts_link( 'Older posts', $query->max_num_pages ); ?></div>
		<div class="nav-next"><?php previous_posts_link( 'Newer posts' ); ?></div>
		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>
	<?php
} else {
	echo "No Results Found";
}
?>
