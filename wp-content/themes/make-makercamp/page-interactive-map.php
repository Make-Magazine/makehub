<?php
/*
 * Template name: Interactive Map
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();
?>

<script>
jQuery(document).ready(function(){
	jQuery('[data-toggle="popover"]').popover({
		container:  'body',
	});
	jQuery('[data-toggle="popover"]').on('click', function (e) {
	    jQuery('[data-toggle="popover"]').not(this).popover('hide');
	});
});
</script>

<div id="primary" class="content-area bb-grid-cell">
	<main id="main" class="site-main interactive-map">

		<?php if ( have_posts() ) { ?>

			<?php
				echo(get_field('background_svg'));
				$sections = get_field('map_sections');
				foreach($sections as $section) {
			?>
				<a  href="javascript:void(0);"
					id="<?php echo($section['section_type']); ?>"
					class="map-image-btn"
					data-html="true"
					data-content="<?php echo($section['section_short_description']); ?><div class='project-count'><?php echo($section['section_projects']); ?> Projects</div><a href='<?php echo($section['section_link']); ?>' class='btn universal-btn map-btn'>See Projects</a>"
					rel="popover"
					data-container="body"
					data-original-title="<?php echo($section['section_title']); ?>"
					data-trigger="click"
					data-toggle="popover">
					<img src="<?php echo($section['section_image']); ?>" />
				</a>
			<?php
				}
			?>

		<?php } // end if have_posts ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
