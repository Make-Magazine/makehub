<?php

namespace threewp_broadcast\premium_pack\elementor;

use Exception;

/**
	@brief			Adds support for the <a href="https://wordpress.org/plugins/elementor/">Elementor Page Builder plugin</a>.
	@plugin_group	3rd party compatability
	@since			2017-04-28 23:16:00
**/
class Elementor
	extends \threewp_broadcast\premium_pack\base
{
	use \threewp_broadcast\premium_pack\classes\broadcast_data_trait;
	use \threewp_broadcast\premium_pack\classes\parse_and_preparse_content_trait;

	/**
		@brief		These are where images are usually found.
		@since		2022-03-07 18:56:36
	**/
	public static $image_settings = [
		'background_image',
		'bg_image',
		'image',
	];

	/**
		@brief		parseable_settings
		@since		2018-12-06 12:30:42
	**/
	public static $parseable_settings = [
		'link',
		'shortcode',
		'text',
		'url',
	];

	public function _construct()
	{
		$this->add_action( 'elementor/editor/after_save', 'elementor_editor_after_save' );
		$this->add_action( 'broadcast_php_code_load_wizards' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_collect_post_type_taxonomies' );
		$this->add_action( 'threewp_broadcast_get_post_types' );
		$this->add_action( 'threewp_broadcast_wp_update_term' );
		new Elementor_Template_Shortcode();
	}

	/**
		@brief		elementor_editor_after_save
		@since		2018-12-26 13:04:37
	**/
	public function elementor_editor_after_save( $post_id )
	{
		if ( defined( 'BROADCAST_ELEMENTOR_NO_UPDATE_ON_SAVE' ) )
			return;
		$this->debug( 'elementor_editor_after_save: %s', $post_id );
		ThreeWP_Broadcast()->api()->update_children( $post_id );
	}

	/**
		@brief		Add the wizard for JetEngine.
		@since		2020-05-09 21:42:58
	**/
	public function broadcast_php_code_load_wizards( $action )
	{
		$wizard = $action->new_wizard();
		$wizard->set( 'group', '3rdparty' );
		$wizard->set( 'id', 'elementor_jetengine_copy_tables' );
		$wizard->set( 'label', __( "Elementor: Copy Jet Engine custom post table and taxonomies database tables", 'threewp_broadcast' ) );
		$wizard->load_code_from_disk( __DIR__ . '/php_code/' );
		$action->add_wizard( $wizard );
	}

	/**
		@brief		threewp_broadcast_broadcasting_before_restore_current_blog
		@since		2017-04-28 23:39:15
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->elementor ) )
			return;

		$this->maybe_restore_conditions( $bcd );
		$this->maybe_restore_data( $bcd );
	}

	/**
		@brief		threewp_broadcast_broadcasting_started
		@since		2017-04-28 23:39:00
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;

		$this->maybe_save_data( $bcd );
		$this->maybe_save_conditions( $bcd );
	}

	/**
		@brief		Save the taxonomy thumbnail, as per the Elementor Powerpack.
		@since		2021-10-07 21:40:30
	**/
	public function threewp_broadcast_collect_post_type_taxonomies( $action )
	{
		$bcd = $action->broadcasting_data;

		$this->prepare_bcd( $bcd );

		foreach( $bcd->parent_blog_taxonomies as $parent_post_taxonomy => $taxonomy_data )
		{
			$terms = $taxonomy_data[ 'terms' ];

			$this->debug( 'Collecting termmeta for %s', $parent_post_taxonomy );
			// Get all of the fields for all terms
			foreach( $terms as $term )
			{
				$term_id = $term->term_id;

				// Save the image.
				$key = 'taxonomy_thumbnail_id';
				$image_id = get_term_meta( $term_id, $key, true );

				if ( $image_id > 0 )
				{
				  $this->debug( 'Found %s %s for term %s (%s)',
				  	  $key,
					  $image_id,
					  $term->slug,
					  $term_id
				  );

				  $bcd->try_add_attachment( $image_id );
				  $bcd->elementor->collection( 'taxonomy_thumbnail_id' )->set( $term_id, $image_id );
				}
			}
		}
	}

	/**
		@brief		Add post types.
		@since		2015-10-02 12:47:49
	**/
	public function threewp_broadcast_get_post_types( $action )
	{
		$action->add_type( 'elementor_library' );
	}

	/**
		@brief		Restore the image.
		@since		2021-10-07 21:43:27
	**/
	public function threewp_broadcast_wp_update_term( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->elementor ) )
			return;

		$old_term_id = $action->old_term->term_id;
		$new_term_id = $action->new_term->term_id;

		$old_image_id = $bcd->elementor->collection( 'taxonomy_thumbnail_id' )->get( $old_term_id );

		if ( ! $old_image_id )
			return;

		ThreeWP_Broadcast()->copy_attachments_to_child( $bcd );

		$key = 'taxonomy_thumbnail_id';
		$new_image_id = $bcd->copied_attachments()->get( $old_image_id );

		if ( $new_image_id > 0 )
		{
			$this->debug( 'Setting new %s %s for term %s.', $key, $new_image_id, $new_term_id );
			update_term_meta( $new_term_id, $key, $new_image_id );
		}
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- SAVE
	// --------------------------------------------------------------------------------------------

	/**
		@brief		maybe_save_conditions
		@since		2021-03-09 18:24:02
	**/
	public function maybe_save_conditions( $bcd )
	{
		$meta_key = '_elementor_conditions';
		$conditions = $bcd->custom_fields()->get_single( $meta_key );
		if ( ! $conditions )
			return;

		$this->prepare_bcd( $bcd );

		$conditions = maybe_unserialize( $conditions );
		$bcd->elementor->set( 'conditions', $conditions );

		foreach( $conditions as $condition )
		{
			$parts = explode( '/', $condition );
			if ( ! isset( $parts[ 2 ] ) )
				continue;

			$this->debug( 'Condition: %s', $condition );
			// Taxonomies
			if ( strpos( $parts[ 2 ], 'in_' ) === 0 )
			{
				$taxonomy = $parts[ 2 ];
				$taxonomy = str_replace( 'in_', '', $taxonomy );
				$taxonomy = str_replace( '_children', '', $taxonomy );
				$bcd->taxonomies()
					->also_sync( null, $taxonomy )
					->use_term( $parts[ 3 ] );
			}
		}
	}

	/**
		@brief		Maybe save the elementor data.
		@since		2021-03-09 18:35:33
	**/
	public function maybe_save_data( $bcd )
	{
		$ed = $bcd->custom_fields()->get_single( '_elementor_data' );
		if ( ! $ed )
			return;

		$ed = json_decode( $ed );
		if ( ! $ed )
			return $this->debug( 'Warning! Elementor data is invalid!' );

		$this->prepare_bcd( $bcd );

		$this->debug( 'Elementor data found: %s', $ed );

		// Remember things.
		foreach( $ed as $index => $section )
			$this->parse_element( $bcd, $section );

		$bcd->elementor->set( 'old_post_css_filename', $this->get_post_css_file( $bcd->post->ID ) );
		$this->debug( 'Saved old Elementor CSS filename %s', $bcd->elementor->get( 'old_post_css_filename' ) );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- RESTORE
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Maybe restore the conditions.
		@since		2021-03-09 18:52:55
	**/
	public function maybe_restore_conditions( $bcd )
	{
		$conditions = $bcd->elementor->get( 'conditions' );

		if ( ! $conditions )
			return;

		$new_conditions = [];

		foreach( $conditions as $condition )
		{
			$parts = explode( '/', $condition );
			if ( ! isset( $parts[ 2 ] ) )
			{
				$new_conditions []= $condition;
				continue;
			}

			$this->debug( 'Condition: %s', $condition );

			// Taxonomies
			if ( strpos( $parts[ 2 ], 'in_' ) === 0 )
			{
				$old_term_id = $parts[ 3 ];
				$new_term_id = $bcd->terms()->get( $old_term_id );
				$parts[ 3 ] = $new_term_id;
				$this->debug( 'Replacing term %s with %s', $old_term_id, $new_term_id );
			}

			// Posts
			if ( in_array( $parts[ 2 ], [
				'page',
				'post',
				'any_child_of',
			] ) )
			{
				$old_post_id = $parts[ 3 ];
				$new_post_id = $bcd->equivalent_posts()->get_or_broadcast( $bcd->parent_blog_id, $old_post_id, get_current_blog_id() );
				$parts[ 3 ] = $new_post_id;
				$this->debug( 'Replacing post %s with %s', $old_post_id, $new_post_id );
			}

			$condition = implode( '/', $parts );
			$new_conditions []= $condition;
		}

		$this->debug( 'New conditions: %s', $new_conditions );
		$meta_key = '_elementor_conditions';
		$bcd->custom_fields()
			->child_fields()
			->update_meta( $meta_key, $new_conditions );
	}

	/**
		@brief		maybe_restore_data
		@since		2021-03-09 18:37:05
	**/
	public function maybe_restore_data( $bcd )
	{
		$meta_key = '_elementor_data';

		$ed = $bcd->custom_fields()->get_single( '_elementor_data' );

		if ( ! $ed )
			return;

		$ed = json_decode( $ed );

		if ( ! $ed )
			return;

		foreach( $ed as $index => $element )
			$ed[ $index ] = $this->update_element( $bcd, $element );

		$ed = json_encode( $ed );

		$this->debug( 'Updating elementor data: <pre>%s</pre>', htmlspecialchars( $ed ) );
		$bcd->custom_fields()
			->child_fields()
			->update_meta_json( $meta_key, $ed );

		// Copy the css file.
		if ( ! isset( $bcd->elementor ) )
			return;
		$old_filename = $bcd->elementor->get( 'old_post_css_filename' );
		$new_filename = $this->get_post_css_file( $bcd->new_post( 'ID' ) );

		// Replace the post ID in the file.
		if ( is_readable( $old_filename ) )
		{
			$css_file = file_get_contents( $old_filename );
			$css_file = str_replace( 'elementor-' . $bcd->post->ID, 'elementor-' . $bcd->new_post( 'ID' ), $css_file );

			file_put_contents( $new_filename, $css_file );

			$this->debug( 'Copied Elementor CSS file %s to %s', $old_filename, $new_filename );
		}
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Returns the post's Elementor CSS filename.
		@since		2017-08-09 17:21:09
	**/
	public function get_post_css_file( $post_id )
	{
		$wp_upload_dir = wp_upload_dir();
		$path = sprintf( '%s/elementor/css', $wp_upload_dir['basedir'] );

		if ( ! is_dir( $path ) )
		{
			$this->debug( 'Creating directory %s', $path );
			mkdir( $path, true );
		}

		$new_filename = sprintf( '%s/post-%d.css', $path, $post_id );
		return $new_filename;
	}

	/**
		@brief		Parse an EL element, looking for images and the like.
		@since		2017-04-29 02:14:28
	**/
	public function parse_element( $bcd, $element )
	{
		if ( isset( $element->settings ) )
		{
			foreach( static::$parseable_settings as $type )
			{
				if ( ! isset( $element->settings->$type ) )
					continue;
				$this->preparse_content( [
					'broadcasting_data' => $bcd,
					'content' => $element->settings->$type,
					'id' => 'elementor_' . $element->id,
				] );
			}

			foreach( static::$image_settings as $image_setting )
				if ( isset( $element->settings->$image_setting ) )
				{
					if ( $element->settings->$image_setting->id > 0 )
					{
						$image_id = $element->settings->$image_setting->id;
						if ( $bcd->try_add_attachment( $image_id ) )
							$this->debug( 'Found %s image %s.', $image_setting, $image_id );
					}
				}
		}

		if ( $element->elType == 'widget' )
		{
			switch( $element->widgetType )
			{
				case 'avante-portfolio-grid':
				case 'dyxnet-testimonial-card':
					foreach( $element->settings->slides as $slide )
					{
						$image_id = $slide->slide_image->id;
						if ( $bcd->try_add_attachment( $image_id ) )
							$this->debug( 'Found %s slide widget. Adding attachment %s', $element->widgetType, $image_id );
					}
					break;
				case 'button':
				case 'call-to-action':
				case 'heading':
				case 'image-box':
					if ( ! isset( $element->settings->link ) )
						break;
					$url = $element->settings->link->url;
					$bcd->elementor->collection( 'url' )->set( $url, $this->url_to_broadcast_data( $url ) );
					$this->debug( 'Handling %s link: %s', $element->widgetType, $url );
					break;
				case 'devices-extended':
					$image_id = $element->settings->video_cover->id;
					if ( $bcd->try_add_attachment( $image_id ) )
						$this->debug( 'Found devices-extended widget. Adding attachment %s', $image_id );
					break;
				case 'gallery':
					if ( ! isset( $element->settings->gallery ) )
						break;
					foreach( $element->settings->gallery as $gallery_index => $gallery_item )
					{
						$image_id = $gallery_item->id;
						if ( $bcd->try_add_attachment( $image_id ) )
								$this->debug( 'Found gallery widget. Adding attachment %s', $image_id );
					}
					break;
				case 'icon-box':
					if ( isset( $element->settings->selected_icon->value ) )
					{
						$image_id = $element->settings->selected_icon->value->id;
						if ( $bcd->try_add_attachment( $image_id ) )
							$this->debug( 'Found icon-box widget. Adding attachment %s', $image_id );
					}
					break;
				case 'image':
				case 'image-box':
					$image_id = $element->settings->image->id;
					if ( $bcd->try_add_attachment( $image_id ) )
						$this->debug( 'Found image widget. Adding attachment %s', $image_id );
					break;
				case 'image-carousel':
					foreach( $element->settings->carousel as $carousel_index => $carousel_item )
					{
						$image_id = $carousel_item->id;
						if ( $bcd->try_add_attachment( $image_id ) )
								$this->debug( 'Found image-carousel widget. Adding attachment %s', $image_id );
					}
					break;
				case 'image-gallery':
					foreach( $element->settings->wp_gallery as $gallery_index => $gallery_item )
					{
						$image_id = $gallery_item->id;
						if ( $bcd->try_add_attachment( $image_id ) )
								$this->debug( 'Found image-gallery widget. Adding attachment %s', $image_id );
					}
					break;
				case 'global':
					$this->debug( 'Handling global slides.' );
				case 'media-carousel':
					foreach( $element->settings->slides as $slide_index => $carousel_item )
					{
						$image_id = $carousel_item->image->id;
						if ( $bcd->try_add_attachment( $image_id ) )
								$this->debug( 'Found media-carousel slide %s. Adding attachment %s', $slide_index, $image_id );
						if ( isset( $carousel_item->image_link_to ) )
						{
							$url = $carousel_item->image_link_to->url;
							$bcd->elementor->collection( 'url' )->set( $url, $this->url_to_broadcast_data( $url ) );
						}
					}
					break;
				case 'ProductIntroFullDetail':
					foreach( [
						'bg_image',
						'bg_image_mobile',
						'image',
						'overlay_image',
						'overlay_image_mobile',
					] as $type )
					{
						$image_id = $element->settings->$type->id;
						if ( $bcd->try_add_attachment( $image_id ) )
							$this->debug( 'Found ProductIntroFullDetail %s. Adding attachment %s', $image_id );
					}
					break;
				case 'smartslider':
					// Fake a smartslider shortcode.
					$item_id = $element->settings->smartsliderid;
					$this->debug( 'Found item ID for %s is %s', $element->widgetType, $item_id );
					$preparse_content = ThreeWP_Broadcast()->new_action( 'preparse_content' );
					$preparse_content->broadcasting_data = $bcd;
					$preparse_content->content = '[smartslider3 slider="' . $item_id . '"]';
					$preparse_content->id = 'elementor_' . $element->id;
					$preparse_content->execute();
					break;
				case 'text-editor':
					// Send texts for preparsing.
					$preparse_content = ThreeWP_Broadcast()->new_action( 'preparse_content' );
					$preparse_content->broadcasting_data = $bcd;
					$preparse_content->content = $element->settings->editor;
					$preparse_content->id = 'elementor_' . $element->id;
					$preparse_content->execute();
					break;
				case 'uael-caf-styler':		// Caldera Forms.
					$caf_select_caldera_form_id = $element->settings->caf_select_caldera_form;
					$preparse_content = ThreeWP_Broadcast()->new_action( 'preparse_content' );
					$preparse_content->broadcasting_data = $bcd;
					$preparse_content->content = '[caldera_form id="' . $caf_select_caldera_form_id . '"]';
					$preparse_content->id = 'caldera_form_' . $element->id;
					$preparse_content->execute();
					break;
				case 'vt-saaspot_agency':
					$image_id = $element->settings->agency_image->id;
					if ( $bcd->try_add_attachment( $image_id ) )
						$this->debug( 'Found vt-saaspot_agency widget. Adding attachment %s', $image_id );
					break;
				case 'vt-saaspot_resource':
					foreach( $element->settings->ResourceItems as $index => $resource )
					{
						$image_id = $resource->resource_image->id;
						if ( $bcd->try_add_attachment( $image_id ) )
							$this->debug( 'Found vt-saaspot_resource widget. Adding attachment %s at index %s.', $image_id, $index );
					}
					break;
			}
		}

		if ( ! isset( $element->elements ) )
			return $element;

		// Parse subelements.
		foreach( $element->elements as $element_index => $subelement )
			$this->parse_element( $bcd, $subelement );

		return $element;
	}

	/**
		@brief		Prepare the BCD object.
		@since		2021-03-09 18:33:58
	**/
	public function prepare_bcd( $bcd )
	{
		if ( ! isset( $bcd->elementor ) )
			$bcd->elementor = ThreeWP_Broadcast()->collection();
	}

	/**
		@brief		Update the Elementor data with new values.
		@since		2017-04-29 02:26:52
	**/
	public function update_element( $bcd, $element )
	{
		if ( isset( $element->settings ) )
		{
			foreach( static::$parseable_settings as $type )
			{
				if ( ! isset( $element->settings->$type ) )
					continue;
				$element->settings->$type = $this->parse_content( [
					'broadcasting_data' => $bcd,
					'content' => $element->settings->$type,
					'id' => 'elementor_' . $element->id,
				] );
			}

			foreach( static::$image_settings as $image_setting )
				if ( isset( $element->settings->$image_setting ) )
					if ( $element->settings->$image_setting->id > 0 )
					{
						$old_image_id = $element->settings->$image_setting->id;
						$new_image_id = $bcd->copied_attachments()->get( $old_image_id );
						$this->debug( 'Replacing old %s %s with %s.', $image_setting, $old_image_id, $new_image_id );
						$element->settings->$image_setting->id = $new_image_id;
						$element->settings->$image_setting->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->$image_setting->url );
					}
			}

		if ( $element->elType == 'widget' )
		{
			switch( $element->widgetType )
			{
				case 'avante-portfolio-grid':
				case 'dyxnet-testimonial-card':
					foreach( $element->settings->slides as $slide_index => $slide )
					{
						$image_id = $slide->slide_image->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found %s slide widget. Replacing %s with %s', $element->widgetType, $image_id, $new_image_id );
						$element->settings->slides[ $slide_index ]->slide_image->id = $new_image_id;
						$element->settings->slides[ $slide_index ]->slide_image->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->slides[ $slide_index ]->slide_image->url );
					}
					break;
				case 'ae-post-blocks':
					$template_id = $element->settings->template;
					$new_template_id = $bcd->equivalent_posts()->get_or_broadcast( $bcd->parent_blog_id, $template_id, get_current_blog_id() );
					$this->debug( 'New ae-post-blocks template ID %s is %s', $template_id, $new_template_id );
					$element->settings->template = $new_template_id;

					if ( isset( $element->settings->ae_post_ids ) )
					{
						$new_ids = [];
						foreach( $element->settings->ae_post_ids as $post_id )
							$new_ids []= $bcd->equivalent_posts()->get_or_broadcast( $bcd->parent_blog_id, $post_id, get_current_blog_id() );
						$this->debug( 'New ae-post-blocks post_ids is: %s', $new_ids );
						$element->settings->ae_post_ids = $new_ids;
					}
					break;
				case 'button':
				case 'call-to-action':
				case 'heading':
				case 'image-box':
					if ( ! isset( $element->settings->link ) )
						break;
					$url = $element->settings->link->url;
					$bd = $bcd->elementor->collection( 'url' )->get( $url );
					$new_url = $this->broadcast_data_to_url( $bd, $url );
					$element->settings->link->url = $new_url;
					$this->debug( 'Replacing %s url %s with %s', $element->widgetType, $url, $new_url );
					break;
				case 'devices-extended':
					$image_id = $element->settings->video_cover->id;
					$new_image_id = $bcd->copied_attachments()->get( $image_id );
					$this->debug( 'Found devices-extended widget. Replacing %s with %s.', $image_id, $new_image_id );
					$element->settings->video_cover->id = $new_image_id;
					$element->settings->video_cover->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->video_cover->url );
					break;
				case 'gallery':
					if ( ! isset( $element->settings->gallery ) )
						break;
					foreach( $element->settings->gallery as $gallery_index => $gallery_item )
					{
						$image_id = $gallery_item->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found gallery widget. Replacing %s with %s', $image_id, $new_image_id );
						$element->settings->gallery[ $gallery_index ]->id = $new_image_id;
						$element->settings->gallery[ $gallery_index ]->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->gallery[ $gallery_index ]->url );
					}
					break;
				case 'global':
					$template_id = $element->templateID;
					$this->debug( 'Handling global widget %s', $template_id );
					$new_template_id = $bcd->equivalent_posts()->get_or_broadcast( $bcd->parent_blog_id, $template_id, get_current_blog_id() );
					$this->debug( 'New global widget ID %s is %s', $template_id, $new_template_id );
					$element->templateID = $new_template_id;
					break;
				case 'icon-box':
					if ( isset( $element->settings->selected_icon->value ) )
					{
						$image_id = $element->settings->selected_icon->value->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found icon-box widget. Replacing %s with %s.', $image_id, $new_image_id );
						$element->settings->selected_icon->value->id = $new_image_id;
						$element->settings->selected_icon->value->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->selected_icon->value->url );
					}
					break;
				case 'image':
				case 'image':
				case 'image-box':
					$image_id = $element->settings->image->id;
					$new_image_id = $bcd->copied_attachments()->get( $image_id );
					$this->debug( 'Found image widget. Replacing %s with %s.', $image_id, $new_image_id );
					$element->settings->image->id = $new_image_id;
					$element->settings->image->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->image->url );
					break;
				case 'image-carousel':
					foreach( $element->settings->carousel as $carousel_index => $carousel_item )
					{
						$image_id = $carousel_item->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found carousel widget. Replacing %s with %s', $image_id, $new_image_id );
						$element->settings->carousel[ $carousel_index ]->id = $new_image_id;
						$element->settings->carousel[ $carousel_index ]->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->carousel[ $carousel_index ]->url );
					}
					break;
				case 'image-gallery':
					foreach( $element->settings->wp_gallery as $gallery_index => $gallery_item )
					{
						$image_id = $gallery_item->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found gallery widget. Replacing %s with %s', $image_id, $new_image_id );
						$element->settings->wp_gallery[ $gallery_index ]->id = $new_image_id;
						$element->settings->wp_gallery[ $gallery_index ]->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->wp_gallery[ $gallery_index ]->url );
					}
					break;
				case 'jet-listing-grid':
					// Note that THEY typo'd 'lisitng_id'. Real quality there.
					if ( isset( $element->settings->lisitng_id ) )
					{
						$new_id = $bcd->equivalent_posts()->broadcast_once( $bcd->parent_blog_id, $element->settings->lisitng_id );
						$this->debug( 'In %s, replacing %s with %s.', $element->widgetType, $element->settings->lisitng_id, $new_id );
						$element->settings->lisitng_id = $new_id;
					}
					break;
				case 'jet-smart-filters-checkboxes':
				case 'jet-smart-filters-range':
					if ( isset( $element->settings->filter_id ) )
					{
						$new_post_ids = [];
						foreach( $element->settings->filter_id as $old_post_id )
							$new_post_ids[]= $bcd->equivalent_posts()->broadcast_once( $bcd->parent_blog_id, $old_post_id );
						$this->debug( 'In %s, replacing %s with %s.', $element->widgetType, $element->settings->filter_id, $new_post_ids );
						$element->settings->filter_id = $new_post_ids;
					}
					break;
				case 'global':
					$this->debug( 'Handling global slides.' );
				case 'media-carousel':
					foreach( $element->settings->slides as $slide_index => $carousel_item )
					{
						$image_id = $carousel_item->image->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found media-carousel slide %s. Replacing %s with %s', $slide_index, $image_id, $new_image_id );
						$element->settings->slides[ $slide_index ]->image->id = $new_image_id;
						$element->settings->slides[ $slide_index ]->image->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $carousel_item->image->url );
						if ( isset( $carousel_item->image_link_to ) )
						{
							$url = $carousel_item->image_link_to->url;
							$bd = $bcd->elementor->collection( 'url' )->get( $url );
							$new_url = $this->broadcast_data_to_url( $bd, $url );
							$this->debug( 'Replacing media-carousel image_link_to url %s with %s', $url, $new_url );
							$element->settings->slides[ $slide_index ]->image_link_to->url = $new_url;
						}
					}
					break;
				case 'ProductIntroFullDetail':
					foreach( [
						'bg_image',
						'bg_image_mobile',
						'image',
						'overlay_image',
						'overlay_image_mobile',
					] as $type )
					{
						$image_id = $element->settings->$type->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found ProductIntroFullDetail %s. Replacing %s with %s.', $type, $image_id, $new_image_id );
						$element->settings->$type->id = $new_image_id;
						$element->settings->$type->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->$type->url );
					}
					break;
				case 'template':
					$old_template_id = $element->settings->template_id;
					$new_template_id = $bcd->equivalent_posts()->get( $bcd->parent_blog_id, $old_template_id, get_current_blog_id() );
					$this->debug( 'Found template widget. Replacing %d with %d.', $old_template_id, $new_template_id );
					$element->settings->template_id = $new_template_id;
					break;
				case 'smartslider':
					// Fake a smartslider shortcode.
					$item_id = $element->settings->smartsliderid;
					$parse_content = ThreeWP_Broadcast()->new_action( 'parse_content' );
					$parse_content->broadcasting_data = $bcd;
					$parse_content->content = '[smartslider3 slider="' . $item_id . '"]';
					$parse_content->id = 'elementor_' . $element->id;
					$parse_content->execute();

					// Get the new ID
					$parse_content->content = trim( $parse_content->content, '[]' );
					$atts = shortcode_parse_atts( $parse_content->content );
					$new_value = $atts[ 'slider' ];
					$element->settings->smartsliderid = $new_value;
					$this->debug( 'New item ID for %s is %s', $element->widgetType, $new_value );
					break;
				case 'text-editor':
					$parse_content = ThreeWP_Broadcast()->new_action( 'parse_content' );
					$parse_content->broadcasting_data = $bcd;
					$parse_content->content = $element->settings->editor;
					$parse_content->id = 'elementor_' . $element->id;
					$parse_content->execute();
					$this->debug( 'Replaced element %s text-editor with %s', $element->id, htmlspecialchars( $parse_content->content ) );
					$element->settings->editor = $parse_content->content;
					break;
				case 'uael-caf-styler':		// Caldera Forms.
					// Fake a smartslider shortcode.
					$item_id = $element->settings->caf_select_caldera_form;
					$parse_content = ThreeWP_Broadcast()->new_action( 'parse_content' );
					$parse_content->broadcasting_data = $bcd;
					$parse_content->content = '[caldera_form id="' . $item_id . '"]';
					$parse_content->id = 'caldera_form_' . $element->id;
					$parse_content->execute();

					// Get the new ID
					$parse_content->content = trim( $parse_content->content, '[]' );
					$atts = shortcode_parse_atts( $parse_content->content );
					$new_value = $atts[ 'id' ];
					$element->settings->caf_select_caldera_form = $new_value;
					$this->debug( 'New item ID for %s is %s', $element->widgetType, $new_value );
					break;
				case 'vt-saaspot_agency':
					$image_id = $element->settings->agency_image->id;
					$new_image_id = $bcd->copied_attachments()->get( $image_id );
					$this->debug( 'Found vt-saaspot_agency widget. Replacing %s with %s.', $image_id, $new_image_id );
					$element->settings->agency_image->id = $new_image_id;
					$element->settings->agency_image->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->agency_image->url );
					break;
				case 'vt-saaspot_resource':
					foreach( $element->settings->ResourceItems as $index => $resource )
					{

						$image_id = $resource->resource_image->id;
						$new_image_id = $bcd->copied_attachments()->get( $image_id );
						$this->debug( 'Found vt-saaspot_resource widget. Replacing %s with %s at index %s.', $image_id, $new_image_id, $index );
						$element->settings->ResourceItems[ $index ]->resource_image->id = $new_image_id;
						$element->settings->ResourceItems[ $index ]->resource_image->url = ThreeWP_Broadcast()->update_attachment_ids( $bcd, $element->settings->ResourceItems[ $index ]->resource_image->url );
					}
					break;
				default:
					if ( isset( $element->settings->jet_attached_popup ) )
					{
						$old_popup_id = $element->settings->jet_attached_popup;
						$new_popup_id = $bcd->equivalent_posts()->get_or_broadcast( $bcd->parent_blog_id, $old_popup_id, get_current_blog_id() );
						$this->debug( 'New button jet_attached_popup for %s is %s', $old_popup_id, $new_popup_id );
						$element->settings->jet_attached_popup = $new_popup_id;
					}
					break;
			}
		}

		if ( ! isset( $element->elements ) )
			return $element;

		// Update subelements.
		foreach( $element->elements as $element_index => $subelement )
			$element->elements[ $element_index ] = $this->update_element( $bcd, $subelement );

		return $element;
	}

}
