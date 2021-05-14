<?php
//Prints Note field in front end and retieves exisintg note as placeholder
function nt_course_note_entry_field( $style = null, $manual_id = null ) {

	global $post;

	if( !isset($post->ID) && !$manual_id ) {
		return;
	}

	//ID's
	$current_user 		= get_current_user_id();
	$current_lesson_id  = ( $manual_id ? $manual_id : $post->ID );

	// Filter for others
	$current_lesson_id  = apply_filters( 'nt_notes_current_lesson_id', $current_lesson_id );

	$current_post_type 	= get_post_type();

	if( $style == null ) {
		$style = get_option( 'nt_noteicon_style', 'tab' );
	}

	$hide_on_mobile 	= ( get_option( 'nt_noteicon_hide_on_mobile' ) == 'yes' ? ' nt-hide-mobile ' : '' );
	$location 			= get_option( 'nt_noteicon_placement', 'bottom' );

	if( get_option('ldnt_single_note_page') == $post->ID ) {
		return false;
	}

	//Checks if note exists and changes title and body variables accordingly
	$args = apply_filters( 'ld_existing_note_arg', array(
		'post_type'  	 => 'coursenote',
		'post_status'	=>	array( 'draft', 'publish' ),
		'meta_query'	 => array(
			//'relation' => 'AND',
			array(
				'key'     => 'nt-note-current-lessson-id',
				'value'   => $current_lesson_id,
				'compare' => '=',
			)
		),
		 'author' => $current_user
	) );

	$the_query = new WP_Query($args);

	$title 	   = false;
	$placeholder = false;

	if ($the_query->have_posts()){

	 	while ( $the_query->have_posts() ) : $the_query->the_post();

	 		$title 		= apply_filters( 'ld_nt_note_title', get_the_title() );
	 		$body 		= apply_filters( 'ld_nt_note_content', get_the_content() );
			$note_id 		= apply_filters( 'ld_nt_note_ID', get_the_ID() );

	 	endwhile;

	} else {

		global $post;

		if( !isset($post->ID) && !$manual_id ) {
			return;
		}

		$title_option = apply_filters( 'ld_nt_note_title', get_option('learndash_notes_default_title') );
		$body_option  = apply_filters( 'ld_nt_note_content', get_option('learndash_notes_default_body') );

		$custom_title 	= apply_filters( 'ld_nt_note_title', get_post_meta( $post->ID, '_ldnt_default_note_title', true ) );
		$custom_body 	= apply_filters( 'ld_nt_note_content', get_post_meta( $post->ID, '_ldnt_default_note_text', true ) );

		/**
		  * Hierarchy of titles
		  *
		  * Page option
		  * Global option
		  * Page name
		  */

		if( !$custom_title && !$title_option ) {
			$title = apply_filters( 'ld_nt_note_title', get_the_title( $post->ID ) );
		} elseif( $title_option && !$custom_title ) {
			$title = $title_option;
		} elseif( $custom_title ) {
			$title = $custom_title;
		}

		if( empty($title) ) {
			$title = apply_filters( 'ld_nt_note_title', get_the_title( $post->ID ) );
		}

		/**
		  * Hierarchy of titles
		  *
		  * Page option
		  * Global option
		  * Page name
		  */

		if( !$custom_body && !$body_option ) {
			$body = apply_filters( 'ld_nt_note_content', __( 'Notes:', 'sfwd-lms' ) );
		} elseif( $body_option && !$custom_body ) {
			$body = $body_option;
		} elseif( $custom_body ) {
			$body = $custom_body;
		}
		$placeholder	= $title;
		$note_id 		= apply_filters( 'ld_nt_note_ID', 'new' );

	}

	$all_notes_page = get_option( 'ldnt_all_notes_page' );
	$new_window 	= ( get_option( 'ldnt_link_new_windows', 'no' ) == 'no' ? '' : ' target="new" ' );
	$post_id = ( $post->ID == $current_lesson_id ? '' : $post->ID ); ?>

	<a class="nt-note-tab <?php echo esc_attr($hide_on_mobile); ?> <?php echo $location . ' ldnt-style-' . $style; ?>" href="#" data-postid="<?php echo $post_id; ?>" data-contentid="<?php echo esc_attr( $current_lesson_id ); ?>">
		<i class="nticon-doc"></i> <span class="nt-screen-reader-text"><?php esc_html_e( 'Take Notes', 'sfwd-lms' ); ?></span>
	</a>

	<div class="nt-note-wrapper" id="<?php echo esc_attr('nt-note-wrapper-' . $current_lesson_id ); ?>">

	    <div class="note-header">
			<span class="nt-close-icon">x</span>
			<div class="note-header-actions"></div>
		</div> <!--/note-header-->

		<div id="nt-note-title-bar">
			<?php esc_html_e( 'Take Notes', 'sfwd-lms' ); ?>
		</div>

		<div id="apf-response"></div>

	    <div class="note-body">

	      <form id="nt-course-note" data-bodyid="<?php echo esc_attr( 'nt-note-body-' . $current_lesson_id ); ?>" action="" method="post">

				<?php wp_nonce_field( basename(__FILE__), 'nt-course-note-nonce') ?>

				<div id="nt-note-title-field">
					<input type="text" name="nt-note-title" class="nt-note-title" value="<?php if($title) echo esc_attr( $title ); ?>" placeholder="<?php if($placeholder) echo esc_attr($placeholder); ?>">
				</div>

				<input type="hidden" name="nt-note-user-id" class="nt-note-user-id" value="<?php echo esc_attr( $current_user ); ?>">
				<input type="hidden" name="nt-note-current-lesson-id" class="nt-note-current-lessson-id" value="<?php echo esc_attr( $current_lesson_id ); ?>">
				<input type="hidden" name="nt-note-current-post-type" class="nt-note-current-post-type" value="<?php echo esc_attr( $current_post_type ); ?>">
				<input type="hidden" name="nt-note-id" class="nt-note-id" value="<?php echo esc_attr($note_id); ?>">

				<?php do_action( 'nt_notes_notepad_fields' ); ?>

				<div id="<?php echo esc_attr( 'nt-note-editor-body-' . $current_lesson_id ); ?>" class="nt-note-editor-body">
					<?php
					$args = apply_filters( 'ld_notes_editor_args', array(
						'media_buttons'		=>		false,
						'textarea_name'		=>		'nt-note-body-' . $current_lesson_id,
						'editor_height'		=>		175,
						'quicktags'			=>		false,
						'teeny'				=>		true,
						'tinymce'				=>		array(
							'toolbar1' => 'bold, italic, underline,strikethrough,blockquote,numlist,bullist,fontsizeselect,formatselect',
						),
						'quicktags'			=>		false,
					) );

					$body = apply_filters( 'nt_user_note_body', $body );

					add_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons', 10, 2);
					wp_editor( $body, 'nt-note-body-' . $current_lesson_id, $args );
					remove_filter( 'teeny_mce_buttons', 'nt_tiny_mce_buttons' ); ?>

					<input type="text" id="xyz" name="<?php echo apply_filters( 'honeypot_name', 'date-submitted') ?>" value="" style="display:none">
				</div>

				<div id="nt-note-actions-wrapper">

					<ul id="nt-note-actions">
						<li><input type="submit" class="nt-note-submit" value="<?php esc_attr_e( 'Save', 'sfwd-lms' ); ?>"/></li>
						<li><a href="#" class="learndash-notes-print-modal" data-note="<?php the_ID(); ?>" title="<?php echo esc_attr_e( 'Print', 'sfwd-lms' ); ?>"><i class="nticon-print"></i></a></li>
						<li><a href="#" class="learndash-notes-download-modal" data-note="<?php the_ID(); ?>" title="<?php echo esc_attr_e( 'Download', 'sfwd-lms' ); ?>"><i class="nticon-file-word"></i></a></li>
						<?php if( $note_id != 'new' ): ?>
							<li><a href="<?php echo esc_url(get_permalink()); ?>" <?php echo $new_window; ?> title="<?php echo esc_attr_e( 'Note Page', 'sfwd-lms' ); ?>">→</a></li>
						<?php endif; ?>
					</ul>

				</div>

				<p id="nt-utility-links" class="<?php if($all_notes_page) echo 'all-notes'; ?>">

					<a href="#" class="nt-reset-dimensions"><i class="fa fa-arrows"></i> <?php esc_html_e( 'Reset Dimensions', 'sfwd-lms' ); ?></a>
					<?php
					if( get_option( 'ldnt_all_notes_page' ) ):
						$new_window 	= ( get_option( 'ldnt_link_new_windows', 'no' ) == 'no' ? '' : ' target="new" ' );
						?>
						<a href="<?php echo esc_url( get_permalink( get_option( 'ldnt_all_notes_page' ) ) ); ?>" <?php echo $new_window; ?>><i class="fa fa-files-o"></i> <?php esc_html_e( 'View All Notes', 'sfwd-lms' ); ?></a>
					<?php endif; ?>

				</p>

		  </form>

	  	</div> <!--/.note-body-->

	</div> <!--/.nt-note-wrapper-->

   <?php

   wp_reset_postdata();

}

