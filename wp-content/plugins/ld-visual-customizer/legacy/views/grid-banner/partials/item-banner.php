<div class="lds-course-item-icon">
     <?php
     $style = ( !isset($style) ? false : $style );
     $icon  = '';
     if( has_post_thumbnail($meta['post_id']) ):
         $image_class    = 'custom';
         $size           = ( $style == 'icon' ? 'course_icon' : 'course_banner' );
         $src            = wp_get_attachment_image_src( get_post_thumbnail_id( $meta['post_id'] ), $size );
         $src            = $src[0];
     else:
         $icon			= $meta['icon'];
         $image_class    = 'default';
         $src            = LDS_URL . '/assets/img/shortcodes/course-placeholder.jpg';
     endif; ?>

     <?php if( !$icon ): ?>
         <a href="<?php echo esc_attr( learndash_get_step_permalink($meta['post_id']) ); ?>" style="background-image: url('<?php echo esc_attr( $src ); ?>');" class="<?php echo esc_attr( $image_class ); ?>"></a>
     <?php
     endif;
     if( $icon ): ?>
         <a href="<?php echo esc_attr( learndash_get_step_permalink($meta['post_id']) ); ?>" class="<?php echo esc_attr( $image_class ); ?>"><i class="fa <?php echo esc_attr($icon); ?>"></i></a>
     <?php endif; ?>
</div>
