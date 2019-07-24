<?php

namespace Vimeography\Pro;

class Welcome {

  public function __construct() {

    add_action( 'admin_init', array($this, 'welcome_screen_do_activation_redirect') );

  }

  /**
   * Check to see if we should perform the redirect
   * 
   * @return [type] [description]
   */
  public function welcome_screen_do_activation_redirect() {

    // Bail if no activation redirect
    if ( ! get_transient( '_vimeography_pro_welcome_screen_activation_redirect' ) ) {
      return;
    }

    // Delete the redirect transient
    delete_transient( '_vimeography_pro_welcome_screen_activation_redirect' );
    
    // Bail if activating from network, or bulk
    if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
      return;
    }

    // Redirect
    wp_safe_redirect( add_query_arg( array( 'page' => 'vimeography-pro' ), admin_url( 'admin.php' ) ) );
  }

}
