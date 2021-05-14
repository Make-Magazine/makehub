<?php
function lds_shortcodes_enqueue_scripts() {
    wp_register_style( 'lds-custom-shortcodes', LDS_URL . '/legacy/assets/css/lds-shortcodes.css', null, LDS_VER, false );
    wp_enqueue_style( 'lds-custom-shortcodes' );
}

add_action( 'wp_enqueue_scripts', 'ldvc_dequeue_styles', 100 );
function ldvc_dequeue_styles() {

	$scripts = get_option( 'lds_dequeue_styles' );

	if( empty( $scripts ) )
		return;

	$scripts = explode( ',', str_replace( ' ', '' , $scripts ) );

	foreach( $scripts as $script ) wp_dequeue_style( $script );

}

$lds_sizes = array(
    array(
        'name'      =>  'course_icon',
        'width'     =>  150,
        'height'    =>  150,
        'crop'      =>  true,
    ),
    array(
        'name'      =>  'course_banner',
        'width'     =>  '500',
        'height'    =>  '300',
        'crop'      =>  true,
    )
);

foreach( $lds_sizes as $size ) {
    add_image_size( $size['name'], $size['width'], $size['height'], $size['crop'] );
}

add_action( 'wp_enqueue_scripts', 'lds_frontend_assets' );
function lds_frontend_assets() {

    wp_register_style ('ld-customizer', plugin_dir_url(__FILE__) . 'assets/css/learndash-skins-custom.css.php', null, LDS_VER );
    wp_register_script( 'lds-global', plugin_dir_url(__FILE__).'/assets/js/lds-global.js', array( 'jquery' ), false, true );

    if( get_option('ldvc_add_method') == 'dynamic' ) {
        wp_enqueue_style('ld-customizer');
    }

}

add_action( 'admin_enqueue_scripts', 'lds_admin_assets' );
function lds_admin_assets( $hook_suffix ) {

    wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'lds-admin', plugin_dir_url(__FILE__) . '/assets/css/lds-admin.css' );

    wp_enqueue_script( 'lds-custom-js', plugin_dir_url(__FILE__).'/assets/js/lds-admin.js', array( 'wp-color-picker' ), false, true );

    wp_register_script( 'bootstrap-select', plugin_dir_url(__FILE__) . '/assets/vendor/bootstrap-select/js/bootstrap-select.min.js', false, true );

    wp_register_style( 'bootstrap-select', plugin_dir_url(__FILE__) . '/assets/vendor/bootstrap-select/css/bootstrap-select.min.css');

    $fontawesome_ver = get_option( 'lds_fontawesome_ver', '5' );

    if( $fontawesome_ver == 4 ) {
        wp_register_script( 'fontawesome-iconpicker', plugin_dir_url(__FILE__) . '/assets/vendor/fontawesome-iconpicker/v4/js/fontawesome-iconpicker.min.js', false, true);
        wp_register_style( 'fontawesome-iconpicker', plugin_dir_url(__FILE__) . '/assets/vendor/fontawesome-iconpicker/v4/css/fontawesome-iconpicker.min.css', null, LDS_VER );
        wp_register_style( 'fontawesome', plugin_dir_url(__FILE__) . '/assets/css/font-awesome.min.css', null, LDS_VER );

    } else {
        wp_register_script( 'fontawesome-iconpicker', plugin_dir_url(__FILE__) . '/assets/vendor/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js', false, true);
        wp_register_style( 'fontawesome-iconpicker', plugin_dir_url(__FILE__) . '/assets/vendor/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css', null, LDS_VER );
        wp_register_style( 'fontawesome', plugin_dir_url(__FILE__) . '/assets/css/fontawesome5/all.css', null, LDS_VER );
    }

    $meta_scripts = apply_filters( 'lds_meta_scripts', array(
        'bootstrap-select',
        'fontawesome-iconpicker',
        'fontawesome'
    ) );

	if(get_option('lds_skin') == 'playful') {
		wp_enqueue_style( 'architects-daughter', 'https://fonts.googleapis.com/css?family=Permanent+Marker' );
	}

    $content_types = array(
        'sfwd-courses',
        'sfwd-lessons',
        'sfwd-topic'
    );

    if( in_array( get_post_type(), $content_types ) ) {
        foreach( $meta_scripts as $handle ) {
            wp_enqueue_style( $handle );
            wp_enqueue_script( $handle );
        }
    }

}

add_filter( 'wp_head', 'lds_load_skin_assets', 9999 );
function lds_load_skin_assets() {

    if( get_option('lds_fontawesome_ver') != 4 ) {
        wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) . '/assets/css/fontawesome5/all.css', array(), LDS_VER );
        wp_enqueue_style( 'fontawesome-shim', plugin_dir_url( __FILE__ ) . '/assets/css/fontawesome5/v4-shims.min.css', array(), LDS_VER );
    } else {
        wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) . '/assets/css/font-awesome.min.css', array(), LDS_VER );
    }

	$enqueue_method = get_option( 'ldvc_add_method' );

	if( $enqueue_method == 'dynamic' ) {

		wp_enqueue_style('lds-custom-style', plugin_dir_url( __FILE__ ).'/assets/css/learndash-skins-custom.css.php', array( 'sfwd_template_css' ), LDS_VER );

	} elseif( $enqueue_method == 'generated' ) {

		wp_enqueue_style('lds-custom-style', plugin_dir_url( __FILE__ ).'/assets/css/learndash-compiled.css', array( 'sfwd_template_css' ), LDS_VER );

	} elseif( $enqueue_method == 'inline' ) {

		wp_dequeue_style( 'sfwd_template_css' );

	}

}

add_action( 'wp_head' , 'lds_inline_styles' );
function lds_inline_styles() {

	$enqueue_method = get_option( 'ldvc_add_method' );

	if( $enqueue_method == 'inline' ) { ?>
		<style type="text/css">
			<?php echo lds_generate_styles(); ?>
		</style>
	<?php
	}

}
