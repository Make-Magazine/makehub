<?php

class Youzer_Wall {

	protected $youzer;

    public function __construct() {

    	// Include Wall Functions.
    	$this->include_files();

    	// Add Wall Sidebar.
		add_action( 'yz_global_wall_sidebar', array( &$this, 'get_wall_sidebar' ) );
		
		// Wall Post Attachments
		add_action( 'bp_activity_entry_content', array( &$this, 'get_post' ) );

		// Fomat Post
		add_filter( 'bp_get_activity_content_body', array( &$this, 'get_activity_content_body' ), 10, 2 );

		// Open Activity Post and Comment link on new tabs.
		add_filter( 'bp_get_activity_content_body', array( &$this, 'open_links_in_new_tabs' ) );
		add_filter( 'bp_activity_comment_content', array( &$this, 'open_links_in_new_tabs' ) );
		add_filter( 'bp_get_the_thread_message_content', array( &$this, 'open_links_in_new_tabs' ) );

		// Remove Blog Posts Default Content .
		add_filter( 'bp_activity_create_summary', '__return_false' );

		// Edit Wall Filters.
		add_filter( 'bp_get_activity_show_filters_options', array( &$this, 'edit_wall_filter' ) );

		// Add Embed Urls in a new line so they can be converted to iframes.
		add_action( 'bp_activity_new_update_content', array( &$this, 'activate_autoembed' ) );

		// Add Wall Commnets Number for Non Logged-In Users.
		add_action( 'bp_activity_entry_meta_non_logged_in', array( &$this, 'show_wall_post_comments_number' ) );

    }

    /**
     * Include Wall Files.
     */
    function include_files() {

    	// Include Files.
    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-form.php';
    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-hashtags.php';
    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-functions.php';
    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-attachments.php';

    	// Init Classes.
    	$form = new Youzer_Wall_Form();
    	$hastags = new Youzer_Wall_Hashtags();
    	$functions = new Youzer_Wall_Functions();
    	$attachments = new Youzer_Wall_Attachments();

    	if ( yz_enable_activity_privacy() ) {
	    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-privacy.php';
	    	$privacy = new Youzer_Wall_Privacy();
    	}

    	if ( yz_enable_activity_mood() ) {
	    	require_once YZ_PUBLIC_CORE . 'wall/yz-class-mood.php';
	    	$mood = new Youzer_Mood();
    	}

    	if ( yz_enable_activity_tag_friends() ) {
			require_once YZ_PUBLIC_CORE . 'wall/yz-class-tag-users.php';
			$tag_users = new Youzer_Wall_Tag_Users();
    	}

    }

	/**
	 * Get Wall Post Content.
	 */
	function get_activity_content_body( $content, $activity ) {
	    // Check if activity content is not empty.
	    if ( ! empty( $content ) ) {
	    	$content = '<div class="activity-inner">' . $content . '</div>';
	    }
	    
	    // Filter
	    $content = apply_filters( 'yz_get_activity_content_body', $content, $activity );

	    return $content;
	}

    /**
     * Post Emebds & Attachments.
     */
    function get_post() {

		global $activities_template;

		// Get Activity.
		$activity = $activities_template->activity;

    	// Get Embeds Attachments.
    	$this->embeds( $activity );

    	// Get Post Attachments.
    	$this->post_attachments( $activity );
    
    }
    
    /**
     * Post.
     */
    function embeds( $activity = null ) {

		switch ( $activity->type ) {

			case 'joined_group':
		        $content = bp_is_groups_component() ? $this->embed_user( $activity->user_id ) : $this->embed_group( $activity->item_id );
				break;

			case 'created_group':
		        $content = $this->embed_group( $activity->item_id );
				break;

			case 'new_member':
        		$content = $this->embed_user( $activity->user_id );
				break;
				
			case 'updated_profile':
        		$content = $this->embed_user( $activity->user_id );
				break;
				
			case 'friendship_created':
        		$user_id = ( bp_is_user() && bp_displayed_user_id() != $activity->user_id ) ? $activity->user_id : $activity->secondary_item_id; 
        		$content = $this->embed_user( $user_id );
				break;
				
			case 'new_blog_post':
    			$content = $this->embed_post( $activity->item_id, $activity->secondary_item_id );
				break;
		}

		// Get Embed Post.
	    if ( ! empty( $content ) ) {
	    	echo '<div class="yz-activity-embed"><p>' . $content . '</p></div>';
    	}
    		
    }

