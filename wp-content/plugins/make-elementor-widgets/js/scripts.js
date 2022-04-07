jQuery(document).ready(function(){
	jQuery(".make-elementor-expando-box h4").click(function(){
		jQuery(this).toggleClass( "closed" );
		jQuery(this).next().toggleClass( "closed" );
	});
	// for rss carousel
	if(jQuery(".rss-carousel-read-more").length) {
		jQuery('.custom-rss-elementor.carousel').owlCarousel({
			loop: true,
			slideBy: 'page',
			nav:true,
			navText : ["<i class='fas fa-arrow-alt-circle-left'></i>","<i class='fas fa-arrow-alt-circle-right'></i>"]
		})
	}
});
