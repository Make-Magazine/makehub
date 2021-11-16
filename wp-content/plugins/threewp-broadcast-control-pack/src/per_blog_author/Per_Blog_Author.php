<?php

namespace threewp_broadcast\premium_pack\per_blog_author;

/**
	@brief			Allows for individual control of the author for each child post.
	@plugin_group	Control
	@since		2020-11-11 19:57:14
**/
class Per_Blog_Author
extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_broadcasting_modify_post' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box', 50 );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		threewp_broadcast_broadcasting_modify_post
		@since		2020-11-11 19:57:14
	**/
	public function threewp_broadcast_broadcasting_modify_post( $action )
	{
		$bcd = $action->broadcasting_data;
		$meta_box_data = $bcd->meta_box_data;

		if ( ! isset( $meta_box_data->form->per_blog_author ) )
			return;

		$blog_id = get_current_blog_id();
		$pba = $meta_box_data->form->per_blog_author;
		$select = $pba->get( $blog_id );

		$author_id = $select->get_filtered_post_value();
		$this->debug( 'Setting author: %s', $author_id );
		$bcd->modified_post->post_author = $author_id;
	}

	/**
		@brief		Add author control to the blogs input.
		@since		2020-11-11 18:57:56
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$authors = [];
		foreach( get_users() as $user )
			$authors[ $user->data->ID ] = $user->data->display_name;

		$form = $action->meta_box_data->form;
		$meta_box_data = $action->meta_box_data;
		$post = $action->meta_box_data->post;

		// This is used to cache the inputs for later.
		$form->per_blog_author = ThreeWP_Broadcast()->collection();

		foreach( $form->checkboxes( 'blogs' )->inputs() as $blog_input )
		{
			$blog_id = $blog_input->get_value();
			$blog_name = $blog_input->label->content;
			$blog_name = strip_tags( $blog_name );

			switch_to_blog( $blog_id );
			$child_post_id = $action->meta_box_data->broadcast_data->get_linked_post_on_this_blog();
			$child_post = get_post( $child_post_id );
			$child_author = get_current_user_id();
			if ( $child_post )
				$child_author = $child_post->post_author;
			restore_current_blog();

			$selector_name = sprintf( 'pba_blog_%s_author_selector', $blog_id );
			$selector = $form->select( $selector_name )
				->css_class( 'author_selector', $blog_id )
				->set_attribute( 'data-blog_id', $blog_id )
				// Select label: Category is set manually
				->label_( __( 'Author on %s', 'threewp_broadcast'), $blog_name )
				// Category is: set automatically
				->opts( $authors )
				->value( $child_author );
			$form->per_blog_author->set( $blog_id, $selector );
			$meta_box_data->convert_form_input_later( $selector_name );
		}

		$action->meta_box_data->js->put( 'per_blog_author', $this->paths[ 'url' ] . '/per_blog_author.js' );
	}
}
