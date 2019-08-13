<?php

function bps_escaped_form_data49 ()
{
	list ($form, $location) = bps_template_args ();

	$meta = bps_meta ($form);
	$fields = bps_parse_request (bps_get_request ('form', $form));
	wp_register_script ('bps-template', plugins_url ('bp-profile-search/bps-template.js'), array (), BPS_VERSION);

	$F = new stdClass;
	$F->id = $form;
	$F->title = bps_wpml ($form, '-', 'title', get_the_title ($form));
	$F->location = $location;
	$F->unique_id = bps_unique_id ('form_'. $form);
	$F->errors = 0;

	$dirs = bps_directories ();
	$F->action = $location == 'directory'?
		parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH):
		$dirs[bps_wpml_id ($meta['action'])]->path;

	$F->method = $meta['method'];
	$F->fields = array ();

	foreach ($meta['field_code'] as $k => $code)
	{
		if (empty ($fields[$code]))  continue;

		$f = $fields[$code];
		$mode = $meta['field_mode'][$k];
		if (!bps_Fields::set_display ($f, $mode))  continue;

		$f->label = $f->name;
		$custom_label = bps_wpml ($form, $f->code, 'label', $meta['field_label'][$k]);
		if (!empty ($custom_label))
		{
			$f->label = $custom_label;
			$F->fields[] = bps_set_hidden_field ($f->code. '_label', $f->label);
		}

		$custom_desc = bps_wpml ($form, $f->code, 'comment', $meta['field_desc'][$k]);
		if ($custom_desc == '-')
			$f->description = '';
		else if (!empty ($custom_desc))
			$f->description = $custom_desc;

		if (isset ($f->error_message))
			$F->errors += 1;
		else
			$f->error_message = '';

		switch ($f->display)
		{
		case 'range':
		case 'integer-range':
		case 'date-range':
		case 'range-select':
			if (!isset ($f->value['min']))  $f->value['min'] = '';
			if (!isset ($f->value['max']))  $f->value['max'] = '';
			break;

		case 'textbox':
		case 'integer':
		case 'date':
			if (!isset ($f->value))  $f->value = '';
			break;

		case 'distance':
			if (!isset ($f->value['location']))
				$f->value['distance'] = $f->value['units'] = $f->value['location'] = $f->value['lat'] = $f->value['lng'] = '';
			wp_enqueue_script ($f->script_handle);
			wp_enqueue_script ('bps-template');
			break;

		case 'selectbox':
			if (!isset ($f->value))  $f->value = '';
			$f->options = array ('' => '') + $f->options;
			break;

		case 'radio':
			if (!isset ($f->value))  $f->value = '';
			wp_enqueue_script ('bps-template');
			break;

		case 'multiselectbox':
			$f->multiselect_size = 4;
		case 'checkbox':
			if (!isset ($f->value))  $f->value = array ();
			break;
		}

		$f->html_name = ($mode == '')? $f->code: $f->code. '_'. $mode;
		$f->unique_id = bps_unique_id ($f->html_name);
		$f->mode = bps_Fields::get_filter_label ($mode);

		do_action ('bps_field_before_search_form', $f);
		$F->fields[] = $f;
	}

	$F->fields[] = bps_set_hidden_field (BPS_FORM, $form);
	do_action ('bps_before_search_form', $F);

	foreach ($F->fields as $f)
	{
		if (!is_array ($f->value))
			$f->value = esc_attr (stripslashes ($f->value));
		else foreach ($f->value as $k => $value)
			$f->value[$k] = esc_attr (stripslashes ($value));
		if ($f->display == 'hidden')  continue;

		$f->label = esc_html ($f->label);
		$f->description = esc_html ($f->description);
		$f->error_message = esc_html ($f->error_message);

		$options = array ();
		foreach ($f->options as $key => $label)  $options[esc_attr ($key)] = esc_attr ($label);
		$f->options = $options;
	}

	return $F;
}

function bps_escaped_filters_data49 ()
{
	list ($request, $full) = bps_template_args ();

	$F = new stdClass;

	$action = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$action = add_query_arg (BPS_FORM, 'clear', $action);
	$F->action = $full? esc_url ($action): '';
	$F->fields = array ();

	$fields = bps_parse_request ($request);
	foreach ($fields as $f)
	{
		if (!isset ($f->filter))  continue;

		if (empty ($f->label))
			$f->label = $f->name;
		$f->label = esc_html ($f->label);
		$f->value = stripslashes_deep ($f->value);

		do_action ('bps_field_before_filters', $f);
		$F->fields[] = $f;
	}

	usort ($F->fields, function($a, $b) {return ($a->order <= $b->order)? -1: 1;});

	do_action ('bps_before_filters', $F);
	return $F;
}
