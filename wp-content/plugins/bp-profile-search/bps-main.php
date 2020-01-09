<?php
/*
Plugin Name: BP Profile Search
Plugin URI: http://www.dontdream.it/bp-profile-search/
Description: Member search and member directories for BuddyPress.
Version: 5.2.4
Author: Andrea Tarantini
Author URI: http://www.dontdream.it/
Text Domain: bp-profile-search
*/

define ('BPS_VERSION', '5.2.4');
define ('BPS_PLUGIN_BASENAME', plugin_basename (__FILE__));

add_action ('admin_notices', 'bps_no_buddypress');
function bps_no_buddypress ()
{
?>
	<div class="notice notice-error is-dismissible">
		<p><?php _e('BP Profile Search requires BuddyPress.', 'bp-profile-search'); ?></p>
	</div>
<?php
}

add_action ('bp_include', 'bps_buddypress');
function bps_buddypress ()
{
	remove_action ('admin_notices', 'bps_no_buddypress');
	include 'bps-start.php';
}

add_action ('in_plugin_update_message-'. BPS_PLUGIN_BASENAME, 'bps_update_message', 10, 2);
function bps_update_message ($plugin_data, $response)
{
	$posts = get_posts (array ('post_type' => 'bps_form', 'nopaging' => true));
	foreach ($posts as $post)
	{
		$meta = bps_meta ($post->ID);
		if (bps_outdated_template ($meta['template']) == 'outdated')
			$outdated[$post->post_title] = $meta['template'];
	}

	if (isset ($outdated))
	{
		echo "</p><p style='color: red;'>Warning! You are using templates not supported in version {$response->new_version}:<br>";
		foreach ($outdated as $form => $template)
			echo "=> {$template} (form '{$form}')<br>";
	}
	else
	{
		echo "</p><p style='color: red;'>Warning! Before updating, please review the changes introduced in version 5.3<br>";
	}

	echo '<a href="https://dontdream.it/bp-profile-search-5-3/">'. __('more information...', 'bp-profile-search'). '</a>';
}

function bps_outdated_template ($template)
{
	static $results = array ();
	if (isset ($results[$template]))  return $results[$template];

	$located = bp_locate_template ($template. '.php');
	if ($located === false)
	{
		$results[$template] = 'not found';
	}
	else
	{
		ob_start ();
		$response = include $located;
		ob_get_clean ();

		if ($response == 'end_of_options 4.9')
			$results[$template] = 'ok';
		else
			$results[$template] = 'outdated';
	}

	return $results[$template];
}
