<li class="item<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>">

	<?php if ( has_post_thumbnail() ) { ?>
        <div class="thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div>
    <?php } ?>
    <div class="title"><a href="<?php the_permalink(); ?>" class="recent-post-title-link"><?php the_title(); ?></a>
		<div class="details">
			<span class="time"><?php the_time('l, M j') ?></span>

			<span class="comments"><?php comments_number('0', '1', '%') ?></span>

			<?php if (function_exists('wp_ulike_get_post_likes')) { ?>
			<span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span> 
			<?php } ?>

		</div>

	</li>

<div class="clear"></div>