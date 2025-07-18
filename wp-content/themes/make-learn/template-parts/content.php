<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */
?>

<?php 
global $post;

// Build an array of all images associated with the post to create a gallery out of
$post_image_ids = array();
array_push($post_image_ids, get_post_thumbnail_id());
for ($x = 1; $x < 7; $x++) {
    if (get_field("image_" . $x)) {
        // if fresh from the form, this comes in as an array, but for some reason once edited it comes in as a number
        if (is_array(get_field("image_" . $x))) {
            array_push($post_image_ids, get_field("image_" . $x)["ID"]);
        } else {
            array_push($post_image_ids, get_field("image_" . $x));
        }
    }
}
$post_image_ids_string = implode(', ', $post_image_ids);

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( !is_single() || is_related_posts() ) { ?>
		<div class="post-inner-wrap">
	<?php } ?>

	<?php 
	if ( ( !is_single() || is_related_posts() ) && function_exists( 'buddyboss_theme_entry_header' ) ) {
		echo '<a href="' . get_permalink() . '"><img src="' . get_the_post_thumbnail_url( $post, 'thumbnail' ) . '" /></a>';
	} 
	?>

	<div class="entry-content-wrap">
		<?php 
		$featured_img_style = buddyboss_theme_get_option( 'blog_featured_img' );

		if ( !empty( $featured_img_style ) && $featured_img_style == "full-fi-invert" ) {

			if ( is_single() && ! is_related_posts() ) { ?>
				<?php if ( has_post_thumbnail() ) { ?>
					<figure class="entry-media entry-img bb-vw-container1">
						<?php the_post_thumbnail( 'large', array( 'sizes' => '(max-width:768px) 768px, (max-width:1024px) 1024px, (max-width:1920px) 1920px, 1024px' ) ); ?>
					</figure>
				<?php } ?>
			<?php } ?>

			<?php
			if ( has_post_format( 'link' ) && ( !is_singular() || is_related_posts() ) ) {
				echo '<span class="post-format-icon"><i class="bb-icon-link-1"></i></span>';
			}

			if ( has_post_format( 'quote' ) && ( !is_singular() || is_related_posts() ) ) {
				echo '<span class="post-format-icon white"><i class="bb-icon-quote"></i></span>';
			}
			?>

            <?php if (!is_singular('lesson') && !is_singular('llms_assignment') ) : ?>

			<header class="entry-header"><?php
				if ( is_singular() && ! is_related_posts() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					$prefix = "";
					if( has_post_format( 'link' ) ){
						$prefix = __( '[Link]', 'buddyboss-theme' );
						$prefix .= " ";//whitespace
					}
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $prefix, '</a></h2>' );
				endif;

				if( has_post_format( 'link' ) && function_exists( 'buddyboss_theme_get_first_url_content' ) && ( $first_url = buddyboss_theme_get_first_url_content( $post->post_content ) ) != "" ) : ?>
					<p class="post-main-link"><?php echo $first_url;?></p>
				<?php endif; ?></header><!-- .entry-header -->

            <?php endif; ?>

			<?php if ( !is_singular() || is_related_posts() ) { ?>
				<div class="entry-content">
					<?php 
					if( empty($post->post_excerpt) ) {
						the_excerpt();
					} else {
						echo bb_get_excerpt($post->post_excerpt, 150);
					}
					?>
				</div>
			<?php } ?>

			<?php if ( ( isset($post->post_type) && $post->post_type === 'post' ) || ( ! has_post_format( 'quote' ) && is_singular( 'post' ) ) ) : ?>
				<?php get_template_part( 'template-parts/entry-meta' ); ?>
			<?php endif; ?>

		<?php } else { ?>

			<?php
			if ( has_post_format( 'link' ) && ( !is_singular() || is_related_posts() ) ) {
				echo '<span class="post-format-icon"><i class="bb-icon-link-1"></i></span>';
			}

			if ( has_post_format( 'quote' ) && ( !is_singular() || is_related_posts() ) ) {
				echo '<span class="post-format-icon white"><i class="bb-icon-quote"></i></span>';
			}
			?>

			<header class="entry-header">
				<?php
				if ( is_singular() && ! is_related_posts() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					$prefix = "";
					if( has_post_format( 'link' ) ){
						$prefix = __( '[Link]', 'buddyboss-theme' );
						$prefix .= " ";//whitespace
					}
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $prefix, '</a></h2>' );
				endif;
				?>

				<?php if( has_post_format( 'link' ) && function_exists( 'buddyboss_theme_get_first_url_content' ) && ( $first_url = buddyboss_theme_get_first_url_content( $post->post_content ) ) != "" ):?>
				<p class="post-main-link"><?php echo $first_url;?></p>
				<?php endif; ?>
			</header><!-- .entry-header -->

			<?php if ( !is_singular() || is_related_posts() ) { ?>
				<div class="entry-content">
					<?php 
					if( empty($post->post_excerpt) ) {
						the_excerpt();
					} else {
						echo bb_get_excerpt($post->post_excerpt, 150);
					}
					?>
				</div>
			<?php } ?>

			<?php if ( ( isset($post->post_type) && $post->post_type === 'post' ) || ( ! has_post_format( 'quote' ) && is_singular( 'post' ) ) ) : ?>
				<?php get_template_part( 'template-parts/entry-meta' ); ?>
			<?php endif; ?>

			<?php // HERE IS OUR BOY, PUT THE IMAGE GALLERY HERE
			 	if ( is_single() && ! is_related_posts() ) { ?>
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="gallery-wrapper">
					<?php
						echo do_shortcode('[gallery ids="' . $post_image_ids_string . '" size="small" order="DESC" orderby="ID"]');
						if (count($post_image_ids) != 1) {
							?>
							<a id="showAllGallery" class="universal-btn" href="javascript:void(jQuery('.psgal .msnry_item:first-of-type a').click())"><i class="fas fa-images"></i></a>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>

		<?php } ?>
		
		<?php if ( is_singular() && ! is_related_posts() ) { ?>
			<div class="entry-content">
			<?php
				the_content( sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'buddyboss-theme' ), array(
					'span' => array(
						'class' => array(),
					),
				)
				), get_the_title()
				) );
			?>
			</div><!-- .entry-content -->
		<?php } ?>
	</div>

	<?php if ( !is_single() || is_related_posts() ) { ?>
		</div><!--Close '.post-inner-wrap'-->
	<?php } ?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php if( is_single() && ( has_category() || has_tag() ) ) { ?>
	<div class="post-meta-wrapper">
		<?php if  ( has_category() ) : ?>
			<div class="cat-links">
				<i class="bb-icon-folder"></i>
				<?php _e( 'Categories: ', 'buddyboss-theme' ); ?>
				<span><?php the_category( __( ', ', 'buddyboss-theme' ) ); ?></span>
			</div>
		<?php endif; ?>

		<?php if  ( has_tag() ) : ?>
			<div class="tag-links">
				<i class="bb-icon-tag"></i>
				<?php _e( 'Tagged: ', 'buddyboss-theme' ); ?>
				<?php the_tags( '<span>', __( ', ', 'buddyboss-theme' ),'</span>' ); ?>
			</div>
		<?php endif; ?>
	</div>
<?php } ?>

<?php
get_template_part( 'template-parts/content-subscribe' );
get_template_part( 'template-parts/author-box' );
get_template_part( 'template-parts/related-posts' );