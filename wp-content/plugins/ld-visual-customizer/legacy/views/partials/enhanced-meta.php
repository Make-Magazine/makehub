<?php
$meta = array(
    'content_type'  =>  get_post_meta( $lds_post_id, '_lds_content_type', true ),
    'duration'      =>  get_post_meta( $lds_post_id, '_lds_duration', true ),
    'description'   =>  get_post_meta( $lds_post_id, '_lds_short_description', true ),
    'icon'          =>  ldvc_get_content_icon( $lds_post_id )
);

if( $meta['description'] && !empty( $meta['description'] ) ): ?>
    <b class="lds-enhanced-short-description">
        <?php echo esc_html($meta['description']); ?>
    </b>
<?php endif;

if( !empty( $meta['content_type'] ) || !empty( $meta['duration'] ) ): ?>
    <b class="lds-enhanced-meta">
        <?php
        if( $meta['icon'] ) echo '<b class="lds-meta-item"><i class="fa ' . esc_attr( $meta['icon'] ) . '"></i></b>';
        if( !empty( trim($meta['duration']) ) ) echo '<b class="lds-meta-item"><i class="fa fa-clock-o"></i> ' . esc_attr( $meta['duration'] ) . '</b>'; ?>
    </b>
<?php
endif;
