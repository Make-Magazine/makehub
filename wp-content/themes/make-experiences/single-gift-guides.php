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
$gallery[] = get_the_post_thumbnail_url();
foreach($images as $image) {
	$gallery[] = $image['product_image'];
}
$gallery[] = $video_url;
var_dump($gallery);

get_header();
?>

<div id="primary" class="content-area">
	<nav class="breadcrumbs">
		<a href="<?php echo $referrer_parts['domain'] . $referrer_parts['path']; ?>">Gift Guide</a>
		<?php
		foreach($parameters as $key => $value) {
			$parameter_arr = explode(",", $value);
			foreach($parameter_arr as $parameter) {
				echo("<a href='" . $referrer_parts['domain'] . $referrer_parts['path'] . "?" . $key . "=" . $parameter . "'>" . ucwords(str_replace("-", " / ", $parameter)) . "</a>");
			}
		}
		?>
	</nav>
    <main id="main" class="site-main">
		<div class="gift-guide-gallery">
			<?php echo $video_url; ?>
		</div>
    </main><!-- #main -->
</div><!-- #primary -->


<?php
get_footer();
