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
								<h1>To access this content, upgrade today!</h1>
								<h4><a href="/register/premium-subscriber?upgrade=65WSJ3T3GY">Upgrade your subscription</a> for digital Make: Magazine access and exclusive videos.<br />Introductory offer $24.99 the first year.</h4>
								<div class="membership-tables">
								<a href="/register/community" class="current" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Community</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-community-membership.png" width="100%" alt="Get a Premium Make: Free Membership" />
										<ul class="membership-benefits">
											<li>Member Directory</li>
											<li>Maker Space Directory</li>
											<li>Make: Online Platform</li>
											<li>Interest Groups</li>
											<li>Make: Newsletter</li>
											<li>Maker Shed Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">Current Membership</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
								<a href="/register/premium-subscriber?upgrade=65WSJ3T3GY" class="featured" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Premium</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-premium-membership.png" width="100%" alt="Get a Premium Make: Community Membership" />
										<ul class="membership-benefits">
											<li>Community Access +</li>
											<li>Complete Make: Digital Archive</li>
											<li>Print Make: Subscription<sup>*</sup></li>
											<li>Digital Make: Subscription</li>
											<li>Member only Videos</li>
											<li>Member Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">$24.99 first year**</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
								<a href="/register/multiseat-membership" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Multi-seat</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-multi-seat-membership.png" width="100%" alt="Get a Multiseat Make: Community Membership" />
										<ul class="membership-benefits">
											<li>Community Access +</li>
											<li>5 Sub Accounts</li>
											<li>Complete Make: Digital Archive</li>
											<li>Print Make: Subscription<sup>*</sup</li>
											<li>Digital Make: Subscription</li>
											<li>Member only Videos</li>
											<li>Member Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">$249.99 per year</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
							</div>
							<div class="disclaimer">* U.S. Residents Only</div>
							<div class="disclaimer">** Full price ($59.99) applied <b>annually</b> after first year. **</div>
							<?php } else { ?>
								<h1>You are already a Member!</h1>
								<a href="/activity" class="universal-btn-reversed" style="margin:0 auto;width:95%px;display:flex;font-size:24px;min-height:100px;padding:10px 20px;text-transform:capitalize">See what's happening on Make: Community</a>
							<?php } ?>
						<?php } else { ?>
							<h1>Become a Member of Make: Community</h1>
							<h3>As our favorite magazines and outlets vanish, we urge you to stand with us.<br />If you love our publication and believe in the power of the maker community, join.</h3>
							<a href="javascript:void();" class="login-btn"><h4>Already a member? <span class="underline">Log in now.</span></h4></a>
							<div class="membership-tables">
								<a href="/register/community" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Community</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-community-membership.png" width="100%" alt="Get a Premium Make: Free Membership" />
										<ul class="membership-benefits">
											<li>Member Directory</li>
											<li>Maker Space Directory</li>
											<li>Make: Online Platform</li>
											<li>Interest Groups</li>
											<li>Make: Newsletter</li>
											<li>Maker Shed Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">Free</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
								<a href="/register/premium-subscriber" class="featured" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Premium</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-premium-membership.png" width="100%" alt="Get a Premium Make: Community Membership" />
										<ul class="membership-benefits">
											<li>Community Access +</li>
											<li>Complete Make: Digital Archive</li>
											<li>Print Make: Subscription<sup>*</sup></li>
											<li>Digital Make: Subscription</li>
											<li>Member only Videos</li>
											<li>Member Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">$59.99 per year</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
								<a href="/register/multiseat-membership" onclick="pintrk('track', 'addtocart');">
									<div class="membership-table">
										<h3 class="membership-benefits-header">Multi-seat</h3>
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/join-multi-seat-membership.png" width="100%" alt="Get a Multiseat Make: Community Membership" />
										<ul class="membership-benefits">
											<li>Community Access +</li>
											<li>5 Sub Accounts</li>
											<li>Complete Make: Digital Archive</li>
											<li>Print Make: Subscription<sup>*</sup</li>
											<li>Digital Make: Subscription</li>
											<li>Member only Videos</li>
											<li>Member Newsletter</li>
										</ul>
										<div class="universal-btn membership-btn">$249.99 per year</div>
									</div>
									<div class="membership-shadow"></div>
								</a>
							</div>
							<div class="disclaimer">* U.S. Residents Only</div>
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