function nt_tiny_mce_buttons( $buttons, $editor_id ) {

	return apply_filters( 'nt_notes_wysiwyg_buttons', array( 'bold', 'italic', 'underline', 'bullist', 'numlist', 'link', 'unlink', 'forecolor', 'undo', 'redo', 'table' ) );

}



function nt_course_breadcrumbs( $ids = NULL ) {

	if( empty($ids) || !is_array($ids) ) {
		return false;
	}

	$new_window_option = get_option( 'ldnt_link_new_windows', 'no' );

	$new_window = ( $new_window_option == 'no' ? '' : ' target="new" ' );
	$output		= '';

	$ld_permalinks = array(
		'sfwd-lessons',
		'sfwd-topic',
		'sfwd-quiz'
	);

	if( is_array($ids) ) {
		foreach( $ids as $id ) {

			if( in_array( get_post_type($id), $ld_permalinks ) ) {
				$permalink = learndash_get_step_permalink($id);
			} else {
				$permalink = get_the_permalink($id);
			}

			$output .= '<a href="' . esc_url($permalink) .'" ' . $new_window . '>' . get_the_title($id) . '</a> &raquo; ';

		}
	} elseif( is_int($ids) ) {
		$output .= '<a href="' . esc_url( learndash_get_step_permalink($ids) ) .'" ' . $new_window . '>' . get_the_title($ids) . '</a> &raquo; ';
	}

	return rtrim( $output, '&raquo; ' );

}