    /**
     * Get Wall Attachments.
     */
	function post_attachments( $activity = null ) {
		
		// Get Attachments
		$attachments = yz_get_activity_attachments( $activity->id );

		echo '<div class="yz-post-attachments">';

		// if ( empty( $attachments ) ) {
			// }
		switch ( $activity->type ) {
			case 'activity_photo':
				$this->get_wall_post_images( $attachments, $activity->id );
				break;
			case 'activity_video':
				$this->get_wall_post_video( $attachments );
				break;
			case 'activity_audio':
				$this->get_wall_post_audio( $attachments );
				break;
			case 'activity_link':
				$this->get_wall_post_link( $attachments, $activity->id );
				break;
			case 'activity_slideshow':
				$this->get_wall_post_slideshow( $attachments );
				break;		
			case 'activity_file':
				$this->get_wall_post_file( $attachments, $activity->id );
				break;
			case 'activity_quote':
				$this->get_wall_post_quote( $attachments, $activity->id );
				break;
			case 'new_cover':
				$this->get_wall_post_cover( $attachments );
				break;
			case 'new_avatar':
				$this->get_wall_post_avatar( $attachments, $activity->id  );
				break;
			case 'activity_giphy':
				$this->get_wall_post_giphy( $activity->id );
				break;
		}

		// Get Url Preview
		$this->get_activity_url_preview( $activity->id, $activity->content );

		echo '</div>';

	}

	/**
	 * Cover Post.
	 */
	function get_wall_post_cover( $attachments ) {

		// Get Cover Photo Url.
		$cover_url =  yz_get_wall_file_url( $attachments[0] );
		
		if ( $cover_url ) {
			echo '<img src="' . $cover_url . '" alt="">';
		}

	}

	/**
	 * Avatar Post.
	 */
	function get_wall_post_avatar( $attachments, $activity_id ) {

		// Get avatar Photo Url.
		$avatar_url = yz_get_wall_file_url( $attachments[0] );

		if ( ! empty( $avatar_url ) ) {
			echo '<a href="' . $avatar_url . '" data-lightbox="yz-post-' . $activity_id . '" class="yz-img-with-padding"><img src="' . $avatar_url . '" alt=""></a>';
		}

	}

	/**
	 * Cover Post.
	 */
	function get_wall_post_giphy( $activity_id ) {

		// Get Image Url.
		$img_url = bp_activity_get_meta( $activity_id, 'giphy_image' );

		?>
		<a href="<?php echo $img_url; ?>" rel="nofollow" class="yz-img-with-padding" data-lightbox="yz-post-<?php echo $activity_id; ?>">
			<img src="<?php echo $img_url; ?>" alt="" />
		</a>
		<?php

	}

	/**
	 * Quote Post.
	 */
	function get_wall_post_quote( $attachments, $activity_id ) {

		// Get Quote Cover Url. 
		$cover_img = ! empty( $attachments ) ? yz_get_wall_file_url( $attachments[0] ) : false;

		// Get Link Data
		$quote_txt = bp_activity_get_meta( $activity_id, 'yz-quote-text' );
		$quote_owner = bp_activity_get_meta( $activity_id, 'yz-quote-owner' );

		// Get User Data
	    $quote_bg = "style='background-image:url( $cover_img );'";

	    ?>

	    <div class="yzw-quote-post">
		    <div class="yzw-quote-content quote-with-img">
		        <?php if ( $cover_img ) : ?>
		            <div class="yzw-quote-cover" <?php echo $quote_bg; ?>></div>
		        <?php endif; ?>
		        <div class="yzw-quote-main-content">
		            <div class="yzw-quote-icon"><i class="fas fa-quote-right"></i></div>
		            <blockquote><?php echo $quote_txt; ?></blockquote>
		            <h3 class="yzw-quote-owner"><?php echo $quote_owner; ?></h3>
		        </div>
		    </div>
	    </div>
		
		<?php
	}

