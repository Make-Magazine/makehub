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

$show_desc = get_field('show_short_description');
$show_button = get_field('show_button');
$button_text = get_field('button_text');
$type = (str_contains(strtolower($_SERVER['REQUEST_URI']), "gift-guide")) ? "Products" : "Projects";

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $query->have_posts() ) {
	?>

	<div class="results-info">
		<span><?php echo $query->found_posts . " " . $type; ?> </span>
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

			$secondary_image = get_the_post_thumbnail_url();
			$link = get_permalink();
			if(get_post_type() == "contestants") {
				$first_name = 		get_field( 'user_first_name', $post->ID, true );
				$last_name = 		get_field( 'user_last_name', $post->ID, true );
				$short_desc = 		get_field( 'project_short_description', $post->ID, true );
				$vote_count = 		get_post_meta( get_the_ID(), 'votes_count', true );
				$video_url = 		get_field( 'project_video_link', $post->ID, true );
			} else if(get_post_type() == "gift-guides") {
				$short_desc = 		get_field( 'product_description', $post->ID, true );
				$cost = 			get_field( 'cost', $post->ID, true );
				$images =			get_field( 'product_image_links', $post->ID, true );
				if(!empty($images[0]['product_image'])) {
					$secondary_image = 	$images[0]['product_image'];
				}
				$link = 			get_field( 'purchase_url', $post->ID, true );
			}

			?>
			<div class="result-item">
				<?php if ( isset($video_url) ) { ?>
					<div class="video-preview">
						<?php if( str_contains($video_url, "youtube") || str_contains($video_url, "youtu.be") ) {
							echo do_shortcode("[embedyt]" . $video_url . "[/embedyt]");
						} else if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $video_url, $regs)) {
							echo('<iframe src="https://player.vimeo.com/video/' . $regs[1] . '"></iframe>');
						} else {
							echo $video_url;
						}?>
					</div>
				<?php } else { ?>
					<div class="featured-image"><a href="<?php the_permalink(); ?>"><img src="<?php the_post_thumbnail_url(); ?>" onmouseover="this.src='<?php echo $secondary_image; ?>'" onmouseout="this.src='<?php the_post_thumbnail_url(); ?>'" /></a></div>
				<?php } ?>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php /*
					if(get_field('what_will_you_learn', $post->ID)) {
						echo "<p>" . first_sentence(get_field('what_will_you_learn', $post->ID)) . "</p>";
					} */
				?>
				<div class="results-meta">
					<?php if( isset($first_name) ) { ?>
						<p class="author"><?php echo $first_name . " " . $last_name; ?></p>
					<?php } ?>
					<?php if( isset($cost) ) { ?>
						<p class="cost">$<?php echo $cost; ?></p>
					<?php } ?>
					<?php if( $show_desc == true ) { ?>
						<p class="short_desc"><?php echo $short_desc; ?></p>
					<?php } ?>
					<?php if( isset($vote_count) ) { ?>
						<p><b>Community Votes:</b> <?php echo $vote_count  ?></p>
					<?php } ?>
					<?php if( $show_button == true ) { ?>
						<a class="universal-btn" target="_blank" href="<?php echo $link; ?>"><?php echo $button_text; ?></a>
					<?php } ?>
				</div>
			</div>

			<hr />
	<?php } ?>
	</div>
	<?php /* Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br /> */ ?>

	<div class="pagination">

		<div class="nav-previous"><?php next_posts_link( 'Previous', $query->max_num_pages ); ?></div>
		<div class="nav-next"><?php previous_posts_link( 'Next' ); ?></div>
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
