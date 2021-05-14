<?php
add_action( 'admin_enqueue_scripts', 'nt_admin_assets' );
function nt_admin_assets() {
    wp_register_style( 'snap-admin', LD_NOTES_URL .'/css/admin/snap-admin.css', array(), rand() );
    wp_enqueue_style( 'snap-admin' );
}

// Enqueue CSS
function nt_enqueue_css() {

  wp_enqueue_style( 'learndash-notes', plugins_url( '/css/note.css', __FILE__ ), null, LDNT_VER );

  if( get_post_type() == 'coursenote' ) {

     nt_frontend_note_assets();

     wp_enqueue_script( 'nt-notes', plugins_url( '/js/nt_notes.js', __FILE__ ), array( 'jquery' ), LDNT_VER );

     wp_localize_script( 'nt-notes', 'nt_ajax_call', array(
        'adminAjax'         => admin_url('admin-ajax.php'),
        'nt_saved_txt'      =>  __( 'Note saved.', 'sfwd-lms' ),
        'nt_saved_at_txt'   =>  __( 'Note saved at', 'sfwd-lsm' ),
    ) );

   }

}
add_action( 'wp_enqueue_scripts', 'nt_enqueue_css');

function nt_frontend_note_assets() {

     wp_enqueue_style( 'learndash-notes', plugins_url( '/css/note.css', __FILE__ ), null, LDNT_VER );
     wp_enqueue_script( 'nt-notes', plugins_url( '/js/nt_notes.js', __FILE__ ), array('jquery-ui-draggable', 'jquery-ui-resizable'), LDNT_VER );

	wp_register_script( 'learndash-notes-lib', plugins_url( '/js/nt_notes_lib.js', __FILE__ ), array( 'jquery' ), LDNT_VER );

	wp_enqueue_script( 'learndash-notes-lib' );

	wp_localize_script( 'learndash-notes-lib', 'nt_ajax_call', array(
		'adminAjax' => admin_url('admin-ajax.php'),
		'security' 	=> wp_create_nonce( 'nt-course-note-nonce'),
        'nt_saved_txt'      =>  __( 'Note saved.', 'sfwd-lms' ),
        'nt_saved_at_txt'   =>  __( 'Note saved at', 'sfwd-lsm' ),
        'nt_delete_txt'     =>  __( 'Are you sure you want to delete this?', 'sfwd-lms' )
	) );

}

// Enqueue JS
function nt_enqueue_scripts() {

    // Only fires if logged in
    if( !is_user_logged_in() ) return;

	$types 		= ldnt_get_supported_types();
	$post_type 	= get_post_type();

	if( !in_array( $post_type, $types ) ) {
        return;
    }

    /**
     * WordPress script libraries
     * @var array
     */

    $script_handles = array(
        'tiny_mce',
        'jquery-ui-draggable',
        'jquery-ui-resizable'
    );
    foreach( $script_handles as $handle ) {
        wp_enqueue_script( $handle );
    }

    /**
     * Register Notes specific script libraries
     * @var [type]
     */

    wp_register_script( 'nt-notes', plugins_url( '/js/nt_notes.js', __FILE__ ), array('jquery-ui-draggable', 'jquery-ui-resizable'), LDNT_VER );
    wp_register_script( 'learndash-notes-lib', plugins_url( '/js/nt_notes_lib.js', __FILE__ ), array( 'jquery' ), LDNT_VER );
    wp_register_script( 'jquery-ui-touch-punch', plugins_url( '/js/jquery.ui.touch-punch.min.js', __FILE__ ), array( 'jquery' ), LDNT_VER );

    /**
     * Enqueue the scripts
     * @var [type]
     */

    wp_enqueue_script('learndash-notes-lib');
    wp_enqueue_script('jquery-ui-touch-punch');
    wp_enqueue_script('nt-notes');

    /**
     * Custom JS variables to pass into the page
     * @var [type]
     */

    $cuser 		= wp_get_current_user();

	$position_x = get_user_meta( $cuser->ID, '_nt_position_top', true );
	$position_y = get_user_meta( $cuser->ID, '_nt_position_left', true );
	$width		= get_user_meta( $cuser->ID, '_nt_width', true );
	$height		= get_user_meta( $cuser->ID, '_nt_height', true );
	$tinymce	= get_user_meta( $cuser->ID, '_nt_tinymce_height', true );

  	wp_localize_script( 'nt-notes', 'nt_ajax_call', array(
  		'adminAjax' => admin_url('admin-ajax.php'),
  		'security' 	=> wp_create_nonce( 'nt-course-note-nonce'),
		'user_id'	=> $cuser->ID,
		'nt_position_top'	=>	$position_x,
		'nt_position_left'	=>	$position_y,
		'nt_width'			=>	$width,
		'nt_height'			=>	$height,
		'nt_tinymce_height'	=>	$tinymce,
        'nt_permalinks'     =>  ( get_option( 'permalink_structure' ) ? 'yes' : 'no' ),
        'nt_saved_txt'      =>  __( 'Note saved.', 'sfwd-lms' ),
        'nt_saved_at_txt'   =>  __( 'Note saved at', 'sfwd-lms' ),
        'nt_delete_txt'     =>  __( 'Are you sure you want to delete this?', 'sfwd-lms' ),
        'nt_autosave'       =>  get_option( 'ldnt_autosave', 'no' ),
  	) );


}
add_action('wp_enqueue_scripts','nt_enqueue_scripts');

