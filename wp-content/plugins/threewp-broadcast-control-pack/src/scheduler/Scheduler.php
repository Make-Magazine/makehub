<?php

namespace threewp_broadcast\premium_pack\scheduler
{

/**
	@brief			Automatically change the publish date of child posts during broadcasting.
	@plugin_group	Control
	@since			2021-07-18 21:15:16
**/
class Scheduler
	extends \threewp_broadcast\premium_pack\base
{
	/**
		@brief		The meta key where the schedule type is stored.
		@since		2021-07-18 23:08:46
	**/
	public static $schedule_id_meta_key = 'broadcast_schedule_id';

	/**
		@brief		The meta key where the schedule type is stored.
		@since		2021-07-18 23:08:46
	**/
	public static $schedule_meta_key = 'broadcast_schedule';

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_broadcasting_after_update_post' );
		$this->add_action( 'threewp_broadcast_broadcasting_modify_post' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Edit a schedule.
		@since		2021-07-18 22:02:59
	**/
	public function admin_edit_schedule( $schedule_id )
	{
		$schedules = $this->schedules();
		$schedule = $schedules->get( $schedule_id );

		if ( ! $schedule )
			wp_die( 'Invalid schedule!' );

		$form = $this->form();
		$form->css_class( 'plainview_form_auto_tabs' );
		$r = '';

		$fs = $form->fieldset( 'fs_general' );
		// Fieldset label
		$fs->legend->label( __( 'General settings', 'threewp_broadcast' ) );

		$description_input = $fs->text( 'description' )
			->description( __( 'Describe this schedule for yourself.', 'threewp_broadcast' ) )
			->label( __( 'Description', 'threewp_broadcast' ) )
			->required()
			->size( 100, 1000 )
			->value( $schedule->get( 'description' ) );

		$schedule->add_form_inputs( $form );

		$save_button = $form->primary_button( 'save' )
			->value( __( 'Save', 'threewp_broadcast' ) );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$schedule->parse_form_inputs( $form );

			$schedule->set( 'description', $description_input->get_post_value() );
			$schedules->save();

			$r .= $this->info_message_box()
				->_( __( 'The settings for this term have been saved!', 'threewp_broadcast' ) );
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		admin_menu_overview
		@since		2021-07-18 21:19:00
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
		$row->th( 'type' )->text( __( 'Type', 'threewp_broadcast' ) );

		$schedules = $this->schedules();

		$fs = $form->fieldset( 'fs_create_new' );
		$fs->legend->label( 'Create a new schedule' );

		$schedule_types = schedules\Schedule::get_types();

		$schedule_type_input = $fs->select( 'schedule_type' )
			->label( 'Schedule type' )
			->opts( $schedule_types )
			->value( 'random' );

		$button_create_schedule = $form->primary_button( 'create_schedule' )
			// Create schedule button
			->value( __( 'Create a new schedule', 'threewp_broadcast' ) );

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
							$schedules->forget( $id );

						$r .= $this->info_message_box()
							->_( __( 'The selected rows have been deleted.', 'threewp_broadcast' ) );
						$schedules->save();
					break;
				}
			}

			if ( $button_create_schedule->pressed() )
			{
				$schedule_type = $schedule_type_input->get_post_value();
				$schedule = schedules\Schedule::create_type( $schedule_type );
				$schedules->append( $schedule );
				$this->message( sprintf(
					__( 'A new schedule, %s, has been created.', 'threewp_broadcast' ),
					'<em>' . $schedule->get( 'description' ) . '</em>'
				) );
				$schedules->save();
			}
		}

		foreach( $schedules as $schedule )
		{
			$row = $table->body()->row();
			$schedule_id = $schedule->get( 'id' );
			$table->bulk_actions()->cb( $row, $schedule_id );

			$edit_url = add_query_arg( 'schedule_id', $schedule_id );
			$edit_url = add_query_arg( 'tab', 'edit', $edit_url );

			$description = sprintf( '<a href="%s" title="%s">%s</a>',
				$edit_url,
				__( 'Edit this schedule', 'threewp_broadcast' ),
				$schedule->get( 'description' )
			);

			$row->td( 'description' )->text( $description );
			$row->td( 'type' )->text( $schedule->get_type_description() );
		}

