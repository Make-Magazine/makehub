jQuery(document).ready(function(){
	jQuery(".make-elementor-expando-box h4").click(function(){
		jQuery(this).toggleClass( "closed" );
		jQuery(this).next().toggleClass( "closed" );
	});
	// for rss carousel
	if(jQuery(".rss-carousel-read-more").length) {
		jQuery(".rss-carousel-clicker-right").click(function(){
			var carousel_read_more_x = jQuery(".rss-carousel-read-more").offset().left;
			var carousel_clicker_x = jQuery(".rss-carousel-clicker-right").offset().left;
			if( carousel_read_more_x > carousel_clicker_x - 100) {
				jQuery(".elementor-widget-makecustomrss ul.custom-rss-elementor.horizontal.carousel li").css( "left", "-=335" );
			}
		});
		jQuery(".rss-carousel-clicker-left").click(function(){
			var carousel_first_x = jQuery("ul.carousel li:first-of-type").offset().left;
			console.log(carousel_first_x);
			var carousel_clicker_x = jQuery(".rss-carousel-clicker-left").offset().left;
			if( carousel_first_x < carousel_clicker_x - 100) {
				jQuery(".elementor-widget-makecustomrss ul.custom-rss-elementor.horizontal.carousel li").css( "left", "+=335" );
			}
		});
	}
});
