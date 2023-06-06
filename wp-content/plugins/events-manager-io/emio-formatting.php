<?php

/**
 * @param string $replace
 * @param EM_Event $EM_Event
 * @param string $result
 * @return string
 */
function emio_event_output_placeholder($replace, $EM_Event, $result){
	switch ( $result ){
		case '#_EMIOEVENTURL':
			$replace = !empty($EM_Event->event_attributes['event_url']) ? $EM_Event->event_attributes['event_url'] : '';
			break;
		case '#_EMIOBOOKINGSURL':
			$replace = !empty($EM_Event->event_attributes['bookings_url']) ? $EM_Event->event_attributes['bookings_url'] : '';
			break;
		case '#_EMIOBOOKINGSPRICE':
			if( isset($EM_Event->event_attributes['bookings_price']) ){
				$price = $EM_Event->event_attributes['bookings_price'];
				$replace = number_format( $price, 2, get_option('dbem_bookings_currency_decimal_point','.'), get_option('dbem_bookings_currency_thousands_sep',',') );
			}else{
				$replace = emio_placeholder_not_available();
			}
			break;
		case '#_EMIOCURRENCY':
		case '#_EMIOCURRENCYSYMBOL':
			if( !empty($EM_Event->event_attributes['bookings_currency']) ){
				$currency = $EM_Event->event_attributes['bookings_currency'];
				$replace = $result == '#_EMIOCURRENCY' ? @em_get_currency_name($currency) : @em_get_currency_symbol(false, $currency);
				if( empty($replace) ) $replace = $currency;
			}else{
				$replace = $result == '#_EMIOCURRENCY' ? '':emio_placeholder_not_available();
			}
			break;
		case '#_EMIOSPACES':
			$replace = isset($EM_Event->event_attributes['bookings_spaces']) ? $EM_Event->event_attributes['bookings_spaces'] : emio_placeholder_not_available();
			break;
		case '#_EMIOAVAILABLESPACES':
			$replace = isset($EM_Event->event_attributes['bookings_available']) ? $EM_Event->event_attributes['bookings_available'] : emio_placeholder_not_available();
			break;
		case '#_EMIOBOOKEDSPACES':
			$replace = isset($EM_Event->event_attributes['bookings_confirmed']) ? $EM_Event->event_attributes['bookings_confirmed'] : emio_placeholder_not_available();
			break;
		default :
			if( !empty($EM_Event->event_attributes['import_source']) && preg_match('/^#_EMIOSOURCE(\{(.+)\})?/', $result, $match) ){
				if( !empty($EM_Event->event_attributes['import_source']) ){
					EMIO_Loader::import();
					$EMIO_Import = EMIO_Imports::get_format($EM_Event->event_attributes['import_source']);
					if( !empty($EMIO_Import::$format_name) ) $source = $EMIO_Import::$format_name;
				}
				if( empty($source) && !empty($match[2]) ){
					$replace = $match[2];
				}else{
					$replace = !empty($source) ? $source : __('Not Imported', 'events-manager-io');
				}
			}
	}
	return $replace;
}
add_filter('em_event_output_placeholder','emio_event_output_placeholder',10,3);

function emio_placeholder_not_available(){
	return apply_filters('emio_placeholder_not_available', esc_html__('N/A', 'events-manager-io'));
}

/**
 * @param bool $show
 * @param string $condition
 * @param string $full_match
 * @param EM_Event $EM_Event
 * @return bool
 */