add_action( 'show_user_profile', 'nt_list_user_notes_on_profile' );
add_action( 'edit_user_profile', 'nt_list_user_notes_on_profile' );
function nt_list_user_notes_on_profile( $user ) {

	$cuser = wp_get_current_user();

	if( ( !current_user_can('edit_others_pages') && !current_user_can('read_others_nt_notes') ) && $cuser->ID != $user->ID ) return;

	$paged = ( isset($_GET['paged']) ) ? $_GET['paged'] : 1;

	$args = array(
			'post_type' 		=> 'coursenote',
			'posts_per_page' 	=> apply_filters( 'lds_nt_user_notes_pagination', get_option( 'posts_per_page' ) ),
			'post_status' 		=> array('draft', 'publish'),
			'author__in' 		=> $user->ID,
			'paged'				=> $paged,
	);

	$notes = new WP_Query( $args );
	$i = 1;

	if( $notes->have_posts() ): ?>
		<h2 id="ld-user-notes"><?php esc_html_e( 'Users Notes', 'sfwd-lms' ); ?></h2>
		<table class="wp-list-table widefat fixed pages">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Title' ); ?></th>
					<th><?php esc_html_e( 'Date' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php while( $notes->have_posts() ): $notes->the_post(); global $post; ?>
					<tr <?php if( $i %2 == 0 && $i > 1 ) echo 'class="alternate"'; ?>>
						<td id="post-<?php the_ID(); ?>" class="column-title">
							<a href="<?php echo esc_url( get_edit_post_link(get_the_ID()) ); ?>"><strong><?php the_title(); ?></strong></a>
							<p class="nt-location"><?php esc_html_e( 'Location:', 'sfwd-lms' ); ?> <?php echo nt_course_breadcrumbs( get_post_meta( $post->ID, '_nt-course-array', true ) ); ?></p>
						</td>
						<td>
							<?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?>
						</td>
					</tr>
				<?php $i++; endwhile; ?>
			</tbody>
		</table>
		<?php
		if ( $notes->max_num_pages > 1 ): // check if the max number of pages is greater than 1  ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
						<div class="pagination-links">
						<?php
						$big = 999999999;

						$args = array(
							'base' 		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ) . '#ld-user-notes',
							'format' 	=> '?paged=%#%',
							'current' 	=> max( 1, $_GET['paged'] ),
							'total' 	=> $notes->max_num_pages
						);
						echo paginate_links($args); ?>
						</div>
	    			</div>
	  			</div>
			</div>
		<?php endif;
	endif;
}

