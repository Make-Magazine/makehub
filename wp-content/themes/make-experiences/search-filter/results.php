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
		<span><?php echo $query->found_posts; ?> Projects</span>
		<?php /* <span>Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?></span> */ ?>
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
			$query->the_post();

			$first_name = 		get_field( 'user_first_name', $post->ID, true );
			$last_name = 		get_field( 'user_last_name', $post->ID, true );
			$age_range = 		get_field( 'age_group', $post->ID, true );
			$project_type = 	implode(', ', get_field( 'project_type', $post->ID, true ));
			$vote_count = 		get_post_meta( get_the_ID(), 'votes_count', true );
			$video_url = 		get_field( 'project_video_link', $post->ID, true );

			?>
			<div class="result-item">
				<?php if ( !empty($video_url) ) { ?>
					<div class="video-preview">
						<?php if( str_contains($video_url, "youtube") || str_contains($video_url, "youtu.be") ) {
							echo do_shortcode("[embedyt]" . $video_url . "[/embedyt]");
						} else if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $video_url, $regs)) {
							echo('<iframe src="https://player.vimeo.com/video/' . $regs[1] . '"></iframe>');
						} else {
							echo $video_url;
						}?>
					</div>
				<?php } ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php /*
					if(get_field('what_will_you_learn', $post->ID)) {
						echo "<p>" . first_sentence(get_field('what_will_you_learn', $post->ID)) . "</p>";
					} */
				?>
				<div class="results-meta">
					<p><b>Name:</b> <?php echo $first_name . " " . $last_name; ?></p>
					<p><b>Age:</b> <?php echo $age_range; ?></p>
					<p><b>Type:</b> <?php echo $project_type; ?></p>
				</div>
				<a href="<?php the_permalink(); ?>" class="btn universal-btn">More</a>
			</div>

			<hr />
	<?php } ?>
	</div>
	<?php /* Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br /> */ ?>

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
