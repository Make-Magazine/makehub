<?php

namespace threewp_broadcast\premium_pack\scheduler\schedules;

/**
	@brief		An even spread of hours.
	@since		2021-07-18 22:38:01
**/
class even_spread
	extends Schedule
{
	/**
		@brief		Construct ourself.
		@since		2021-07-18 22:36:58
	**/
	public function _construct()
	{
		$this->set( 'spread_hours', '3' );
	}

	/**
		@brief		Add our inputs to the edit form.
		@since		2021-07-18 22:47:49
	**/
	public function add_form_inputs( $form )
	{
		$form->number( 'spread_hours' )
			->description( 'This is the amount of hours between each publishing date.' )
			->label( 'Hours to spread' )
			->min( 1 )
			->value( $this->get( 'spread_hours' ) );
	}

	/**
		@brief		Allow each subclass to apply it's new schedule to the new blogs.
		@since		2021-07-21 17:05:12
	**/
	public function apply_type_to_bcd( $options )
	{
		$max_time = strtotime( $options->bcd->post->post_date );
		foreach( $options->schedule as $blog_id => $time )
			$max_time = max( $max_time, $time );

		$spread_hours = $this->get( 'spread_hours' );
		foreach( $options->new_blogs as $blog_id )
		{
			$max_time = $max_time + ( $spread_hours * 3600 );
			broadcast_scheduler()->debug( 'New time for blog %s is %s (%s)', $blog_id, date( 'Y-m-d H:i:s', $max_time ), $max_time );
			$options->schedule[ $blog_id ] = $max_time;
		}
	}

	/**
		@brief		Parse any changed settings from the edit form.
		@since		2021-07-18 22:48:24
	**/
	public function parse_form_inputs( $form )
	{
		$value = $form->input( 'spread_hours' )->get_filtered_post_value();
		$this->set( 'spread_hours', $value );
	}
}
