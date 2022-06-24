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
	jQuery('[data-toggle="popover"]').popover();
	jQuery('[data-toggle="popover"]').on('click', function (e) {
		jQuery(".popover").attr('id', jQuery(this).attr("data-class"));
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
					data-content="<?php echo($section['section_short_description']); ?><a href='<?php echo($section['section_link']); ?>' class='project-count'><?php echo($section['section_projects']); ?> Projects</a>"
					data-original-title="<?php echo($section['section_title']); ?>"
					data-trigger="click"
					data-toggle="popover"
					data-class="<?php echo($section['section_type']); ?>">
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
