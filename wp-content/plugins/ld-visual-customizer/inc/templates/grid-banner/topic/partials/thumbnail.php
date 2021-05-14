<?php
$thumbnail = get_the_post_thumbnail_url( $topic_id, 'lds-grid-thumbnail' );
$style = ( $thumbnail && !empty($thumbnail) ? 'style="background-image: url(\'' . $thumbnail . '\')"' : '' ); ?>
<span class="lds-grid-banners-thumbnail" <?php echo $style; ?>>

</span>
