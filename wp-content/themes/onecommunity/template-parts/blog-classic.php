<div class="blog-post">

	<article>

		<h1 class="blog-post-title">
			<a href="<?php the_permalink(); ?>"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 57; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a>
		</h1>


	<div class="single-post-details">
	<span class="single-post-category"><?php the_category(', ') ?></span>

	<span class="single-blog-comments"><?php comments_number('0', '1', '%'); ?></span>

	<?php
	if ( shortcode_exists( 'wp_ulike' ) ) {
		echo do_shortcode('[wp_ulike]');
	} ?>

	<span class="single-blog-time"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'onecommunity' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>

	<div class="clear"></div>

	</div>

	    <?php if ( has_post_thumbnail() ) { ?>
	    <div class="featured-image">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
		</div>
		<?php } ?>

		<?php the_content( esc_attr__('Read more','onecommunity') ); ?>

    <div class="clear"></div>


	</article>

</div><!--blog-post-->