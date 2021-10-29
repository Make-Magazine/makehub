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
            the_post(); ?>

			<header class="entry-header">
				<div class="header-text logged-in-refresh">
					<?php if(is_user_logged_in()){ ?>
						<h1>You are already a member!</h1>
						<!--<h1>To access this content, upgrade your membership today!</h1>
						<h4>Introductory offer 19.99 - Renews at 59.99.</h4>-->
					<?php } else { ?>
						<h1>Become a Member of Make: Community</h1>
						<a href="javascript:void();" class="login-btn"><h4>Already a member? <span class="underline">Login now.</span></h4></a>
					<?php } ?>
					<?php if(is_user_logged_in()){ ?>
						<a href="/activity" class="btn universal-btn-reversed" style="margin:0 auto;width:95%px;display:flex;font-size:24px;min-height:7px;padding:10px 20px;text-transform:capitalize;">See what's happening on Make: Community</a>
						<!--<a href="/register/?lid=20" class="btn universal-btn-reversed membership-btn special-membership-btn">Upgrade</a>-->
					<?php } else { ?>
						<a href="/register/?lid=5" class="btn universal-btn-reversed membership-btn">Join Today!</a>
					<?php } ?>
					<?php if(!is_user_logged_in()){ ?>
			   			<div class="disclaimer">** Membership Fees are applied <b>annually</b>. **</div>
					<?php } ?>
				</div>
			</header>

			<?php
			if ( !empty(get_the_content()) ) {
			  echo get_the_content();
			}

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
