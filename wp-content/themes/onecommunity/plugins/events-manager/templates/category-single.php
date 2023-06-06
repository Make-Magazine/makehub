<?php
/*
 * This page displays a single event, called during the em_content() if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */

global $EM_Category;
echo '<div class="event-wrapper event-category">';
echo do_shortcode('[categories_list include="' . $EM_Category->id . '"]
<div class="thumbnail thumbnail-wide">#_CATEGORYIMAGE</div>
#_CATEGORYNOTES
<div class="category-events">
<h3>' . esc_attr__('Upcoming Events', 'onecommunity') . '</h3>
#_CATEGORYNEXTEVENTS

<h3>' . esc_attr__('Past Events', 'onecommunity') . '</h3>
#_CATEGORYPASTEVENTS
</div>
[/categories_list]');
echo "<div>";

?>