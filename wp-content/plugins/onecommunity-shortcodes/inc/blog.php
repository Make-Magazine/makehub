<?php
function onecommunity_blog_categories($atts, $content = null) {
	extract(shortcode_atts(array(
		"taxonomy" => 'category',
		"limit" => ''	
	), $atts));

$transient = get_transient( 'onecommunity_blog_categories' );
if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_categories_enable', 0 ) == 1 ) {

global $wpdb;
$home = home_url("/");

$query = "
	SELECT name,slug,count FROM {$wpdb->prefix}terms
	INNER JOIN {$wpdb->prefix}term_taxonomy
	    ON {$wpdb->prefix}terms.term_id = {$wpdb->prefix}term_taxonomy.term_id
	WHERE taxonomy = '{$taxonomy}'
	ORDER BY count DESC
";

$categories = $wpdb->get_results($query, OBJECT);
$items = '';
$items .= '<ul class="categories-icons">';
$i = 0;
	foreach ( $categories as $category )
	{
		$i++;
		if ($category->count > 0) {
			$items .= '<li class="cat-item cat-item-'. $category->slug .'">';
			$items .= '<img src="'.plugin_dir_url( __DIR__ ).'img/category/'. $category->slug .'.svg" alt=" ">';
			$items .= '<a href="' . $home . $taxonomy . '/' . $category->slug . '">';
			$items .= $category->name;
			$items .= '</a>';
			$items .= '<span class="count">'.$category->count.'</span>';
			$items .= '</li>';
			if ($i == $limit) break;
		}
	}

$items .= '</ul>';

if ( get_theme_mod( 'onecommunity_transient_categories_enable', 0 ) == 1 ) {
	set_transient( 'onecommunity_blog_categories', $items, get_theme_mod( 'onecommunity_transient_categories_expiration', 4320 ) );
}

return $items;

} else {
	return '<!-- Transient onecommunity_blog_categories ('.get_theme_mod( 'onecommunity_transient_categories_expiration', 4320 ).' minutes) -->'.$transient;
}

}
add_shortcode("onecommunity-blog-categories", "onecommunity_blog_categories");


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_recent_blog_posts_tag($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_blog_posts" => '3',
		"tag" => '',
		"col" => '1'
	), $atts));

ob_start(); ?>

<ul class="shortcode-small-recent-posts shortcode-general-list col-<?php echo $col ?>">

<?php
$wp_query = '';
$paged = '';
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('tag=' . $tag . '&posts_per_page=' . $number_of_blog_posts . ''.'&paged='.$paged);
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li class="item<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>" data-post-id="<?php the_ID(); ?>">
		<?php if ( has_post_thumbnail() ) { ?>
        	<div class="thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div>
        <?php } ?>
       	<div class="title"><a href="<?php the_permalink(); ?>" class="recent-post-title-link"><?php the_title(); ?></a>
		<div class="details">
			<span class="time"><?php the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ) ?></span>

			<span class="comments"><?php comments_number('0', '1', '%') ?></span>

			<?php if (function_exists('wp_ulike_get_post_likes')) { ?>
				<span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span>
			<?php } ?>

		</div>
	</li>
	<div class="clear"></div>

<?php endwhile; // end of loop
wp_reset_postdata(); ?>

</ul>

<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;

}

add_shortcode("onecommunity-recent-blog-posts-tag", "onecommunity_recent_blog_posts_tag");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function my_get_comment_excerpt($comment_ID = 0) {
	$comment = get_comment( $comment_ID );
	$comment_text = strip_tags($comment->comment_content);

	if(strlen($comment_text)>70){
		$comment_text = mb_substr($comment_text, 0, 80, 'utf-8');
		$comment_text = $comment_text . '...';
		return $comment_text;
	} else {
		return $comment_text;
	}

}