function emio_event_output_show_condition($show, $condition, $full_match, $EM_Event){
	if( $condition == 'is_imported' ){ //item was imported by EM I/O
		$show = !empty($EM_Event->event_attributes['import_source']);
	}elseif( $condition == 'not_imported' ){ //item was not imported by EM I/O
		$show = empty($EM_Event->event_attributes['import_source']);
	}elseif( $condition == 'imported_is_public' ){ //item was imported and is publicly available at the source
		$show = empty($EM_Event->event_attributes['import_private']) || !isset($EM_Event->event_attributes['import_private']);
		$show = $show && !empty($EM_Event->event_attributes['import_source']);
	}elseif( $condition == 'imported_is_private' ){ //item was imported and visibility is restricted at the source
		$show = !empty($EM_Event->event_attributes['import_private']);
		$show = $show && !empty($EM_Event->event_attributes['import_source']);
	}elseif( $condition == 'has_event_url' ){ //item has an external event url
		$show = !empty($EM_Event->event_attributes['event_url']);
	}elseif( $condition == 'has_event_url' ){ //item doesn't have an external event url
		$show = !empty($EM_Event->event_attributes['event_url']);
	}elseif( $condition == 'has_bookings_url' ){ //item has an external url for bookings
		$show = !empty($EM_Event->event_attributes['bookings_url']);
	}elseif( $condition == 'no_bookings_url' ){ //item doesn't have an external url for bookings
		$show = empty($EM_Event->event_attributes['bookings_url']);
	}elseif( $condition == 'has_bookings_price' ){ //item has booking price  defined (might still be 0)
		$show = isset($EM_Event->event_attributes['bookings_price']);
	}elseif( $condition == 'no_bookings_price' ){ //item doesn't have booking price  defined (might still be 0)
		$show = !isset($EM_Event->event_attributes['bookings_price']);
	}elseif( $condition == 'has_bookings_spaces' ){ //item has booking spaces defined (might still be 0)
		$show = isset($EM_Event->event_attributes['bookings_spaces']);
	}elseif( $condition == 'no_bookings_spaces' ){ //item doesn't have booking spaces defined (might still be 0)
		$show = !isset($EM_Event->event_attributes['bookings_spaces']);
	}elseif( $condition == 'has_bookings_confirmed' ){ //item has booking confirmed spaces defined (might still be 0)
		$show = isset($EM_Event->event_attributes['bookings_confirmed']);
	}elseif( $condition == 'no_bookings_confirmed' ){ //item doesn't have booking confirmed spaces defined (might still be 0)
		$show = !isset($EM_Event->event_attributes['bookings_confirmed']);
	}elseif( preg_match('/^(is|not)_imported_(.+)$/',$condition, $matches) ){
		//item is or is not imported by a specific format source
		if( $matches[1] == 'is' ){
			$show = !empty($EM_Event->event_attributes['import_source']) && $EM_Event->event_attributes['import_source'] == $matches[2];
		}else{
			$show = empty($EM_Event->event_attributes['import_source']) || $EM_Event->event_attributes['import_source'] != $matches[2];
		}
	}
	return $show;
}
add_action('em_event_output_show_condition', 'emio_event_output_show_condition', 1, 4);

/**
 * @param string $replace
 * @param EM_Location $EM_Location
 * @param string $result
 * @return string
 */
function emio_location_output_placeholder($replace, $EM_Location, $result){
	switch ( $result ){
		case '#_EMIOLOCATIONURL':
			$replace = !empty($EM_Location->location_attributes['location_url']) ? $EM_Location->location_attributes['location_url'] : '';
			break;
		default :
			if( !empty($EM_Location->location_attributes['import_source']) && preg_match('/^#_EMIOSOURCE(\{(.+)\})?/', $result, $match) ){
				if( !empty($EM_Location->location_attributes['import_source']) ){
					EMIO_Loader::import();
					$EMIO_Import = EMIO_Imports::get_format($EM_Location->location_attributes['import_source']);
					if( !empty($EMIO_Import::$format_name) ) $source = $EMIO_Import::$format_name;
				}
				if( empty($source) && !empty($match[2]) ){
					$replace = $match[2];
				}else{
					$replace = !empty($source) ? $source : __('Not Imported', 'events-manager-io');
				}
			}
	}
	return $replace;
}
add_filter('em_location_output_placeholder','emio_location_output_placeholder',10,3);

/**
 * @param bool $show
 * @param string $condition
 * @param string $full_match
 * @param EM_Location $EM_Location
 * @return bool
 */
function emio_location_output_show_condition($show, $condition, $full_match, $EM_Location){
	if( $condition == 'is_imported' ){ //item was imported by EM I/O
		$show = !empty($EM_Location->location_attributes['import_source']);
	}elseif( $condition == 'not_imported' ){ //item was not imported by EM I/O
		$show = empty($EM_Location->location_attributes['import_source']);
	}elseif( $condition == 'imported_is_public' ){ //item was imported and is publicly available at the source
		$show = empty($EM_Location->location_attributes['import_private']) || !isset($EM_Location->location_attributes['import_private']);
		$show = $show && !empty($EM_Location->location_attributes['import_source']);
	}elseif( $condition == 'imported_is_private' ){ //item was imported and visibility is restricted at the source
		$show = !empty($EM_Location->location_attributes['import_private']);
		$show = $show && !empty($EM_Location->location_attributes['import_source']);
	}elseif( $condition == 'has_location_url' ){ //item has an external event url
		$show = !empty($EM_Location->location_attributes['location_url']);
	}elseif( $condition == 'has_location_url' ){ //item doesn't have an external event url
		$show = !empty($EM_Location->location_attributes['location_url']);
	}elseif( preg_match('/^(is|not)_imported_(.+)$/',$condition, $matches) ){
		//item is or is not imported by a specific format source
		if( $matches[1] == 'is' ){
			$show = !empty($EM_Location->location_attributes['import_source']) && $EM_Location->location_attributes['import_source'] == $matches[2];
		}else{
			$show = empty($EM_Location->location_attributes['import_source']) || $EM_Location->location_attributes['import_source'] != $matches[2];
		}
	}
	return $show;
}
add_action('em_location_output_show_condition', 'emio_location_output_show_condition', 1, 4);