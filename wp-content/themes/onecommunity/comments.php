<?php

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div class="clear"></div>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

	<h2>

			<?php
				$comments_number = get_comments_number();
				if ( 1 === $comments_number ) {
					/* translators: %s: post title */
					printf( esc_attr__( 'One comment', 'onecommunity' ), get_the_title() );
				} else {
					printf(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'%1$s comments',
							'%1$s comments',
							$comments_number,
							'comments title',
							'onecommunity'
						),
						number_format_i18n( $comments_number ),
						get_the_title()
					);
				}
			?>

		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php wp_list_comments( 'type=comment&callback=mytheme_comment' ); ?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php endif; // Check for have_comments(). ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_attr_e( 'Comments are closed.', 'onecommunity' ); ?></p>
	<?php endif; ?>

	<?php
		comment_form( array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		) );
	?>

</div><!-- .comments-area -->
