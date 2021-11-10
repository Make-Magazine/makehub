<?php

namespace threewp_broadcast\premium_pack\parent_pull;

use \threewp_broadcast\posts\actions\bulk\wp_ajax;

/**
	@brief			Allow content to be pulled from (parent) blogs.
	@plugin_group	Control
	@since			2020-06-10 08:55:12
**/
class Parent_Pull
	extends \threewp_broadcast\premium_pack\base
{
	/**
		@brief		The custom field that stored the pullable status.
		@since		2020-06-10 09:13:02
	**/
	public static $custom_field = 'broadcast_parent_pullable';

	/**
		@brief		Constructor.
		@since		2020-06-10 08:55:53
	**/
	public function _construct()
	{
		$this->add_filter( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_get_post_bulk_actions' );
		$this->add_action( 'threewp_broadcast_manage_posts_custom_column' );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'threewp_broadcast_post_action' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
	}

	/**
		@brief		Edit a pull setup.
		@since		2020-06-21 21:24:05
	**/
	public function admin_edit_pull_setup( $pull_setup_id )
	{
		$pull_setups = $this->pull_setups();
		$pull_setup = $pull_setups->get( $pull_setup_id );

		if ( ! $pull_setup )
			wp_die( 'Invalid pull setup!' );

		$form = $this->form();
		$form->css_class( 'plainview_form_auto_tabs' );
		$r = '';

		$fs = $form->fieldset( 'fs_general' );
		// Fieldset label
		$fs->legend->label( __( 'General settings', 'threewp_broadcast' ) );

		$description_input = $fs->text( 'description' )
			->description( __( 'Describe this pull setup for yourself.', 'threewp_broadcast' ) )
			->label( __( 'Description', 'threewp_broadcast' ) )
			->required()
			->size( 100, 1000 )
			->value( $pull_setup->get( 'description' ) );

		$fs = $form->fieldset( 'fs_blogs' );
		// Fieldset label
		$fs->legend->label( __( 'Blog selection', 'threewp_broadcast' ) );

		$parent_blogs = $this->add_blog_list_input( [
			'description' => __( 'From which blogs are the child blogs allowed to pull. To allow selection from all blogs, select none.', 'threewp_broadcast' ),
			'form' => $fs,
			'label' => __( 'Parent blogs', 'threewp_broadcast' ),
			'multiple' => true,
			'name' => 'parent_blogs',
			'required' => false,
			'value' => $pull_setup->get( 'parent_blogs' ),
		] );

		$child_blogs = $this->add_blog_list_input( [
			'description' => __( 'Which blogs are allowed to pull content from the parents above. To allow selection from all blogs, select none.', 'threewp_broadcast' ),
			'form' => $fs,
			'label' => __( 'Child blogs', 'threewp_broadcast' ),
			'multiple' => true,
			'name' => 'child_blogs',
			'required' => false,
			'value' => $pull_setup->get( 'child_blogs' ),
		] );

		$fs = $form->fieldset( 'fs_post_types' );
		// Fieldset label
		$fs->legend->label( __( 'Post types', 'threewp_broadcast' ) );

		$post_types_input = $fs->textarea( 'post_types' )
			->description( __( 'If you want to limit pulling of specific post types, enter their slugs. One post type per line.', 'threewp_broadcast' ) )
			->label( __( 'Post types', 'threewp_broadcast' ) )
			->rows( 10, 20 )
			->value( $pull_setup->get( 'post_types' ) );

		$save_button = $form->primary_button( 'save' )
			->value( __( 'Save', 'threewp_broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$pull_setup->set( 'description', $description_input->get_post_value() );
			$pull_setup->set( 'child_blogs', $child_blogs->get_post_value() );
			$pull_setup->set( 'parent_blogs', $parent_blogs->get_post_value() );
			$pull_setup->set( 'post_types', $post_types_input->get_post_value() );
			$pull_setups->save();

			$r .= $this->info_message_box()
				->_( __( 'The settings for this pull setup have been saved!', 'threewp_broadcast' ) );
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the parent pull overview.
		@since		2017-08-01 12:39:11
	**/
	public function admin_menu_overview()
	{
		$form = $this->form();
		$r = '';
		$table = $this->table();

		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( __( 'Delete', 'threewp_broadcast' ), 'delete' )
			->cb( $row );
		$row->th( 'description' )->text( __( 'Description', 'threewp_broadcast' ) );
		$row->th( 'parents' )->text( __( 'Parents', 'threewp_broadcast' ) );
		$row->th( 'children' )->text( __( 'Children', 'threewp_broadcast' ) );
		$row->th( 'post_types' )->text( __( 'Post types', 'threewp_broadcast' ) );

		$pull_setups = $this->pull_setups();

		$button_create_parent = $form->primary_button( 'create_pull_setup' )
			->value( __( 'Create a new pull setup', 'threewp_broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			if ( $table->bulk_actions()->pressed() )
			{
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'delete':
						$ids = $table->bulk_actions()->get_rows();

						foreach( $ids as $id )
							$pull_setups->forget( $id );

						$r .= $this->info_message_box()
							->_( __( 'The selected pull setups have been deleted.', 'threewp_broadcast' ) );
						$pull_setups->save();
					break;
				}
			}

			if ( $button_create_parent->pressed() )
			{
				$pull_setup = new Pull_Setup();
				$pull_setups->append( $pull_setup );
				$this->message( sprintf(
					__( 'A new pull setup, %s, has been created.', 'threewp_broadcast' ),
					'<em>' . $pull_setup->get( 'description' ) . '</em>'
				) );
				$pull_setups->save();
			}
		}

		foreach( $pull_setups as $pull_setup )
		{
			$row = $table->body()->row();
			$pull_setup_id = $pull_setup->get( 'id' );
			$table->bulk_actions()->cb( $row, $pull_setup_id );

			$edit_url = add_query_arg( 'pull_setup_id', $pull_setup_id );
			$edit_url = add_query_arg( 'tab', 'edit', $edit_url );

			$description = sprintf( '<a href="%s" title="%s">%s</a>',
				$edit_url,
				__( 'Edit this pull setup', 'threewp_broadcast' ),
				$pull_setup->get( 'description' )
			);

			$row->td( 'description' )->text( $description );
			$row->td( 'parents' )->text( wpautop( $pull_setup->get_parent_blogs_text() ) );
			$row->td( 'children' )->text( wpautop( $pull_setup->get_child_blogs_text() ) );
			$row->td( 'post_types' )->text( wpautop( $pull_setup->get_post_types_text() ) );
		}

		$r .= $this->p( __( 'This add-on allows pulling of content from parent blogs.', 'threewp_broadcast' ) );

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the pull list.
		@since		2020-06-21 22:53:56
	**/
	public function admin_menu_pull_list()
	{
		$form = $this->form();
		$pull_setups = $this->pull_setups();

		$sections = ThreeWP_Broadcast()->collection();

		$r = '';

		$r .= $this->p( __( 'Select the content you wish to pull to this blog and press the button when you are ready.', 'threewp_broadcast' ) );
		$r .= '<div class="plainview_form_auto_tabs">';

		$r .= $form->open_tag();
		$blogs = $pull_setups->get_available_parents();
		$original_child_blog = get_current_blog_id();
		foreach( $blogs as $parent_blog_id )
		{
			switch_to_blog( $parent_blog_id );
			$post_types = $pull_setups->get_available_post_types( $original_child_blog );

			foreach( $post_types as $post_type )
			{
				$posts = $this->get_pullable_posts( [
					'blog_id' => $parent_blog_id,
					'post_type' => $post_type,
				] );

				// Remove all posts that are already linked to the original child blog.
				foreach( $posts as $index => $post )
				{
					$linking = ThreeWP_Broadcast()->api()->linking( $post->ID );
					$children = $linking->children();
					if ( isset( $children[ $original_child_blog ] ) )
						unset( $posts[ $index ] );
				}
				if ( count( $posts ) > 0 )
				{
					$blog_name = get_option( 'blogname' );
					$html = $sections->collection( $post_type )->collection( $blog_name )->get( 'html' );

					$table = $this->table();

					$row = $table->head()->row();
					$table->bulk_actions()
						->form( $form )
						->cb( $row );
					$row->th( 'post_title' )->text( __( 'Post title', 'threewp_broadcast' ) );
					foreach( $posts as $post )
					{
						$row = $table->body()->row();
						$table->bulk_actions()->cb( $row, $parent_blog_id . '_' . $post->ID );
						$text = sprintf( '<a href="%s">%s</a>', get_permalink( $post->ID ), $post->post_title );
						$row->td( 'post_title' )->text( $text );
					}

					$html .= $table;
					$sections->collection( $post_type )->collection( $blog_name )->set( 'html', $html );
				}
			}
			restore_current_blog();
		}

		$button_create_parent = $form->primary_button( 'pull_posts' )
			->value( __( 'Pull the selected posts', 'threewp_broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			if ( isset( $_POST[ 'cb' ] ) )
			{
				$current_blog_id = get_current_blog_id();
				foreach( $_POST[ 'cb' ] as $blog_id_post_id => $ignore )
				{
					$blog_id_post_id = explode( '_', $blog_id_post_id );
					$blog_id = $blog_id_post_id[ 0 ];
					$post_id = $blog_id_post_id[ 1 ];
					switch_to_blog( $blog_id );
					ThreeWP_Broadcast()->api()->broadcast_children( $post_id, [ $current_blog_id ] );
					restore_current_blog();
				}

				$r .= $this->info_message_box()
					->_( __( 'The selected posts have been pulled!', 'threewp_broadcast' ) );
			}
		}

		foreach( $sections as $post_type => $blogs )
		{
			$r .= '<div class="fieldset">';
			$r .= '<h3 class="title">' . $post_type . '</h3>';
			$blogs = $blogs->to_array();
			ksort( $blogs );

			foreach( $blogs as $blog_name => $data )
			{
				$r .= '<h3>' . $blog_name . '</h3>';
				$r .= $sections->collection( $post_type )->collection( $blog_name )->get( 'html' );
			}
			$r .= '</div>';
		}

		$r .= '</div>';
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Admin tabs.
		@since		2017-08-01 12:39:22
	**/
	public function admin_menu_tabs()
	{
		$tabs = $this->tabs();

		if ( is_super_admin() )
		{
			$tabs->tab( 'overview' )
				->callback_this( 'admin_menu_overview' )
				// Tab heading
				->heading( __( 'Parent Pull overview', 'threewp_broadcast' ) )
				// Tab name
				->name( __( 'Overview', 'threewp_broadcast' ) )
				->sort_order( 25 );

			if ( isset( $_GET[ 'tab' ] ) )
			{
				if ( $_GET[ 'tab' ] == 'edit' )
					$tabs->tab( 'edit' )
						->callback_this( 'admin_edit_pull_setup' )
						// Tab name
						->name( __( 'Edit pull setup', 'threewp_broadcast' ) )
						->parameters( $_GET[ 'pull_setup_id' ] );
			}
		}
			$tabs->tab( 'pull_list' )
				->callback_this( 'admin_menu_pull_list' )
				// Tab heading
				->heading( __( 'Parent Pull list', 'threewp_broadcast' ) )
				// Tab name
				->name( __( 'Pull list', 'threewp_broadcast' ) );
		echo $tabs->render();
	}

	/**
		@brief		Return an array of the pullable posts.
		@since		2020-06-22 22:00:31
	**/
	public function get_pullable_posts( $options )
	{
		$options = array_merge( [
			'blog_id' => get_current_blog_id(),
			'post_type' => false,
		], $options );
		$options = (object) $options;

		$get_posts_options = [
    		'posts_per_page' => -1,
			'meta_key' => static::$custom_field,
			'meta_value' => true,
			'order' => 'ASC',
			'orderby' => 'title',
		];
		if ( $options->post_type )
			$get_posts_options[ 'post_type' ] = $options->post_type;

		switch_to_blog( $options->blog_id );
		$posts = get_posts( $get_posts_options );
		restore_current_blog();

		return $posts;
	}

	/**
		@brief		Loader for parent pulls object.
		@since		2020-06-21 20:45:49
	**/
	public function pull_setups()
	{
		return Pull_Setups::load();
	}

	/**
		@brief		Save pullability status.
		@since		2020-06-10 09:21:52
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;
		$mbd = $bcd->meta_box_data;

		$input = $mbd->form->input( 'parent_pull' );
		if ( ! $input )
			return;

		$parent_pull = $input->get_post_value();

		if ( ! $parent_pull )
		{
			$this->debug( 'Unmarking post as %s.', static::$custom_field );
			$bcd->custom_fields()->forget( static::$custom_field );
			delete_post_meta( $bcd->post->ID, static::$custom_field );
			return;
		}

		// Save the custom field to the parent.
		$this->debug( 'Marking post as %s.', static::$custom_field );
		update_post_meta( $bcd->post->ID, static::$custom_field, true );
	}

	/**
		@brief		Add our bulk actions.
		@since		2020-06-21 22:24:17
	**/
	public function threewp_broadcast_get_post_bulk_actions( $action )
	{
		$a = new wp_ajax;
		$a->set_ajax_action( 'broadcast_post_bulk_action' );
		$a->set_data( 'subaction', 'parent_pull_mark' );
		$a->set_id( 'bulk_parent_pull_mark' );
		$a->set_name( __( 'Parent Pull - Mark', 'threewp_broadcast' ) );
		$a->set_nonce( 'broadcast_post_bulk_actionparent_pull_mark' );
		$action->add( $a );

		$a = new wp_ajax;
		$a->set_ajax_action( 'broadcast_post_bulk_action' );
		$a->set_data( 'subaction', 'parent_pull_unmark' );
		$a->set_id( 'bulk_parent_pull_unmark' );
		$a->set_name( __( 'Parent Pull - Unmark', 'threewp_broadcast' ) );
		$a->set_nonce( 'broadcast_post_bulk_actionparent_pull_unmark' );
		$action->add( $a );
	}

	/**
		@brief		Show the pullable status.
		@since		2020-06-21 22:33:00
	**/
	public function threewp_broadcast_manage_posts_custom_column( $action )
	{
		$value = get_post_meta( $action->parent_post_id, static::$custom_field, true );
		if ( ! $value )
			return;
		$action->html->put( 'parent_pull', 'Parent pullable' );
	}

	/**
		@brief		Menu.
		@since		2017-08-01 12:37:08
	**/
	public function threewp_broadcast_menu( $action )
	{
		$action->menu_page
			->submenu( 'bc_parent_pull' )
			->callback_this( 'admin_menu_tabs' )
			->menu_title( __( 'Parent Pull', 'threewp_broadcast' ) )
			->page_title( __( 'Broadcast Parent Pull', 'threewp_broadcast' ) );
	}

	/**
		@brief		Handle the post action.
		@since		2020-06-21 22:28:36
	**/
	public function threewp_broadcast_post_action( $action )
	{
		$post_id = $action->post_id;
		switch( $action->action )
		{
			case 'parent_pull_mark':
				update_post_meta( $post_id, static::$custom_field, true );
			break;
			case 'parent_pull_unmark':
				delete_post_meta( $post_id, static::$custom_field, true );
			break;
		}
	}

	/**
		@brief		threewp_broadcast_prepare_meta_box
		@since		2020-06-10 08:57:18
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$form = $meta_box_data->form;

		$linked_parent = $meta_box_data->broadcast_data->get_linked_parent();
		// This is for parents only.
		if ( $linked_parent )
			return;

		$form->checkbox( 'parent_pull' )
			->checked( get_post_meta( $meta_box_data->post->ID, static::$custom_field, true ) )
			->label( __( 'Allow parent pull', 'threewp_broadcast' ) )
			->title( __( 'Allow this post to be pulled from child blogs.', 'threewp_broadcast' ) );

		$meta_box_data->html->insert_before( 'blogs', 'parent_pull', '' );
		$meta_box_data->convert_form_input_later( 'parent_pull' );
	}

	/**
		@brief		Site options.
		@since		2020-06-21 20:05:05
	**/
	public function site_options()
	{
		return array_merge( [
			'pull_setups' => false,
		], parent::site_options() );
	}
}