function onecommunity_recent_blog_comments($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_comments" => '5',
		"col" => '1'
	), $atts));

	$transient = get_transient( 'onecommunity_comments' );
	if ( false === $transient || !get_theme_mod( 'onecommunity_transient_comments_enable', 0 ) == 1 ) {

		$items = '';
		$items .= '<div class="shortcode-recent-comments col-' . $col . '">';

			$items .= '<ul class="shortcode-general-list">';

				$args_comm = array( 'number' => $number_of_comments, 'status' => 'approve', 'post_type' =>'post' );
				$comments = get_comments($args_comm);
				foreach($comments as $comment) {

					$items .= '<li class="comment">';


						$items .= '<div class="avatar">' . get_avatar($comment, 60) . '</div>';

						$items .= '<div class="content">';
							$items .= '&quot;'.my_get_comment_excerpt($comment->comment_ID).'&quot;';

							$items .= '<div class="details">';
								$items .= $comment->comment_author;
								$items .= ' '.__("in", "onecommunity-shortcodes").' ';
								$items .= '<a href="'. get_comment_link($comment->comment_ID).'">';
									$post_title = get_the_title($comment->comment_post_ID);
										if(strlen($post_title)>55){
											$post_title = mb_substr($post_title, 0, 55, 'utf-8');
											$post_title = $post_title . '...';
										}
									$items .= $post_title;
								$items .= '</a>';
							$items .= '</div><!-- details -->';

						$items .= '</div><!-- content -->';

						$items .= '<div class="clear"></div>';

					$items .= '</li><!-- comment -->';

				}
			$items .= '</ul>';

		$items .= '</div><!-- shortcode-recent-comments -->';

			if ( get_theme_mod( 'onecommunity_transient_comments_enable', 0 ) == 1 ) {
				set_transient( 'onecommunity_comments', $items, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_comments_expiration', 20) );
			}

			return $items;

	} else {
		return '<!-- Transient onecommunity_comments (20 min) -->'.$transient;
	}
}

add_shortcode("onecommunity-recent-blog-comments", "onecommunity_recent_blog_comments");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_blog_posts($atts, $content = null) {
	extract(shortcode_atts(array(
		"per_page" => 3,
		"col" => 3,
		"title" => "Recent News",
		"number_of_posts" => 3
	), $atts));

ob_start();
?>

<div class="shortcode-posts-container">

<h4><?php echo $title; ?></h4>

	<div id="item-nav">
		<div id="object-nav" class="item-list-tabs" role="navigation">
			<ul>
				<li data-posts-type="2" data-tab-page="1" data-per-page="<?php echo $per_page; ?>"><?php _e('Most Liked', 'onecommunity-shortcodes'); ?></li>				
				<li data-posts-type="3" data-tab-page="1" data-per-page="<?php echo $per_page; ?>"><?php _e('Most Commented', 'onecommunity-shortcodes'); ?></li>
				<li data-posts-type="1" data-tab-page="1" data-per-page="<?php echo $per_page; ?>" class="current"><?php _e('Recent', 'onecommunity-shortcodes'); ?></li>
			</ul>

		</div>
	</div>

<div class="clear"></div>

<ul class="blog-1 blog-1-full-width col-<?php echo $col ?> list-unstyled">

<?php
$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=' . $per_page . '&post_status=publish&paged=1');
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
?>

</ul>
<div class="clear"></div>

<?php
wp_reset_query();
$wp_query = null; $wp_query = $temp;?>

<div class="load-more-container">
<span id="shortcode-load-more-posts" class="load-more show" data-posts-type="1" data-tab-page="1" data-per-page="<?php echo $per_page; ?>"><?php _e('Load More', 'onecommunity-shortcodes'); ?></span>
</div>

</div><!-- shortcode-posts-container -->

<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;

}
add_shortcode("onecommunity-blog-posts", "onecommunity_blog_posts");

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_shortcode_blog_posts_load() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );
$per_page = esc_html( $_POST['per_page'] );

