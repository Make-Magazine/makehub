<?php

/**
 * Wall Functions
 */
class Youzer_Wall_Functions  {

	function __construct( ) {
		
		// Get Live Preview Url.
		add_action( 'wp_ajax_yz_get_url_live_preview', array( $this, 'get_live_url_preview' ) );

		// Set Wall Posts Per Page.
		add_filter( 'bp_legacy_theme_ajax_querystring', array( $this, 'wall_posts_per_page' ) );

		// Set Activity Default Filter.
		add_filter( 'bp_after_has_activities_parse_args', array( $this, 'activity_default_filter' ) );

		// Set Post Elements Visibility
		add_filter( 'bp_activity_can_favorite', array( $this, 'set_likes_visibility' ) );
		add_filter( 'bp_activity_can_comment', array( $this, 'set_comments_visibility' ) );
		add_filter( 'bp_activity_user_can_delete', array( $this, 'set_delete_visibility' ) );
		add_filter( 'bp_activity_can_comment_reply', array( $this, 'set_replies_visibility' ) );

		// Embeds Options
		add_filter( 'bp_use_oembed_in_activity', array( $this, 'enable_posts_embeds' ) );
		add_filter( 'bp_use_embed_in_activity_replies', array( $this, 'enable_comments_embeds' ) );

		// Hide Private Users Posts.
		add_filter( 'bp_activity_get_where_conditions', array( $this, 'hide_private_users_posts' ), 10 );

		// Add Delete Post Tool
		add_filter( 'yz_activity_tools', array( $this, 'add_delete_activity_tool' ), 99, 2 );

		// Add Embeds Wrapper.
		add_filter( 'bp_embed_oembed_html',  array( $this, 'embed_videos_container' ), 10, 4 );
	}

	/**
	 * Embeds Wrapper.
	 */
	function embed_videos_container( $html, $url, $attr, $rawattr ) {

		// Wrapped Providers.
		$providers = array( 'soundcloud.com', 'vimeo.com', 'youtube.com', 'youtu.be', 'dailymotion.com' );

		foreach ( $providers as $provider ) {
			
			if ( strpos( $url, $provider ) !== false ) {
				return '<div class="yz-embed-wrapper">' . $html . '</div>';	
			}
		}

	    return $html;
	}

	/**
	 * Add Delete Activity Tool.
	 */
	function add_delete_activity_tool( $tools, $post_id ) {
		
		if ( ! bp_activity_user_can_delete() ) {
			return $tools;
		}

		// Get Tool Data.
		$tools[] = array(
			'icon' => 'fas fa-trash-alt',
			'title' =>  __( 'Delete', 'youzer' ),
			'action' => 'delete-activity',
			'class' => array( 'yz-delete-tool', 'yz-delete-post' ),
			'attributes' => array( 'item-type' => 'activity', 'nonce' => wp_create_nonce( 'bp_activity_delete_link' ) )
		);

		return $tools;
	}



	/**
	 * Get Wall Posts Per page
	 */
	function wall_posts_per_page( $query ) {
		
		// Get Posts Per Page Number.
		if ( bp_is_activity_directory() ) {
			$posts_per_page = yz_options( 'yz_activity_wall_posts_per_page' );
		} elseif( bp_is_user_activity() ) {
			$posts_per_page = yz_options( 'yz_profile_wall_posts_per_page' );
		} elseif( bp_is_groups_component() ) {
			$posts_per_page = yz_options( 'yz_groups_wall_posts_per_page' );
		} else {
			$posts_per_page = '';
		}

		if ( ! empty( $posts_per_page ) ) {

			if ( ! empty( $query ) ) {
		        $query .= '&';
		    }

			// Query String.
			$query .= 'per_page=' . $posts_per_page;

		}

		// echo $query;
		return $query;
	    
	}

	/**
	 * Set Activity Default Filter
	 */
	function activity_default_filter( $retval ) { 
	    
	    // Youzer Filter Option.
	    if ( 'off' == yz_options( 'yz_enable_youzer_activity_filter' ) ) {
	        return $retval;
	    }
	    
	    if ( ! isset( $retval['type'] ) || ( isset( $retval['type'] ) && $retval['type'] == 'null' ) )  {
		    $show_everything = yz_wall_show_everything_filter();
	        $retval['action'] = $show_everything;    
	    }

	    return $retval;

	}

	/**
	 * Enable/Disable Wall Posts Likes
	 */
	function set_likes_visibility() {

		// Get Likes Visibility
		if ( 'on' == yz_options( 'yz_enable_wall_posts_likes' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enable/Disable Wall Posts Comments
	 */
	function set_comments_visibility() {

		// Get Comments Visibility
		if ( 'on' == yz_options( 'yz_enable_wall_posts_comments' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enable/Disable Wall Posts Comments Reply
	 */
	function set_replies_visibility() {

		// Get Replies Visibility
		if ( 'on' == yz_options( 'yz_enable_wall_posts_reply' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enable/Disable Wall Posts Delete Button
	 */
	function set_delete_visibility( $can_delete ) {

		// Get Delete Button Visibility
		if ( $can_delete && 'on' == yz_options( 'yz_enable_wall_posts_deletion' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enable Wall Posts Embeds
	 */
	function enable_posts_embeds() {
		if ( 'off' == yz_options( 'yz_enable_wall_posts_embeds' ) ) {
		    return false;
		}
		return true;
	}

	/**
	 * Enable Wall Comments Embeds
	 */
	function enable_comments_embeds() {
		if ( 'off' == yz_options( 'yz_enable_wall_comments_embeds' ) ) {
		    return false;
		}
		return true;
	}
		
	/**
	 * Wall - Hide Private Users Posts
	 */
	function hide_private_users_posts( $where ) {

		// If Private Profile Not Allowed Show Default Query or is an admin show all activities.
	    if ( 'off' == yz_options( 'yz_allow_private_profiles' ) || is_super_admin( bp_loggedin_user_id() ) ) {
			return $where;
	    }

	    // Get List of Private Users.
	    $private_users = yz_get_private_user_profiles();

	    // Check if there's no private users.
	    if ( empty( $private_users ) ) {
	    	return $where;
	    }

	    // Add Where Statment.
	    $where['hide_private_users'] = 'a.user_id NOT IN(' . implode( ',', $private_users ) . ')';

	    return $where;
	}

	/** 
	 * Get Url Live Preview
	 */
	function get_live_url_preview() {

		include_once YZ_PUBLIC_CORE . "functions/live-preview/classes/LinkPreview.php";

		SetUp::init();

		$data = json_decode( urldecode( base64_decode( $_POST['data'] ) ) );

		$text = $data->text;
		$imageAmount = $data->imageAmount;
		$text = str_replace( '\n', ' ', $text );
		$header = "";

		$linkPreview = new LinkPreview();
		$answer = $linkPreview->crawl( $text, $imageAmount, $header );

		echo $answer;

		SetUp::finish();

		die();
	}

}