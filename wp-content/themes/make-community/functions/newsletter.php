<?php

add_filter( 'comments_open', 'newsletter_comments_open', 10, 2 );
function newsletter_comments_open( $open, $post_id ) {

  $post = get_post( $post_id );

  if ( 'newsletter' == $post->post_type )
      $open = true;

  return $open;
}