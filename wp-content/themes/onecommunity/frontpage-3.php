<?php
/*
Template Name: Frontpage 3
*/
?>

<?php get_header() ?>

<div id="frontpage-template-3">

<!-- ******************************************************************************************************
***********************************************1st row starts**********************************************
****************************************************************************************************** -->
<div class="frontpage-3-row-1 frontpage-row dark-bg">

<div class="wrapper">

<!-- left column  -->

	<div class="frontpage-row-1-left">

		<h1 class="bounceInDown">Work <span>colaborations</span></h1>
		<h2 class="bounceInDown">without borders</h2>

		<div class="desc fadeInUp">
		Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal that has evolved from generation X is on the runway heading towards a streamlined cloud solution.
		</div>

		<div class="stats fadeInDelayed">

			<div class="stat">
				<span class="counter" data-target="54">0</span>+ <span>Community Groups</span>
			</div>

			<div class="stat">
				<span class="counter" data-target="27">0</span>+ <span>Upcoming Events</span>
			</div>

			<div class="stat">
				<span class="counter" id="counter-3" data-target="10000">0</span>+ <span>Registered Members</span>
			</div>
		
		</div>

	</div>

<!-- left column  -->


<!-- right column  -->
	<div class="frontpage-row-1-right fadein">

		<h3>Last active groups</h3>

		<?php echo do_shortcode( '[onecommunity-bp-groups-listing-2 number_of_groups="6" sort_type="active" type=""]' ); ?>

	</div>

<!-- right column -->

</div><!-- wrapper -->

</div><!-- frontpage-row-1 -->
<!-- ******************************************************************************************************
*************************************************1st row ends**********************************************
****************************************************************************************************** -->


<!-- ******************************************************************************************************
***********************************************2nd row starts**********************************************
****************************************************************************************************** -->
<div class="frontpage-row-2 frontpage-row">

	<div class="wrapper">
	
		<div class="left">
			<img src="<?php echo esc_attr( get_bloginfo( 'template_directory', 'display' ) ); ?>/img/avatars.png" class="avatars" alt="Avatars" />
		</div>

		<div class="right">

			<div class="shortcode-features">
				<ul>
					<li class="feature feature-1">
						<h3>Create profiles</h3>
						<span class="desc">Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal that has evolved from generation.</span>
					</li>

					<li class="feature feature-2">
						<h3>Interact in groups</h3>
						<span class="desc">Collaboratively administrate empowered markets via plug-and-play networks. Dynamically procrastinate B2C users after installed base benefits.</span>
					</li>

					<li class="feature feature-3">
						<h3>Send private messages</h3>
						<span class="desc">Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables.</span>
					</li>

					<li class="feature feature-4">
						<h3>Share multimedia files</h3>
						<span class="desc">Completely synergize resource taxing relationships via premier niche markets. Professionally cultivate one-to-one customer service with robust ideas.</span>
					</li>
				</ul>
			</div>
			
		</div>

	</div><!-- wrapper  -->

</div>
<!-- ******************************************************************************************************
*************************************************2nd row ends**********************************************
****************************************************************************************************** -->



<!-- ******************************************************************************************************
***********************************************5th row starts**********************************************
****************************************************************************************************** -->

