<?php

namespace threewp_broadcast\premium_pack\gutenberg_protect;

/**
	@brief			Protects specific Gutenberg blocks from being overwritten during broadcasting.
	@plugin_group	Control
	@since			2020-03-18 12:11:01
**/
class Gutenberg_Protect
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_broadcasting_after_switch_to_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_modify_post', 100 );	// Wait until everyone else is done.
		$this->add_action( 'threewp_broadcast_menu' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		threewp_broadcast_broadcasting_after_switch_to_blog
		@since		2020-03-18 12:12:13
	**/
	public function threewp_broadcast_broadcasting_after_switch_to_blog( $action )
	{
		$bcd = $action->broadcasting_data;
		$this->protected_blocks = [];

		// Does this child exist?
		$child_post_id = $bcd->broadcast_data->get_linked_child_on_this_blog();
		if ( ! $child_post_id )
			return;

		$block_ids = $this->get_block_ids();

		$this->debug( 'Block IDs: %s', $block_ids );

		// Find all blocks.
		$child_post = get_post( $child_post_id );
		$blocks = ThreeWP_Broadcast()->gutenberg()->parse_blocks( $child_post->post_content );

		if ( count( $blocks ) < 1 )
			return;

		foreach( $blocks as $block )
		{
			$block_id = $block[ 'attrs' ][ 'id' ];
			if ( ! in_array( $block_id, $block_ids ) )
				continue;

			$this->debug( 'Protecting block %s', $block_id );
			$this->protected_blocks[ $block_id ] = $block;
		}
	}

	/**
		@brief		threewp_broadcast_broadcasting_modify_post
		@since		2020-03-18 12:12:00
	**/
	public function threewp_broadcast_broadcasting_modify_post( $action )
	{
		if ( count( $this->protected_blocks ) < 1 )
			return;

		$bcd = $action->broadcasting_data;
		$modified_post = $bcd->modified_post;
		$modified_post_content = $modified_post->post_content;

		// Find all of the blocks again
		$blocks = ThreeWP_Broadcast()->gutenberg()->parse_blocks( $modified_post_content );

		// Replace the protected blocks with our stuff.
		foreach( $blocks as $block )
		{
			$block_id = $block[ 'attrs' ][ 'id' ];
			if ( ! isset( $this->protected_blocks[ $block_id ] ) )
				continue;
			$this->debug( 'Restoring %s', $block_id );
			$modified_post_content = ThreeWP_Broadcast()->gutenberg()->replace_text_with_block( $block[ 'original' ], $this->protected_blocks[ $block_id ], $modified_post_content );
		}

		$bcd->modified_post->post_content = $modified_post_content;
	}

	/**
		@brief		Add ourself to the menu.
		@since		2020-03-18 12:15:00
	**/
	public function threewp_broadcast_menu( $action )
	{
		// Only super admin is allowed to see us.
		if ( ! is_super_admin() )
			return;

		$action->menu_page
			->submenu( 'broadcast_gutenberg_protect' )
			->callback_this( 'settings' )
			// Menu item for menu
			->menu_title( __( 'Gutenberg Protect', 'threewp_broadcast' ) )
			// Page title for menu
			->page_title( __( 'Broadcast Gutenberg Protect', 'threewp_broadcast' ) );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Convenience function to retrieve only block IDs.
		@since		2020-03-18 15:57:55
	**/
	public function get_block_ids()
	{
		$block_ids = $this->get_site_option( 'block_ids' );
		$block_ids = array_filter( $block_ids );
		foreach( $block_ids as $index => $block_id )
		{
			$block_ids[ $index ] = trim( $block_id );
			if ( strpos( '#', $block_id ) !== false )
				unset( $block_ids[ $index ] );
		}
		return $block_ids;
	}

	/**
		@brief		Show the settings page.
		@since		2020-03-18 15:48:36
	**/
	public function settings()
	{
		$form = $this->form();
		$r = '';

		$r .= wpautop( 'If you have Gutenberg blocks on child posts that should retain their info instead of being overwritten from the parent post, specify their block IDs in the text area below.' );

		$block_ids = $this->get_site_option( 'block_ids' );
		$block_ids = implode( "\n", $block_ids );
		$input_block_ids = $form->textarea( 'block_ids' )
			->description( __( "Protect the blocks with the specified IDs from being overwritten on the child post.", 'threewp_broadcast' ) )
			->cols( 40, 10 )
			->label( __( 'Block IDs to protect', 'threewp_broadcast' ) )
			->value( $block_ids );

		$save = $form->primary_button( 'save' )
			->value( __( 'Save settings', 'threewp_broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$value = $input_block_ids->get_filtered_post_value();
			$value = explode( "\n", $value );
			$this->update_site_option( 'block_ids', $value );

			$this->info_message_box()->_( 'Settings saved!' );
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $this->wrap( $r, __( 'Broadcast Gutenberg Protect', 'threewp_broadcast' ) );
	}

	/**
		@brief		Options we store.
		@since		2020-03-18 15:52:26
	**/
	public function site_options()
	{
		return array_merge( [
			/**
				@brief		Which block IDs we are to protect.
				@since		2020-03-18 15:52:39
			**/
			'block_ids' => [
				'# This is the testimonial block',
				'block_5e21c87155e22',
			],
		], parent::site_options() );
	}
}	// class
