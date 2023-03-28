<?php
function onecommunity_recent_forum_topics($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_topics" => '5',
		"col" => '1',
	), $atts));

	$transient = get_transient( 'onecommunity_forum_topics' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_forum_topics_enable', 0 ) == 1 ) {
	$items = '';

		$items .= '<div class="shortcode-recent-forum-topics col-'.$col.'">';

			if ( bbp_has_topics( array( 'author' => 0, 'show_stickies' => false, 'order' => 'DESC', 'post_parent' => 'any', 'paged' => 1, 'posts_per_page' => $number_of_topics ) ) ) :

				$items .= '<ul class="topics shortcode-general-list">';

				while ( bbp_topics() ) : bbp_the_topic();

				$items .= '<li class="topic">';
					$items .= '<a href="' . bbp_get_topic_permalink() . '" class="topic-title" title="' . bbp_get_topic_title() . '">' . bbp_get_topic_title() . '</a>';

						$items .= '<div class="details">';
						$items .= bbp_get_author_link( array( 'post_id' => bbp_get_topic_last_active_id(), 'type' => 'name' ) );
						$items .= ' '.bbp_get_topic_freshness_link();
						$items .= ' '.esc_attr__( 'in',  'onecommunity-shortcodes' ).' ';
						$items .= '<a href="' . bbp_get_forum_permalink( bbp_get_topic_forum_id() ) . '">' . bbp_get_forum_title( bbp_get_topic_forum_id() ) . '</a>';
					$items .= '</div>';
				$items .= '</li>';

				endwhile;

				$items .= '</ul>';

			else :

				esc_attr_e( 'Oh bother! No topics were found here!', 'onecommunity-shortcodes' );

			endif;

		$items .= '</div>';

			if ( get_theme_mod( 'onecommunity_transient_forum_topics_enable', 0 ) == 1 ) {
				set_transient( 'onecommunity_forum_topics', $items, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_forum_topics_expiration', 20) );
			}

		return $items;

	} else {
		return '<!-- Transient onecommunity_forum_topics (20 min) -->'.$transient;
	}

}
add_shortcode("onecommunity-recent-forum-topics", "onecommunity_recent_forum_topics");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////