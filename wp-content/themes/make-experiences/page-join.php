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
							<h1>You are already a member!</h1>
							<!--<h1>To access this content, upgrade your membership today!</h1>
							<h4>Introductory offer 19.99 - Renews at 59.99.</h4>-->
						<?php } else { ?>
							<h1>Become a Member of Make: Community</h1>
							<a href="javascript:void();" class="login-btn"><h4>Already a member? Login now.</h4></a>
						<?php } ?>
							<?php if(is_user_logged_in()){ ?>
								<a href="/activity" class="btn universal-btn-reversed" style="margin:0 auto;width:360px;display:flex;font-size:24px;min-height:100px;padding:10px 20px;">See what's happening on Make: Community</a>
								<!--<a href="/register/?lid=20" class="btn universal-btn-reversed membership-btn special-membership-btn">Upgrade</a>-->
							<?php } else { ?>
								<a href="/register/?lid=5" class="btn universal-btn-reversed membership-btn">Join Today!</a>
							<?php }  ?>
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
