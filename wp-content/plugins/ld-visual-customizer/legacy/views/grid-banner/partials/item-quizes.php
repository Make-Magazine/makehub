<?php
$cols = intval(get_option( 'lds_grid_columns', 2 ));

switch( $cols ) {
    case(2):
        $class = 'lds-col-md-6';
        break;
    case(3):
        $class = 'lds-col-md-4';
        break;
    case(4):
        $class = 'lds-col-md-3';
        break;
}

$qi     = 1;
$q      = 0;

foreach( $quizzes as $quiz ):

    if( $q % $cols == 0 && $i > 1 ) echo '</div><div class="lds-row">';

    $class  .= ' quiz-status-' . $quiz['status'];
    $meta   = array(
        'content_type'  =>  get_post_meta( $quiz['post']->ID, '_lds_content_type', true ),
        'duration'      =>  get_post_meta( $quiz['post']->ID, '_lds_duration', true ),
        'description'   =>  get_post_meta( $quiz['post']->ID, '_lds_short_description', true ),
        'icon'          =>  ldvc_get_content_icon( $quiz['post']->ID ),
        'status'		=>	$quiz['status'],
        'post_id'       =>  $quiz['post']->ID
    );
    ?>
    <div class="lds-course-item <?php echo esc_attr( $class ); ?>">

        <?php
        include( ldvc_get_template_part('grid-banner/partials/item-banner.php') ); ?>

        <h3 class="m-lds-grid-title">
            <a href="<?php echo esc_attr( learndash_get_step_permalink($quiz['post']->ID) ); ?>">
                <?php echo esc_html( $qi . '. ' . get_the_title($quiz['post']->ID) ); ?>
            </a>
        </h3>

        <?php
        include( ldvc_get_template_part('grid-banner/partials/item-meta.php') ); ?>

    </div>

    <?php $q++; $qi++;
endforeach; ?>
