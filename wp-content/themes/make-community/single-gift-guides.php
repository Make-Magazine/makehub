<?php
/**
 * Single page template for gift guide pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Make-Commiunity
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Rio Roth-Barreiro
 */

$short_desc = 		get_field( 'product_description' );
$cost = 			get_field( 'cost' );
$images =			get_field( 'product_image_links' );
$video_url =		get_field( 'product_video_url' );
$link = 			get_field( 'purchase_url' );
$referrer_url = 	isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
$parameters = 		array();
if(!empty($referrer_url)) {
	$referrer_parts = parse_url($referrer_url);
	if(!empty($referrer_parts['query'])) {
		parse_str($referrer_parts['query'], $parameters);
	}
}
$gallery[] = 		get_the_post_thumbnail_url();
foreach($images as $image) {
	$gallery[] = $image['product_image'];
}
$extra_videos =		array();
$categories = get_the_terms( get_the_ID(), 'gift_guide_categories' );
$audiences = get_the_terms( get_the_ID(), 'audiences' );

get_header();
?>
<section class="wrapper">
	<nav class="breadcrumbs">
		<a href="/gift-guide">Gift Guide</a>
		<?php
		foreach($parameters as $key => $value) {
			if($key == "_sft_gift_guide_categories" || $key == "_sft_audiences") {
				$parameter_arr = explode(",", $value);
				foreach($parameter_arr as $parameter) {
					echo("<a href='" . $referrer_parts['scheme'] . $referrer_parts['host'] . $referrer_parts['path'] . "?" . $key . "=" . $parameter . "'>" . ucwords(str_replace("-", " / ", $parameter)) . "</a>");
				}
			}
		}
		?>
	</nav>
    <main id="content" class="no-sidebar">
        <article>
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
						echo "<div class='gg-gallery-btn'><img class='gg-gallery-thumbnail' src='" . get_resized_remote_image_url($gallery_image, 100, 100) . "' data-src='" . $gallery_image . "' /></div>";
					}
					if(!empty($video_url)) {
						echo "<div class='gg-gallery-btn'><img class='gg-video-thumbnail' src='/wp-content/universal-assets/v2/images/play-btn.png' /></div>";
					}
				?>
			</div>
			<div class="gg-taxonomy-section">
			<?php
				echo("<h3>Categories</h3><ul class='gg-taxonomy-list'>");
				foreach($categories as $category) {
					echo("<li><a href='/gift-guide?_sft_gift_guide_categories=" . $category->slug . "'>" . $category->name . "</a></li>");
				}
				echo("</ul>");
				echo("<h3>Audiences</h3><ul class='gg-taxonomy-list'>");
				foreach($audiences as $audience) {
					echo("<li><a href='/gift-guide?_sft_audiences=" . $audience->slug . "'>" . $audience->name . "</a></li>");
				}
				echo("</ul>");
			?>
			</div>
		</div>
		<div class="gg-info">
			<h1><?php echo get_the_title(); ?></h1>
			<div class="gg-description"><?php echo $short_desc; ?></div>
			<div class="gg-cost">$<?php echo $cost; ?></div>
			<a class="universal-btn" href="<?php echo $link; ?>" target="_blank">Get it now</a>
			<?php
			if (class_exists('ESSB_Plugin_Options')) {
				$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				echo do_shortcode('[easy-social-share buttons="facebook,pinterest,twitter,love" animation="essb_icon_animation6" style="icon" fullwidth="yes" template="4" postid="' . get_the_ID() . '" url="' . $url . '" text="' . preg_replace('@\[.*?\]@', '', get_the_title()) . '"]');
			}
			?>
		</div>
        </article>
    </main>
</section>	

<?php
get_footer();
