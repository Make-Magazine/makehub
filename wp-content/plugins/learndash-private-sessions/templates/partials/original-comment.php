<div class="ldms-comment">
    <div class="ldms-comment-wrapper">
        <div class="ldms-comment-author">
            <?php
            echo get_avatar( get_the_author_meta('ID') ); ?>
        </div>
        <div class="ldms-comment-body">

            <h5 class="ldms-comment-username"><strong><?php the_author(); ?></strong> - <span class="ldms-comment-date"><?php echo esc_html( get_the_date( get_option('date_format') . ' ' . get_option('time_format') ) ); ?></span>
    - <a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Comment Link', 'ldmessenger' ); ?></a></h5>

            <?php echo wpautop(get_the_content()); ?>

        </div>
    </div>
</div>
