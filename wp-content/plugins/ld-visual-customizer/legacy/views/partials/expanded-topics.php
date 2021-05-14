<?php foreach ( $topics as $key => $topic ) : ?>
    <?php $completed_class = empty( $topic->completed ) ? 'lds-content-status-notcompleted':'lds-content-status-completed'; ?>
    <li>
        <a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( get_permalink( $topic->ID ) ); ?>' title='<?php echo esc_attr( $topic->post_title ); ?>'>

            <?php
            $icon = ldvc_get_content_icon( $topic->ID );
            echo '<span class="lds-ec-icon fa ' . esc_attr($icon) . '"></span>' . esc_html($topic->post_title);

            $lds_post_id = $topic->ID;

            if( get_post_meta( $topic->ID, '_lds_short_description', true ) ) echo '<span class="lds-ec-description">' . get_post_meta( $topic->ID, '_lds_short_description', true ) . '</span>';

            if( get_post_meta( $topic->ID, '_lds_duration', true ) ) echo '<span class="lds-ec-duration standalone"><i class="fa fa-clock-o"></i> ' . get_post_meta( $topic->ID, '_lds_duration', true ) . '</span>'; ?>
        </a>
    </li>
<?php endforeach; ?>