if ( $blog_posts_type == 1 ) {
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page='.$per_page.'&post_status=publish&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 2) {
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&post_status=publish&posts_per_page='.$per_page.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 3) {
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&post_status=publish&posts_per_page='.$per_page.'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();


get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_shortcode_blog_posts_load', 'onecommunity_shortcode_blog_posts_load' );
add_action( 'wp_ajax_onecommunity_shortcode_blog_posts_load', 'onecommunity_shortcode_blog_posts_load' );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_shortcode_blog_posts_more() {
$blog_posts_type = esc_html( $_POST['blog_posts_type'] );
$page = esc_html( $_POST['page'] );
$per_page = esc_html( $_POST['per_page'] );
$taxonomy = esc_html( $_POST['taxonomy'] );
$term_id = esc_html( $_POST['term_id'] );

if ( $blog_posts_type == 1 ) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('posts_per_page=' . $per_page . '&' . $taxonomy . '=' . $term_id . '&post_status=publish&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 2) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&post_status=publish&posts_per_page=' . $per_page . '&' . $taxonomy . '=' . $term_id .'&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop

} elseif ($blog_posts_type == 3) {

$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&posts_per_page=' . $per_page . '&' . $taxonomy . '=' . $term_id . '&post_status=publish&paged='.$page);
while ($wp_query->have_posts()) : $wp_query->the_post();

get_template_part( 'template-parts/blog', 'grid' );

endwhile; // end of loop
}

wp_reset_query();
wp_die();

}
add_action( 'wp_ajax_nopriv_onecommunity_shortcode_blog_posts_more', 'onecommunity_shortcode_blog_posts_more' );
add_action( 'wp_ajax_onecommunity_shortcode_blog_posts_more', 'onecommunity_shortcode_blog_posts_more' );


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_blog_tags($atts, $content = null) {

ob_start();
$args_tags = array(
    'smallest'                  => 14,
    'largest'                   => 14,
    'unit'                      => 'px',
    'number'                    => 45,
    'format'                    => 'flat',
    'separator'                 => ' ',
    'orderby'                   => 'count',
    'order'                     => 'ASC',
    'exclude'                   => null,
    'include'                   => null,
    'link'                      => 'view',
    'taxonomy'                  => 'post_tag',
    'echo'                      => true,
    'child_of'                   => null
);
?>

<div class="shortcode-blog-tags">
<?php
wp_tag_cloud( $args_tags );
?>
</div>
<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;
}

add_shortcode("onecommunity-blog-tags", "onecommunity_blog_tags");


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



function onecommunity_popular_blog_posts($atts, $content = null) {
	extract(shortcode_atts(array(
		"number_of_posts" => '3'
	), $atts));
ob_start();
?>

<div class="shortcode-popular-posts">

		<div class="shortcode-popular-posts-menu"></div>
 		<div class="shortcode-popular-posts-menu-drop-down">
  			<span data-tab="1" data-tab-title="tab-news-1-title" data-per-page="<?php echo $number_of_posts ?>" class="current"><?php _e('Most Liked', 'onecommunity-shortcodes'); ?></span>
 			<span data-tab="2" data-tab-title="tab-news-2-title" data-per-page="<?php echo $number_of_posts ?>"><?php _e('Most commented', 'onecommunity-shortcodes'); ?></span>
 			<span data-tab="3" data-tab-title="tab-news-3-title" data-per-page="<?php echo $number_of_posts ?>"><?php _e('Featured', 'onecommunity-shortcodes'); ?></span>
  			<span data-tab="4" data-tab-title="tab-news-4-title" data-per-page="<?php echo $number_of_posts ?>"><?php _e('Events', 'onecommunity-shortcodes'); ?></span>
 		</div>

<?php
$transient = get_transient( 'onecommunity_most_popular' );
if ( false === $transient || !get_theme_mod( 'onecommunity_transient_most_popular_enable', 0 ) == 1 ) {

$items = '';
$items .= '<ul class="tab-news-content">';

$row = null;
$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&post_status=publish&posts_per_page=' . $number_of_posts . ''.'&paged=null');
while ($wp_query->have_posts()) : $wp_query->the_post();

	$items .= '<li>';
		$items .= '<div class="pop-left">';
		$items .= '<a href="'.get_the_permalink().'"><img src="'.get_the_post_thumbnail_url(get_the_ID(),'thumbnail').'" alt=""></a>';
		$items .= '</div>';
		$items .= '<div class="pop-right">';
    	$items .= '<div class="title">';
    		$items .= '<a href="'.get_the_permalink().'">';
    		$thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 60; 
    		$items .= mb_substr($thetitle, 0, $thelength, 'UTF-8'); 
    		if ($getlength > $thelength) 
    			$items .= "..."; 
    		$items .= '</a>';
    		$items .= '</div>';

		$items .= '<div class="details">';
			$items .= '<span class="time">';
				$items .= get_the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) );
			$items .= '</span>';

			$items .= '<span class="comments">'.get_comments_number('0', '1', '%').'</span>';

			if (function_exists('wp_ulike_get_post_likes')) { 
				$items .= '<span class="likes">'.wp_ulike_get_post_likes(get_the_ID()).'</span>'; 
			}

		$items .= '</div>';
	$items .= '</div>';
	$items .= '<div class="clear"></div>';
	$items .= '</li>';

endwhile;
wp_reset_postdata();

$items .= '</ul>';


if ( get_theme_mod( 'onecommunity_transient_most_popular_enable', 0 ) == 1 ) {
	set_transient( 'onecommunity_most_popular', $items, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_most_popular_expiration', 1440 ) );
}

echo $items;

} else {
	echo '<!-- Transient onecommunity_most_popular ('.get_theme_mod( 'onecommunity_transient_most_popular_expiration', 1440 ).' min) -->'.$transient;
}
?>

</div><!-- shortcode-popular-posts -->

<?php
$shortcode_content = ob_get_clean();
return $shortcode_content;
}
add_shortcode("onecommunity-popular-blog-posts", "onecommunity_popular_blog_posts");



