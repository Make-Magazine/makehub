<?php

/**
 * User Replies Created
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php do_action( 'bbp_template_before_user_replies' ); ?>

	<div id="bbp-user-replies-created" class="bbp-user-replies-created">

		<div class="bbp-user-section">

			<?php if ( bbp_get_user_replies_created() ) : ?>

				<?php bbp_get_template_part( 'loop',       'replies' ); ?>

				<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

			<?php else : ?>

				<p><?php bbp_is_user_home() ? esc_attr_e( 'You have not replied to any topics.', 'bbpress' ) : esc_attr_e( 'This user has not replied to any topics.', 'bbpress' ); ?></p>

			<?php endif; ?>

		</div>
	</div><!-- #bbp-user-replies-created -->

	<?php do_action( 'bbp_template_after_user_replies' ); ?>
