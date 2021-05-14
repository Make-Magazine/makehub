<?php
$cuser          = wp_get_current_user();
$user_id        = $_GET['user'];
$user_object    = get_user_by( 'id', $user_id );

if( !current_user_can('read_others_nt_notes') && !learndash_is_group_leader_of_user( $cuser->ID, $user_id ) ) {
    echo '<p class="ld-notes-notice ld-notes-error">' . __( 'You do not have access to this users notes.', 'sfwd-lms' ) . '</p>';
    return ob_get_clean();
}

$cuser_groups       = learndash_get_administrators_group_ids($cuser->ID);
$user_groups        = learndash_get_users_group_ids($user_id);
$overlapping_groups = array_intersect($cuser_groups, $user_groups);
$course_ids         = array();

foreach( $overlapping_groups as $group_id ) {
    $course_ids = array_merge( $course_ids, learndash_group_enrolled_courses( $group_id ) );
} ?>

<p class="nt-breadcrumbs"><strong><?php esc_html_e( 'Users Notes:', 'sfwd-lms' ); ?></strong> <a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Groups', 'sfwd-lms' ); ?></a> &raquo; <?php echo esc_html_e( $user_object->user_nicename . '\'s notes', 'sfwd-lms' ); ?></p>

<h2><?php echo esc_html( $user_object->user_nicename ); ?></h2>

<table class="notes-listing">
    <thead>
        <tr>
            <th><?php esc_html_e( 'Courses', 'sfwd-lms' ); ?></th>
            <th><?php esc_html_e( 'Notes', 'sfwd-lms' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach( $course_ids as $course_id ):
            $args = array(
    				'post_type'         => 'coursenote',
    				'posts_per_page'    => -1,
    				'post_status'       => array('draft', 'publish'),
    				'author__in'        => $user_id,
                    'meta_key'			=>	'nt-note-current-lessson-id',
                    'meta_value'        => $course_id,
                    'fields'			=>	'ids'
    		);
            $course_notes = new WP_Query($args); ?>
            <tr>
                <td>
                    <a href="<?php echo esc_url( get_permalink() . '?user=' . $user_id . '&course=' . $course_id ); ?>">
                        <?php echo esc_html( get_the_title($course_id) ); ?>
                    </a>
                </td>
                <td><?php echo esc_html($course_notes->post_count); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
