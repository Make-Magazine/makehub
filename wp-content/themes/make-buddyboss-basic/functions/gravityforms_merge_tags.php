<?php

//MF custom merge tags
add_filter('gform_custom_merge_tags', 'mf_custom_merge_tags', 99, 4);
add_filter('gform_replace_merge_tags', 'mf_replace_merge_tags', 99, 7);

/**
 * add custom merge tags
 */
function mf_custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
    $merge_tags[] = array('label' => 'Event Group Link', 'tag' => '{event_group_link}');
    return $merge_tags;
}

/**
 * replace custom merge tags in notifications
 */
function mf_replace_merge_tags($text, $form, $lead, $url_encode, $esc_html, $nl2br, $format) {
    global $wpdb;

    //faire id
    if (strpos($text, '{event_group_link}') !== false) {
        if (isset($lead["post_id"])) {
            $event_id = $lead["post_id"];
            $group_id = get_field('group_id', $event_id);
            $group = groups_get_group( array( 'group_id' => $group_id) );

            $text = str_replace('{event_group_link}', bp_get_group_link($group), $text);
        }        
    }
    return $text;
}
