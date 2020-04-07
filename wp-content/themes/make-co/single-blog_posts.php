<?php
/**
 * Template Name: Blog Posts
 */
	get_header();

    if ( bp_is_my_profile() || is_super_admin() ) {
			  $status = 'any';
		} else {
			  $status = 'publish';
		}
	
    $query_args = array(
		'author'        => bp_displayed_user_id(),
		'post_type'     => buddyblog_get_posttype(),
		'post_status'   => $status,
		'p'             => intval( buddyblog_get_post_id( bp_action_variable( 0 ) ) )
    );

	query_posts( $query_args );
	global $post;
   
   // we're going to display this page like it belongs to the author
	set_displayed_user(get_the_author_meta('ID'));

	$cover_image_url = bp_attachments_get_attachment( 'url', array( 'item_id' => bp_displayed_user_id() ) );
	$city_meta = yz_options( 'yz_hheader_meta_type_2' );
    $city_value = yz_get_user_field_data( $city_meta, bp_displayed_user_id() );

   do_action( 'youzer_profile_before_profile' );

?>
<div id="buddypress" class="youzer yz-page yz-profile <?php echo yz_get_profile_class(); ?>">
	
	<?php do_action( 'youzer_profile_before_content' ); ?>

	<div class="yz-content">

		<header id="yz-profile-header" class="yz-profile-header yz-hdr-v1 yz-hdr-v8 yz-header-overlay yz-header-pattern yz_effect">
			<?php do_action( 'youzer_profile_header' ); ?>
			<div class="yz-header-cover" style="background-image:url(<?php echo $cover_image_url; ?>);background-size:cover;">
				<div class="yz-cover-content">
					<div class="yz-inner-content">
						<div class="yz-profile-photo yz-photo-circle yz-photo-border yz-profile-photo-effect">
							<a href="<?php echo bp_core_get_user_domain($bp->displayed_user->id); ?>" class="yz-profile-img">
								<img src="<?php echo bp_core_fetch_avatar(array('item_id'=>$bp->displayed_user->id, 'html'=>false)); ?>" class="avatar user-2-avatar avatar-150 photo" alt="Profile Photo" width="150" height="150">
							</a>
						</div>					
						<div class="yz-head-content">
							<div class="yz-name">
								<h2><?php echo bp_displayed_user_username() ?></h2>
							</div>
							<div class="yz-usermeta">
								<ul>
									<li><i class="fas fa-globe"></i><span><?php echo bp_displayed_user_username() ?></span></li>
									<?php if(!empty($city_value)) { ?>
									<li><i class="far fa-hospital"></i><span><?php echo $city_value; ?></span></li>
									<?php } ?>
								</ul>
							</div>											
						</div>						
					</div>	
				</div>
			</div>
		</header>
   	<div class="yz-profile-content">

			<div class="yz-inner-content">

            <?php do_action( 'youzer_profile_settings' ); ?>
				<?php do_action( 'youzer_profile_navbar' ); ?>
				<div class="container breadcrumbs">
					<a href="<?php echo(bp_core_get_user_domain($bp->displayed_user->id).'blog'); ?>">&laquo; Back to <?php echo(bp_displayed_user_username()); ?>'s Blog</a>
				</div>
				<main class="yz-page-main-content">
 
					<?php do_action( 'bp_before_member_home_content' ); ?>
					 <div class="user-post">
						  <div class="post-header">
						  		<h1><?php the_title();?></h1>
							  	<span class="alignright"><?php printf( __( '%1$s', 'buddyblog' ), get_the_time( get_option( 'date_format' ) ) ); ?></span>
						  </div>

						  <div class="post-entry">

							  <?php if( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ): ?>
									 <div class="post-featured-image">
										  <?php  the_post_thumbnail();?>
									 </div>

							  <?php endif;?>  

								<div class="entry">
									  <?php the_content(  ); ?>
								</div>
							  
							   <div class="clear"></div>

								<?php 
							      if($bp->displayed_user->id === get_current_user_id()) {
										echo("<div class='universal-btn edit-btn'>".buddyblog_get_edit_link()."</div>"); 
									}
							   ?>
							  
							   <div class="clear"></div>
							  
							   <?php 
							     // need to set this to null to allow the comment form to show up!
							     $bp->displayed_user->id = null;
							   ?>
							  	<div id="comments">
									<ol class="comment-list">
										<?php echo wp_list_comments('callback=format_comment&page=' . $post->ID); ?>
									</ol><!-- .comment-list -->

									<?php do_action( 'bp_after_blog_comment_list' ); ?>

									<?php if ( get_option( 'page_comments' ) ) : ?>
										<div class="comment-navigation paged-navigation">
											<?php paginate_comments_links(); ?>
										</div>
									<?php endif; ?>

								</div><!-- #comments -->
							   <?php
							     if ( $post->comment_status == 'open' ) comment_form(); 
							     // comments template not working, so have to have the ridiulouseness above
							     comments_template(); 
							   ?>
							  
								<div class="clear"></div>

						  </div>
   
						 
					</div>
				</main>
			</div>
		</div>
						
        <?php
            //used to hook back BuddyPress Theme compatibility comment closing function
            do_action( 'buddyblog_after_blog_post' );
        ?>
   </div>
</div>


<?php 
wp_reset_postdata();
wp_reset_query();
get_footer();
?>  