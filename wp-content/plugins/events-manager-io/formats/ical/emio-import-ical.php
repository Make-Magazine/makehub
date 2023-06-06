<?php

/**
 * Class EMIO_Import_iCal
 */
class EMIO_Import_iCal extends EMIO_Import {
	/**
	 * Short reference for this parser, such as 'ical' or 'csv'
	 * @var string
	 */
	public static $format = 'ical';
	/**
	 * Display name for this parser
	 * @var string
	 */
	public static $format_name = 'iCal';
	/**
	 * The extension of ical files
	 * @var string
	 */
	public static $ext = 'ics';
	/**
	 * Whether this parser supports recurring imports or not.
	 * @var bool
	 */
	public static $recurring = true;
	/**
	 * iCal supports events with or without locations, but for importing purposes we could scan events and only import the listed locations.
	 * @var array
	 */
	public static $supports = array('events' => array('locations'), 'locations');

	/**
	 * @var array
	 */
	public static $options = array(
		'fuzzy_location' => array(
				'default' => true,
				'delimiter' => true,
				'placeholder_format' => true,
				'format' => true,
				'google_api' => true,
				'country' => true,
		),
		'ignore_uid' => true
	);

	/**
	 * @var array
	 */
	public static $option_defaults = array(
		'fuzzy_location' => array(
				'default' => 0,
				'delimiter' => ',',
				'placeholder_format' => 'NAME, ADDRESS',
				'format' => array('address','town','state','postcode','country'),
				'google_api' => 1,
				'country' => false,
		)
	);
	
	/**
	 * Array of vtimezone offsets, containing
	 * @var array
	 */
	public $vtimezones = array();
	
