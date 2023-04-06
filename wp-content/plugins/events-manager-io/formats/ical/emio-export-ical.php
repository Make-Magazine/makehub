<?php
/**
 * Class EMIO_Format_iCal
 */
class EMIO_Export_iCal extends EMIO_Export {
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
	 *
	 */
	public static $mime_type = array('text/calendar');
	/**
	 * iCal can only export events with or without locations, it cannot export locations without events, as locations must belong to an event.
	 * @var array
	 */
	public static $supports = array('events' => array('locations'));
	
	public $export_event_ids = array();
	
	/**
	 * Takes the provided Data in an array format and exports the format. Makes use of EM's ical feed
	 * @param bool $output_headers
	 * @return bool:
	 */
	public function export_done( $output_headers = true ){
		remove_all_filters('em_calendar_template_args'); //here we ensure only the export filter options are adhered to, not site-wide options for ical output
		add_filter('em_calendar_template_args', array($this, 'em_calendar_template_args'));
		add_filter('pre_option_dbem_ical_limit', array($this, 'pre_option_dbem_ical_limit'));
		em_locate_template('templates/ical.php', true);
		return true;
	}
	
	public function pre_option_dbem_ical_limit( $val ){
		return count($this->export_event_ids);
	}
	
	public function em_calendar_template_args( $args ){
		return $this->export_event_ids;
	}
	
	public function export_event( $EM_Event, $update_id = null ){
		$this->export_event_ids[$EM_Event->event_id] = $EM_Event->event_id;
		return true;
	}
	
}
//register this format
EMIO_Exports::register_format('EMIO_Export_iCal');