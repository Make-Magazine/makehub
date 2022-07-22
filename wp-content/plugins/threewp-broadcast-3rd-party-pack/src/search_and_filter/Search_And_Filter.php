<?php

namespace threewp_broadcast\premium_pack\search_and_filter;

/**
	@brief			Adds support for the <a href="https://searchandfilter.com/">Search And Filter</a> plugin.
	@plugin_group	3rd party compatability
	@since			2021-08-22 19:55:47
**/
class Search_And_Filter
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
	}

	/**
		@brief		threewp_broadcast_broadcasting_before_restore_current_blog
		@since		2021-08-22 19:56:11
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;
		$new_post_id = $bcd->new_post( 'ID' );
		do_action('search_filter_update_post_cache', $new_post_id );
	}
}
