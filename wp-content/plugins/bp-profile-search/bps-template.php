<?php

add_filter ('bp_get_template_stack', 'bps_template_stack', 20);
function bps_template_stack ($stack)
{
	$stack[] = dirname (__FILE__). '/templates';
	return $stack;
}

function bps_templates ()
{
	$templates = array ('members/bps-form-default', 'members/bps-form-nouveau', 'members/bps-form-legacy', 'members/bps-form-sample-1', 'members/bps-form-sample-2');
	return apply_filters ('bps_templates', $templates);
}

function bps_default_template ()
{
	$templates = bps_templates ();
	return $templates[0];
}

function bps_is_template ($template)
{
	$templates = bps_templates ();
	return in_array ($template, $templates);
}

function bps_valid_template ($template)
{
	return bps_is_template ($template)? $template: bps_default_template ();
}

function bps_template_info ($template)
{
	$retired = array ('members/bps-form-nouveau', 'members/bps-form-legacy', 'members/bps-form-sample-1', 'members/bps-form-sample-2');

	$located = bp_locate_template ($template. '.php');
	if ($located === false)
	{
		if (in_array ($template, $retired))
			echo '<strong style="color:red;">'. $template. '</strong><br>'. __('this template has been retired, please switch to bps-form-default', 'bp-profile-search');
		else
			echo '<strong style="color:red;">'. $template. '</strong><br>'. __('template not found', 'bp-profile-search');
		return false;
	}

	if (dirname ($located) == dirname (__FILE__). '/templates/members')
	{
		if (in_array ($template, $retired))
		{
			echo '<strong style="color:red;">'. $template. '</strong><br>'. __('outdated template, please consider switching to bps-form-default', 'bp-profile-search');
			echo '<br><a href="https://dontdream.it/new-form-template-structure/">'. __('more information...', 'bp-profile-search'). '</a>';
		}
		else
			echo '<strong style="color:green;">'. $template. '</strong><br>'. __('built-in template', 'bp-profile-search');
		return $located;
	}

	ob_start ();
	$response = include $located;
	ob_get_clean ();

	$path = str_replace (WP_CONTENT_DIR. '/', '', $located);
	$path = str_replace ($template. '.php', '', $path);

	if ($response == 'end_of_options 4.9')
		echo '<strong style="color:blue;">'. $template. '</strong><br>'. sprintf (__('custom template located in: %1$s', 'bp-profile-search'), $path);
	else
	{
		echo '<strong style="color:red;">'. $template. '</strong><br>'. sprintf (__('outdated custom template located in: %1$s', 'bp-profile-search'), $path);
		echo '<br><a href="https://dontdream.it/new-form-template-structure/">'. __('more information...', 'bp-profile-search'). '</a>';
	}
	return $located;
}

function bps_call_template ($template, $args = array ())
{
	$located = bp_locate_template ($template. '.php');

	if ($located === false)
		return bps_error ('template_not_found', $template);

	echo "\n<!-- BP Profile Search ". BPS_VERSION. " $template -->\n";
	if (bps_debug ())
	{
		$path = str_replace (WP_CONTENT_DIR, '', $located);
		echo "<!--\n";
		echo "path $path\n";
		echo "args "; print_r ($args);
		echo "-->\n";
	}

	$GLOBALS['bps_template_args'][] = $args;
	include $located;
	array_pop ($GLOBALS['bps_template_args']);

	echo "\n<!-- BP Profile Search end $template -->\n";
	return true;
}

function bps_call_form_template ($template, $args)
{
	$template = bps_valid_template ($template);
	$located = bp_locate_template ($template. '.php');

	if ($located === false)
		return bps_error ('template_not_found', $template);

	$form = $args[0];
	$meta = bps_meta ($form);
	$options = isset ($meta['template_options'][$template])? $meta['template_options'][$template]: array ();

	echo "\n<!-- BP Profile Search ". BPS_VERSION. " $template -->\n";
	if (bps_debug ())
	{
		$path = str_replace (WP_CONTENT_DIR, '', $located);
		echo "<!--\n";
		echo "path $path\n";
		echo "args "; print_r ($args);
		echo "options "; print_r ($options);
		echo "-->\n";
	}

	$GLOBALS['bps_template_args'][] = $args;
	include $located;
	array_pop ($GLOBALS['bps_template_args']);

	echo "\n<!-- BP Profile Search end $template -->\n";
	return true;
}

function bps_template_args ()
{
	return end ($GLOBALS['bps_template_args']);
}

function bps_jquery_ui_themes ()
{
	$themes = array (
		'' => __('no jQuery UI', 'bp-profile-search'),
		'base' => 'Base',
		'black-tie' => 'Black Tie',
		'blitzer' => 'Blitzer',
		'cupertino' => 'Cupertino',
		'dark-hive' => 'Dark Hive',
		'dot-luv' => 'Dot Luv',
		'eggplant' => 'Eggplant',
		'excite-bike' => 'Excite Bike',
		'flick' => 'Flick',
		'hot-sneaks' => 'Hot Sneaks',
		'humanity' => 'Humanity',
		'le-frog' => 'Le Frog',
		'mint-choc' => 'Mint Choc',
		'overcast' => 'Overcast',
		'pepper-grinder' => 'Pepper Grinder',
		'redmond' => 'Redmond',
		'smoothness' => 'Smoothness',
		'south-street' => 'South Street',
		'start' => 'Start',
		'sunny' => 'Sunny',
		'swanky-purse' => 'Swanky Purse',
		'trontastic' => 'Trontastic',
		'ui-darkness' => 'UI darkness',
		'ui-lightness' => 'UI lightness',
		'vader' => 'Vader',
	);	

	return apply_filters ('bps_jquery_ui_themes', $themes);
}

function bps_escaped_form_data ($version = '')
{
	if ($version == '')	return bps_escaped_form_data47 ();
	if ($version == '4.9')	return bps_escaped_form_data49 ();

	return false;
}

function bps_escaped_filters_data ($version = '')
{
	if ($version == '')	return bps_escaped_filters_data47 ();
	if ($version == '4.9')	return bps_escaped_filters_data49 ();

	return false;
}

function bps_set_hidden_field ($name, $value)
{
	$new = new stdClass;
	$new->display = 'hidden';
	$new->code = $name;		// to be removed
	$new->html_name = $name;
	$new->value = $value;
	$new->unique_id = bps_unique_id ($name);

	return $new;
}

function bps_sort_fields ($a, $b)
{
	return ($a->order <= $b->order)? -1: 1;
}