function onecommunity_top_news_load() {
$posts_list_type = esc_html( $_POST['posts_list_type'] );
$number_of_posts = esc_html( $_POST['per_page'] );

if ( $posts_list_type == 1 ) {

$row = null;
$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('orderby=meta_value_num&meta_key=_liked&post_status=publish&posts_per_page=' . $number_of_posts . ''.'&paged=null');
while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

	<li>
		<div class="pop-left">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
		</div>
		<div class="pop-right">
    	<div class="title"><a href="<?php the_permalink(); ?>"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 60; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></div>
		<div class="details"><span class="time"><?php the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ); ?></span> <span class="comments"><?php comments_number('0', '1', '%'); ?></span> <?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?></div>
		</div>
		<div class="clear"></div>
	</li>

<?php
endwhile;
wp_reset_postdata();
wp_die();

 } elseif ($posts_list_type == 2) {

$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('orderby=comment_count&post_status=publish&posts_per_page=' . $number_of_posts . ''.'&paged=null');
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li>
		<div class="pop-left">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
		</div>
		<div class="pop-right">
    	<div class="title"><a href="<?php the_permalink(); ?>"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 60; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></div>
		<div class="details"><span class="time"><?php the_time('l, M j'); ?></span> <span class="comments"><?php comments_number('0', '1', '%'); ?></span> <?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?></div>
		</div>
		<div class="clear"></div>
	</li>

<?php
endwhile;
wp_reset_postdata();
wp_die();
} elseif ($posts_list_type == 3) {

$counter_3 = null;
$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('tag=Featured&orderby=comment_count&post_status=publish&posts_per_page=' . $number_of_posts . ''.'&paged=null');
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li>
		<div class="pop-left">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
		</div>
		<div class="pop-right">
    	<div class="title"><a href="<?php the_permalink(); ?>"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 60; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></div>
		<div class="details"><span class="time"><?php the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ); ?></span> <span class="comments"><?php comments_number('0', '1', '%'); ?></span> <?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?></div>
		</div>
		<div class="clear"></div>
	</li>