add_action( 'wp_head', 'nt_custom_notepad_styling' );
function nt_custom_notepad_styling() {

    $post_type 	= get_post_type();
	$types 		= ldnt_get_supported_types();
	global $post;


	if ( is_user_logged_in() ){ ?>

            <?php $colors = ldnt_get_the_color_settings(); ?>

            <style type="text/css">

                <?php
                if( get_option('nt_noteicon_placement_right') ) {
                    echo '.nt-note-tab, .nt-note-wrapper { right: ' . get_option('nt_noteicon_placement_right') . 'px; }';
                } ?>

                <?php if( !empty( $colors[ 'background_color' ] ) ): ?>
                    #ldnt-shortcode table.notes-listing thead tr th,
                    #ldnt-shortcode table.nt-course-list-table thead tr th,
                    #ldnt-search-btn,
                    .nt-note-tab {
                        background-color: <?php echo $colors[ 'background_color' ]; ?>;
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'text_color' ] ) ): ?>
                    #nt-note-title-bar,
                    .nt-note-wrapper,
                    .nt-note-tab,
                    .nt-note-wrapper p,
                    .nt-note-tab i,
                    .nt-note-wrapper label {
                        color: <?php echo $colors[ 'text_color' ]; ?>;
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'header_background' ] ) ): ?>
                    #ldnt-shortcode table.notes-listing thead tr th,
                    #ldnt-shortcode table.nt-course-list-table thead tr th,
                    #nt-note-title-bar,
                    .note-header-title {
                        background: <?php echo $colors[ 'header_background' ]; ?>
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'header_text' ] ) ): ?>
                    #ldnt-shortcode table.notes-listing thead tr th,
                    #ldnt-shortcode table.nt-course-list-table thead tr th,
                    .note-header-title,
                    .nt-close-icon {
                        color: <?php echo $colors[ 'header_text' ]; ?>;
                        text-shadow: none;
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'link_color' ] ) ): ?>
                    #ldnt-shortcode table.notes-listing a,
                    #ldnt-shortcode table.nt-course-list-table a,
                    .nt-note-wrapper a {
                        color: <?php echo $colors[ 'link_color' ]; ?>;
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'button_background' ] ) ): ?>
                    #ldnt-search-btn,
                    input.nt-note-submit {
                        background: <?php echo $colors[ 'button_background' ]; ?>;
                    }
                <?php endif; ?>

                <?php if( !empty( $colors[ 'button_text' ] ) ): ?>
                    #ldnt-search-btn,
                    input.nt-note-submit {
                        color: <?php echo $colors[ 'button_text' ]; ?>;
                    }
                <?php endif; ?>

            </style>
            <?php

    }

}
