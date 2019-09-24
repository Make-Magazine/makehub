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
   //var_dump($post);

	global $Youzer; 
   
   // we're going to display this page like it belongs to the author
	set_displayed_user(get_the_author_meta('ID'));

	// Get Header Data
	$header_effect = yz_options( 'yz_hdr_load_effect' );
	$header_data   = $Youzer->widgets->get_loading_effect( $header_effect );
	$header_class  = $Youzer->header->get_class( 'user' );

   do_action( 'youzer_profile_before_profile' );

?>
<div id="buddypress" class="youzer yz-page yz-profile <?php echo yz_get_profile_class(); ?>">
	
	<?php do_action( 'youzer_profile_before_content' ); ?>

	<div class="yz-content">

		<header id="yz-profile-header" class="<?php echo $header_class; ?>" <?php echo $header_data; ?>>
			<?php do_action( 'youzer_profile_header' ); ?>
		</header>
   	<div class="yz-profile-content">

			<div class="yz-inner-content">

            <?php do_action( 'youzer_profile_settings' ); ?>
				<?php do_action( 'youzer_profile_navbar' ); ?>

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