<?php
endwhile;
wp_reset_postdata();
wp_die();
} else {

$wp_query = null;
$temp = $wp_query;
$wp_query = new WP_Query();
$wp_query->query('tag=Events&orderby=comment_count&post_status=publish&posts_per_page=' . $number_of_posts . ''.'&paged=null');
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li>
		<div class="pop-left">
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
		</div>
		<div class="pop-right">
    	<div class="title"><a href="<?php the_permalink(); ?>"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 60; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a></div>
		<div class="details"><span class="time"><?php the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ); ?></span> <span class="comments"><?php comments_number('0', '1', '%'); ?></span> <?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?></div>
		</div>
		<div class="clear"></div>
	</li>

<?php
endwhile;
wp_reset_postdata();
wp_die();
}


}
add_action( 'wp_ajax_nopriv_onecommunity_top_news_load', 'onecommunity_top_news_load' );
add_action( 'wp_ajax_onecommunity_top_news_load', 'onecommunity_top_news_load' );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_cat_posts($atts, $content = null) {
	extract(shortcode_atts(array(
		"cat_id" => ''
	), $atts));

$transient = get_transient( 'onecommunity_cat_posts' );
if ( false === $transient || !get_theme_mod( 'onecommunity_transient_cat_posts_enable', 0 ) == 1 ) {
$items = '';

$items .= '<div id="shortcode-category-posts">';


	$items .= '<div class="shortcode-category-posts-menu">';
	$items .= '<ul>';
	$items .= '<li class="cat-item cat-item-0 current"><a href="javascript: void(0)">'.__( 'All', 'onecommunity-shortcodes' ).'</a></li>';
	ob_start();
	wp_list_categories( array(
	    'include' => $cat_id,
	    'title_li' => '',
	    'orderby'=> 'count',
	    'order' => 'DESC',
 	   'show_count' => false
	) );
	$cats = ob_get_clean();

	$items .= $cats;

	$cats_array = explode(",", $cat_id);
	
	$items .= '</ul>';
	$items .= '</div>';


	$items .= '<div class="big-post-container">';

		$items .= '<ul class="big-post col-1">';

		$wp_query = null;
		$paged = null;
		$temp = $wp_query;
		$wp_query= null;
		$wp_query = new WP_Query();
		$wp_query->query('cat=0&posts_per_page=1&post_status=publish'.'&paged='.$paged);
		while ($wp_query->have_posts()) : $wp_query->the_post();

			$items .= '<li class="box-blog-entry';
			if ( !has_post_thumbnail() ) { 
			$items .= ' no-thumbnail';
			}
			$items .= '">';

 		   		$items .= '<div class="box-blog-thumb">';
 		   			if ( has_post_thumbnail() ) {
 		   				$items .= '<a href="'.get_the_permalink().'"><img src="'.get_the_post_thumbnail_url(get_the_ID(),'post-thumbnail').'" alt=""></a>';
 		   			}

				$items .= '</div>';

				$items .= '<div class="box-blog-details">';
					$items .= '<div class="box-blog-details-top">';
						$items .= '<span class="box-blog-cat">';
						ob_start();
						the_category(', ');
						$items .= ob_get_clean();
						$items .= '</span>';
						$items .= '<span class="box-blog-time">';
						$time = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
						$items .= sprintf( __( '%s ago', 'onecommunity-shortcodes'), $time);
						$items .= '</span>';
					$items .= '</div>';

					$items .= '<a href="'.get_the_permalink().'" class="blog-thumb-title-anchor">';
					$thetitle = get_the_title();
					$getlength = strlen($thetitle);
					$thelength = 57;
					$items .=  mb_substr($thetitle, 0, $thelength, 'UTF-8'); 
					if ($getlength > $thelength) {
						$items .= '...';
					}
					$items .= '</a>'; 

					$items .= '<div class="box-blog-details-bottom">';

						$items .= '<span class="box-blog-details-bottom-author">';
						$author = get_author_posts_url( get_the_author_meta('ID') );
							$items .= '<a href="'.$author.'" rel="author">';
							$items .= get_avatar( get_the_author_meta('ID'), 25 );
							$items .= '</a>';
							$items .= ' '.esc_attr__('by', 'onecommunity-shortcodes').' ';
							$items .= get_the_author();
						$items .= '</span>';

						if (function_exists('wp_ulike_get_post_likes')) {
							$items .= '<span class="box-blog-likes">';
							$items .= wp_ulike_get_post_likes(get_the_ID());
							$items .= '</span>';
						}

						$items .= '<span class="box-blog-comments">';
						$items .= get_comments_number('0', '1', '%');
						$items .= '</span>';

						$items .= '<div class="clear"></div>';

					$items .= '</div><!-- box-blog-details-bottom -->';

				$items .= '</div><!-- box-blog-details -->';

			$items .= '</li>';

		endwhile;
		wp_reset_postdata();

		$items .= '</ul>';

	$items .= '</div><!-- big-post-container -->';




	$items .= '<div class="cat-posts-list-container">';

		$items .= '<ul class="cat-posts-list col-2 shortcode-category-id-'.$cats_array[0].'">';

		$wp_query = null;
		$paged = null;
		$temp = $wp_query;
		$wp_query= null;
		$wp_query = new WP_Query();
		$wp_query->query('cat=0&posts_per_page=8&post_status=publish&offset=1'.'&paged='.$paged);
		while ($wp_query->have_posts()) : $wp_query->the_post();

			$items .= '<li class="recent-post';
			if ( !has_post_thumbnail() ) { 
				$items .= ' no-thumbnail'; 
			}
			$items .= '">';
				if ( has_post_thumbnail() ) {
        			$items .= '<div class="recent-post-thumb">';
        			$items .= '<a href="' . get_the_permalink() . '">';
        			$items .=  get_the_post_thumbnail(get_the_ID(),'thumbnail');
        			$items .= '</a>';
        			$items .= '</div>';
        		}

        		$items .= '<div class="recent-post-content">';

       	 			$items .= '<div class="recent-post-title"><a href="'.get_the_permalink().'" class="recent-post-title-link">'.mb_strimwidth(get_the_title(), 0, 70, '...').'</a></div>';
					$items .= '<div class="recent-post-bottom">';
						$items .= '<span class="recent-post-bottom-date">'.get_the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ).'</span>';
						if (function_exists('wp_ulike_get_post_likes')) { 
							$items .= '<span class="box-blog-likes">';
							 $likes = wp_ulike_get_post_likes(get_the_ID()); 
							 if($likes == 0) { 
							 	$items .= '0';
							 	} else { 
							 	 	$items .= $likes; 
							 	} 
							 	$items .= '</span>';
							 	}
						$items .= '<span class="box-blog-comments">'.get_comments_number('0', '1', '%').'</span>';
					$items .= '</div>';

				$items .= '</div>';
			$items .= '</li>';

		endwhile; // end of loop
		wp_reset_postdata();

		$items .= '</ul>';

	$items .= '</div><!-- cat-posts-list-container -->';



$items .= '</div><!-- #shortcode-category-posts -->';


	if ( get_theme_mod( 'onecommunity_transient_cat_posts_enable', 0 ) == 1 ) {
		set_transient( 'onecommunity_cat_posts', $items, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_cat_posts_expiration', 1440 ) );
	}

return $items;

} else {
	return '<!-- Transient onecommunity_cat_posts ('.get_theme_mod( 'onecommunity_transient_cat_posts_expiration', 1440 ).' minutes) -->'.$transient;
}

}
add_shortcode("onecommunity-cat-posts", "onecommunity_cat_posts");




