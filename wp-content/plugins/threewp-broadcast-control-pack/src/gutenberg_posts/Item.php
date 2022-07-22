<?php

namespace threewp_broadcast\premium_pack\gutenberg_posts;

/**
	@brief		Gutenberg Item.
	@since		2019-07-30 20:30:20
**/
class Item
	extends \threewp_broadcast\premium_pack\classes\gutenberg_items\Item
{
	/**
		@brief		Apply a wizard.
		@since		2019-07-30 20:30:20
	**/
	public function apply_wizard( $type )
	{
		switch( $type )
		{
			case 'block':
				$this->set_slug( 'block' );
				$this->add_value( 'ref' );
				break;
			default:
				$this->set_slug( 'item' );
				$this->add_value( 'id' );
		}
	}

	/**
		@brief		Return which wizards are available.
		@since		2019-07-30 20:31:43
	**/
	public function get_wizard_options()
	{
		$array = [
			'Empty' => '',
			'Reusable block' => 'block',
		];

		$r = [];

		foreach( $array as $index => $data )
			if ( $data != '' )
				$r [ sprintf( '%s (%s)', $index, $data ) ] = $data;
			else
				$r [ $index ] = $data;

		return $r;
	}
}
