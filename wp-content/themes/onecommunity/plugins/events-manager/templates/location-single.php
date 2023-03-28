<?php
/* 
 * Remember that this file is only used if you have chosen to override location pages with formats in your events manager settings!
 * You can also override the single location page completely in any case (e.g. at a level where you can control sidebars etc.), as described here - http://codex.wordpress.org/Post_Types#Template_Files
 * Your file would be named single-location.php
 */
/*
 * This page displays a single event, called during the em_content() if this is an event page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output() 
 */
global $EM_Location;

echo do_shortcode('[location post_id="' . $EM_Location->post_id . '"]
	#_LOCATIONMAP

	<ul id="event-location-details">
		<li><div class="left"><b>' . esc_attr__("Address", "onecommunity") . ':</b></div> <div class="right">#_LOCATIONNAME #_LOCATIONADDRESS</div></li>
		<li><div class="left"><b>' . esc_attr__("City", "onecommunity") . ':</b></div> <div class="right">#_LOCATIONTOWN</div></li>
		<li><div class="left"><b>' . esc_attr__("Country", "onecommunity") . ':</b></div> <div class="right">#_LOCATIONCOUNTRY</div></li>
		<li><div class="left"><b>' . esc_attr__("Location", "onecommunity") . ':</b></div> <div class="right">#_LOCATIONLATITUDE, #_LOCATIONLONGITUDE</div></li>
	</ul>

	<div id="event-location-desc">#_LOCATIONNOTES</div>
	<div class="clear"></div>

	<div class="location-events">
	<h3>' . esc_attr__('Upcoming Events', 'onecommunity') . '</h3>
	#_LOCATIONNEXTEVENTS

	<h3>' . esc_attr__('Past Events', 'onecommunity') . '</h3>
	#_LOCATIONPASTEVENTS
	</div>
	[/location]');