	/**
	 * File Post.
	 */
	function get_wall_post_file( $attachments, $activity_id ) { 

		// Get Attachment Data
		$data = yz_get_activity_attachments( $activity_id, 'data' );

		// Get File Data
		$real_name = $data[0]['real_name'];
	    $file_url  = yz_get_wall_file_url( $attachments[0] ); 
		$name_excerpt = yz_get_filename_excerpt( $real_name, 45 );
		$file_size = yz_file_format_size( $data[0]['file_size'] );

		?>

		<div class="yzw-file-post">
			<i class="fas fa-cloud-download-alt yzw-file-icon"></i>
			<div class="yzw-file-title" title="<?php echo $real_name; ?>"><?php echo $name_excerpt; ?></div>
			<div class="yzw-file-size"><?php echo $file_size; ?></div>
			<a rel="nofollow" href="<?php echo $file_url; ?>" class="yzw-file-download"><i class="fas fa-download"></i><?php _e( 'download', 'youzer' ); ?></a>
		</div>

		<?php
	}

	/**
	 * Link Post.
	 */
	function get_wall_post_link( $attachments, $activity_id ) {

		// Get Link Data
		$link_url = bp_activity_get_meta( $activity_id, 'yz-link-url' );
		$link_desc = bp_activity_get_meta( $activity_id, 'yz-link-desc' );
		$link_title = bp_activity_get_meta( $activity_id, 'yz-link-title' );

		?>

		<a class="yz-wall-link-content" rel="nofollow" href="<?php echo $link_url; ?>" target="_blank">
			<?php if ( ! empty( $attachments ) && isset( $attachments[0]['original'] ) ) : ?>
				<img src="<?php echo yz_get_wall_file_url( $attachments[0] ); ?>" alt="">
			<?php endif; ?>
			<div class="yz-wall-link-data">
				<div class="yz-wall-link-title"><?php echo $link_title; ?></div>
				<div class="yz-wall-link-desc"><?php echo $link_desc; ?></div>
				<div class="yz-wall-link-url"><?php echo $link_url; ?></div>
			</div>
		</a>

		<?php
	}

	/**
	 * Check if we should show Live Url Preview.
	 **/
	function show_url_preview( $data, $activity_content = null ) {

		$show = true;

		// Remove Empty Spaces.
		// $activity_content = trim( $activity_content );
		
		// Get Preview Link.
		$preview_link = ! empty( $data['link'] ) ? $data['link'] : null;

		if ( empty( $data ) || empty( $preview_link ) ) {
			$show = false;
		}
			
		if ( $show == true ) {

			// Get Post Urls.
			preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $activity_content, $match );

			if ( isset( $match[0] ) && ! empty( $match[0] ) ) {

				foreach ( array_unique( $match[0] ) as $link ) {

					if ( wp_oembed_get( $link ) ) {
						$show = false;
						break;
					}

				}
			
			}
		}

		// 	if ( strpos( $activity_content, 'youtu.be' ) !== false ) {
		// 		$activity_content = preg_replace( "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","https://www.youtube.com/watch?v=$2", $activity_content );
		// 	}

		// 	// Check if preview url already exist in content.
		// 	if ( ! empty( $activity_content ) && strpos( $preview_link, $activity_content ) !== false ) {

		// 		// Get Embed Url Code.
		// 	    $embed_code = wp_oembed_get( $preview_link );

		// 	    // Check if url is supported from buddypress.
		// 	    if ( $embed_code ) {
		// 	    	$show = false;
		// 	    }

		// 	}
		// }

