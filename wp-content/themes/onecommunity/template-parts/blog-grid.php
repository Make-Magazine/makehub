<li class="box-blog-entry<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>" data-post-id="<?php the_ID(); ?>">
    <div class="box-blog-thumb">

    	<?php if ( has_post_thumbnail() ) { ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
		<?php } ?>

	</div>

		<div class="box-blog-details">
			<div class="box-blog-details-top">
				<span class="box-blog-cat"><?php the_category(', ') ?></span>
				<span class="box-blog-time"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'onecommunity' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
			</div>
				<a href="<?php the_permalink(); ?>" class="blog-thumb-title-anchor"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 120; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a>

			<div class="box-blog-details-bottom">
			<span class="box-blog-details-bottom-author">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
			<?php echo get_avatar( get_the_author_meta( 'ID' ) , 50); ?>
			</a>
			<?php esc_attr_e('by', 'onecommunity'); ?> <?php echo get_the_author(); ?></span>
			<?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="box-blog-likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?>
			<span class="box-blog-comments"><?php comments_number('0', '1', '%'); ?></span>
			<div class="clear"></div>
			</div><!-- blog-box-comments -->
		</div><!-- blog-thumb-title -->

</li>