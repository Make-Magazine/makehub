<?php

namespace threewp_broadcast\premium_pack\parent_pull;

/**
	@brief		A parent pull setup.
	@since		2020-06-21 20:44:32
**/
class Pull_Setup
	extends \threewp_broadcast\collection
{
	public function __construct()
	{
		parent::__construct();
		$this->new_id();
		$this->set( 'description', sprintf( __( 'Pull setup created %s', 'threewp_broadcast' ), date( 'Y-m-d H:i:s' ) ) );
		$this->set( 'parent_blogs', [ 1 ] );		// Main blog as the default parent.
		$this->set( 'post_types', "post\npage" );
	}

	/**
		@brief		Return all of the parent blogs available.
		@since		2020-06-22 21:27:36
	**/
	public function get_available_parents( $blog_id = false )
	{
		if ( ! $blog_id )
			$blog_id = get_current_blog_id();
		if ( ! $this->has_child_blog( $blog_id ) )
			return [];
		$parent_blogs = $this->get( 'parent_blogs', [] );
		if ( count( $parent_blogs ) < 1 )
			return [ 1, 2, 3 ];
		return $parent_blogs;
	}

	/**
		@brief		Return all of the post types on this blog.
		@since		2020-06-22 21:42:00
	**/
	public function get_available_post_types( $blog_id = false )
	{
		if ( ! $blog_id )
			$blog_id = get_current_blog_id();
		if ( ! $this->has_child_blog( $blog_id ) )
			return [];
		return $this->get_post_types();
	}

	/**
		@brief		Return a text of the selected blogs.
		@since		2020-06-21 21:56:37
	**/
	public function get_blogs_text( $type )
	{
		$user_writeable_blogs = new \threewp_broadcast\actions\get_user_writable_blogs;
		$user_writeable_blogs->user_id = Parent_Pull::instance()->user_id();
		$user_writeable_blogs = $user_writeable_blogs->execute()->blogs;

		$blogs = $this->get( $type, [] );
		$r = [];

		foreach( $blogs as $blog_id )
		{
			$blog = $user_writeable_blogs->get( $blog_id );
			$r []= sprintf( '%s (%s)', $blog->blogname, $blog_id );
		}

		if ( count( $r ) < 1 )
			return 'All';

		ksort( $r );

		$r = implode( "\n", $r );
		return $r;
	}

	/**
		@brief		Return a text describing the selected child blogs.
		@since		2020-06-21 21:53:34
	**/
	public function get_child_blogs_text()
	{
		return $this->get_blogs_text( 'child_blogs' );
	}

	/**
		@brief		Return a text describing the selected parent blogs.
		@since		2020-06-21 21:53:34
	**/
	public function get_parent_blogs_text()
	{
		return $this->get_blogs_text( 'parent_blogs' );
	}

	/**
		@brief		Return an array of selected post types.
		@since		2020-06-21 22:02:21
	**/
	public function get_post_types()
	{
		$post_types = $this->get( 'post_types' );
		$post_types = str_replace( "\r", '', $post_types );
		$post_types = explode( "\n", $post_types );
		$post_types = array_filter( $post_types );
		return $post_types;
	}

	/**
		@brief		Return a text of the selected post types.
		@since		2020-06-21 22:01:35
	**/
	public function get_post_types_text()
	{
		$post_types = $this->get_post_types();

		if ( count( $post_types ) < 1 )
			return 'None';

		return implode( "\n", $post_types );
	}

	/**
		@brief		Return whether the specified blog is listed in the child blog list.
		@since		2020-06-22 21:46:46
	**/
	public function has_child_blog( $blog_id )
	{
		$child_blogs = $this->get( 'child_blogs', [] );
		if ( count( $child_blogs ) < 1 )
			return true;
		return in_array( $blog_id, $child_blogs );
	}

	public function new_id()
	{
		$this->set( 'id', time() . rand( 1000, 9999 ) );
	}
}