		$r .= $this->p( __( 'This add-on changes the publish date of child posts during broadcasting.', 'threewp_broadcast' ) );

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		admin_menu_tabs
		@since		2021-07-18 21:17:43
	**/
	public function admin_menu_tabs()
	{
		$tabs = $this->tabs();

		$tabs->tab( 'overview' )
			->callback_this( 'admin_menu_overview' )
			->heading( 'Broadcast Scheduler overview' )
			// Tab name
			->name( 'Overview' )
			->sort_order( 25 );

		if ( $tabs->get_is( 'edit' ) )
		{
			$tabs->tab( 'edit' )
				->callback_this( 'admin_edit_schedule' )
				// Tab name
				->name( __( 'Edit schedule', 'threewp_broadcast' ) )
				->parameters( $_GET[ 'schedule_id' ] );
		}

		echo $tabs->render();
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Modify the post date if necessary.
		@since		2021-07-21 17:18:20
	**/
	public function threewp_broadcast_broadcasting_after_update_post( $action )
	{
		$bcd = $action->broadcasting_data;

		$bcd->scheduler_set_future = false;

		$key = Broadcast_Scheduler()::$schedule_meta_key;
		$schedule = $bcd->custom_fields()->get_single( $key );
		if ( ! $schedule )
			return;

		if ( ! isset( $schedule[ $bcd->current_child_blog_id ] ) )
			return;

		$gmt_offset = current_time( 'timestamp' ) - current_time( 'timestamp', true );
		$new_post_time = $schedule[ $bcd->current_child_blog_id ];
		$key = 'post_date_gmt';
		$bcd->post->$key = date( 'Y-m-d H:i:s', $new_post_time + $gmt_offset );
		$this->debug( 'Set scheduled post: %s', $bcd->post->$key );

		if ( $new_post_time > time() )
			$bcd->scheduler_set_future = true;
	}

	/**
		@brief		threewp_broadcast_broadcasting_modify_post
		@since		2021-07-21 19:13:32
	**/
	public function threewp_broadcast_broadcasting_modify_post( $action )
	{
		$bcd = $action->broadcasting_data;
		if ( ! isset( $bcd->scheduler_set_future ) )
			return;

		if ( $bcd->scheduler_set_future )
		{
			$this->debug( 'Setting post to future' );
			ThreeWP_Broadcast()->set_post_status( $bcd->new_post( 'ID' ), 'future' );
		}
	}

	/**
		@brief		Started.
		@since		2021-07-20 22:51:06
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;
		$mbd = $bcd->meta_box_data;

		$input = $mbd->form->input( 'broadcast_schedule_id' );
		if ( ! $input )
			return;

		$schedule_id = $input->get_filtered_post_value();

		if ( $schedule_id == '' )
		{
			// Is there a current schedule type set?
			$schedule_id = get_post_meta( $bcd->post->ID, static::$schedule_id_meta_key, true );
			if ( ! $schedule_id )
				return;
		}

		if ( $schedule_id == 'delete' )
		{
			$this->debug( 'Clearing the schedule.' );
			static::clear_schedule( $bcd->post->ID, $bcd );
			return;
		}

		$schedules = $this->schedules();
		$schedule = $schedules->get( $schedule_id );

		if ( ! $schedule )
			return $this->debug( 'No schedule found: %s', $schedule_id );

		$schedule->apply_to_bcd( $bcd );
	}

	/**
		@brief		Add ourself to Broadcast's menu.
		@since		20131014
	**/
	public function threewp_broadcast_menu( $action )
	{
		$action->menu_page
			->submenu( 'threewp_broadcast_scheduler' )
			->callback_this( 'admin_menu_tabs' )
			// Menu item for menu
			->menu_title( 'Scheduler' )
			// Page title for menu
			->page_title( 'Broadcast Scheduler' );
	}

	/**
		@brief		Modify the meta box with modification data.
		@since		20131016
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		// We need at least one schedule.
		$schedules = $this->schedules();
		if ( $schedules->count() < 1 )
			return;

		$meta_box_data = $action->meta_box_data;
		$form = $meta_box_data->form;
		$post = $action->meta_box_data->post;

		$schedule_type = get_post_meta( $post->ID, static::$schedule_id_meta_key, true );

		$broadcast_schedule_id_input = $form->select( 'broadcast_schedule_id' )
			// Input label
			->label( 'Schedule' )
			->opt( '', 'No change' )
			->opt( 'delete', 'Clear the schedule' )
			->value( $schedule_type );
		foreach( $schedules as $schedule_id => $schedule )
			$broadcast_schedule_id_input->opt( $schedule_id, $schedule->get( 'description' ) );
		$meta_box_data->convert_form_input_later( 'broadcast_schedule_id' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Convenience method to clear a schedule.
		@since		2021-07-21 17:38:18
	**/
	public static function clear_schedule( $post_id, $bcd = false )
	{
		delete_post_meta( $post_id, static::$schedule_id_meta_key );
		delete_post_meta( $post_id, static::$schedule_meta_key );

		if ( $bcd )
		{
			$bcd->custom_fields()->forget( static::$schedule_id_meta_key );
			$bcd->custom_fields()->forget( static::$schedule_meta_key );
		}
	}

	/**
		@brief		Loader for the schedules.
		@since		2021-07-18 21:32:52
	**/
	public function schedules()
	{
		return schedules\Schedules::load();
	}
}

} // namespace

namespace
{
	function Broadcast_Scheduler()
	{
		return threewp_broadcast\premium_pack\scheduler\Scheduler::instance();
	}
}
