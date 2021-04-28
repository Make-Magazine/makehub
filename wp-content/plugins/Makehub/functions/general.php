<?php

// get just the text
function get_summary($html) {
    $summary = preg_replace('/<a[^>]*>([\s\S]*?)<\/a[^>]*>/', '', $html);
    $summary = strip_tags(str_replace('The post appeared first on .', '', $summary));
    return $summary;
}

//shortens description
function shorten($string, $length) {
    $suffix = '&hellip;';
    $short_desc = trim(str_replace(array("\r", "\n", "\t"), ' ', strip_tags($string)));
    $desc = trim(substr($short_desc, 0, $length));
    $lastchar = substr($desc, -1, 1);
    if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?')
        $suffix = '';
    $desc .= $suffix;
    return $desc;
}
