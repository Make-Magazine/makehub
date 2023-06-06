<ul class="forum-lastposts-container shortcode-general-list">
	<span class="forum-lastposts-v-spacer"></span>
	<?php while ( bbp_topics() ) : bbp_the_topic(); ?>
		<?php bbp_get_template_part( 'loop', 'mysingle-topic' ); ?>
	<?php endwhile; ?>
</ul>
