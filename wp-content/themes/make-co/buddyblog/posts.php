<?php
/**
 * This file is used for listing the posts on profile
 */
?>

<?php if ( buddyblog_user_has_posted() ): ?>
<?php
    //let us build the post query
    if ( bp_is_my_profile() || is_super_admin() ) {
 		$status = 'any';
	} else {
		$status = 'publish';
	}
	
    $paged = bp_action_variable( 1 );
    $paged = $paged ? $paged : 1;
    
	$query_args = array(
		'author'        => bp_displayed_user_id(),
		'post_type'     => buddyblog_get_posttype(),
		'post_status'   => $status,
		'paged'         => intval( $paged )
    );
	//do the query
    query_posts( $query_args );
	?>
    
	<?php if ( have_posts() ): ?>
		
		<?php while ( have_posts() ): the_post();
			global $post;
		?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					
					 
						 <?php /*<div class="author-box">
							  <?php echo bp_displayed_user_avatar( 'type=full' ); ?>
							  <p><?php printf( _x( 'by %s', 'Post written by...', 'buddyblog' ), bp_core_get_userlink( $post->post_author ) ); ?></p>

							  <?php if ( is_sticky() ) : ?>
									<span class="activity sticky-post"><?php _ex( 'Featured', 'Sticky post', 'buddyblog' ); ?></span>
							  <?php endif; ?>
						 </div> */ ?>

						 <div class="post-content container-fluid">
							<div class="row yz-tab-post">
								<div class="col-sm-5 col-xs-12 post-featured-image<?php if(!has_post_thumbnail(get_the_ID())){echo(' no-thumbnail');} ?>" style="background-image:url('<?php echo(has_post_thumbnail( get_the_ID() ) ? the_post_thumbnail_url() : get_stylesheet_directory_uri()."/images/grey-makey.png"); ?>')"></div>
								<div class="col-sm-7 col-xs-12 yz-post-inner-content">
									<div class="yz-post-head">
										<h2 class="yz-post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddyblog' ) . " " . the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
										<div class="yz-post-meta">
											<ul>
												<li><i class="far fa-calendar-alt"></i><?php printf( __( '%1$s <span>in %2$s</span>', 'buddyblog' ), get_the_date(), get_the_category_list( ', ' ) ); ?></li>
												<li><i class="fas fa-tags"></i><?php the_tags(  __( 'Tags: ', 'buddyblog' ), ', ' ); ?> </li>
												<li><i class="far fa-comments"></i><?php comments_popup_link( __( 'No Comments &#187;', 'buddyblog' ), __( '1 Comment &#187;', 'buddyblog' ), __( '% Comments &#187;', 'buddyblog' ) ); ?></li>
											</ul>
										</div>
									</div>

								  <div class="yz-post-text">
										<?php the_excerpt(); ?>
										<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddyblog' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
								  </div>

								  <div class="post-actions">
										<?php echo buddyblog_get_post_publish_unpublish_link( get_the_ID() );?>
										<?php echo buddyblog_get_edit_link();?>
										<?php echo buddyblog_get_delete_link();?>
								  </div>   
								</div>
							</div> <!-- end row -->
						 </div>
					
				</div>
                   
        <?php endwhile;?>
            <div class="pagination">
                <?php buddyblog_paginate(); ?>
            </div>
    <?php else: ?>
            <p><?php _e( 'There are no posts by this user at the moment. Please check back later!', 'buddyblog' );?></p>
    <?php endif; ?>

    <?php 
       wp_reset_postdata();
       wp_reset_query();
    ?>

<?php elseif ( bp_is_my_profile() && buddyblog_user_can_post( get_current_user_id() ) ): ?>
    <p> <?php _e( "You haven't posted anything yet.", 'buddyblog' );?> <a href="<?php echo buddyblog_get_new_url();?>"> <?php _e( 'New Post', 'buddyblog' );?></a></p>

<?php endif; ?>
