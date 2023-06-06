<?php 
get_header(); 
$categoryPage = is_category();
$uri = $_SERVER['REQUEST_URI'];

if ( have_posts() || $categoryPage) : ?>

	<section class="wrapper">

	<main id="content" class="<?php if( !is_active_sidebar('sidebar-blog') ) { ?>no-sidebar<?php } ?>">

		<div class="breadcrumbs">
			<?php esc_attr_e('You are here:', 'onecommunity'); ?> <a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php echo onecommunity_get_the_archive_title(); ?></span>
		</div>

		<h1 class="page-title half"><?php echo onecommunity_get_the_archive_title(); ?></h1>
		<?php
		if(!$categoryPage){
			?>
			<div id="item-nav">
				<div id="object-nav" class="item-list-tabs archive-tabs" role="navigation">			
					<?php 
					$taxonomy = '';
					$user_id = '';
					$queried_object = get_queried_object();
					
					if ($queried_object != NULL) {
						$obj_id = get_queried_object_id();
						$current_url = get_term_link( $obj_id );
						
						$object_name = get_class($queried_object);

						if ($object_name == 'WP_Term') {

							if ($queried_object->taxonomy == 'category') {
								$taxonomy = 'cat';							
							} elseif ($queried_object->taxonomy == 'post_tag') {
								$taxonomy = 'tag_id';
							} else {
								echo "Wrong taxonomy parameter.";
					 		}
						} elseif ($object_name == 'WP_User') {
							$user_id = $queried_object->ID;
						}
						
						?>

					<ul>
						<?php if (function_exists('wp_ulike_get_post_likes')) { ?><li data-posts-type="2" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" data-tab-page="1" data-term-id="<?php echo esc_attr( $queried_object->term_id ); ?>"><?php esc_attr_e('Most liked', 'onecommunity'); ?></li><?php } ?>
						<li data-posts-type="3" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" data-tab-page="1" data-term-id="<?php echo esc_attr( $queried_object->term_id ); ?>"><?php esc_attr_e('Most commented', 'onecommunity'); ?></li>
						<li class="current" data-posts-type="1" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" data-tab-page="1" data-term-id="<?php echo esc_attr( $queried_object->term_id ); ?>"><?php esc_attr_e('Recent', 'onecommunity'); ?></li>
					</ul>

					<?php 
					}
					?>
				</div><!-- .item-list-tabs -->
			</div><!-- item-nav -->			
			<?php 			
		}
		?>
		<div class="clear"></div>
		<?php
		if ($queried_object != NULL) {
			$desc = $queried_object->description;
			if ($desc) {
				echo '<div class="subtitle desc">';
				echo esc_attr( $desc );
				echo '</div>';
				echo '<div class="clear"></div>';
			}
		}

		?>

		<ul class="blog-1 blog-1-sidebar col-2 list-unstyled">

		<?php
			//if this is a category page, let's call makezine and pull in the feed 
			if($categoryPage){				
				
				$url='https://makezine.com'.$uri;
				$rssFeed = fetch_feed($url.'/feed/');
				
				if (!is_wp_error($rssFeed)) {
				    foreach ($rssFeed->get_items() as $item) {				    	
				    	$category = $item->get_category();				    	
				    	if (is_object($category)) {
				    		$category = $category->term;
				    	}	
				    	
				    	$link = $item->get_link();
				        while (stristr($link, 'http') != $link) {
				            $link = substr($link, 1);
				        }
				        $link = esc_url(strip_tags($link));

				        //set the title
				        $title = esc_html(trim(strip_tags($item->get_title())));
				        if (empty($title)) {
				            $title = __('Untitled');
				        }
				        $title = '<div class="rssTitle">' . $title . "</div>";

				        //set image
				        $image = '<img src="' . get_first_image_url($item->get_content()) . '" alt="' . $item->get_title() . '" />';

				        //author				        			       
			            $author = $item->get_author();
			            if (is_object($author)) {
			                $author = $author->get_name();
			                $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';
			            }				    	
				        get_template_part( 'template-parts/cat', 'grid', array( 
									     'link' => $link,
									     'title' => $title,
									     'image' => $image,
									     'author'  => $author,
									     'category' => $category
									     )  );
				    }    
				} else{
					echo 'none found';
				}	
				
			}else{
				$temp = $wp_query;
				if ($queried_object != NULL) {
					$term_id = $queried_object->term_id;
					$wp_query->query('posts_per_page=6&post_status=publish&' . $taxonomy . '=' . $term_id . '&author='.$user_id.'&paged=1');
				}
			
				while ($wp_query->have_posts()) : $wp_query->the_post();

					get_template_part( 'template-parts/blog', 'grid' );

				endwhile; 
			}			
		?>

		<div class="clear"></div>

		</ul>

<?php else : ?>

		<h2 class="center"><?php esc_attr_e('Not Found', 'onecommunity'); ?></h2>
		<?php get_search_form(); ?>

<?php endif; ?>

		<?php $wp_query = null; $wp_query = $temp; ?>

		<?php		
		if($categoryPage){
			echo '<a class="universal-btn-reversed" style="width:200px;" href="'.$url.'" target="_none">See More</a>';
		}else{
			if ($queried_object != NULL) { ?>

				<div class="load-more-container">
					<span id="load-more-archive" class="show load-more" data-posts-type="1" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>" data-tab-page="1" data-term-id="<?php echo esc_attr( $queried_object->term_id ); ?>"><?php esc_attr_e('Load More', 'onecommunity'); ?></span>
				</div>

			<?php

			} else {

				echo '<div class="clear"></div>';
				echo '<div class="pagination-blog">';

				$big = 999999999; // need an unlikely integer
				echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, get_query_var('paged') ),
					'total' => $wp_query->max_num_pages
				) );

				echo '</div>';

			} 
		}
		?>

	</main><!-- content -->

<?php if( is_active_sidebar('sidebar-blog') ) { ?>

<div id="sidebar-spacer"></div>

<aside id="sidebar" class="sidebar">
	
	<?php 
	$transient = get_transient( 'onecommunity_sidebar_blog' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_sidebar_blog_enable', 0 ) == 1 ) {
		ob_start();

		if (function_exists('dynamic_sidebar') && dynamic_sidebar('sidebar-blog')) : endif;

		$sidebar = ob_get_clean();
		print_r($sidebar);
	
	if ( get_theme_mod( 'onecommunity_transient_sidebar_blog_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_sidebar_blog', $sidebar, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_sidebar_blog_expiration', 20 ) );
	}

	} else {
		echo '<!-- Transient onecommunity_sidebar_blog ('.get_theme_mod( 'onecommunity_transient_sidebar_blog_expiration', 20 ).' min) -->';
		print_r( $transient );
	}
	?>

</aside><!--sidebar ends-->

<?php } ?>

</section><!-- .wrapper -->

<?php get_footer(); ?>
