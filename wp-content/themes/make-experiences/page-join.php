<?php
/**
 * Template Name: Join Page
 */
get_header();
?>

<div id="primary" class="content-area bb-grid-cell">
    <main id="main" class="site-main">

        <?php
        if ( have_posts() ) {

            do_action(THEME_HOOK_PREFIX . '_template_parts_content_top');

            //while (have_posts()) :
                the_post(); ?>

				<header class="entry-header">
					<div class="header-text logged-in-refresh">
						<?php if(is_user_logged_in()){ ?>
							<h1>To access this content, upgrade your membership today!</h1>
						<?php } else { ?>
							<h1>Become a Member of Make: Community</h1>
							<a href="javascript:void();" class="login-btn"><h4>Already a member? Login now.</h4></a>
						<?php } ?>
						<a href="/register/?lid=5" class="btn universal-btn-reversed membership-btn">
							<?php if(is_user_logged_in()){ ?>
								Upgrade
							<?php } else { ?>
								Join Today!
							<?php }  ?>
						</a>
				   		<div class="disclaimer">** Membership Fees are applied <b>annually</b>. **</div>
					</div>
				</header>

				<?php
				$the_content = apply_filters('the_content', get_the_content());
				if ( !empty($the_content) ) {
				  echo $the_content;
				}

            //endwhile; // End of the loop.
        } else {
            get_template_part('template-parts/content', 'none');
		}
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
if (is_search()) {
    get_sidebar('search');
} else {
    get_sidebar('page');
}
?>

<?php
get_footer();
