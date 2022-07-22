<?php

namespace threewp_broadcast\premium_pack\scheduler\schedules;

/**
	@brief		A randomized schedule.
	@since		2021-07-18 22:38:01
**/
class random
	extends Schedule
{
	/**
		@brief		Construct ourself.
		@since		2021-07-18 22:36:58
	**/
	public function _construct()
	{
		$this->set( 'random_hours', 72 );
		$this->set( 'minimum_hours', 0 );
	}

	/**
		@brief		Add our inputs to the edit form.
		@since		2021-07-18 22:47:49
	**/
	public function add_form_inputs( $form )
	{
		$form->number( 'random_hours' )
			->description( 'This is the maximum amount of hours to randomize the publishing date on each blog.' )
			->label( 'Hours to randomize' )
			->min( 1 )
			->value( $this->get( 'random_hours' ) );
		$form->number( 'minimum_hours' )
			->description( 'Apply at least these many hours to the publishing date.' )
			->label( 'Minimum hours' )
			->min( 0 )
			->value( $this->get( 'minimum_hours' ) );
	}

	/**
		@brief		Allow each subclass to apply it's new schedule to the new blogs.
		@since		2021-07-21 17:05:12
	**/
	public function apply_type_to_bcd( $options )
	{
		$random_hours = $this->get( 'random_hours' );
		$minimum_hours = $this->get( 'minimum_hours' );
		$post_time = strtotime( $options->bcd->post->post_date );
		foreach( $options->new_blogs as $blog_id )
		{
			$random_hour = rand( 1, $random_hours );
			$new_time = $post_time + ( $random_hour * 3600 ) + ( $minimum_hours * 3600 );
			broadcast_scheduler()->debug( 'New time for blog %s is %s hours (%s )', $blog_id, $random_hour, $new_time );
			$options->schedule[ $blog_id ] = $new_time;
		}
	}

	/**
		@brief		Parse any changed settings from the edit form.
		@since		2021-07-18 22:48:24
	**/
	public function parse_form_inputs( $form )
	{
		$value = $form->input( 'random_hours' )->get_filtered_post_value();
		$value = intval( $value );
		$this->set( 'random_hours', $value );
		$value = $form->input( 'minimum_hours' )->get_filtered_post_value();
		$value = intval( $value );
		$this->set( 'minimum_hours', $value );
	}
}
