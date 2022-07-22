<?php

namespace threewp_broadcast\premium_pack\comments\actions;

use threewp_broadcast\premium_pack\comments\Comments;

/**
	@brief		Sync the comments of a child post with the parent.
	@since		2014-12-28 11:44:19
**/
class sync_comments
	extends action
{
	/**
		@brief		IN, OPTIONAL: The ID of the child blog.
		@details	If this is set, switch_to_blog will be called.
		@since		2014-12-28 11:44:43
	**/
	public $child_blog_id = null;

	/**
		@brief		IN: The ID of the post which will receive the new comments.
		@since		2014-12-28 11:44:56
	**/
	public $child_post_id;

	/**
		@brief		An collection of equivalent comment IDs.
		@since		2017-09-26 23:09:56
	**/
	public $equivalent_comment_ids;

	/**
		@brief		IN, OPTIONAL: ID of parent blog.
		@details	Either set the parent_* properties or the $comments property.
		@since		2014-12-28 11:45:26
	**/
	public $parent_blog_id = null;

	/**
		@brief		IN, OPTIONAL: ID of parent post.
		@details	Either set the parent_* properties or the $comments property.
		@since		2014-12-28 11:45:29
	**/
	public $parent_post_id = null;

	/**
		@brief		Delete the existing comments before copying.
		@since		2014-12-28 11:45:31
	**/
	public $delete_existing_comments = false;

	/**
		@brief		IN, OPTIONAL: An array of comments to insert to the child.
		@details	If this is not set, the comment array will be fetched from the parent blog post.
		@since		2014-12-28 11:45:33
	**/
	public $comments = null;

	/**
		@brief		Constructor.
		@since		2017-09-26 23:10:49
	**/
	public function _construct()
	{
		$this->equivalent_comment_ids = ThreeWP_Broadcast()->collection();
	}

	/**
		@brief		Add (generate) all of the IDs for these columns.
		@since		2021-02-27 20:21:04
	**/
	public function add_comment_ids( $comments )
	{
		$blog_id = get_current_blog_id();

		foreach( $comments as $index => $comment )
		{
			$comment_hash = Comments::generate_comment_hash( $comment );
			$this->add_equivalent_comment_id( $blog_id, $comment_hash, $blog_id, $comment->comment_ID );
		}
	}

	/**
		@brief		Return the comment array.
		@since		2020-01-19 22:08:29
	**/
	public function get_comments()
	{
		return $this->comments;
	}

	/**
		@brief		Set the ID of the child blog.
		@since		2014-12-28 11:50:33
	**/
	public function set_child_blog_id( $child_blog_id )
	{
		$this->child_blog_id = $child_blog_id;
	}

	/**
		@brief		Set the ID of the child post which will receive the new comments.
		@since		2014-12-28 11:50:06
	**/
	public function set_child_post_id( $child_post_id )
	{
		$this->child_post_id = $child_post_id;
	}

	/**
		@brief		Set the comments array.
		@since		2014-12-28 11:49:26
	**/
	public function set_comments( $comments )
	{
		$this->comments = $comments;
		$this->add_comment_ids( $comments );
	}

	/**
		@brief		Convenience method to add an equivalent comment ID.
		@since		2017-09-26 23:11:09
	**/
	public function add_equivalent_comment_id( $blog_id, $comment_hash, $comment_id )
	{
		$this->equivalent_comment_ids->collection( $blog_id )
			->set( $comment_hash, $comment_id );
	}

	/**
		@brief		Return the equivalent comment ID.
		@since		2020-01-19 22:01:34
	**/
	public function get_equivalent_comment_id( $blog_id, $comment_hash )
	{
		return $this->equivalent_comment_ids->collection( $blog_id )
			->get( $comment_hash );
	}
}