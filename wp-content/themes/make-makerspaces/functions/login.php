<?php

function login_redirect( $redirect_to, $request, $user ){
    return home_url('/');
}
add_filter( 'login_redirect', 'login_redirect', 10, 3 );

function custom_login_stylesheets() {
    wp_enqueue_style('custom-login', '/wp-content/themes/make-co/css/style-login.css');
    wp_enqueue_style('custom-login', '/wp-content/universal-assets/v1/css/universal.css');
}

// style the login page and give it the universal header and footer
add_action('login_enqueue_scripts', 'custom_login_stylesheets');

add_action('login_header', function() {
    get_header();
});
add_action('login_footer', function() {
    get_footer();
});

?>