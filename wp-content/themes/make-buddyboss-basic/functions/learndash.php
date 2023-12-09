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


/**
 * Get all the URLs of current course ( lesson, topic, quiz )
   *
   * @param        $course_id
   * @param        $lession_list
   * @param string $course_quizzes_list
   *
   * @return array
   */
function ld_custom_pagination( $course_id, $lession_list, $course_quizzes_list = '' ) {
   global $post;

   $navigation_urls = [];
   if ( ! empty( $lession_list ) ) :

      foreach ( $lession_list as $lesson ) {

         $lesson_topics = learndash_get_topic_list( $lesson->ID );

         $navigation_urls[] = urldecode( trailingslashit( get_permalink( $lesson->ID ) ) );

         if ( ! empty( $lesson_topics ) ) :
            foreach ( $lesson_topics as $lesson_topic ) {
               $navigation_urls[] = urldecode( trailingslashit( get_permalink( $lesson_topic->ID ) ) );

               $topic_quizzes = learndash_get_lesson_quiz_list( $lesson_topic->ID );

               if ( ! empty( $topic_quizzes ) ) :
                  foreach ( $topic_quizzes as $topic_quiz ) {
                     $navigation_urls[] = urldecode( trailingslashit( get_permalink( $topic_quiz['post']->ID ) ) );
                  }
               endif;

            }
         endif;

         $lesson_quizzes = learndash_get_lesson_quiz_list( $lesson->ID );

         if ( ! empty( $lesson_quizzes ) ) :
            foreach ( $lesson_quizzes as $lesson_quiz ) {
               $navigation_urls[] = urldecode( trailingslashit( get_permalink( $lesson_quiz['post']->ID ) ) );
            }
         endif;
      }

   endif;

   $course_quizzes = learndash_get_course_quiz_list( $course_id );
   if ( ! empty( $course_quizzes ) ) :
      foreach ( $course_quizzes as $course_quiz ) {
         $navigation_urls[] = urldecode( trailingslashit( get_permalink( $course_quiz['post']->ID ) ) );
      }
   endif;


   return $navigation_urls;
}


/**
 * return the next and previous URL based on the course current URL.
   *
   * @param array $url_arr
   * @param string $current_url
   *
   * @return array|string
   */
function ld_custom_next_prev_url( $url_arr = [], $current_url = '' ) {
   if ( empty( $url_arr ) ) {
      return;
   }

   // Protocol
   $url = ( is_ssl() ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

   // Get current URL
   $current_url = trailingslashit( $url );
   if ( ! $query = parse_url( $current_url, PHP_URL_QUERY ) ) {
      $current_url = trailingslashit( $current_url );
   }

   $key = array_search( urldecode( $current_url ), $url_arr );


   $url = [];

   $next = current( array_slice( $url_arr, array_search( $key, array_keys( $url_arr ) ) + 1, 1 ) );
   $prev = current( array_slice( $url_arr, array_search( $key, array_keys( $url_arr ) ) - 1, 1 ) );

   $last_element = array_values( array_slice( $url_arr, - 1 ) )[0];

   $url['next'] = ( isset( $next ) && $last_element != $current_url ) ? '<a href="' . $next . '" class="next-link" rel="next">' . esc_html__( 'Next', 'buddyboss-theme' ) . '<span class="meta-nav" data-balloon-pos="up" data-balloon="' . esc_attr__( 'Next', 'buddyboss-theme' ) . '">&rarr;</span></a>' : '';
   $url['prev'] = ( isset( $prev ) && $last_element != $prev ) ? '<a href="' . $prev . '" class="prev-link" rel="prev"><span class="meta-nav" data-balloon-pos="up" data-balloon="' . esc_attr__( 'Previous', 'buddyboss-theme' ) . '">&larr;</span> ' . esc_html__( 'Previous', 'buddyboss-theme' ) . '</a>' : '';


   return $url;
}