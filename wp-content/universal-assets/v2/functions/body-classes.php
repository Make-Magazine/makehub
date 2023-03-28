<?php
function add_universal_body_classes($classes) {
	// let's get the subdomain in to the body class
	global $current_blog;
	$domain = strtok($current_blog->domain, ".");
	if($domain == "www" || $domain == "make" || $domain == "devmakehub" || $domain == "stagemakehub") {
		$domain = "makeco";
	}
	$classes[] = $domain;
	return $classes;
}
add_filter('body_class', 'add_universal_body_classes', 999, 1);
