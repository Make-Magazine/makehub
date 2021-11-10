<?php

namespace threewp_broadcast\premium_pack\comments
{

/**
	@brief			Broadcasting and sync comments between linked posts.
	@plugin_group	Control
	@since			2014-05-20 18:15:43
**/
class Comments
	extends \threewp_broadcast\premium_pack\base
{
	public static $meta_key = '_broadcast_comments_sync';

	/**
		@brief		The type of queue data we handle.
		@since		2017-09-24 19:34:57
	**/
	public static $queue_data_type = 'comments';

	/**
		@brief		An array of comment IDs that have already been synced.
		@since		2014-12-28 13:23:54
	**/
	public $__synced_comments = [];

	/**
		@brief		Are we busy syncing comments?
		@since		2014-12-28 13:23:25
	**/
	public $__syncing = false;

	public function _construct()
	{
		$this->add_action( 'save_post' );

		$this->add_action( 'broadcast_comments_prepare_sync', 100 );
		$this->add_action( 'broadcast_comments_sync_comments', 100 );
		$this->add_action( 'broadcast_queue_display_settings' );
		$this->add_action( 'broadcast_queue_process_data_item' );
		$this->add_action( 'broadcast_queue_save_settings' );
		$this->add_action( 'broadcast_queue_show_queue_table_data' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );

		// These hooks are used when comments are created / updated / deleted
		$this->add_action( 'comment_post', 'maybe_resync_comments', 100 );
		$this->add_action( 'transition_comment_status', 'transition_comment_status', 100, 3 );
/**
		$this->add_action( 'edit_comment', 'maybe_resync_comments', 100 );
		$this->add_action( 'trashed_comment', 'maybe_resync_comments', 100 );
		$this->add_action( 'untrashed_comment', 'maybe_resync_comments', 100 );
		$this->add_action( 'wp_insert_comment', 'maybe_resync_comments', 100 );
**/
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		broadcast_comments_prepare_sync
		@since		2017-09-26 22:08:51
	**/
	public function broadcast_comments_prepare_sync( $action )
	{
		// Always be gracious to other plugins that might have handled this action before us.
		if ( $action->is_finished() )
			return;

		$broadcast_data = ThreeWP_Broadcast()->get_post_broadcast_data( $action->blog_id, $action->post_id );

		if ( $action->sync_type == 'from_parent' )
		{
			// User wants comments synced only from the parent.
			if ( ! $broadcast_data->has_linked_children() )
				return $this->debug( 'Post %s does not have any linked children.', $post_id );
			$action->sync_targets = $broadcast_data->get_linked_children();
		}

		if ( $action->sync_type == 'both' )
		{
			// If this post is a parent, fetch all of the children.
			if ( $broadcast_data->has_linked_children() )
				$action->sync_targets = $broadcast_data->get_linked_children();
			else
			{
				// Retrieve the BCD of the parent post.
				$parent = $broadcast_data->get_linked_parent();
				if ( ! $parent )	// This should never happen, but let's be defensive.
					return;
				$broadcast_data = ThreeWP_Broadcast()->get_post_broadcast_data( $parent[ 'blog_id' ], $parent[ 'post_id' ] );
				$action->sync_targets = $broadcast_data->get_linked_children();
				// Don't forget to broadcast to the parent.
				$action->sync_targets[ $parent[ 'blog_id' ] ] = $parent[ 'post_id' ];
				// Don't resync this child's comments.
				unset( $action->sync_targets[ $action->blog_id ] );
			}
		}

		$action->sync_targets = array_filter( $action->sync_targets );
	}

	/**
		@brief		Sync comments to a child post.
		@since		2014-12-28 11:52:33
	**/
	public function broadcast_comments_sync_comments( $action )
	{
		// Always be gracious to other plugins that might have handled this action before us.
		if ( $action->is_finished() )
			return;

		// To prevent recursion.
		$this->__syncing = true;

		// Do we need to fetch the comments from the parent post?
		if ( ! is_array( $action->comments ) )
		{
			if ( $action->parent_blog_id > 0 )
				switch_to_blog( $action->parent_blog_id );

			$action->comments = static::get_comments( $action->parent_post_id );

			if ( $action->parent_blog_id > 0 )
				restore_current_blog();
		}

		// We now have the comments.
		if ( $action->child_blog_id > 0 )
			switch_to_blog( $action->child_blog_id );

		if ( $action->delete_existing_comments )
		{
			$comments = static::get_comments( $action->child_post_id );
			foreach( $comments as $comment )
			{
				$this->debug( 'Deleting existing child comment %s.', $comment->comment_ID );
				wp_delete_comment( $comment->comment_ID, true );		// True to force delete.
			}

			$this->debug( 'Done deleting.' );

			// Comments are deleted! This is for queue compatibility.
			$action->delete_existing_comments = false;
		}
		else

		// Learn which comments we have and generate their IDs.
		$comments = static::get_comments( $action->child_post_id );
		foreach( $comments as $comment )
		{
			$comment_hash = static::generate_comment_hash( $comment );
			$action->add_equivalent_comment_id( $action->child_blog_id, $comment_hash, $comment->comment_ID );
		}

		// Insert these new comments
		foreach( $action->comments as $index => $comment )
		{
			$comment = clone( $comment );

			$comment_hash = static::generate_comment_hash( $comment );

			$existing_comment_id = $action->get_equivalent_comment_id( $action->child_blog_id, $comment_hash );
			if ( $existing_comment_id < 1 )
			{
				// The post ID must be updated for this new post.
				$comment->comment_post_ID = $action->child_post_id;

				// Update the comment parent if necessary.
				$old_comment_parent = $comment->comment_parent;
				if ( $old_comment_parent > 0 )
				{
					$parent_comment = $action->comments[ $old_comment_parent ];
					$parent_hash = static::generate_comment_hash( $parent_comment );
					$new_comment_parent = $action->get_equivalent_comment_id( $action->child_blog_id, $parent_hash );
					$this->debug( 'Setting new comment parent for %s to %s', $comment->comment_ID, $new_comment_parent );
					$comment->comment_parent = $new_comment_parent;
				}

				// The comment ID should be removed.
				$old_comment_id = $comment->comment_ID;
				unset( $comment->comment_ID );

				$new_comment_id = wp_insert_comment( (array)$comment );
				$this->debug( 'Inserted comment %s.', $new_comment_id );

				$comments[ $new_comment_id ] = $comment;

				// Update the index.
				$this->debug( 'New comment ID for comment %s is %s', $old_comment_id, $new_comment_id );
				$action->add_equivalent_comment_id( $action->child_blog_id, $comment_hash, $new_comment_id );
			}
			else
				$new_comment_id = $existing_comment_id;

			// Delete all existing meta.
			$metas = get_comment_meta( $new_comment_id );
			foreach( $metas as $meta_key => $ignore )
			{
				$this->debug( 'Deleting existing meta %s', $meta_key );
				delete_comment_meta( $new_comment_id, $meta_key );
			}

			// Insert the comment meta.
			$this->debug( 'Inserting comment meta for comment %s: %s', $new_comment_id, $comment->meta );
			foreach( $comment->meta as $meta_key => $meta_values )
			{
				$meta_value = reset( $meta_values );
				$meta_value = maybe_unserialize( $meta_value );
				update_comment_meta( $new_comment_id, $meta_key, $meta_value );
			}
		}

		if ( $action->child_blog_id > 0 )
			restore_current_blog();

		$this->__syncing = false;
	}

	/**
		@brief		broadcast_queue_display_settings
		@since		2017-12-20 14:54:12
	**/
	public function broadcast_queue_display_settings( $action )
	{
		$form = $action->form;

		$fs = $form->fieldset( 'fs_comments' )
			// Fieldset label
			->label( __( 'Comments', 'threewp_broadcast' ) );

		$fs->checkbox( 'use_comment_queue' )
			->checked( $this->get_site_option( 'use_comment_queue' ) )
			// Input title / description
			->description( __( 'Use the queue system for comments.', 'threewp_broadcast' ) )
			// Input label
			->label( __( 'Enabled for comments', 'threewp_broadcast' ) );
	}

	/**
		@brief		broadcast_queue_process_data_item
		@since		2017-08-13 21:45:18
	**/
	public function broadcast_queue_process_data_item( \threewp_broadcast\premium_pack\queue\actions\process_data_item $action )
	{
		if ( $action->data->type != static::$queue_data_type )
			return;

		$this->debug( 'Preparing to broadcast comment.' );

		$sync_comments_action = $action->data->uncompress();

		$start = time();
		do
		{
			$all_comments = $sync_comments_action->get_comments();
			if ( count( $all_comments ) < 1 )
				break;

			$first_comment = array_shift( $all_comments );

			$sync_comments_action->set_comments( [ $first_comment ] );

			$sync_comments_action->execute();

			$sync_comments_action->set_comments( $all_comments );
			$diff = time() - $start;
			$this->debug( 'Time diff is: %s. %s comments left.', $diff, count( $all_comments ) );
		}
		while ( $diff < 20 );

		$action->data->compress( $sync_comments_action );
		$action->data->db_update();

		if ( count( $sync_comments_action->comments ) < 1 )
		{
			$this->debug( 'Deleting data since no more comments are available.' );
			$action->data->db_delete();
		}
	}

	/**
		@brief		Save the settings.
		@since		2017-12-20 15:03:19
	**/
	public function broadcast_queue_save_settings( $action )
	{
		$form = $action->form;

		$value = $form->get_post_value( 'use_comment_queue' );
		$this->update_site_option( 'use_comment_queue', $value );
	}

	/**
		@brief		Save the sync status.
		@since		2015-01-06 21:14:17
	**/
	public function save_post( $post_id )
	{
		if ( ! isset( $_POST[ 'broadcast' ] ) )
			return;

		if ( ! isset( $_POST[ 'broadcast' ][ 'comments_sync' ] ) )
			return;

		$sync = $_POST[ 'broadcast' ][ 'comments_sync' ];
		$this->debug( 'Setting comment sync of post %s to %s', $post_id, $sync );
		if ( $sync == '' )
			delete_post_meta( $post_id, self::$meta_key );
		else
			update_post_meta( $post_id, self::$meta_key, $sync );
	}

	/**
		@brief		broadcast_queue_show_queue_table_data
		@since		2017-08-13 22:51:57
	**/
	public function broadcast_queue_show_queue_table_data( \threewp_broadcast\premium_pack\queue\actions\show_queue_table_data $action )
	{
		if ( $action->data->type != static::$queue_data_type )
			return;

		$data = $action->data->uncompress();

		$key = 'blog' . $data->child_blog_id;
		$blog = broadcast_queue()->cache->collection( 'blogs' )->get( $key );
		if ( $blog === null )
		{
			$blog = get_blog_details( $data->child_blog_id );
			broadcast_queue()->cache->collection( 'blogs' )->set( $key, $blog );
		}

		$key = 'post' . $data->child_blog_id . '_' . $data->child_post_id;
		$post = broadcast_queue()->cache->collection( 'posts' )->get( $key );
		if ( $post === null )
		{
			switch_to_blog( $data->child_blog_id );
			$post = get_post( $data->child_post_id );
			broadcast_queue()->cache->collection( 'posts' )->set( $key . 'permalink', get_permalink( $data->child_post_id ) );
			restore_current_blog();
			broadcast_queue()->cache->collection( 'posts' )->set( $key, $post );
		}

		$blogpost = sprintf( '<a href="%s"><em>%s</em></a> on %s',
			broadcast_queue()->cache->collection( 'posts' )->get( $key . 'permalink' ),
			$post->post_title,
			$blog->blogname
		);

		$pd = broadcast_queue()->new_process_data();
		$pd->item_count = 1;
		$pd->show_item_count = false;
		$pd->type = static::$queue_data_type;
		$pd->build();

		$action->row->td( 'details' )->text( sprintf( "Comment for %s<br>" . $pd->html, $blogpost ) );
	}

	/**
		@brief		Maybe broadcast the comments.
		@since		2014-05-20 18:28:40
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->comments ) )
			return;

		if ( count( $bcd->comments->comments ) < 1 )
			return;

		$prepare_sync = new actions\prepare_sync();
		$prepare_sync->blog_id = $bcd->parent_blog_id;
		// Find the last comment.
		$last_comment = $bcd->comments->comments[ count( $bcd->comments->comments ) - 1 ];
		$prepare_sync->comment_id = $last_comment->comment_ID;;
		$prepare_sync->post_id = $bcd->parent_post_id;
		$prepare_sync->sync_type = 'from_parent';
		$prepare_sync->sync_targets = [ get_current_blog_id() => $bcd->new_post( 'ID' ) ];

		switch_to_blog( $bcd->parent_blog_id );

		$this->sync_comments( [
			'prepare_sync' => $prepare_sync,
		] );

		restore_current_blog();
	}

	/**
		@brief		Prepare the broadcasting of comments. Maybe.
		@since		2014-05-20 18:17:36
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;

		$input = $bcd->meta_box_data->form->input( 'broadcast_comments' );
		if ( ! $input )
			return;

		if ( ! $input->is_checked() )
		{
			$this->debug( 'User did not request that comments be synced.' );
			return;
		}

		$this->debug( 'Comments are going to be synced.' );

		$bcd->comments = ThreeWP_Broadcast()->collection();
		$bcd->comments->comments = static::get_comments( $bcd->post->ID );
		$this->debug( '%s comments are going to be broadcasted.', count( $bcd->comments->comments ) );
	}

	/**
		@brief		Allow the user to choose what to do with comments.
		@since		2014-05-20 18:16:32
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$form = $meta_box_data->form;

		$name = 'comments_sync';
		$meta = get_post_meta( $meta_box_data->post->ID, self::$meta_key, true );
		$comments = $form->select( $name )
			// Input label
			->label( __( 'Comments sync', 'threewp_broadcast' ) )
			// Do not sync comments option
			->option( __( 'No sync', 'threewp_broadcast' ), '' )
			// Sync parent comments to children option
			->option( __( 'Parent to children', 'threewp_broadcast' ), 'from_parent' )
			// Sync comments in both directions option
			->option( __( 'Both directions', 'threewp_broadcast' ), 'both' )
			// Input title (description)
			->title( __( 'How to keep the comments synced between parent and children', 'threewp_broadcast' ) )
			->value( $meta );
		$action->meta_box_data->html->insert_before( 'blogs', $name, '' );
		$meta_box_data->convert_form_input_later( $name );

		$name = 'broadcast_comments';
		$broadcast_comments = $form->checkbox( $name )
			->checked( isset( $meta_box_data->last_used_settings[ $name ] ) )
			// Input label
			->label( __( 'Sync comments to children now', 'threewp_broadcast' ) )
			// Input title
			->title( __( 'Sync the comments to the children now', 'threewp_broadcast' ) );
		$action->meta_box_data->html->insert_before( 'blogs', $name, '' );
		$meta_box_data->convert_form_input_later( $name );
	}

	/**
		@brief		transition_comment_status
		@since		2014-12-28 12:48:20
	**/
	public function transition_comment_status( $new, $old, $comment )
	{
		// No point syncing deleted comments.
		if ( $new == 'delete' )
			return;
		$this->debug( 'transition_comment_status (%s) %s %s', $comment->ID, $old, $new );
		$this->maybe_resync_comments( $comment->comment_ID );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Convenience function to retrieve the comments in the correct order.
		@since		2014-12-28 12:55:55
	**/
	public static function get_comments( $post_id )
	{
		// Retrieve the comments themselves.
		$r = get_comments( [
			'post_id' => $post_id,
			'order' => 'ASC',
			'orderby' => [ 'comment_ID', 'comment_parent' ],
		] );

		// Also retrieve each comment's meta.
		foreach( $r as $index => $comment )
			$r[ $index ]->meta = get_comment_meta( $comment->comment_ID );

		$r = static::array_rekey( $r, 'comment_ID' );

		return $r;
	}

	/**
		@brief		Generate a "unique" ID based on this comment.
		@since		2021-02-27 20:06:13
	**/
	public static function generate_comment_hash( $comment )
	{
		$key = '';
		foreach( [
			'comment_author',
			'comment_author_email',
			'comment_author_url',
			'comment_author_IP',
			'comment_content',
			'comment_date',
			'comment_agent',
			] as $type )
			$key .= '--' . $comment->$type;
		$hash = md5( $key );
		$hash = md5( $hash );
		return $hash;
	}

	/**
		@brief		Decides whether the comments of a post need to be resynced with the child posts.
		@since		2014-12-28 12:48:56
	**/
	public function maybe_resync_comments( $comment_id )
	{
		$this->debug( 'maybe_resync_comments: %s', current_action() );
		if ( $this->__syncing )
			return $this->debug( 'Already in a comment sync.' );

		$blog_id = get_current_blog_id();

		// Prevent double-syncing. edit_comment, for example, will fire an edit_comment action and then a transition.
		if ( isset( $this->__synced_comments[ $blog_id . '_' . $comment_id ] ) )
			return $this->debug( 'Comment %s has already been synced.', $comment_id );

		// Find the post of the comment.
		$comment = get_comment( $comment_id );

		// And the associated post.
		$post_id = $comment->comment_post_ID;

		// Is the "comments sync" meta set?
		$meta = get_post_meta( $post_id, self::$meta_key, true );
		$this->debug( 'Post meta is: %s', $meta );
		if ( ! $meta )
			return $this->debug( 'Post %s does not want comments to be kept updated.', $post_id );

		$prepare_sync = new actions\prepare_sync();
		$prepare_sync->blog_id = $blog_id;
		$prepare_sync->comment_id = $comment_id;
		$prepare_sync->post_id = $post_id;
		$prepare_sync->sync_type = $meta;
		$prepare_sync->execute();

		$this->sync_comments( [
			'prepare_sync' => $prepare_sync,
		] );
	}

	/**
		@brief		Convenience method to programmatically merge comments from one post to another.
		@details	The posts in question must be linked to each other.
		@since		2021-02-27 20:02:15
	**/
	public function merge_comments( $options )
	{
		$options = array_merge( [
			'blog_id' => get_current_blog_id(),
			'comment_id' => 0,
			'post_id' => 0,
			'sync_type' => 'both',
		], (array) $options );
		$options = (object) $options;

		$prepare_sync = new actions\prepare_sync();
		$prepare_sync->blog_id = $options->blog_id;
		$prepare_sync->comment_id = $options->comment_id;
		$prepare_sync->post_id = $options->post_id;
		$prepare_sync->sync_type = $options->sync_type;
		$prepare_sync->execute();

		$this->sync_comments( [
			'prepare_sync' => $prepare_sync,
			'delete_existing_comments' => false,
		] );
	}

	/**
		@brief		Sync the comments of a post.
		@details	Requires an array of
					- prepare_sync A prepare_sync action.
		@since		2017-09-26 22:51:05
	**/
	public function sync_comments( $options )
	{
		$options = (object) $options;

		if ( count( $options->prepare_sync->sync_targets ) < 1 )
			return $this->debug( 'No targets found.' );

		$use_comment_queue = $this->get_site_option( 'use_comment_queue' );

		// Good to go.
		$action = new actions\sync_comments();
		$action->parent_blog_id = $options->prepare_sync->blog_id;
		$action->parent_post_id = $options->prepare_sync->post_id;
		$action->set_comments( $this->get_comments( $action->parent_post_id ) );

		if ( isset( $options->delete_existing_comments ) )
			$action->delete_existing_comments = $options->delete_existing_comments;

		foreach( $options->prepare_sync->sync_targets as $target_blog_id => $target_post_id )
		{
			$target_action = clone( $action );		// To prevent finish conflicts.
			$target_action->set_child_blog_id( $target_blog_id );
			$target_action->set_child_post_id( $target_post_id );
			if ( $use_comment_queue )
			{
				// First create the queue data.
				$data = broadcast_queue()->new_queue_data();
				$data->created = $this->now();
				$data->compress( $target_action );
				$data->type = static::$queue_data_type;		// For the sake of clarity.
				$this->debug( 'Inserting queue data %s bytes.', strlen( serialize( $data ) ) );
				$data->db_insert();
				$this->debug( 'Data %s inserted.', $data->id );

				$comment_count = count( $action->get_comments() );
				$this->debug( 'Inserting %s items.', $comment_count );

				for( $counter = 0; $counter < $comment_count; $counter++ )
				{
					// And now the item for the queue data.
					$item = broadcast_queue()->new_queue_item();
					$item->blog = 0;
					$item->data_id = $data->id;
					$item->lock_key = $item->generate_lock_key();
					$this->debug( 'Inserting queue item. %s bytes.', strlen( serialize( $item ) ) );
					$item->db_insert();
					$this->debug( 'Item %s inserted.', $item->id );
				}
			}
			else
				$target_action->execute();
		}

		$this->__synced_comments[ $options->prepare_sync->blog_id . '_' . $options->prepare_sync->comment_id ] = true;
	}
}

} // namespace threewp_broadcast\premium_pack\comments

namespace
{
	/**
		@brief		Convenience function to return an instance to the Comments add-on.
		@since		2017-09-26 17:28:42
	**/
	function broadcast_comments()
	{
		return \threewp_broadcast\premium_pack\comments\Comments::instance();
	}
}