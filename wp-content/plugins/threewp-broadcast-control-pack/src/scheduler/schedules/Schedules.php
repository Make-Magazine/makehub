<?php

namespace threewp_broadcast\premium_pack\scheduler\schedules;

/**
	@brief		Container for schedules.
	@since		2021-07-18 21:34:40
**/
class Schedules
	extends \threewp_broadcast\collection
{
	use \plainview\sdk_broadcast\wordpress\object_stores\Site_Option;

	public function append( $term )
	{
		$this->set( $term->get( 'id' ), $term );
	}

	public static function store_container()
	{
		return broadcast_scheduler();
	}

	public static function store_key()
	{
		return 'schedules';
	}
}
