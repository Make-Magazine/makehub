<?php
add_action( 'wp_ajax_nt_delete_note', 'nt_delete_note' );
function nt_delete_note() {

    if( !isset($_POST['note_id']) ) {
        return false;
    }

    $note_id    = $_POST[ 'note_id' ];
    $cuser      = wp_get_current_user();

    if( ( $cuser->ID != get_post_field( 'post_author', $note_id ) ) && ( !current_user_can( 'manage_options' ) ) ) {
        return false;
        wp_die();
    }

    wp_delete_post( $note_id );
    wp_send_json_success( array( 'success' => true, 'data' => $note_id ) );

    wp_die();

}

//AJAX - Submits Note and Saves extra fields to Postmeta
function nt_process_course_note() {

	if ( ! empty( $_POST[ 'submission' ] ) ) {
		wp_send_json_error( 'Honeypot Check Failed' );
	}

	if ( ! check_ajax_referer( 'nt-course-note-nonce', 'security' ) ) {
		wp_send_json_error( 'Security Check failed' );
	}

	$course_title 	= nt_generate_course_array( $_POST['data']['currentPostType'] ,$_POST['data']['currentLessonId'] );

    if( !isset($_POST['data']['title']) || empty($_POST['data']['title'] ) ) {

        $current_post_id = $_POST['data']['currentLessonId'];
        $custom_title 	 = get_post_meta( $current_post_id, '_ldnt_default_note_title', true );

        $title = ( $custom_title ? $custom_title : get_the_title( $current_post_id ) );

    } else {
        $title = $_POST['data']['title'];
    }

	$notes_data = apply_filters( 'ldnt_process_note_data', array(
		'post_title' 	=> sanitize_text_field( $title ),
		'post_status'	=>	'publish',
		'post_type' 	=> 'coursenote',
		'post_content' 	=> $_POST['data']['body']
	) );

	//If note id already exists update exisiting note else insert new note
    $note_Id_update = ( $_POST['data']['noteId'] != 'new' ? $_POST['data']['noteId'] : false );
	$post_author 	= get_post_meta( $note_Id_update, 'nt-note-user-id', true );

    $cuser = wp_get_current_user();

	if( $note_Id_update && ( $post_author == $_POST['data']['userId'] ) ){

		$post_id = wp_update_post( array(
			'ID'           => $note_Id_update,
			'post_content' => $_POST['data']['body'],
               'post_title'   => esc_html( $_POST['data']['title'] ),
		), true );

		do_action( 'learndash_notes_after_note_update', $post_id );

		wp_send_json_success( array( 'success' => true, 'data' => get_the_permalink($post_id) ) );

	} else {

		$post_id = wp_insert_post( $notes_data, true );

		if ( $post_id ) {

            $relationships = ldnt_get_post_relationships( $_POST['data']['currentLessonId'] );

            if( !empty( $relationships ) ) {

                foreach( $relationships as $key => $val ) {
                    update_post_meta( $post_id, '_' . $key, $val );
                }

            }

			update_post_meta( $post_id, 'nt-note-user-id', $_POST['data']['userId'] );
			update_post_meta( $post_id, 'nt-note-current-lessson-id', $_POST['data']['currentLessonId'] );
			update_post_meta( $post_id, '_nt-course-array', $course_title );

			do_action( 'learndash_notes_after_note_creation', $post_id );

		}

		wp_send_json_success( array( 'success' => true, 'data' => get_the_permalink($post_id), 'post_id' => $post_id ) );

	}


}
add_action( 'wp_ajax_nt_process_course_note', 'nt_process_course_note' );
add_action( 'wp_ajax_nt_nopriv_process_course_note', 'nt_process_course_note' );


add_action( 'wp_ajax_nt_print_note', 'nt_print_note' );
add_action( 'wp_ajax_nopriv_nt_print_note', 'nt_print_note' );
function nt_print_note() {

	$post_id 	= $_POST[ 'note_id' ];
	$cuser		= wp_get_current_user();

	if( ( get_post_meta( $post_id, 'nt-note-user-id', true ) != $cuser->ID ) && ( !current_user_can( 'manage_options' ) ) ) {

		return false;
		wp_die();

	}

	$post = get_post( $post_id ); //get_posts( array( 'post_type' => 'coursenote', 'post_id' => $post_id ) );

	$post_content = '<h1>' . $post->post_title . '</h1>' . $post->post_content . '<p>' . nt_course_breadcrumbs( get_post_meta( $post->ID, '_nt-course-array', true ) ) . '</p>';

	wp_send_json_success( array( 'success' => true, 'data' => $post_content ) );
	wp_die();

}

add_action( 'wp_ajax_nt_note_save_coordinates', 'nt_note_save_coordinates' );
add_action( 'wp_ajax_nopriv_nt_note_save_coordinates', 'nt_note_save_coordinates' );

function nt_note_save_coordinates() {

	$cords = array(
		'_nt_position_top' 		=>	( isset( $_POST[ 'position_top'] ) ? $_POST[ 'position_top' ] : null ),
		'_nt_position_left'		=>	( isset( $_POST[ 'position_left'] ) ? $_POST[ 'position_left' ] : null ),
		'_nt_width'				=>	( isset( $_POST[ 'width'] ) ? $_POST[ 'width' ] : null ),
		'_nt_height'			=>	( isset( $_POST[ 'height'] ) ? $_POST[ 'height' ] : null ),
		'_nt_tinymce_height'	=>	( isset( $_POST[ 'tinymce_height' ] ) ? $_POST[ 'tinymce_height' ] : null )
	);

	$cuser = wp_get_current_user();

	foreach( $cords as $key => $val ) {

		if( ( isset( $val ) ) && ( !empty( $val ) ) ) {

			if( $val == 'reset' ) {

				delete_user_meta( $cuser->ID, $key );

			} else {

				update_usermeta( $cuser->ID, $key, $val );

			}

		}

	}

	wp_send_json( $cords );

	exit();

}

add_action( 'wp_ajax_nt_get_editor_markup', 'nt_get_editor_markup' );
add_action( 'wp_ajax_nopriv_nt_get_editor_markup', 'nt_get_editor_markup' );
function nt_get_editor_markup() {

    $post_id    = $_POST[ 'note_id' ];
    $content_id = $_POST[ 'content_id' ];
    $body       = '';

    if( isset( $post_id ) && !empty( $post_id ) ) {

        $content_post   = get_post( $post_id );

        if( $content_post ) $body = $content_post->post_content;

    } else {

        $default_body 	 = __( 'Notes:', 'sfwd-lms' );
        $custom_body 	 = get_post_meta( $content_id, '_ldnt_default_note_text', true );
        $body 	         = ( empty( $custom_body ) ? $default_body : $custom_body );

    }

    $args = array(
        'media_buttons'		=>		false,
        'textarea_name'		=>		'nt-note-body',
        'editor_height'		=>		175,
        'quicktags'			=>		false,
        'teeny'				=>	true,
        'quicktags'			=>		false,
    );

    $body = apply_filters( 'nt_user_note_body', $body );

    add_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons', 10, 2);
    wp_editor( $body, 'nt-note-body', $args );
    remove_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons' );

    die();

}
