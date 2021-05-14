<?php
$cuser          = wp_get_current_user();
$user_id        = $_GET['user'];
$user_object    = get_user_by( 'id', $user_id );

if( !current_user_can('read_others_nt_notes') && !learndash_is_group_leader_of_user( $cuser->ID, $user_id ) ) {
    echo '<p class="ld-notes-notice ld-notes-error">' . __( 'You do not have access to this users notes.', 'sfwd-lms' ) . '</p>';
    return ob_get_clean();
}

$args = array(
    'post_type'         => 'coursenote',
    'posts_per_page'    => -1,
    'post_status'       => array('draft', 'publish'),
    'author__in'        => $user_id,
);

$new_window 	= ( get_option( 'ldnt_link_new_windows', 'no' ) == 'no' ? '' : ' target="new" ' );
$download_sep 	= ( get_option( 'permalink_structure' ) ? '?' : '&' );

$query          = new WP_Query($args);

if ( $query->have_posts() ) { ?>

    <p class="nt-breadcrumbs">
        <strong><?php esc_html_e( 'Users Notes:', 'sfwd-lms' ); ?></strong>
        <a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Groups', 'sfwd-lms' ); ?></a>
        &raquo; <a href="<?php echo esc_url( get_the_permalink() . '?user=' . $user_id ); ?>">
            <?php echo esc_html_e( $user_object->user_nicename . '\'s notes', 'sfwd-lms' ); ?>
        </a>
        &raquo; <?php echo esc_html( get_the_title($course_id) ); ?>
    </p>
    <h2><?php echo esc_html_e( $user_object->user_nicename . '\'s notes', 'sfwd-lms' ); ?>: <?php echo esc_html( get_the_title($course_id) ); ?></h2>
    <?php
    include( ldnt_get_template('archive-note-listing') );

} else {

    echo '<p class="ld-notes-notice ld-notes-error">' . __( 'User has no notes.', 'sfwd-lms' ) . '</p>';

}
