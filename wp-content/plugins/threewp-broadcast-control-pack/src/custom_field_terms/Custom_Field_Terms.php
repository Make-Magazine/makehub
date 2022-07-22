<?php

namespace threewp_broadcast\premium_pack\custom_field_terms;

/**
	@brief			Allow post custom field containing taxonomy term IDs to be broadcasted correctly.
	@plugin_group	Control
	@since			2019-05-31 09:32:31
**/
class Custom_Field_Terms
	extends \threewp_broadcast\premium_pack\classes\custom_field_items\Custom_Field_Items
{
	use \threewp_broadcast\premium_pack\classes\generic_items\Replace_Terms_Trait;

	/**
		@brief		Add the IDs found in the custom field.
		@since		2019-05-31 08:42:57
	**/
	public function add_ids( $options, $ids, $key )
	{
		$taxonomies_to_sync = [];
		foreach( $ids as $id )
		{
			$term = get_term( $id );
			$taxonomies_to_sync [ $term->taxonomy ]= $term->taxonomy;
		}
		$this->debug( 'Also syncing: %s', $taxonomies_to_sync );
		$bcd = $options->broadcasting_data;
		foreach( $taxonomies_to_sync as $taxonomy )
			$bcd->taxonomies()->also_sync( null, $taxonomy );
		$bcd->taxonomies()->use_terms( $ids );
	}

	/**
		@brief		Return the unique settings for this class.
		@since		2019-05-31 09:08:20
	**/
	public function get_class_settings()
	{
		return (object)[
			'slug' => 'broadcast_custom_field_terms',
			'long_name' => 'Broadcast Custom Field Terms',
			'short_name' => 'Custom Field Terms',
		];
	}
}
