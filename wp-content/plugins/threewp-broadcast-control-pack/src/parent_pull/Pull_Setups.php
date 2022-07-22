<?php

namespace threewp_broadcast\premium_pack\parent_pull;

/**
	@brief		Store all of the pull setup.
	@since		2020-06-21 20:44:09
**/
class Pull_Setups
	extends \threewp_broadcast\collection
{
	use \plainview\sdk_broadcast\wordpress\object_stores\Site_Option;

	public function append( $pull_setup )
	{
		$this->set( $pull_setup->get( 'id' ), $pull_setup );
	}

	/**
		@brief		Return an array of all available parent blogs for the specified blog.
		@since		2020-06-22 21:25:54
	**/
	public function get_available_parents( $blog_id = false)
	{
		if ( ! $blog_id )
			$blog_id = get_current_blog_id();
		$r = [];
		foreach( $this as $pull_setup )
			$r = array_merge( $r, $pull_setup->get_available_parents( $blog_id ) );
		return $r;
	}

	/**
		@brief		Return an array of all post types on this blog.
		@since		2020-06-22 21:40:35
	**/
	public function get_available_post_types( $blog_id = false)
	{
		if ( ! $blog_id )
			$blog_id = get_current_blog_id();
		$r = [];
		foreach( $this as $pull_setup )
			$r = array_merge( $r, $pull_setup->get_available_post_types( $blog_id ) );
		$r = array_unique( $r );
		return $r;
	}

	public static function store_container()
	{
		return Parent_Pull::instance();
	}

	public static function store_key()
	{
		return 'pull_setups';
	}
}
