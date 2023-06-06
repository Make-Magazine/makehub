<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

	<li class="bbp-forum-info">

		<?php if ( bbp_is_user_home() && bbp_is_subscriptions() ) : ?>

			<span class="bbp-row-actions">

				<?php do_action( 'bbp_theme_before_forum_subscription_action' ); ?>

				<?php bbp_forum_subscription_link( array( 'before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;' ) ); ?>

				<?php do_action( 'bbp_theme_after_forum_subscription_action' ); ?>

			</span>

		<?php endif; ?>

		<?php do_action( 'bbp_theme_before_forum_title' ); ?>

		<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>

		<?php do_action( 'bbp_theme_before_forum_description' ); ?>

		<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>

		<?php do_action( 'bbp_theme_after_forum_description' ); ?>

		<?php do_action( 'bbp_theme_before_forum_sub_forums' ); ?>

		<?php bbp_list_forums(); ?>

		<?php do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>

	</li>

	<div class="bbp-forum-right-bg"></div>

	<li class="bbp-forum-right">

	<div class="bbp-forum-count">
		<div class="bbp-forum-topic-count"><span><?php bbp_forum_topic_count(); ?></span><?php esc_attr_e('Topics', 'onecommunity'); ?></div>
		<div class="bbp-forum-reply-count"><span><?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></span><?php esc_attr_e('Posts', 'onecommunity'); ?></div>
		<div class="bbp-forum-count-v-spacer"></div>
	</div>

	<div class="bbp-forum-freshness">
			<?php do_action( 'bbp_theme_before_topic_author' ); ?>

			<span class="bbp-topic-freshness-author"><?php esc_attr_e('Last post by', 'onecommunity'); ?> <?php bbp_author_link( array( 'post_id' => bbp_get_forum_last_active_id(), 'type' => 'name' ) ); ?><br>
			<?php bbp_forum_freshness_link(); ?></span>

			<?php do_action( 'bbp_theme_after_topic_author' ); ?>
	</div>

	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
