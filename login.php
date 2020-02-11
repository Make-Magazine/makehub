function custom_login() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/functions/assets/login-style.css' );
    wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/functions/assets/login-style.js' );
}
add_action( 'login_enqueue_scripts', 'custom_login' );