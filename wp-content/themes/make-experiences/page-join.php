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
						<h1>To access this content, upgrade your membership today!</h1>
						<h4>Introductory offer 19.99 - Renews at 59.99.</h4>
					<?php } else { ?>
						<h1>Become a Member of Make: Community</h1>
						<a href="javascript:void();" class="login-btn"><h4>Already a member? <span class="underline">Login now.</span></h4></a>
					<?php } ?>
					<?php if(is_user_logged_in()){ ?>
						<div onclick="ihcBuyNewLevelFromAp('Membership', '19.99', 20, '<?php echo CURRENT_URL; ?>/account/?ihcnewlevel=true&amp;lid=20&amp;urlr=<?php echo urlencode(CURRENT_URL); ?>%2Faccount%2F%3Fihc_ap_menu%3Dsubscription');" class="btn universal-btn-reversed membership-btn">Upgrade</div>
					<?php } else { ?>
						<a href="/register/?lid=5" class="btn universal-btn-reversed membership-btn">Join Today!</a>
					<?php } ?>
			   		<div class="disclaimer">** Membership Fees are applied <b>annually</b>. **</div>
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
