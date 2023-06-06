<li class="forum-lastposts">
	<div class="forum-lastposts-content">
	<a href="<?php bbp_topic_permalink(); ?>" class="shortcode_forum_topic_title" title="<?php bbp_topic_title(); ?>"><?php bbp_topic_title(); ?></a><br />

		<div class="forum-lastposts-details">
		<?php bbp_author_link( array( 'post_id' => bbp_get_topic_last_active_id(), 'type' => 'name' ) ); ?>
		<?php bbp_topic_freshness_link(); ?>
		<?php  esc_attr_e( 'in',  'onecommunity' ); ?> <a href="<?php echo bbp_get_forum_permalink( bbp_get_topic_forum_id() ) ?>"><?php echo bbp_get_forum_title( bbp_get_topic_forum_id() ); ?></a>
		</div>
	</div>
</li>