<?php if ( class_exists( 'EM_Scripts_and_Styles' ) ) { ?>

<div class="wrapper">
	<div class="frontpage-row-5 row-events frontpage-row centered-heading">

  	<h4>Upcoming Events</h4>

  	<p class="row-subtitle">Praesent ut luctus risus, tempor scelerisque sem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>


<!-- left column  -->
	<div class="left">

		<div class="shortcode-events">
		<?php echo do_shortcode( '[events_list_grouped mode="monthly"]
		<div class="shortcode-events-row">
		<div class="shortcode-events-left">#_EVENTDATES<br> #_EVENTTIMES</div>
		<div class="shortcode-events-right">
		<div class="shortcode-event-title">
		#_EVENTLINK in #_LOCATIONTOWN</div>
		<div class="shortcode-event-location">#_LOCATIONNAME</div></div>
		</div>
		[/events_list_grouped]' ); ?>
		</div>

	</div>
<!-- left column -->


<!-- right column -->
	<div class="right">

	<?php echo do_shortcode( '[event post_id="735"]
	<div class="dd_single-event">
	<a class="dd_single-event-image" href="#_EVENTURL">
	<img src="#_EVENTIMAGEURL">
		<span>
			Free
			<small>Entrance</small>
		</span>
	</a>

	<div class="dd_single-event-cat">#_EVENTCATEGORIES</div> <div class="dd_single-event-time">#_EVENTTIMES, #M #j</div>

	<h4><a href="#_EVENTURL">#_EVENTNAME</a> in #_LOCATIONTOWN</h4>

	<div class="shortcode-event-location">#_LOCATIONNAME #_LOCATIONADDRESS, #_LOCATIONCOUNTRY</div>
	</div>
	[/event]' ); ?>

	</div>
<!-- right column -->

	</div>
</div><!-- wrapper -->

<?php } ?>

<!-- ******************************************************************************************************
***********************************************5th row ends**********************************************
****************************************************************************************************** -->


<!-- ******************************************************************************************************
************************************** 3-columns tag posts row starts *************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row-3-col-posts frontpage-row">

		<div class="columns-container">

			<div class="spacer-1"></div><div class="spacer-2"></div>

				<div class="col col-1"><h4>Featured</h4>
					<?php echo do_shortcode( '[onecommunity-recent-blog-posts-tag number_of_blog_posts="3" tag="Featured" col="1"]' ); ?>
				</div>

				<div class="col col-2"><h4>Events</h4>
					<?php echo do_shortcode( '[onecommunity-recent-blog-posts-tag number_of_blog_posts="3" tag="Events" col="1"]' ); ?>
				</div>

				<div class="col col-3"><h4>Hot</h4>
					<?php echo do_shortcode( '[onecommunity-recent-blog-posts-tag number_of_blog_posts="3" tag="Hot" col="1"]' ); ?>
				</div>

		</div>

	</div>

</div><!-- wrapper -->

<!-- ******************************************************************************************************
*************************************** 3-columns tag posts row ends **************************************
****************************************************************************************************** -->



<!-- ******************************************************************************************************
************************************** 3-columns posts row starts *************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row">

		<?php echo do_shortcode( '[onecommunity-blog-posts per_page="3" col="3" title="Recent News"]' ); ?>

	</div>

</div><!-- wrapper -->

<!-- ******************************************************************************************************
*************************************** 3-columns posts row ends **************************************
****************************************************************************************************** -->



<!-- ******************************************************************************************************
*********************************************** Pricing row **********************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row centered-heading">


		<h4>Get Paid Membership</h4>

		<p class="row-subtitle">Praesent ut luctus risus, tempor scelerisque sem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>


	<div class="offers-container">

		<div class="offer">
			<h5>Basic</h5>
			<div class="price">$0
			<span>/quarter</span>
			</div>


			<ul class="features">
			<li>Create profile</li>
			<li>Post activities</li>
			<li>Receive messages</li>
			<li>Basic badges</li>
			<li class="not-available">Send friendship requests</li>
			<li class="not-available">Send Messages</li>
			<li class="not-available">Earn more badges</li>
			<li class="not-available">Post images</li>
			</ul>

			<a href="register" class="button login-popup-action">Join Now!</a>

		</div><!-- offer -->


		<div class="offer best">
			<h5>Standard</h5>
			<div class="label">BEST OFFER</div>
			<div class="price">
			$5
			<span>/quarter</span>
			</div>


			<ul class="features">
			<li>Create profile</li>
			<li>Post activities</li>
			<li>Receive messages</li>
			<li>Basic badges</li>
			<li>Send friendship requests</li>
			<li>Send Messages</li>
			<li class="not-available">Earn more badges</li>
			<li class="not-available">Post images</li>
			</ul>

			<a href="register" class="button">Get it Now!</a>

		</div><!-- offer -->


		<div class="offer">
			<h5>Advanced</h5>
			<div class="price">$12
			<span>/quarter</span>
			</div>


			<ul class="features">
			<li>Post activities</li>
			<li>Receive messages</li>
			<li>Basic badges</li>
			<li>Send friendship requests</li>
			<li>Send Messages</li>
			<li>Premium posts</li>
			<li>Post videos</li>
			<li>Live notifications</li>
			</ul>

			<a href="register" class="button">Get it Now!</a>

		</div><!-- offer -->

	</div><!-- offers-container -->

	</div><!-- frontpage-row-6 -->

</div><!-- wrapper -->

<!-- ******************************************************************************************************
*********************************************** Pricing row ends **********************************************
****************************************************************************************************** -->




<!-- ******************************************************************************************************
*********************************************** WooCommerce row **********************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row woocommerce-shortcode centered-heading">

		<h4>Our Featured Products</h4>

		<p class="row-subtitle">Integer aliquet sodales nisi sed volutpat. Nulla odio nunc, lobortis et diam ac, fermentum finibus est. Duis at leo vitae est tristique imperdiet. Phasellus facilisis justo enim, et consequat eros interdum et. Sed porttitor neque id eleifend vulputate.</p>

		<?php echo do_shortcode( '[onecommunity-featured-products]' ); ?>

	</div><!-- frontpage-row-7 -->

</div><!-- wrapper -->

<!-- ******************************************************************************************************
******************************************** WooCommerce row ends******************************************
****************************************************************************************************** -->


</div><!-- #frontpage-template-3  -->


<?php get_footer() ?>