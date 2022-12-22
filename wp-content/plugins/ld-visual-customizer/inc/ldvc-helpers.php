<?php
function ldvc_get_google_font_familes( $key = false ) {

    $transient = get_transient( 'ldvc_font_families_json' );

    if( !empty($transient) ) {
        return $transient;
    }

    $fonts = json_decode( file_get_contents( LDS_PATH . '/assets/fonts/googlefonts.json') );

    // set_transient( 'ldvc_font_families_json', $output, WEEK_IN_SECONDS );

    return $fonts;

}

function ldvc_get_google_font( $font_name = null ) {

    if( !$font_name ) {
        return false;
    }

    $fonts = ldvc_get_google_font_familes();

    if( !$fonts ) {
        return false;
    }

    foreach( $fonts->items as $font ) {
        if( $font->family == $font_name ) {
            return $font;
        }
    }

    return false;

}

function ldvc_get_content_icon( $post_id = null ) {

    if( $post_id == null ) {
        $post_id = get_the_ID();
    }

    $custom_icon = ( get_post_meta( $post_id, '_lds_course_icon', true ) ? get_post_meta( $post_id, '_lds_course_icon', true ) : get_post_meta( $post_id, '_lds_content_type', true ) );

    return ( $custom_icon ? $custom_icon : '' );

}

add_filter( 'learndash_wrapper_class', 'lds_theme_template_wrapper_classes' );
function lds_theme_template_wrapper_classes( $class ) {

    $ldvc_theme = get_option('lds_skin');

    if( $ldvc_theme && !empty($ldvc_theme) ) {
        $class .= ' lds-theme-' . $ldvc_theme;
    }

    $ldvc_template = get_option('lds_listing_style');

    if( $ldvc_template == 'grid-banner' ) {

        $columns = get_option('lds_grid_columns');

        $class .= ' lds-columns-' . ( $columns ? $columns : '2' );

    }

    if( $ldvc_template && !empty($ldvc_template) ) {
        $class .= ' lds-template-' . $ldvc_template;
    }

    return $class;

}

function ldvc_color_shift( $hex, $percent ) {

    // validate hex string

	$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
	$new_hex = '#';

	if ( strlen( $hex ) < 6 ) {
		$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
	}

	// convert to decimal and change luminosity
	for ($i = 0; $i < 3; $i++) {
		$dec = hexdec( substr( $hex, $i*2, 2 ) );
		$dec = min( max( 0, $dec + $dec * $percent ), 255 );
		$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
	}

	return $new_hex;

}

function ldvc_compress_css( $css ) {

    $css = preg_replace('/\/\*((?!\*\/).)*\*\//','',$css); // negative look ahead
    $css = preg_replace('/\s{2,}/',' ',$css);
    $css = preg_replace('/\s*([:;{}])\s*/','$1',$css);
    $css = preg_replace('/;}/','}',$css);

    return $css;

}

function ldvc_use_iconpicker() {

    if( get_option('lds_fontawesome_ver' ) == '4' && function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
        return false;
    }

    if( get_option( 'lds_disable_fa_picker' ) == 'yes' ) {
        return false;
    }

    return true;

}

function lds_in_use( $value = null ) {

     if( $value == null || !$value || empty($value) ) {
          return false;
     }

     return true;

}

function ldvc_is_focus() {

     $focus_mode = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'focus_mode_enabled' );

	$post_types = array(
		'sfwd-lessons',
		'sfwd-topic',
		'sfwd-quiz',
		'sfwd-assignment',
	);

	if ( 'yes' === $focus_mode && in_array( get_post_type(), $post_types, true ) ) {
		return true;
	}

     return false;


}
