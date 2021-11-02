<?php

namespace threewp_broadcast\premium_pack\scheduler\schedules;

/**
	@brief		The basic schedule.
	@since		2021-07-18 22:36:26
**/
abstract class Schedule
	extends \threewp_broadcast\collection
{
	public function __construct()
	{
		parent::__construct();
		$this->new_id();
		$this->set( 'description', sprintf( __( 'Created %s', 'threewp_broadcast' ), date( 'Y-m-d H:i:s' ) ) );
		$this->_construct();
	}

	/**
		@brief		Construct ourself.
		@since		2021-07-18 22:36:58
	**/
	public abstract function _construct();

	/**
		@brief		Add our inputs to the edit form.
		@since		2021-07-18 22:47:49
	**/
	public function add_form_inputs( $form )
	{
	}

	/**
		@brief		Apply this schedule to the broadcasting data.
		@since		2021-07-20 23:03:23
	**/
	public function apply_to_bcd( $bcd )
	{
		$o = (object)[];	// Options
		$key = Broadcast_Scheduler()::$schedule_meta_key;
		$o->schedule = $bcd->custom_fields()->get_single( $key );
		if ( ! is_array( $o->schedule ) )
			$o->schedule = [];

		$o->new_blogs = [];
		foreach( $bcd->blogs as $blog_id => $ignore )
		{
			if ( ! isset( $o->schedule[ $blog_id ] ) )
				$o->new_blogs [ $blog_id ]= $blog_id;
		}
		$o->bcd = $bcd;

		$this->apply_type_to_bcd( $o );

		$bcd->custom_fields()->set( $key, $o->schedule );

		$key = Broadcast_Scheduler()::$schedule_id_meta_key;
		$schedule_id = $this->get( 'id' );
		$bcd->custom_fields()->set( $key, $schedule_id );
	}

	/**
		@brief		Allow each subclass to apply it's new schedule to the new blogs.
		@since		2021-07-21 17:05:12
	**/
	public function apply_type_to_bcd( $options )
	{
	}

	/**
		@brief		Create a schedule of this type.
		@since		2021-07-18 22:37:28
	**/
	public static function create_type( $type )
	{
		$classname = __NAMESPACE__ . '\\' . $type;
		$s = new $classname();
		$s->set( 'type', $type );
		return $s;
	}

	/**
		@brief		Return the types of schedules.
		@since		2021-07-18 22:10:08
	**/
	public static function get_types()
	{
		return [
			'even_spread' => 'Fixed delay between each blog.',
			'random' => 'Randomize each blog a certain amount of hours.',
		];
	}

	/**
		@brief		Return the description of this schedule type.
		@since		2021-07-18 22:44:49
	**/
	public function get_type_description()
	{
		$types = static::get_types();
		$type = $this->get( 'type' );
		return $types[ $type ];
	}

	public function new_id()
	{
		$this->set( 'id', time() . rand( 1000, 9999 ) );
	}

	/**
		@brief		Parse any changed settings from the edit form.
		@since		2021-07-18 22:48:24
	**/
	public function parse_form_inputs( $form )
	{
	}
}
