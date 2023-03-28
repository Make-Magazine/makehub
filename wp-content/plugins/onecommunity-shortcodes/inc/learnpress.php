<?php
function onecommunity_learnpress_featured($atts, $content = null) {
	extract(shortcode_atts(array(
		'limit' => '3',
		'col' => '3',
		'excerpt' => 'yes',
		'duration' => 'yes',
		'load_more' => 'yes'
	), $atts));

ob_start();

$transient = get_transient( 'onecommunity_learnpress_featured' );
if ( false === $transient ) {

query_posts(array(
    'post_type' => 'lp_course',
    'showposts' => $limit,
    'paged' => 1,
    'meta_query' => array(
    array(
        'key' => '_lp_featured',
        'value' => 'yes',
        )
    )

) );  

echo '<ul class="learnpress-featured col-'.$col.'">';

while (have_posts()) : the_post(); ?>

<li class="course-entry<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>">

	<div class="box-course-thumb">
        <?php if ( has_post_thumbnail() ) { ?>
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
		<?php } ?>
	</div>

	<div class="box-course-details">

			<div class="box-course-details-top">

				<?php onecommunity_course_category(); ?>

				<span class="box-course-price">
					<?php
					$course_id = get_the_ID();
					$course = learn_press_get_course( $course_id );
				 	echo $course->get_price_html();
					if ( $course->has_sale_price() ) {
						echo ' <span class="origin-price">' . $course->get_origin_price_html() . '</span>';
					}
				 	?>
				 </span>
			</div>

			<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

			<div class="box-course-details-bottom">
				<span class="box-course-details-bottom-author">
					<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_avatar( get_the_author_meta( 'user_email' ), 25 ); ?></a>
					<?php echo get_the_author(); echo " "; echo get_the_author_meta('last_name'); ?></span>

					<?php if ($duration == 'yes') { ?>
					<span class="course-duration">
						<?php 
						echo esc_attr__('Duration', 'onecommunity-shortcodes') . " ";
						onecommunity_course_duration();
						?>
					</span>
					<?php } ?>

			<div class="clear"></div>

			<?php if ($excerpt == 'yes') { echo '<p class="excerpt">' . get_the_excerpt() . '</p>'; } ?>
    
			</div>

	</div>

	<?php
echo '</li>';

endwhile;

echo '</ul>';

wp_reset_postdata();
wp_reset_query();
?>

<?php if ($load_more == 'yes') { ?>
<div class="load-more-container">
<span id="load-more-courses" class="show" data-page="1" duration="<?php echo $duration ?>" excerpt="<?php echo $excerpt ?>"><?php esc_attr_e( 'Load More', 'onecommunity-shortcodes' ); ?></span>
</div>
<?php } ?>

<?php
$shortcode_content = ob_get_clean();

	set_transient( 'onecommunity_learnpress_featured', $shortcode_content, HOUR_IN_SECONDS * 24 );

	return $shortcode_content;

} else {
	return '<!-- Transient onecommunity_learnpress_featured (24H) -->'.$transient;
}

}

