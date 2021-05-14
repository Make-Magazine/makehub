<?php
$item_border_radius = get_option('lds_content_list_border_radius');

if( $item_border_radius && $item_border_radius != '' ): ?>

    .learndash-wrapper .ld-item-list.ld-lesson-list .ld-pagination .ld-pages {
        border-bottom-left-radius: <?php echo $item_border_radius; ?>px;
        border-bottom-right-radius: <?php echo $item_border_radius; ?>px;
    }

<?php
endif;