	/**
	 * Parses the passed on import object and returns array of items for processing.
	 * Each parser should return a standardized format of arrays, so that it once parsed the data is consistent with any other format.
	 * @return array|WP_Error
	 * @throws Exception
	 */
	public function import(){
		require_once 'class.iCalReader.php';
		//EMIO_Import will handle url or file upload itself, this format just accesses the temporary file created by EM IO
		$ical_source = preg_split('/$\R?^/m', $this->get_source());
		// remove whitelines from $ical_source
		foreach( $ical_source as $k => $v ){
			if( empty($v) ){
				unset($ical_source[$k]);
				$reshuffle = true;
			}
		}
		if( !empty($reshuffle) ) $ical_source = array_values( $ical_source );
		// feed array to ical parser and continue
		$ical = defined('WP_DEBUG') && WP_DEBUG ? new ICal($ical_source) : @new ICal($ical_source);
		$ical_events = $ical->events();
		if( !is_array($ical_events) ){
			$error = new WP_Error('invalid-source', __('The source could not be parsed correctly. Is it a valid iCal file?'));
			return $error;
		}
		$items = array();
		foreach ( $ical_events as $ical_event ) {
			$item = array();
			array_walk_recursive($ical_event, "EMIO_Import_iCal::fix_newlines");
			$ical_event = wp_unslash($ical_event);
			if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'events' ) {
				if( empty($ical_event['SUMMARY']) ) continue;
				$item['uid'] = $ical_event['UID'];
				$item['name'] = $ical_event['SUMMARY'];
				// check all day events before we force a 0 time
				if( !preg_match('/T[0-9]{6}Z?$/', $ical_event['DTSTART']) && !preg_match('/T[0-9]{6}Z?$/', $ical_event['DTEND']) ){
					$item['all_day'] = true;
					// add times for EM_DateTime
					$ical_event['DTSTART'] .= 'T000000';
					$ical_event['DTEND'] .= 'T235959';
				}
				//sort out start and end dates
				foreach( array('start' => 'DTSTART', 'end' => 'DTEND') as $date_k => $date_v ){
					// fix dates with no times
					if( !preg_match('/T[0-9]{6}Z?$/', $ical_event[$date_v]) ){
						$ical_event[$date_v] .= 'T000000';
					}
					//check timezone dates and get UTC timestamps of event start
					if( preg_match('/Z$/', $ical_event[$date_v]) ){
						//UTC timestamp provided
						$EM_DateTime = new EM_DateTime($ical_event[$date_v], 'UTC');
						//set timezone, once since the event has only one start/end timezone
						if( empty($item['timezone']) ){
							$this->set_ical_timezone($EM_DateTime, false, $ical);
							$item['timezone'] = $EM_DateTime->getTimezone()->getName();
						}
					}else{
						if( !empty($item['timezone']) ){
							//If we determined the TZ for the start date/time we use the same one (esp.since we have the same starting/ending timezone
							$EM_DateTime = new EM_DateTime($ical_event[$date_v], $item['timezone']);
						}else{
							//Determine if TZID provided
							if( !empty($ical_event[$date_v.'_array'][0]['TZID']) ){
								$tzid = $ical_event[$date_v.'_array'][0]['TZID'];
							}elseif( !empty($ical->cal['VCALENDAR']['TZID']) ){
								$tzid = $ical->cal['VCALENDAR']['TZID'];
							}else{
								$EM_DateTime = new EM_DateTime($ical_event[$date_v]);
							}
							if( !empty($tzid) ){
								//format date for easy creation of date
								try{
									$EM_DateTimeZone = new EM_DateTimeZone($tzid);
									$EM_DateTime = new EM_DateTime($ical_event[$date_v], $EM_DateTimeZone);
								}catch( Exception $e ){
									$EM_DateTime = new EM_DateTime($ical_event[$date_v], 'UTC');
									$timezone = $this->set_ical_timezone($EM_DateTime, $tzid, $ical);
									$EM_DateTime = new EM_DateTime($ical_event[$date_v], $timezone); // set timezone again since we now have a valid timezone to use
								}
							}
							$item['timezone'] = $EM_DateTime->getTimezone()->getName();
						}
					}
					//now finally get the UTC timestamp and timezone
					$item[$date_k] = $EM_DateTime->getTimestamp();
				}
				// handle all day event times by subtracting one day from end date, since ical supplies the end date is not inclusive
				if( !empty($item['all_day']) ){
					$item['end'] = $item['end'] - 86400;
				}
				// add description (if there is one)
				$item['content'] = !empty($ical_event['DESCRIPTION']) ? $ical_event['DESCRIPTION'] : '';
				if( !empty($ical_event['URL']) ) $item['meta'] = array('event_url' => $ical_event['URL']);
			}
			if( $this->scope == 'all' || $this->scope == 'events+locations' || $this->scope == 'locations') {
				//get the location if there is any
				if( !empty($ical_event['LOCATION']) && str_replace(array(',',' '), '', $ical_event['LOCATION']) != '' ){
					$item['location'] = array('location' => $ical_event['LOCATION']);
					//get coordinates if available, useful for accurate location parsing
					if( !empty($ical_event['GEO']) ){
						$location_geo = explode(';',$ical_event['GEO']);
						if( count($location_geo) == 2 && is_numeric($location_geo[0]) && is_numeric($location_geo[1]) ){
							$item['location']['latitude'] = $location_geo[0];
							$item['location']['longitude'] = $location_geo[1];
						}
					}
				}
			}
			if( !empty($ical_event['ATTACH_array']) ){
				foreach( $ical_event['ATTACH_array'] as $ical_attachment ){
					//for now we go through each array item and find the first attachment matching an image format.
					if( !is_array($ical_attachment) && preg_match('/http(s)?:\/\/.+\.(jpg|jpeg|png|gif|ico)(\?.+)?$/i', $ical_attachment) ){
						$item['image'] = $ical_attachment;
						break;
					}
				}
			}
			if( !empty($ical_event['CATEGORIES']) ){
				$item['categories'] = $ical_event['CATEGORIES'];
			}
			$items[] = $this->scope == 'location' ? new EMIO_Location($item['location']) : new EMIO_Event($item);
		}
		return $items;
	}
	
	/**
	 * Sets the timezone of supplied $EM_DateTime to the supplied $ical object, and if a $tzid is provided, the $ical object is searched first for a custom-defined VTIMEZONE TZID that is not PHP-compatible.
	 * This is intended for use when simply supplying EM_DateTime this specific $tzid throws an exception because it's either not a PHP-valid DateTimeZone or not UTC.
	 * This should work with RRULE and other recurring rules if defined in VTIMEZONE, as well as respecting DST obeservances within custom VTIMEZONE rules.
	 * @param EM_DateTime $EM_DateTime
	 * @param string $tzid
	 * @param ICal $ical
	 * @return bool
	 * @throws Exception
	 */
	public function set_ical_timezone($EM_DateTime, $tzid, $ical ){
		//if no tzid supplied, use ical default timezone
		if( empty($tzid) ){
			if( !empty($ical->cal['VCALENDAR']['TZID']) ){
				$tzid = $ical->cal['VCALENDAR']['TZID'];
				try{
					$EM_DateTimeZone = new EM_DateTimeZone($ical->cal['VCALENDAR']['TZID']);
					$EM_DateTime->setTimezone($EM_DateTimeZone);
					return $EM_DateTime->getTimezone()->getName();
				}catch( Exception $e ){
					//just continue
				}
			}
		}
		if( !empty($tzid) && !empty($ical->cal['VTIMEZONE']) ){
			foreach( $ical->cal['VTIMEZONE'] as $vtimezone ){
				if( !empty($vtimezone['TZID']) && $vtimezone['TZID'] == $tzid && !empty($vtimezone['OFFSETS']) ){
					//we found our timezone, so get our offsets
					$offset_array = array('ts' => $EM_DateTime->getTimestamp(), 'offset' => false);
					$offsets = array('start' => $offset_array, 'end' => $offset_array);
					$EM_DateTime_Start = $EM_DateTime->copy()->sub('P1Y');
					$EM_DateTime_End = $EM_DateTime->copy()->add('P1Y');
					if( !empty($this->vtimezones[$tzid]) && $this->vtimezones[$tzid]['range'][0] <= $EM_DateTime_Start->getTimestamp() && $this->vtimezones[$tzid]['range'][1] >= $EM_DateTime_End->getTimestamp()){
						// we have cache, use that for rrule instead
						foreach( $this->vtimezones[$tzid]['offsets'] as $offset_timestamp => $timezone_offset ){
							$offsets = $this->set_offset_vars( $offsets, $offset_timestamp, $timezone_offset );
						}
					}else{
						// first, check the cache to see if the start and end dates will work for us.
						foreach( $vtimezone['OFFSETS'] as $timezone_offset ){
							if( empty($timezone_offset['DTSTART']) ) continue;
							if( !empty($timezone_offset['RRULE']) ){
								// if using RRULE we get a range of dates based on these rules and check against them
								EMIO_Loader::libraries();
								$rrule_string = $timezone_offset['RRULE'];
								$rrule_until = $EM_DateTime_End->format('Ymd\THis\Z');
								if( !preg_match('/UNTIL:/', $rrule_string) ){
									$rrule_string .= ';UNTIL='. $rrule_until;
								}else{
									$rrule_string = preg_replace('/UNITL:[^;]+/', $rrule_until, $rrule_string);
								}
								$rrule = new RRule\RRule($rrule_string, $EM_DateTime_Start);
								foreach($rrule as $dt){ /* @var DateTime $dt */
									// add caching for this instance
									$this->set_offset_cache($tzid, $dt->getTimestamp(), $timezone_offset);
									// identify whether it's a starting or ending offset
									$offsets = $this->set_offset_vars( $offsets, $dt->getTimestamp(), $timezone_offset );
								}
							}else{
								//identify whether it's a starting or ending offset
								$dt = new EM_DateTime($timezone_offset['DTSTART'],'UTC');
								$this->set_offset_cache($tzid, $dt->getTimestamp(), $timezone_offset);
								$offsets = $this->set_offset_vars( $offsets, $dt->getTimestamp(), $timezone_offset );
							}
						}
					}
					//check whether we have an offset
					if( !empty($offsets['start']['offset']) ){
						$offset = $offsets['start']['offset'];
					}elseif( !empty($offsets['end']['offset']) ){
						$offset = $offsets['end']['offset'];
					}
					//if we have an offset, calculate a UTC-equivaelent of this
					if( !empty($offset) ){
						$offset_type = substr($offset, 0, 1);
						$offset_hour = substr($offset, 1, 2);
						$offset_min = substr($offset, 3, 2);
						$timezone = 'UTC'.$offset_type.floatval(number_format($offset_hour.'.'.$offset_min, 2));
						$EM_DateTime->setTimezone($timezone);
						return $EM_DateTime->getTimezone()->getName();
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * @param array $offsets
	 * @param int $offset_timestamp
	 * @param array $timezone_offset
	 * @return mixed
	 */
	protected function set_offset_vars( $offsets, $offset_timestamp, $timezone_offset ){
		if( $offset_timestamp < $offsets['start']['ts'] ){
			$offsets['start']['ts'] = $offset_timestamp;
			$offsets['start']['offset'] = $timezone_offset['TZOFFSETTO'];
		}
		if( $offset_timestamp > $offsets['end']['ts'] ){
			$offsets['end']['ts'] = $offset_timestamp;
			$offsets['end']['offset'] = $timezone_offset['TZOFFSETFROM'];
		}
		return $offsets;
	}
	
	/**
	 * @param string $tzid
	 * @param int $offset_timestamp
	 * @param array $timezone_offset
	 */
	protected function set_offset_cache( $tzid, $offset_timestamp, $timezone_offset ){
		if( empty($this->vtimezones[$tzid]['range']) ){
			$this->vtimezones[$tzid]['range'] = array($offset_timestamp);
		}else{
			$this->vtimezones[$tzid]['range'][1] = $offset_timestamp; // keep setting the last value in range to current loop value until it ends
		}
		$this->vtimezones[$tzid]['offsets'][$offset_timestamp] = array('TZOFFSETTO' => $timezone_offset['TZOFFSETTO'], 'TZOFFSETFROM' => $timezone_offset['TZOFFSETFROM']);
	}

	/**
	 * Fixes new lines in the ical format
	 * @param $element
	 */
	public static function fix_newlines(&$element){
		//this is the opposite of what we do to format ical strings in EM_Event::output
		//unescape characters
		$element = str_replace('\\\\', '\\', $element);
		$element = str_replace('\;', ';', $element);
		$element = str_replace('\,', ',', $element);
		//add and define line breaks from ical format
		$element = str_replace('\n', '\\\\n', $element);
		$element = str_replace('\n', "\r\n", $element);
		$element = str_replace('\n', "\n", $element);
	}
}
//register this format
EMIO_Imports::register_format('EMIO_Import_iCal');