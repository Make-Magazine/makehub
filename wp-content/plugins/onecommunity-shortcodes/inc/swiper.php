<?php
function onecommunity_swiper_one($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '5',
    "tag" => ''
	), $atts));

	$transient = get_transient( 'onecommunity_swiper_one' );
	if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_swiper_one_enable', 0 ) == 1 ) {

		ob_start(); ?>


			<div class="swiper-one-slider">

				<div class="swiper-container swiper-one">
				<div class="swiper-wrapper">

				<?php
				$wp_query = '';
				$paged = '';
				$temp = $wp_query;
				$wp_query= null;
				$wp_query = new WP_Query();
				$wp_query->query('tag=' . $tag . '&posts_per_page=' . $limit . '&post_status=publish&paged='.$paged);
				while ($wp_query->have_posts()) : $wp_query->the_post();
				?>

					<!-- *** SLIDE *** -->

  					<div class="swiper-slide" style="background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>')">

  						<div class="wrapper">

    						<div class="post">

      						<div class="details">

        						<span class="category"><?php the_category(', ') ?></span>

        						<span class="comments"><?php comments_number('0', '1', '%'); ?></span>

        						<?php
        						if ( shortcode_exists( 'wp_ulike' ) ) {
        						echo do_shortcode('[wp_ulike]');
        						} ?>
  
        						<span class="time"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'onecommunity' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>

      						</div><!-- .details ends -->

      						<h1 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

      						<div class="desc"><?php the_excerpt(); ?></div>

      						<div class="bottom">
        						<span class="author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_avatar( get_the_author_meta( 'ID' ) , 50); ?></a> <?php esc_attr_e('by', 'onecommunity'); ?> <?php echo get_the_author(); ?></span>
        						<a href="<?php the_permalink(); ?>" class="more"><?php esc_attr_e('Continue reading &rarr;', 'onecommunity'); ?></a>
      						</div>

    						</div><!-- .post ends -->

  						</div><!-- .wrapper ends -->

					   </div><!-- .swiper-slide ends -->

					<!-- *** SLIDE *** -->

				<?php endwhile; // end of loop
				wp_reset_postdata(); ?>

				</div><!-- .swiper-wrapper ends -->

				<!-- Add Arrows -->
				<div class="swiper-button-next swiper-button-white"></div>
				<div class="swiper-button-prev swiper-button-white"></div>

				</div><!-- .swiper-container.swiper-one ends -->



				<div class="wrapper swiper-bullets swiper-one-bullets">
  					<div class="gallery-thumbs">
    					<div class="swiper-wrapper">

  						  <?php
  						  $wp_query = '';
  						  $paged = '';
  						  $temp = $wp_query;
  						  $wp_query= null;
  						  $wp_query = new WP_Query();
  						  $wp_query->query('tag=' . $tag . '&posts_per_page=' . $limit . '&post_status=publish&paged='.$paged);
  						  while ($wp_query->have_posts()) : $wp_query->the_post();
  						  ?>

    						  <div class="swiper-slide">
      							<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'post-thumbnail') ?>">
      							<h5><?php the_title(); ?></h5>
    						  </div>

  						  <?php endwhile; // end of loop
  						  wp_reset_postdata(); ?>

              </div><!-- ."swiper-wrapper ends -->
  					</div><!-- .gallery-thumbs ends -->
				</div><!-- .wrapper.swiper-bullets ends -->

				<script>
            var viewport = window.innerWidth;

            if (viewport < 760) { 

              var swiperOneThumbs = new Swiper('.swiper-one-bullets .gallery-thumbs', {
                spaceBetween: 30,
                slidesPerView: 2,
                loop: true,
                freeMode: true,
                loopedSlides: 2, //looped slides should be the same
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
              });

              var heroPosts = new Swiper('.swiper-one', {
                spaceBetween: 3,
                loop: true,
                autoplay: {
                  delay: 4000,
                  disableOnInteraction: false,
                },
                loopedSlides: 2, //looped slides should be the same
                navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
                },
                thumbs: {
                  swiper: swiperOneThumbs,
                },
            });


            } else if (viewport < 1024) { 

              var swiperOneThumbs = new Swiper('.swiper-one-bullets .gallery-thumbs', {
                spaceBetween: 30,
                slidesPerView: 3,
                loop: true,
                freeMode: true,
                loopedSlides: 3, //looped slides should be the same
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
              });

              var heroPosts = new Swiper('.swiper-one', {
                spaceBetween: 3,
                loop: true,
                autoplay: {
                  delay: 4000,
                  disableOnInteraction: false,
                },
                loopedSlides: 3, //looped slides should be the same
                navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
                },
                thumbs: {
                  swiper: swiperOneThumbs,
                },
              });

            } else {

              var swiperOneThumbs = new Swiper('.swiper-one-bullets .gallery-thumbs', {
                spaceBetween: 30,
                slidesPerView: 5,
                loop: true,
                freeMode: true,
                loopedSlides: 5, //looped slides should be the same
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
              });
                var heroPosts = new Swiper('.swiper-one', {
                  spaceBetween: 3,
                  loop: true,
                  autoplay: {
                   delay: 4000,
                   disableOnInteraction: false,
                },
                loopedSlides: 5, //looped slides should be the same
                navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
                },
                thumbs: {
                  swiper: swiperOneThumbs,
                },
              });

            }


				</script>

			</div><!-- .swiper-one-slider end -->

		<?php
		$shortcode_content = ob_get_clean();

		if ( get_theme_mod( 'onecommunity_transient_swiper_one_enable', 0 ) == 1 ) {
			set_transient( 'onecommunity_swiper_one', $shortcode_content, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_swiper_one_expiration', 1440 ) );
		}

    return $shortcode_content;

	} else {
		return '<!-- Transient onecommunity_swiper_one ('.get_theme_mod( 'onecommunity_transient_swiper_one_expiration', 1440 ).' minutes) -->'.$transient;
	}

}
add_shortcode("onecommunity-swiper-one", "onecommunity_swiper_one");

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_swiper_two($atts, $content = null) {
  extract(shortcode_atts(array(
    "limit" => '7',
    "tag" => ''
  ), $atts));

  $transient = get_transient( 'onecommunity_swiper_two' );
  if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_swiper_two_enable', 0 ) == 1 ) {

    ob_start(); ?>

      <div class="swiper-two-container">
  
        <div class="swiper-container swiper-two">
          <div class="swiper-wrapper">

            <?php
            $wp_query = null;
            $temp = $wp_query;
            $wp_query = new WP_Query();
            $wp_query->query('tag=' . $tag . '&posts_per_page=' . $limit . '&post_status=publish&paged=1');
            while ($wp_query->have_posts()) : $wp_query->the_post();
            ?>

            <div class="swiper-slide">
              <?php get_template_part( 'template-parts/blog', 'grid' ); ?>
            </div>

            <?php endwhile;
            wp_reset_postdata(); // end of loop ?>

          </div>

          <!-- Add Pagination -->
          <div class="swiper-pagination"></div>

        </div>

        <!-- Initialize Swiper -->
        <script>
        var viewport = window.innerWidth;

        if (viewport < 760) {

          var swiper = new Swiper('.swiper-container.swiper-two', {
            slidesPerView: 1,
            spaceBetween: 15,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
            },
          });            

        } else if (viewport < 1024) { 

          var swiper = new Swiper('.swiper-container.swiper-two', {
            slidesPerView: 2,
            spaceBetween: 30,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
            },
          });

        } else if (viewport < 1300) {

          var swiper = new Swiper('.swiper-container.swiper-two', {
            slidesPerView: 3,
            spaceBetween: 30,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
            },
          });

        } else {

          var swiper = new Swiper('.swiper-container.swiper-two', {
            slidesPerView: 4,
            spaceBetween: 30,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
            },
          });

        }
        </script>

      </div><!-- swiper-two-container -->

    <?php
    $shortcode_content = ob_get_clean();

    if ( get_theme_mod( 'onecommunity_transient_swiper_two_enable', 0 ) == 1 ) {
      set_transient( 'onecommunity_swiper_two', $shortcode_content, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_swiper_two_expiration', 1440 ) );
    }

    return $shortcode_content;

  } else {
    return '<!-- Transient onecommunity_swiper_two ('.get_theme_mod( 'onecommunity_transient_swiper_two_expiration', 1440 ).' minutes) -->'.$transient;
  }

}
add_shortcode("onecommunity-swiper-two", "onecommunity_swiper_two");

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function onecommunity_swiper_three($atts, $content = null) {
  extract(shortcode_atts(array(
    "limit" => '7',
    "tag" => ''
  ), $atts));

  $transient = get_transient( 'onecommunity_swiper_three' );
  if ( false === $transient OR !get_theme_mod( 'onecommunity_transient_swiper_three_enable', 0 ) == 1 ) {

    ob_start(); ?>

      <div class="swiper-container swiper-three">

        <div class="swiper-wrapper">

          <?php
          $wp_query = null;
          $temp = $wp_query;
          $wp_query = new WP_Query();
          $wp_query->query('tag=' . $tag . '&posts_per_page=' . $limit . '&post_status=publish&paged=1');
          while ($wp_query->have_posts()) : $wp_query->the_post();
          ?>

            <div class="swiper-slide" style="background-image:url('<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>')">

              <div class="post">

                <div class="details">

                  <span class="category"><?php the_category(', ') ?></span>

                  <span class="comments"><?php comments_number('0', '1', '%'); ?></span>

                  <?php
                  if ( shortcode_exists( 'wp_ulike' ) ) {
                    echo do_shortcode('[wp_ulike]');
                  } ?>
  
                  <span class="time"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'onecommunity' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>

                </div><!-- .details ends -->

                <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

              </div><!-- post ends -->

            </div><!-- swiper-slide ends -->

          <?php endwhile; // end of loop
          wp_reset_postdata(); ?>

        </div><!-- swiper-wrapper ends -->

        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>

      </div><!-- swiper-container swiper-three ends -->

      <!-- Initialize Swiper -->
      <script>
        var swiper = new Swiper('.swiper-container.swiper-three', {
          slidesPerView: 'auto',
          spaceBetween: 20,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
        });
      </script>

    <?php
    $shortcode_content = ob_get_clean();

    if ( get_theme_mod( 'onecommunity_transient_swiper_three_enable', 0 ) == 1 ) {
      set_transient( 'onecommunity_swiper_three', $shortcode_content, MINUTE_IN_SECONDS * get_theme_mod( 'onecommunity_transient_swiper_three_expiration', 1440 ) );
    }

    return $shortcode_content;

  } else {
    return '<!-- Transient onecommunity_swiper_three ('.get_theme_mod( 'onecommunity_transient_swiper_three_expiration', 1440 ).' minutes) -->'.$transient;
  }

}
add_shortcode("onecommunity-swiper-three", "onecommunity_swiper_three");

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////