function ldnt_get_template( $template ) {
	return apply_filters( 'ldnt_template_' . $template, LD_NOTES_PATH . 'templates/' . $template . '.php' );
}

apply_filters( 'learndash-lesson-row-attributes', 'ldnt_force_lesson_row_attributes' );
function ldnt_force_lesson_row_attributes() {
	return true;
}

// add_action( 'learndash-topic-row-title-after', 'ldnt_add_note_icon_to_lesson_row', 999, 3 );
// add_action( 'learndash-lesson-components-after', 'ldnt_add_note_icon_to_lesson_row', 10, 3 );
function ldnt_add_note_icon_to_lesson_row( $lesson_id, $course_id, $user_id ) {

	if( !is_user_logged_in() ) {
		return;
	}

	$args = array(
		'post_type'  	 => 'coursenote',
		'post_status'	=>	array( 'draft', 'publish' ),
		'meta_query'	 => array(
			//'relation' => 'AND',
			array(
				'key'     => 'nt-note-current-lessson-id',
				'value'   => $lesson_id,
				'compare' => '=',
			)
		),
		'author' 		 => $user_id,
		'posts_per_page' => 1,
	);

	$notes = get_posts($args);

	if( !$notes || empty($notes) ) {
		return;
	}

	foreach( $notes as $note ): ?>
		<span class="ld-note-icon">
			<i class="fa fa-file-text"></i> <?php esc_html_e( 'Notes', 'sfwd-lms' ); ?>
		</span>
	<?php
	endforeach;

}

add_filter( 'the_content', 'ldnt_single_note_content' );
function ldnt_single_note_content( $content ) {

	global $post;

	$ldnt_single_note_page = get_option('ldnt_single_note_page');

	if( !$ldnt_single_note_page || $ldnt_single_note_page != $post->ID || !isset( $_GET['ldnt_id'] ) ) {
		return $content;
	}

	$note = get_post( intval($_GET['ldnt_id']) );

	if( $note && !empty($note) ) {
		ob_start();
		echo ldnt_single_note_template( $note->post_content, $note, true );
		$content .= ob_get_clean();
	}

	return $content;

}

add_filter( 'body_class', 'ldnt_add_notes_body_class' );
function ldnt_add_notes_body_class( $classes = null ) {

	global $post;

	if( empty($post->post_content) ) {
		return $classes;
	}

	if( has_shortcode( $post->post_content, 'note_editor' ) || has_shortcode( $post->post_content, 'notepad' ) || ldnt_user_can_take_notes() ) {
		return array_merge( $classes, array( 'has-notepad' ) );
	}

	return $classes;

}
