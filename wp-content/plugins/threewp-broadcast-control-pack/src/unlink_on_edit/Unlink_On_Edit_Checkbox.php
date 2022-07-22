<?php

namespace threewp_broadcast\premium_pack\unlink_on_edit;

/**
	@brief			Unlink a child post when the post is modified - checkbox version
	@plugin_group	Control
	@since			2018-08-24 22:35:38
**/
class Unlink_On_Edit_Checkbox
	extends Unlink_On_Edit_Base
{
	/**
		@brief		The name of the custom field in which the data is stored.
		@since		2020-05-28 09:58:31
	**/
	public static $custom_field = 'broadcast_unlink_on_edit';

	public function _construct()
	{
		parent::_construct();
		$this->add_filter( 'broadcast_unlink_on_edit_post_updated', 'broadcast_unlink_on_edit_post_updated_checkbox', 10, 3 );
		$this->add_filter( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Check if the custom field is set.
		@since		2020-05-28 09:57:43
	**/
	public function broadcast_unlink_on_edit_post_updated_checkbox( $modified, $before, $after )
	{
		$cf = get_post_meta( $before->ID, static::$custom_field, true );

		$modified = true;

		// If the custom field is not specifically set to yes, then do not regard the post as modified.
		if ( $cf != 'yes' )
			$modified = false;

		$this->debug( 'Modified is: %s', $modified );

		return $modified;
	}

	/**
		@brief		Save the checkbox.
		@since		2020-05-28 10:04:36
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;
		$name = 'unlink_on_edit';

		$input = $bcd->meta_box_data->form->input( $name );
		if ( ! $input )
			return;

		if ( $input->is_checked() )
		{
			$this->debug( 'Unlinking children on edit.' );
			$bcd->custom_fields()->set( static::$custom_field, 'yes' );
		}
		else
		{
			$this->debug( 'Not unlinking children on edit.' );
			$bcd->custom_fields()->forget( static::$custom_field );
		}

	}

	/**
		@brief		Add the input.
		@since		2014-12-11 11:39:19
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$mbd = $action->meta_box_data;
		$form = $mbd->form;

		$name = 'unlink_on_edit';
		$mbd->$name = $form->checkbox( $name )
			->checked( isset( $mbd->last_used_settings[ $name ] ) )
			->label( __( 'Unlink on edit', 'threewp_broadcast' ) )
			->title( __( 'Unlink the child post when it is edited.', 'threewp_broadcast' ) );

		$mbd->html->insert_before( 'blogs', $name, $mbd->$name );
	}
}
