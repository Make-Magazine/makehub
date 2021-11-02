<?php

namespace threewp_broadcast\premium_pack\term_meta_attachments;

/**
	@brief			Allow taxonomy term meta containing attachment IDs to be broadcasted correctly.
	@plugin_group	Control
	@since			2021-04-08 22:01:58
**/
class Term_Meta_Attachments
	extends \threewp_broadcast\premium_pack\classes\term_meta_items\Term_Meta_Items
{
	use \threewp_broadcast\premium_pack\classes\generic_items\Replace_Attachments_Trait;

	/**
		@brief		Add the IDs found in the custom field.
		@since		2019-05-31 08:42:57
	**/
	public function add_ids( $options, $ids, $key )
	{
		$bcd = $options->broadcasting_data;
		$bcd->collection( 'term_meta_attachments' )->collection( 'ids' )->set( $key, $ids );
		$this->debug( 'Found IDs in key %s: %s', $key, $ids );
		foreach( $ids as $id )
		{
			$id = intval( $id );
			if ( $id < 1 )
			{
				$this->debug( 'Skipping image #0.' );
				continue;
			}

			if ( ! is_object( get_post( $id ) ) )
			{
				$this->debug( 'Invalid post %s.', $id );
				continue;
			}

			if ( $bcd->try_add_attachment( $id ) )
				$this->debug( 'Adding attachment data for the image %s.', $id );
		}
	}

	/**
		@brief		Return the unique settings for this class.
		@since		2019-05-31 09:08:20
	**/
	public function get_class_settings()
	{
		return (object)[
			'slug' => 'broadcast_term_meta_attachments',
			'long_name' => 'Broadcast Term Meta Attachments',
			'short_name' => 'Term Meta Attachments',
		];
	}

	/**
		@brief		Replace a single ID.
		@since		2019-05-31 08:54:27
	**/
	public function replace_id( $bcd, $id )
	{
		return $bcd->copied_attachments()->get( $id );
	}

	/**
		@brief		The site options we are expeciting to use.
		@since		2019-05-31 09:24:58
	**/
	public function site_options()
	{
		return array_merge( parent::site_options(), [
			'id_fields' => [
				'category uncategorized image',
				'category * image',
				'category author_1 gallery',
				'category author_* gallery',
			],
		] );
	}


}
