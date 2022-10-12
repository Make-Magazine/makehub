<?php
/**
 * Single page template for gift guide pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Make-Experiences
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Rio Roth-Barreiro
 */

$short_desc = 		get_field( 'product_description' );
$cost = 			get_field( 'cost' );
$images =			get_field( 'product_image_links' );
$video_url =		get_field( 'product_video_url' );
$link = 			get_field( 'purchase_url' );
$referrer_url = 	$_SERVER['HTTP_REFERER'];
$referrer_parts = 	parse_url($referrer_url);
parse_str($referrer_parts['query'], $parameters);
$gallery[] = 		get_the_post_thumbnail_url();
foreach($images as $image) {
	$gallery[] = $image['product_image'];
}
$extra_videos =		array();


get_header();
?>

<div id="primary" class="content-area">
	<nav class="breadcrumbs">
		<a href="<?php echo $referrer_parts['domain'] . $referrer_parts['path']; ?>">Gift Guide</a>
		<?php
		foreach($parameters as $key => $value) {
			if($key == "_sft_gift_guide_categories" || $key == "_sft_audiences") {
				$parameter_arr = explode(",", $value);
				foreach($parameter_arr as $parameter) {
					echo("<a href='" . $referrer_parts['domain'] . $referrer_parts['path'] . "?" . $key . "=" . $parameter . "'>" . ucwords(str_replace("-", " / ", $parameter)) . "</a>");
				}
			}
		}
		?>
	</nav>
    <main id="main" class="site-main">
		<div class="gg-gallery">
			<div class="gg-gallery-viewer">
				<img id="gg-gallery-viewer-img" onmousemove="galleryZoomIn(event)" onmouseout="galleryZoomOut()" src="<?php the_post_thumbnail_url(); ?>" />
				<div id="gg-gallery-overlay"></div>
				<div id="gg-video-viewer" class="video-preview gg-gallery-off">
					<?php if( str_contains($video_url, "youtube") || str_contains($video_url, "youtu.be") ) {
						echo do_shortcode("[embedyt]" . $video_url . "[/embedyt]");
					} else if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $video_url, $regs)) {
						echo('<iframe src="https://player.vimeo.com/video/' . $regs[1] . '"></iframe>');
					} else {
						echo $video_url;
					}?>
				</div>
			</div>
			<div class="gg-gallery-buttons">
				<?php
					foreach($gallery as $gallery_image) {
						echo "<img class='gg-gallery-thumbnail' src='" . get_resized_remote_image_url($gallery_image, 100, 100) . "' data-src='" . $gallery_image . "' />";
					}
					if(!empty($video_url)) {
						echo "<img class='gg-video-thumbnail' src='/wp-content/universal-assets/v1/images/play-btn.png' />";
					}
				?>
			</div>
		</div>
		<div class="gg-info">
			<h1><?php echo get_the_title(); ?></h1>
			<div class="gg-description"><?php echo $short_desc; ?></div>
			<div class="gg-cost">$<?php echo $cost; ?></div>
			<a class="universal-btn" href="<?php echo $purchase_url; ?>">Get it now</a>
		</div>
    </main><!-- #main -->
</div><!-- #primary -->


<?php
get_footer();
