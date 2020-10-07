<?php
// After the gravity view is updated, we want to update the created post associated with it. 
// SO FAR, THIS IS UPDATING THE TITLE, CONTENT, FEATURED IMAGE, AND TEXT ACF FIELDS... needs work for taxonomies
add_action('gravityview/edit_entry/after_update', 'gravityview_event_update', 10, 4);

function gravityview_event_update($form, $entry_id, $entry_object) {
    error_log('gravityview_event_update');
    $entry = $gv_entry_obj->entry;
    error_log(print_r($entry, TRUE));
    $post_id = $entry["post_id"];
    //update event
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $entry['1'],
        'post_content' => $entry['2'],        
    );
    wp_update_post($post_data);
    
    //update acf
    
   /*
    $post_data = array(
        'ID' => $post_id,
        'post_title' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Event Title"),
        'post_content' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Describe What You Do"),
        'post_category' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"),
            //'tags_input' => gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags")
    );
    wp_update_post($post_data);
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Type"), TRUE));
    //error_log(print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Category Tags"), TRUE));
    //error_log("Featured Image: " . print_r(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image")), TRUE);
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $media = media_sideload_image(gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Featured Image"), $post_id);
    if (!empty($media) && !is_wp_error($media)) {
        $args = array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'post_parent' => $post_id
        );
        $attachments = get_posts($args);
        if (isset($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $image = wp_get_attachment_image_src($attachment->ID, 'full');
                // determine if in the $media image we created, the string of the URL exists
                if (strpos($media, $image[0]) !== false) {
                    // if so, we found our image. set it as thumbnail
                    set_post_thumbnail($post_id, $attachment->ID);
                    break;
                }
            }
        }
    }
    // Not sure how else to update all the fields but to mention the by name
    update_field("about", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "About You"), $post_id);
    update_field("location", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Location"), $post_id);
    update_field("materials", gf_get_value_by_label($form, GFAPI::get_entry($entry_id), "Experience Materials"), $post_id);
    * 
    */
}

// rather than use potentially changing field ids, look up by label
function gf_get_value_by_label($form, $entry, $label) {
    foreach ($form['fields'] as $field) {
        $lead_key = $field->label;
        if (strToLower($lead_key) == strToLower($label)) {
            return $entry[$field->id];
        }
    }
    return false;
}