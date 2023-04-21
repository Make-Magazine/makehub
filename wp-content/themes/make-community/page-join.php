<?php
/**
 * Template Name: Join Page
 */
get_header();

while ( have_posts() ) : the_post(); ?>

<section class="wrapper">
    <main id="content" class="no-sidebar">
        <article>
        	<header class="entry-header">
				<div class="header-text logged-in-refresh">
					<?php if( class_exists('MeprUtils') ) { ?>
						<?php if(isset($_GET["mepr-unauth-page"])) {
							echo(do_shortcode('[mepr-unauthorized-message]'));
						} ?>
						<div class="join-membership-wrapper">
						<?php if(is_user_logged_in() && IS_MEMBER == true){
							if( CAN_UPGRADE == true ) { ?>
								<h1>To access this content, upgrade your membership today!</h1>
								<h4>Upgrade your subscription for digital Make: Magazine access and exclusive videos. Introductory offer $24.99 the first year.</h4>
								<a href="/register/premium-subscriber?upgrade=65WSJ3T3GY" class="universal-btn membership-btn upgrade" onclick="pintrk('track', 'addtocart');">Upgrade</a>
								<div class="disclaimer">** Full membership price ($59.99) applied <b>annually</b> after first year. **</div>
							<?php } else { ?>
								<h1>You are already a Member!</h1>
								<a href="/activity" class="universal-btn-reversed" style="margin:0 auto;width:95%px;display:flex;font-size:24px;min-height:100px;padding:10px 20px;text-transform:capitalize">See what's happening on Make: Community</a>
							<?php } ?>
						<?php } else { ?>
							<h1>Become a Member of Make: Community</h1>
							<a href="javascript:void();" class="login-btn"><h4>Already a member? <span class="underline">Login now.</span></h4></a>
							<a href="/register/premium-subscriber" class="universal-btn-reversed membership-btn" onclick="pintrk('track', 'addtocart');">Join Today!</a>
							<div class="disclaimer">** Membership Fees are applied <b>annually</b>. **</div>
						<?php } ?>
						</div>
					<?php } // end MeprUtils if ?>
				</div>
			</header>
            <?php the_content(); ?>                  
        </article>
    </main>
</section>
<?php
// End of the loop.
endwhile;
get_footer();
