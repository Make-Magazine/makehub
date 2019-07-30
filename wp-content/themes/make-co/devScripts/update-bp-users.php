<?php

  $members =  get_users( 'blog_id=1&fields=ID' );
  
  foreach ( $members as $user_id ) {
	     error_log($user_id);
        bp_update_user_last_activity( $user_id, bp_core_current_time() );
  }

