<?php
/**
 * Template Name: Event List pages
 */

get_header();

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title"><?php echo get_the_title(); ?></h1>
			</header><!-- .page-header -->
        

			<div class="events-list">

				<?php
				/* Start the Loop */
				$args = array( 'post_type' => 'espresso_events' );
				$loop = new WP_Query( $args );
				while ( $loop->have_posts() ) : $loop->the_post();

					global $post;
				    $date = $post->EE_Event->first_datetime(); 
				 	$dateFormat = date('D <\b/>j<\/b/>', strtotime($date->start_date()));
					$startime = date('F j, Y @ g:i a', strtotime($date->start_time()));
					$endime = date('g:i a', strtotime($date->end_time()));
					$return = '<a href="' . get_permalink() . '">
							     <article id="post-' . $post->ID . '" '. esc_attr( implode( ' ', get_post_class() ) )  .'>
								   <div class="event-truncated-date">' . $dateFormat . '</div>
								   <div class="event-image">
								   	 <img src="' . get_the_post_thumbnail_url( $post->ID, 'thumbnail' ) . '" />
								   </div>
								   <div class="event-info">
								   	 <div class="event-date">'. $startime . ' - ' . $endime . '</div>
									 <div class="event-title"></div>
								   </div>';
									
					$return .= ' </article>
							   </a>';
				    echo $return;

				endwhile;
				?>
			</div>

			<?php
			buddyboss_pagination();

		else :
			get_template_part( 'template-parts/content', 'none' );
			?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php
get_footer();
