<?php
$status = array();

switch( $meta['status'] ) {
    case('notcompleted'):
        $status['label']  = __( 'Incomplete', 'lds_skins' );
        $status['icon']   = 'fa fa-circle';
        break;
    case('notavailable'):
        $status['label']  = __( 'Not Available', 'lds_skins' );
        $status['icon']   = 'fa fa-calendar';
        break;
    case('completed'):
        $status['label']  = __( 'Complete', 'lds_skins' );
        $status['icon']   = 'fa fa-check-circle';
        break;
    default:
        $status['label']  = __( 'Incomplete', 'lds_skins' );
        $status['icon']   = 'fa fa-circle';
        break;
}

$status = array(
    'label' =>  ( $meta['status'] == 'notcompleted' ? __( 'Incomplete', 'lds_skins' ) : __( 'Complete', 'lds_skins' ) ),
    'icon'  =>  ( $meta['status'] == 'notcompleted' ? 'fa fa-circle' : 'fa fa-check-circle' )
);
if( $meta['status'] == 'notavailable' ) {
    $status['label'] = __( 'Not Available', 'lds_skins' );
    $status['icon'] = 'fa fa-calendar';
}

?>
<p class="m-lds-item-meta">
    <?php if( $meta['icon'] ): ?>
        <span class="m-lds-meta-item">
            <i class="<?php echo esc_attr( 'fa ' . $meta['icon'] ); ?>"></i>
        </span>
    <?php
    endif;
    if( !empty( $meta['duration']) ): ?>
        <span class="m-lds-meta-item">
            <i class="fa fa-clock-o"></i> <?php echo esc_html( $meta['duration'] ); ?>
        </span>
    <?php endif; ?>
    <?php if( $topics && isset($meta['type']) && $meta['type'] != 'lesson' ): ?>
        <span class="m-lds-meta-item">
            <?php echo count($topics) . ' ' . __( 'Topics', 'lds_skins' ); ?>
        </span>
    <?php endif; ?>
    <span class="m-lds-item-status <?php echo esc_attr( 'lds-status-' . $meta['status'] ); ?>">
        <i class="<?php echo esc_attr( $status['icon'] ); ?>"></i> <?php echo esc_html( $status['label'] ); ?>
    </span>
</p>
