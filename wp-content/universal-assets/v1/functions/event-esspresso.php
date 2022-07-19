<?php
function add_universal_body_classes($classes) {
    if ( defined('EVENT_ESPRESSO_VERSION') ){
        $classes[] = "event-espresso";
        return $classes;
    }
}
add_filter('body_class', 'add_universal_body_classes');