function onecommunity_cat_big_post_load() {


$cat_id = esc_html( $_POST['cat_id'] );
$wp_query = null;
$paged = null;
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('cat=' . $cat_id . '&posts_per_page=1&post_status=publish'.'&paged='.$paged);
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li class="box-blog-entry<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>">
    <div class="box-blog-thumb">

    	<?php if ( has_post_thumbnail() ) { ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
		<?php } ?>

	</div>

		<div class="box-blog-details">
			<div class="box-blog-details-top">
				<span class="box-blog-cat"><?php the_category(', ') ?></span>
				<span class="box-blog-time"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'onecommunity' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
			</div>
				<a href="<?php the_permalink(); ?>" class="blog-thumb-title-anchor"><?php $thetitle = get_the_title(); $getlength = strlen($thetitle); $thelength = 57; echo mb_substr($thetitle, 0, $thelength, 'UTF-8'); if ($getlength > $thelength) echo "..."; ?></a>

			<div class="box-blog-details-bottom">
			<span class="box-blog-details-bottom-author">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
			<?php
			echo get_avatar( get_the_author_meta( 'user_email' ), 25 );
			?>
			</a>
			<?php esc_attr_e('by', 'onecommunity'); ?> <?php echo get_the_author(); ?></span>
			<?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="box-blog-likes"><?php echo wp_ulike_get_post_likes(get_the_ID()) ?></span><?php } ?>
			<span class="box-blog-comments"><?php comments_number('0', '1', '%'); ?></span>
			<div class="clear"></div>
			</div><!-- blog-box-comments -->
		</div><!-- blog-thumb-title -->

	</li>

