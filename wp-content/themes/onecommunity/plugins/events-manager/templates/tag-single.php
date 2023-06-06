<?php
/*
 * This page displays a single event, called during the em_content() if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */
global $EM_Tag;
/* @var $EM_Tag EM_Tag */

print_r( $EM_Tag->description );
echo '<div class="category-events">';
print_r( $EM_Tag->output_single() );
echo '</div>';

?>