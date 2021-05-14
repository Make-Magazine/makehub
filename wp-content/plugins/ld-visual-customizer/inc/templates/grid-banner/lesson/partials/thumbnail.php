<?php
$thumbnail = get_the_post_thumbnail_url( $lesson_id, 'lds-grid-thumbnail' );
$style = ( $thumbnail && !empty($thumbnail) ? 'style="background-image: url(\'' . $thumbnail . '\')"' : '' ); ?>
<div class="lds-grid-banners-thumbnail" <?php echo $style; ?>>
    <a href="<?php echo esc_attr( learndash_get_step_permalink($lesson_id) ); ?>"></a>
</div>