<?php
endwhile;
wp_reset_postdata();
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_cat_big_post_load', 'onecommunity_cat_big_post_load' );
add_action( 'wp_ajax_onecommunity_cat_big_post_load', 'onecommunity_cat_big_post_load' );




function onecommunity_cat_posts_load() {
$cat_id = esc_html( $_POST['cat_id'] );
$wp_query = null;
$paged = null;
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query('cat=' . $cat_id . '&posts_per_page=8&offset=1&post_status=publish'.'&paged='.$paged);
while ($wp_query->have_posts()) : $wp_query->the_post();
?>

	<li class="recent-post<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>">
		<?php if ( has_post_thumbnail() ) { ?>
         <div class="recent-post-thumb"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a></div>
         <?php } ?>
         <div class="recent-post-content">
       	 	<div class="recent-post-title"><a href="<?php the_permalink(); ?>" class="recent-post-title-link"><?php echo mb_strimwidth(get_the_title(), 0, 70, '...'); ?></a></div>
			<div class="recent-post-bottom">
			<span class="recent-post-bottom-date"><?php the_time( get_theme_mod( 'onecommunity_shortcodes_date_format', 'l, M j' ) ); ?></span>

			<?php if (function_exists('wp_ulike_get_post_likes')) { ?><span class="box-blog-likes"><?php $likes = wp_ulike_get_post_likes(get_the_ID()); if($likes == 0) { echo "0"; } else { echo $likes; } ?></span><?php } ?>
			<span class="box-blog-comments"><?php comments_number('0', '1', '%'); ?></span>

			</div>
		</div>
	</li>

<?php endwhile; // end of loop
wp_reset_postdata();
wp_die();
}
add_action( 'wp_ajax_nopriv_onecommunity_cat_posts_load', 'onecommunity_cat_posts_load' );
add_action( 'wp_ajax_onecommunity_cat_posts_load', 'onecommunity_cat_posts_load' );

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////