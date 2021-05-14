<?php
/**
 * Controls what templates are rendered
 */
add_filter( 'learndash_template', 'lds_return_custom_templates', 9999, 5 );
function lds_return_custom_templates( $filepath, $name = NULL, $args = NULL, $echo = NULL, $return_file_path = NULL ) {

    $style = get_option( 'lds_listing_style' );
    if( !$style || empty($style) || $style == 'default' ) return $filepath;

    $matches = apply_filters( 'lds_custom_template_matches', array(
        'course.php'    =>  'legacy/views/' . $style . '/course.php',
        'lesson.php'    =>  'legacy/views/' . $style . '/lesson.php',
        'topic.php'     =>  'legacy/views/' . $style . '/topic.php',
    ) );

    foreach( $matches as $match => $new_template ) {

        if( !strpos( $filepath, $match ) ) {
            continue;
        }

        if ( $theme_file = locate_template( array( 'learndash/ldvc/' . $new_template ) ) ) {
          $filepath = $theme_file;
        } else {
          $filepath = LDS_PATH . $new_template;
        }

    }

    return $filepath;

}
