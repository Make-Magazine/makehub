<?php
$date           = get_comment_date( get_option('date_format') . ' ' . get_option('time_format'), $comment->comment_ID );
$comment        = ldms_add_attachment($comment); ?>

<div id="comment-<?php echo $comment->comment_ID; ?>" class="ldms-comment">
    <div class="ldms-comment-wrapper">
        <div class="ldms-comment-author">
            <?php echo get_avatar( $comment->comment_author_email ); ?>
        </div>
        <div class="ldms-comment-body">

            <h5 class="ldms-comment-username">
                <strong><?php echo esc_html($comment->comment_author); ?></strong>
                 - <span class="ldms-comment-date"><?php echo esc_html($date); ?></span>
                 - <a class="ldms-comment-link" href="<?php echo esc_url( get_the_permalink() ) . '#comment-' . $comment->comment_ID; ?>"><?php esc_html_e( 'Comment Link', 'ldmessenger' ); ?></a>
            </h5>

            <?php echo wp_kses_post( wpautop( $comment->comment_content ) ); ?>

        </div>
    </div>
</div>
