<?php

namespace threewp_broadcast\premium_pack\more_children;

use \threewp_broadcast\broadcast_data;

/**
	@brief			Allows children to create more children of the parent.
	@plugin_group	Control
	@since			2021-06-09 20:45:23
**/
class More_Children
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box', 3 );
		$this->add_action( 'save_post', 5 );
	}

	/**
		@brief		threewp_broadcast_prepare_meta_box
		@since		2021-06-09 20:46:11
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;

		// Is there a linked parent?
		if ( $action->is_parent_post() )
			return;

		$bd = $meta_box_data->broadcast_data;
		$parent = $bd->get_linked_parent();
		$parent_bcd = ThreeWP_Broadcast()->get_parent_post_broadcast_data( $parent[ 'blog_id' ], $parent[ 'post_id' ] );

		$meta_box_data->broadcast_data = $parent_bcd;

		$meta_box_data->form->hidden_input( 'more_children' )
			->value( true );
		$meta_box_data->convert_form_input_later( 'more_children' );
	}

	/**
		@brief		Intercept save_post.
		@since		2021-06-09 21:21:33
	**/
	public function save_post( $post_id )
	{
		if ( ! isset( $_POST[ 'broadcast' ] ) )
			return;
		if ( ! isset( $_POST[ 'broadcast' ][ 'more_children' ] ) )
			return;
		$this->debug( 'Activated!' );

		$broadcast_data = ThreeWP_Broadcast()->get_post_broadcast_data( get_current_blog_id(), $post_id );

		// Now load the parent's BD
		$parent = $broadcast_data->get_linked_parent();
		$blog_id = $parent[ 'blog_id' ];
		$post_id = $parent[ 'post_id' ];

		$broadcast_data = ThreeWP_Broadcast()->get_parent_post_broadcast_data( $blog_id, $post_id );

		$blogs = $_POST[ 'broadcast' ][ 'blogs' ];
		$blogs = array_values( $blogs );

		$old_post = $_POST;
		$_POST = [];

		$this->debug( 'Broadcasting post %s on blog %s to %s', $post_id, $blog_id, $blogs );
		$api = ThreeWP_Broadcast()->api();
		$api->low_priority();

		switch_to_blog( $parent[ 'blog_id' ] );

		// Now use the API to broadcast the parent post.

		$api->broadcast_children( $post_id, $blogs );

		restore_current_blog();

		$_POST = $old_post;
	}
}
