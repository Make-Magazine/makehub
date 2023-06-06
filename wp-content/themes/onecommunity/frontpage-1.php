<?php
/*
Template Name: Frontpage 1
*/
?>

<?php get_header() ?>

<!-- ******************************************************************************************************
***********************************************1st row starts**********************************************
****************************************************************************************************** -->
<div class="frontpage-row-1 frontpage-row frontpage-style-1 animated-shapes-container">

<div class="wrapper">

<!-- left column  -->
	<div class="frontpage-row-1-left">

		<h1 class="bounceInDown">Ultimate solution<br> for your community!</h1>

		<div class="frontpage-row-1-desc bounceInLeft">
		Members of your community deserve more. Let's offer them what they won't find on other websites.
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>register" class="hero-button join for-logged-out-users fadeInUp">Join Now!</a>
		<a href="" class="hero-button alternative fadeInUp delayed">Premium</a>

	</div>
<!-- left column  -->


<!-- right column  -->
	<div class="frontpage-row-1-right fadein">

	<div class="for-logged-out-users">
		<?php echo do_shortcode( '[onecommunity-login]' ); ?>
	</div>

		<div class="for-logged-in-users">
		<a href=""><img src="<?php echo get_template_directory_uri(); ?>/img/ad-header.jpg"></a>
	</div>

	</div>
<!-- right column -->

</div><!-- wrapper -->

	<?php
	if ( get_theme_mod( 'onecommunity_front_shapes', true ) == true ) { ?>

	<div class="shape-1"></div>
	<div class="shape-2"></div>
	<div class="shape-3"></div>
	<div class="shape-4"></div>
	<div class="shape-5"></div>
	<div class="shape-6"></div>

	<?php
	}
    ?>


</div><!-- frontpage-row-1 -->
<!-- ******************************************************************************************************
*************************************************1st row ends**********************************************
****************************************************************************************************** -->






<!-- ******************************************************************************************************
***********************************************2nd row starts**********************************************
****************************************************************************************************** -->
<div class="wrapper">
<div class="frontpage-row-2 frontpage-row">
	<?php echo do_shortcode( "[onecommunity-bp-groups-listing title='Community Groups' number_of_groups='6' col='6' type='']" ); ?>
</div>
</div><!-- wrapper  -->
<!-- ******************************************************************************************************
*************************************************2nd row ends**********************************************
****************************************************************************************************** -->







<!-- ******************************************************************************************************
***********************************************4th row starts**********************************************
****************************************************************************************************** -->
<div class="wrapper">
  <div class="frontpage-row-4 frontpage-row">

  	<div class="frontpage-row-4-left">
	<h4><a href="/leaderboard">Members Rank</a></h4>

		<?php
		if ( shortcode_exists( 'onecommunity-gamipress-leaderboard-small' ) ) {
			echo do_shortcode( '[onecommunity-gamipress-leaderboard-small limit="10" name="points" type="_gamipress_points_points"]' );
		}
		?>

  	</div>

  	<div class="frontpage-row-4-right">
	<h4 id="activity-menu-button">New Activity</h4>

	<?php echo do_shortcode( '[onecommunity-activity max="8" col="2"]' ); ?>
  </div>

  </div>
</div>
<!-- ******************************************************************************************************
***********************************************4th row ends**********************************************
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
	<div class="frontpage-row-5-left">

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
	<div class="frontpage-row-5-right">

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
</div>

<?php } ?>

<!-- ******************************************************************************************************
***********************************************5th row ends**********************************************
****************************************************************************************************** -->



<!-- ******************************************************************************************************
***********************************************3rd row starts**********************************************
****************************************************************************************************** -->

<div class="wrapper">
  <div class="frontpage-row-3 frontpage-row">


	<div class="frontpage-row-3-title">
		<h4 id="shortcode-posts-menu-button">Recent News</h4>
	</div>

	<?php echo do_shortcode( '[onecommunity-cat-posts cat_id="15,85,17,21,19,20,86,6,7,5,13"]' ); ?>

  </div>
</div>

<!-- ******************************************************************************************************
***********************************************3rd row ends**********************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row-6 frontpage-row centered-heading">


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




<?php if ( class_exists( 'WooCommerce' ) ) { ?>



<!-- ******************************************************************************************************
***********************************************7th row **********************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row-7 frontpage-row centered-heading">

		<h4>Our Featured Products</h4>

		<p class="row-subtitle">Integer aliquet sodales nisi sed volutpat. Nulla odio nunc, lobortis et diam ac, fermentum finibus est. Duis at leo vitae est tristique imperdiet. Phasellus facilisis justo enim, et consequat eros interdum et. Sed porttitor neque id eleifend vulputate.</p>

		<?php echo do_shortcode( '[onecommunity-featured-products]' ); ?>

	</div><!-- frontpage-row-7 -->

</div><!-- wrapper -->

<!-- ******************************************************************************************************
*********************************************** 7th row ends **********************************************
****************************************************************************************************** -->

<?php } ?>





<?php if ( class_exists( 'LearnPress' ) ) { ?>

<!-- ******************************************************************************************************
***********************************************8th row **********************************************
****************************************************************************************************** -->

<div class="wrapper">

	<div class="frontpage-row-8 frontpage-row centered-heading">

		<h4>Our Featured Courses</h4>

		<p class="row-subtitle">Integer aliquet sodales nisi sed volutpat. Nulla odio nunc, lobortis et diam ac, fermentum finibus est. Duis at leo vitae est tristique imperdiet. Phasellus facilisis justo enim, et consequat eros interdum et. Sed porttitor neque id eleifend vulputate.</p>

		<?php echo do_shortcode( '[onecommunity-learnpress-featured limit="3" col="3" excerpt="yes" duration="yes" load_more="yes"]' ); ?>

	</div><!-- frontpage-row-7 -->

</div><!-- wrapper -->

<!-- ******************************************************************************************************
*********************************************** 8th row ends **********************************************
****************************************************************************************************** -->

<?php } ?>

<?php get_footer() ?>