		return apply_filters( 'yz_display_activity_live_url_preview', $show );
	}

	/**
	 * Get Url Preview
	 */
	function get_activity_url_preview( $activity_id, $activity_content = null ) {

		// Get Url Data.
		$url = bp_activity_get_meta( $activity_id, 'url_preview' );

		// Unserialize data.
		$url = is_serialized( $url ) ? unserialize( $url ) : maybe_unserialize( base64_decode( $url ) );
		
		if ( ! $this->show_url_preview( $url, $activity_content ) ) {
			return;
		}

		?>
 
		<a class="yz-wall-link-content" rel="nofollow" href="<?php echo $url['link']; ?>" target="_blank">
			<?php if ( ! empty( $url['image'] ) && ( empty( $url['use_thumbnail'] ) || $url['use_thumbnail'] == 'off' ) ) : ?><div class="yz-wall-link-thumbnail" style="background-image:url(<?php echo $url['image']; ?>);"></div><?php endif; ?>
			<div class="yz-wall-link-data">
				<?php if ( ! empty( $url['title'] ) ) : ?><div class="yz-wall-link-title"><?php echo $url['title']; ?></div><?php endif; ?>
				<?php if ( ! empty( $url['description'] ) ) : ?><div class="yz-wall-link-desc"><?php echo $url['description']; ?></div><?php endif; ?>
				<?php if ( ! empty( $url['site'] ) ) : ?><div class="yz-wall-link-url"><?php echo $url['site']; ?></div><?php endif; ?>
			</div>
		</a>
		<?php

	}

	/**
	 * Audio Post.
	 */
	function get_wall_post_audio( $attachments ) {

		?>

		<audio controls>
			<source src="<?php echo yz_get_wall_file_url( $attachments[0] ); ?>" type="audio/mpeg">
			<?php _e( 'Your browser does not support the audio element.', 'youzer' ); ?>
		</audio>

		<?php

	}

	/**
	 * Video Post.
	 */
	function get_wall_post_video( $attachments ) {

		$video = $attachments[0];

		if ( ! isset( $video['provider'] ) || $video['provider'] != 'local' ) {
			return;
		}


		$video_content = '<video width="100%" controls preload="metadata"><source src="' . yz_get_wall_file_url( $attachments[0], true ) . '" type="video/mp4">' . __( 'Your browser does not support the video tag.', 'youzer' ) . '</video>';
	
		echo apply_filters( 'yz_get_wall_post_video', $video_content, $attachments );

	}

	/**
	 * Slideshow Post.
	 */
	function get_wall_post_slideshow( $slides ) {

        // Get Slides Height Option
        $height_option = yz_options( 'yz_slideshow_height_type' );

		?>

	    <div class="yzw-slider yz-slides-<?php echo $height_option; ?>-height">

	    <?php

	       	foreach ( $slides as $slide ) :

	        // Get Slide Image Url
	        $slide_url = yz_get_wall_file_url( $slide );

		?>

		<div class="yzw-slideshow-item">

            <?php if ( 'auto' == $height_option ) : ?>
            <img src="<?php echo $slide_url; ?>" alt="" >
            <?php else : ?>
            <div class="yzw-slideshow-img" style="background-image: url(<?php echo $slide_url; ?>)" ></div>
            <?php endif; ?>
	    </div>

	    <?php endforeach; ?>

		</div>

		<?php

	}

	/**
	 * Photo Post.
	 */
	function get_wall_post_images( $attachments, $activity_id ) {

		if ( empty( $attachments ) ) {
			return;
		}
		
		// Get Attachments number.
		$count_atts = count( $attachments );		

		if ( 1 == $count_atts && ! empty( $attachments[0] ) ) { ?>
			
			<?php $img_url = yz_get_wall_file_url( $attachments[0] ); ?>
			<?php 
				$size = yz_get_image_size( $img_url ); 
				$class = isset( $size[0] ) && ( $size[0] < 800 ) ? 'yz-img-with-padding' : 'yz-full-width-img';
			 ?>
			<a href="<?php echo $img_url; ?>" rel="nofollow" class="<?php echo $class; ?>" data-lightbox="yz-post-<?php echo $activity_id; ?>">
				<?php if ( yz_limit_wall_posts_image_height() ) : ?>
					<div class="yz-limited-image-height" style="background-image: url(<?php echo $img_url; ?>)"></div>
					<?php else : ?>
					<img src="<?php echo $img_url; ?>" alt="" />
				<?php endif; ?>
			</a>
			
			<?php } elseif ( 2 == $count_atts || 3 == $count_atts ) { ?>

			<div class="yz-post-<?php echo $count_atts; ?>imgs">

				<?php foreach( $attachments as $i => $attachment ) : ?>
					
					<?php $img_url = yz_get_wall_file_url( $attachment ); ?>
					<a class="yz-post-img<?php echo $i + 1;?>" rel="nofollow" href="<?php echo $img_url; ?>" data-lightbox="yz-post-<?php echo $activity_id; ?>">
						<div class="yz-post-img" style="background-image: url(<?php echo $img_url; ?>)"></div>
					</a>

				<?php endforeach; ?>

			</div>

		<?php } elseif ( 4 <= $count_atts ) { ?>

			<div class="yz-post-4imgs">
				
				<?php foreach( $attachments as $i => $attachment ) : ?>
				<?php $img_url = yz_get_wall_file_url( $attachment ); ?>
				<a class="yz-post-img<?php echo $i + 1; if ( 3 == $i && $count_atts > 4  ) { echo ' yz-post-plus4imgs'; }?>" href="<?php echo $img_url; ?>" rel="nofollow" data-lightbox="yz-post-<?php echo $activity_id; ?>">
					<div class="yz-post-img" style="background-image: url(<?php echo $img_url; ?>)">
						<?php 
							if ( 3 == $i && $count_atts > 4 ) {
								$images_nbr = $count_atts - 4;
								echo '<span class="yz-post-imgs-nbr">+' . $images_nbr . '</span>';
							}
						?>
					</div>
				</a>

				<?php endforeach; ?>

			</div>
			<?php
		}
	}

	/**
	 * 	Wall Embed Group
	 */
	function embed_group( $group_id = false ) {

		if ( ! $group_id ) {
			return false;
		}

		ob_start();
		
		global $Youzer;

	    $group = groups_get_group( array( 'group_id' => $group_id ) );

	    // Get Group Avatar
		$avatar_path = bp_core_fetch_avatar( 
			array(
				'item_id' => $group_id,
				'type'	  => 'full',
				'html' 	  => false,
				'object'  => 'group',
			)
		);

		// Get Cover Photo Path.
	    $cover_path = bp_attachments_get_attachment( 'url', array(
	        'object_dir' => 'groups',
	        'item_id' => $group_id
	        )
	    );

	    // Get Profile Link.
	    $group_url = bp_get_group_permalink( $group );

	    // Get Group Members Number
	    $members_count = bp_get_group_total_members( $group );

		?>

	 	<div class="yz-wall-embed yz-wall-embed-group">
	 		<div class="yz-embed-cover" <?php $this->get_embed_item_cover( $cover_path ); ?>></div>
	 		<a href="<?php echo $group_url; ?>" class="yz-embed-avatar" style="background-image: url( <?php echo $avatar_path; ?> );"></a>
	 		<div class="yz-embed-data">
	 			<div class="yz-embed-head">
		 			<a href="<?php echo $group_url; ?>" class="yz-embed-name"><?php echo $group->name; ?></a>
		 			<div class="yz-embed-meta">
		 				<div class="yz-embed-meta-item"><?php $Youzer->group->status( $group ); ?></div>
		 				<div class="yz-embed-meta-item">
		 					<i class="fas fa-users"></i><span><?php echo sprintf( _n( '%s member', '%s members', $members_count, 'youzer' ), bp_core_number_format( $members_count ) ); ?></span>
		 				</div>
		 			</div>
	 			</div>
	 			<div class="yz-embed-action">
	 				<?php do_action( 'yz_wall_embed_group_actions' );?>
	 				<?php bp_group_join_button( $group ); ?>
	 			</div>
	 		</div>
	 	</div>

		<?php

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

	}

	/**
	 * Wall Embed User
	 */
	function embed_user( $user_id = false ) {

		if ( ! $user_id ) {
			return false;
		}

		global $Youzer;

		ob_start();

		// Get Avatar Path.
		$avatar_path = bp_core_fetch_avatar( 
			array(
				'item_id' => $user_id,
				'type'	  => 'full',
				'html' 	  => false,
			)
		);

		// Get Cover Photo Path.
	    $cover_path = $Youzer->user->cover( 'url', $user_id );

	    // Get Profile Link.
	    $profile_url = bp_core_get_user_domain( $user_id );

		?>

	 	<div class="yz-wall-embed yz-wall-embed-user">
	 		<div class="yz-embed-cover" <?php $this->get_embed_item_cover( $cover_path ); ?>></div>
	 		<a href="<?php echo $profile_url; ?>" class="yz-embed-avatar" style="background-image: url( <?php echo $avatar_path; ?> );"></a>
	 		<div class="yz-embed-data">
	 			<div class="yz-embed-head">
		 			<a href="<?php echo $profile_url; ?>" class="yz-embed-name"><?php echo bp_core_get_user_displayname( $user_id ); ?></a>
		 			<div class="yz-embed-meta">@<?php echo bp_core_get_username( $user_id ); ?></div>
	 			</div>
	 			<div class="yz-embed-action">
	 				<?php do_action( 'yz_wall_embed_user_actions' ); ?>
	 				<?php if ( bp_is_active( 'friends' ) ) { bp_add_friend_button( $user_id ); } ?>
	 				<?php yz_send_private_message_button( $user_id ); ?>
	 			</div>
	 		</div>
	 	</div>

		<?php

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

	}

	/**
	 * Get Embed Cover.
	 */
	function get_embed_item_cover( $cover_path ) {

		$default_cover_path = yz_get_default_profile_cover();

	    if ( ! empty( $cover_path ) && $cover_path != $default_cover_path ) {		
			// Get Cover Style.
			$cover_style = 'background-size: cover;';
	    } else {
			// If cover photo not exist use pattern.
			$cover_path = $default_cover_path;
			// Get Cover Style.
			$cover_style = 'background-size: auto;';
	    }

		// print Cover
		echo "style='background-image:url( $cover_path ); $cover_style'";

	}


	/**
	 * 	Wall New Post Thumbnail
	 */
	function embed_post_thumbnail( $post_id = false ) {

		// Get Image ID.
		$img_id = get_post_thumbnail_id( $post_id );

		// Get Image Url.
	    $img_url = wp_get_attachment_image_src( $img_id , 'large' );

	    if ( ! empty( $img_url[0] ) ) {
	        $thumbnail = '<img src="'. $img_url[0] . '" alt"">';
	    } else {

	    	// Get Post Format
	    	$post_format = get_post_format();

	        // Set Post Format
	        $format = ! empty( $post_format ) ? $post_format : 'standard';

			// If cover photo not exist use pattern.
			$cover_path = YZ_PA . 'images/geopattern.png';	

	        // Get Thumbnail.
	        $thumbnail = '<div class="yz-wall-nothumb" style="background-image:url( ' . $cover_path . ' );">';
	        $thumbnail .= '<div class="yz-thumbnail-icon"><i class="' . yz_get_format_icon( $format ) . '"></i></div>';
	        $thumbnail .= '</div>';

	    }

	    return $thumbnail;
	}

	/**
	 * 	Wall Embed Post
	 */
	function embed_post( $blog_id = false, $post_id = false ) {

		if ( ! $post_id ) {	
			return false;
		}

	    switch_to_blog( $blog_id );

	    // Get Post Data.
	    $post = get_post( $post_id );

	    // Get Categories
	    $post_link = get_the_permalink( $post_id );
	    $post_tumbnail = $this->embed_post_thumbnail( $post_id );
	    $categories = get_the_category_list( ', ', ' ', $post_id );

	    restore_current_blog();


		ob_start();

		?>

	 	<div class="yz-wall-new-post">
	 		<div class="yz-post-img"><a href="<?php echo $post_link; ?>"><?php echo $post_tumbnail; ?></a></div>

	 		<?php do_action( 'yz_after_wall_new_post_thumbnail', $post_id ); ?>

	 		<div class="yz-post-inner">
		 			
		 		<div class="yz-post-head">
		 			<div class="yz-post-title"><a href="<?php echo $post_link; ?>"><?php echo $post->post_title; ?></a></div>
		 			<div class="yz-post-meta">
		 				<?php if ( ! empty( $categories ) ) : ?>
		 				<div class="yz-meta-item"><i class="fas fa-tags"></i><?php echo $categories; ?></div>
		 				<?php endif; ?>
		 				<div class="yz-meta-item"><i class="far fa-calendar-alt"></i><?php echo get_the_date( 'F j, Y', $post_id ); ?></div>
		 				<div class="yz-meta-item"><i class="far fa-comments"></i><?php echo $post->comment_count; ?></div>
		 			</div>
		 		</div>
		 		<div class="yz-post-excerpt">
			        <p><?php echo yz_get_excerpt( $post->post_content, 40 ); ?></p>
		 		</div>
		 		<a href="<?php echo $post_link; ?>" class="yz-post-more-button"><span class="yz-btn-icon"><i class="fas fa-angle-double-right"></i></span><span class="yz-btn-title"><?php echo apply_filters( 'yz_wall_embed_blog_post_read_more_button', __( 'read more', 'youzer' ) ); ?></span></a>
	 		</div>
	 	</div>

		<?php

		$content = ob_get_contents();

		ob_end_clean();

		return $content;

	}

    /**
     * Activate Embeds.
     */
	function activate_autoembed( $content ) {
		
		// Get Post Urls.
		preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $match );

		if ( ! isset( $match[0] ) && empty( $match[0] ) ) {
			return $content;
		}

		foreach ( array_unique( $match[0] ) as $link ) {

			if ( ! wp_oembed_get( $link ) ) {
				continue;
			}

			$content = str_replace( $link, "\n$link\n", $content );
		}

		return $content;

	}

	/**
	 * Get Wall Comments.
	 */
	function show_wall_post_comments_number() {

		// Check if comments allowed.
		if ( ! bp_activity_can_comment() ) {
			return false;
		}

		if ( is_user_logged_in() || 0 == bp_activity_get_comment_count() ) {
			return false;
		}

		?>

		<div class="yz-post-comments-nbr">
			<i class="far fa-comments"></i>
			<?php yz_wall_get_comment_button_title(); ?>
		</div>

		<?php

	}
	/**
	 * Wall Filter Bar.
	 */
	function edit_wall_filter( $filters ) {

		// Get Wall Post Types.
		$post_types = yz_get_wall_post_types_visibility();

		foreach ( $filters as $filter => $name ) {
			if ( isset( $post_types[ $filter ] ) && 'off' == $post_types[ $filter ] ) {
				unset( $filters[ $filter ] );
			}
		}

		// Unset Friendship Filter.
		if ( 'off' == $post_types['friendship_created'] && 'off' == $post_types['friendship_accepted'] ) {
			if ( isset( $filters['friendship_accepted,friendship_created'] ) ) {
				unset( $filters['friendship_accepted,friendship_created'] );
			}
		}

		// Get Unwanted Filters.
		$unwanted_filters = array( 'group_details_updated', 'activity_update', 'update_avatar', 'updated_profile' );

		// Unset Unwanted Filters.
		foreach ( $unwanted_filters as $filter ) {
			if ( isset( $filters[ $filter ] ) ) {
				unset( $filters[ $filter ] );
			}
		}

		return $filters;
	}

	/**
	 * Call Wall Sidebar
	 */
	function get_wall_sidebar() {
		
		if ( apply_filters( 'yz_activate_activity_stream_sidebar', true ) ) {

		  	// Display Widgets.
			if ( is_active_sidebar( 'yz-wall-sidebar' ) ) {
				dynamic_sidebar( 'yz-wall-sidebar' );
			}

		}

	}

	/**
	 * Redirect User.
	 */
	public function redirect( $action, $code, $redirect_to = null ) {

	    // Get Reidrect page.
	    $redirect_to = ! empty( $redirect_to ) ? $redirect_to : wp_get_referer();

	    // Add Message.
	    bp_core_add_message( $this->msg( $code ), $action );

		// Redirect User.
        wp_redirect( $redirect_to );

        // Exit.
        exit;

	}

	/**
	 * Open Wall Post & Comment Content On New Tab.
	 */
	function open_links_in_new_tabs( $content ) {

		if ( ! empty( $content ) ) {

		  	$pattern = '/<a(.*?)?href=[\'"]?[\'"]?(.*?)?>/i';
		    
		    $content = preg_replace_callback( $pattern, function( $m ) {
			        
		        $tpl = array_shift( $m );
		        $hrf = isset( $m[1] ) ? $m[1] : null;
		        
		        if ( preg_match( '/target=[\'"]?(.*?)[\'"]?/i', $tpl ) ) {
		            return $tpl;
		        }

		        if ( trim( $hrf ) && 0 === strpos( $hrf, '#' ) ) {
		            return $tpl;
		        }

		        return preg_replace_callback( '/href=/i', function( $m2 ) {
		            return sprintf( 'target="_blank" %s', array_shift( $m2 ) );
		        }, $tpl );

	    	}, $content );

		}
	    
		return $content;
	}

    /**
     * Get Attachments Error Message.
     */
    public function msg( $code ) {

        // Messages
        switch ( $code ) {

            case 'invalid_image_extension':
            	// Get Image Allowed Extentions.
            	$image_extentions = yz_get_allowed_extentions( 'image', 'text' );
                return sprintf( __( 'Invalid Image Extension.<br> Only %1s are allowed.', 'youzer' ), $image_extentions );

            case 'invalid_video_extension':
            	// Get Video Allowed Extentions.
            	$video_extentions = yz_get_allowed_extentions( 'video', 'text' );
                return sprintf( __( 'Invalid Video Extension.<br> Only %1s are allowed.', 'youzer' ), $video_extentions );

            case 'invalid_file_extension':
            	// Get File Allowed Extentions.
            	$file_extentions = yz_get_allowed_extentions( 'file', 'text' );
                return sprintf( __( 'Invalid file extension.<br> Only %1s are allowed.', 'youzer' ), $file_extentions );

            case 'invalid_audio_extension':
            	// Get Audio Allowed Extentions.
            	$audio_extentions = yz_get_allowed_extentions( 'audio', 'text' );
                return sprintf( __( 'Invalid Audio Extension.<br> Only %1s are allowed.', 'youzer' ), $audio_extentions );

            case 'unpermitted_extension':
                return __( 'Sorry, this file type is not permitted for security reasons.', 'youzer' );

            case 'max_one_file':
                return __( "You can't upload more than one file.", 'youzer' );

            case 'empty_status':
                return __( "Please type some text before posting.", 'youzer' );
                
            case 'invalid_post_type':
                return __( "Invalid post type.", 'youzer' );

            case 'invalid_link_url':
                return __( "Invalid link url.", 'youzer' );

            case 'empty_link_url':
                return __( "Empty link url.", 'youzer' );

            case 'empty_link_title':
                return __( "Please fill the link title field.", 'youzer' );

            case 'empty_link_desc':
                return __( "Please fill the link description field.", 'youzer' );

            case 'empty_quote_owner':
                return __( "Please fill the quote owner field.", 'youzer' );

            case 'empty_quote_text':
                return __( "Please fill the quote text field.", 'youzer' );

            case 'no_attachments':
                return __( "No attachment was uploaded.", 'youzer' );

            case 'slideshow_need_images':
                return __( "Slideshows need at least 2 images to work.", 'youzer' );

            case 'select_image':
                return __( 'Please select an image image before posting.', 'youzer' );

            case 'select_gif_image':
                return __( 'Please select a Gif image.', 'youzer' );

            case 'max_files_number':
        		$max_files = yz_options( 'yz_attachments_max_nbr' );
                return sprintf( __( "You can't upload more than %d files.", 'youzer' ), $max_files );
                
            case 'invalid_file_size':
        		$max_size = yz_options( 'yz_attachments_max_size' );
                return sprintf( __( 'File too large. File must be less than %g megabytes.', 'youzer' ), $max_size );
        }

        return __( 'An unknown error occurred. Please try again later.', 'youzer' );
    }

}