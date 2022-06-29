<?php
function randomString() {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($permitted_chars), 0, 10);
}

function timezone_abbr_from_name($timezone_name) {
    $dateTime = new DateTime();
    $dateTime->setTimeZone(new DateTimeZone($timezone_name));
    return $dateTime->format('T');
}

function get_tag_ID($tag_name) {
	$tag = get_term_by('name', $tag_name, 'post_tag');
	if ($tag) {
		return $tag->term_id;
	} else {
		return 0;
	}
}

/**
 *  Case in-sensitive array_search() with partial matches
 */
 function array_find($needle, array $haystack) {
   foreach ($haystack as $key => $value) {
      if (false !== stripos($value, $needle)) {
           return $key;
       }
   }
   return false;
 }