add_shortcode("onecommunity-learnpress-featured", "onecommunity_learnpress_featured");


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function onecommunity_featured_courses_more() {
$page = esc_html( $_POST['page'] );
$duration = esc_html( $_POST['duration'] );
$excerpt = esc_html( $_POST['excerpt'] );

query_posts(array(
    'post_type' => 'lp_course',
    'showposts' => 3,
    'paged' => $page,
    'meta_query' => array(
    array(
        'key' => '_lp_featured',
        'value' => 'yes',
        )
    )

) );  

echo '<ul class="learnpress-featured">';

while (have_posts()) : the_post(); ?>

<li class="course-entry<?php if ( !has_post_thumbnail() ) { echo " no-thumbnail"; } ?>">
	<div class="box-course-thumb">
        <?php if ( has_post_thumbnail() ) { ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('post-thumbnail'); ?></a>
		<?php } ?>
	</div>


	<div class="box-course-details">
			<div class="box-course-details-top">

				<?php onecommunity_course_category(); ?>

				<span class="box-course-price"><?php
					$course_id = get_the_ID();
				$course = learn_press_get_course( $course_id );
				 echo $course->get_price_html(); ?></span>
			</div>

			<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>

			<div class="box-course-details-bottom">
			<span class="box-course-details-bottom-author">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_avatar( get_the_author_meta( 'user_email' ), 25 ); ?></a>
			<?php echo get_the_author(); echo " "; echo get_the_author_meta('last_name'); ?></span>

			<?php if ($duration == 'yes') { ?>
			<span class="course-duration">
			<?php 
				echo esc_attr__('Duration', 'onecommunity-shortcodes') . " ";
				onecommunity_course_duration();
			?>
			</span>
			<?php } ?>

			<div class="clear"></div>

			<?php if ($excerpt == 'yes') { echo '<p class="excerpt">' . get_the_excerpt() . '</p>'; } ?>
    
			</div>
	</div>

	<?php

echo '</li>';

endwhile;

echo '</ul>';


wp_reset_query();
wp_die();
wp_reset_query();

}
add_action( 'wp_ajax_nopriv_onecommunity_featured_courses_more', 'onecommunity_featured_courses_more' );
add_action( 'wp_ajax_onecommunity_featured_courses_more', 'onecommunity_featured_courses_more' );


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////// GET LEARNPRESS COURSE DURATION ////////////////////////////////////////////////////////


function onecommunity_course_duration() {
	global $wpdb;
	$course = learn_press_get_course();
	$duration = $course->get_data( 'duration' );
	$duration_value = explode(" ", $duration);

	if ( $duration_value[0] == 1 ) {

		echo $duration_value[0];
		echo " ";

		if ( $duration_value[1] == 'minute' ) {
			esc_attr_e('minute', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'hour' ) {
			esc_attr_e('hour', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'day' ) {
			esc_attr_e('day', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'week' ) {
			esc_attr_e('week', 'onecommunity-shortcodes');
		}

	} elseif ($duration_value[0] > 1) {

		echo $duration_value[0];
		echo " ";

		if ( $duration_value[1] == 'minute' ) {
			esc_attr_e('minutes', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'hour' ) {
			esc_attr_e('hours', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'day' ) {
			esc_attr_e('days', 'onecommunity-shortcodes');
		} elseif ($duration_value[1] == 'week' ) {
			esc_attr_e('weeks', 'onecommunity-shortcodes');
		}
	}
}

////////////////////////////////////////////////// GET LEARNPRESS COURSE CATEGORY ///////////////////////////////////////////////////////

function onecommunity_course_category() {

	global $wpdb;
	$home = home_url("/");
	$categories_slug = get_option( 'learn_press_course_category_base' );
	$course_id = get_the_ID();
	$course = learn_press_get_course( $course_id );

	$query = "
    SELECT {$wpdb->prefix}terms.name, {$wpdb->prefix}terms.slug
    FROM {$wpdb->prefix}term_relationships
    INNER JOIN {$wpdb->prefix}term_taxonomy
    	ON {$wpdb->prefix}term_relationships.term_taxonomy_id = {$wpdb->prefix}term_taxonomy.term_taxonomy_id
    INNER JOIN {$wpdb->prefix}terms
    	ON {$wpdb->prefix}term_taxonomy.term_taxonomy_id = {$wpdb->prefix}terms.term_id
    WHERE object_id = {$course_id} AND taxonomy = 'course_category'
    ";

    $categories = $wpdb->get_results($query, OBJECT);


	$i = 0;
		foreach ( $categories as $category ) 
		{
					$i++;
					echo '<span class="course-category">';
					echo '<a href="' . $home . $categories_slug . '/' . $category->slug . '">';
					echo $category->name;
					echo '</a>';
					echo '</span>';
	}

}