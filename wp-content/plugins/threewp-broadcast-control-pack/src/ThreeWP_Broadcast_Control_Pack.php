<?php

namespace threewp_broadcast\premium_pack;

class ThreeWP_Broadcast_Control_Pack
	extends \threewp_broadcast\premium_pack\Plugin_Pack
{
	public $plugin_version = BROADCAST_CONTROL_PACK_VERSION;

	public function edd_get_item_name()
	{
		return 'ThreeWP Broadcast Control Pack';
	}

	public function get_plugin_classes()
	{
		return
		[
			__NAMESPACE__ . '\\all_blogs\\All_Blogs',
			__NAMESPACE__ . '\\all_blogs\\All_Blogs_Superadmin',
			__NAMESPACE__ . '\\all_images\\All_Images',
			__NAMESPACE__ . '\\attachment_control\\Attachment_Control',
			__NAMESPACE__ . '\\back_to_parent\\Back_To_Parent',
			__NAMESPACE__ . '\\comments\\Comments',
			__NAMESPACE__ . '\\custom_field_attachments\\Custom_Field_Attachments',
			__NAMESPACE__ . '\\custom_field_posts\\Custom_Field_Posts',
			__NAMESPACE__ . '\\custom_field_terms\\Custom_Field_Terms',
			__NAMESPACE__ . '\\delete_before_broadcast\\Delete_Before_Broadcast',
			__NAMESPACE__ . '\\gutenberg_attachments\\Gutenberg_Attachments',
			__NAMESPACE__ . '\\gutenberg_menus\\Gutenberg_Menus',
			__NAMESPACE__ . '\\gutenberg_posts\\Gutenberg_Posts',
			__NAMESPACE__ . '\\gutenberg_protect\\Gutenberg_Protect',
			__NAMESPACE__ . '\\gutenberg_terms\\Gutenberg_Terms',
			__NAMESPACE__ . '\\hreflang\\Hreflang',
			__NAMESPACE__ . '\\keep_child_status\\Keep_Child_Status',
			__NAMESPACE__ . '\\link_before_broadcast\\Link_Before_Broadcast',
			__NAMESPACE__ . '\\local_files\\Local_Files',
			__NAMESPACE__ . '\\local_links\\Local_Links',
			__NAMESPACE__ . '\\more_children\\More_Children',
			__NAMESPACE__ . '\\no_new_terms\\No_New_Terms',
			__NAMESPACE__ . '\\parent_pull\\Parent_Pull',
			__NAMESPACE__ . '\\per_blog_author\\Per_Blog_Author',
			__NAMESPACE__ . '\\per_blog_taxonomies\\Per_Blog_Taxonomies',
			__NAMESPACE__ . '\\permalinks\\Permalinks',
			__NAMESPACE__ . '\\protect_child_properties\\Protect_Child_Properties',
			__NAMESPACE__ . '\\redirect_all_children\\Redirect_All_Children',
			__NAMESPACE__ . '\\redirect_parent\\Redirect_Parent',
			__NAMESPACE__ . '\\scheduler\\Scheduler',
			__NAMESPACE__ . '\\search_and_replace\\Search_And_Replace',
			__NAMESPACE__ . '\\shortcode_attachments\\Shortcode_Attachments',
			__NAMESPACE__ . '\\shortcode_menus\\Shortcode_Menus',
			__NAMESPACE__ . '\\shortcode_posts\\Shortcode_Posts',
			__NAMESPACE__ . '\\shortcode_terms\\Shortcode_Terms',
			__NAMESPACE__ . '\\term_meta_attachments\\Term_Meta_Attachments',
			__NAMESPACE__ . '\\thumbnail_sizes\\Thumbnail_Sizes',
			__NAMESPACE__ . '\\unlink_on_edit\\Unlink_On_Edit',
			__NAMESPACE__ . '\\unlink_on_edit\\Unlink_On_Edit_Checkbox',
			__NAMESPACE__ . '\\update_attachments\\Update_Attachments',
			__NAMESPACE__ . '\\update_family\\Update_Family',
			__NAMESPACE__ . '\\user_blog_settings\\User_Blog_Settings',
			__NAMESPACE__ . '\\user_blog_settings_post\\User_Blog_Settings_Post',
		];
	}

	/**
		@brief		Show our license in the tabs.
		@since		2015-10-28 15:10:14
	**/
	public function threewp_broadcast_plugin_pack_tabs( $action )
	{
		$action->tabs->tab( 'control_pack' )
			->callback( [ $this, 'edd_admin_license_tab' ] )		// this, because the tabs object comes from plugin pack, not from here.
			->name_( 'Control pack license' );
	}
}
