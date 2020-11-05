<?php

add_action('gravityview/approve_entries/updated', 'update_entry_status', 10, 3);

function update_entry_status($entry_id, $status) {
    //$status - 1 for approved, 2 for rejected, 3 for pending
    switch ($status) {
        case '1':
            $post_status = 'publish';
            break;
        case '2':
            $post_status = 'trash';
            break;
        default:
            $post_status = 'pending';
    }
    $entry = GFAPI::get_entry($entry_id);

    $post_data = array(
        'ID' => $entry['post_id'],
        'post_status' => $post_status
    );
    wp_update_post($post_data);    
}
