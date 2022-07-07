<?php

// Rewrite the language on the course grid buttons
add_action( 'learndash-lesson-row-attributes-before', 'remove_learndash_actions', 9);
function remove_learndash_actions() {
   remove_action( 'learndash-lesson-row-attributes-before', 'ldvc_lesson_button', 10, 2 );
}
add_filter( 'learndash-lesson-row-attributes-before', 'ldvc_lesson_button_new' );
function ldvc_lesson_button_new( $post_id = null ) {
   $lds_template = get_option('lds_listing_style');
   if( $lds_template == 'grid-banner' ):
        echo '<span class="grid-actions"><a class="lds-btn lds-btn-primary" href="' . esc_url( get_the_permalink($post_id) ) . '">See More</a></span>';
   endif;

}
