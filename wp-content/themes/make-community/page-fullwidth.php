<?php
/*
 * Template name: No Sidebar
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

get_header();

while ( have_posts() ) : the_post(); ?>

	<section class="wrapper">
	    <main id="content" class="no-sidebar">
	        <article>
	            <?php the_content(); ?>        
	        </article>
	    </main>
	</section>
<?php
// End of the loop.
endwhile; ?>

<?php
get_footer();
