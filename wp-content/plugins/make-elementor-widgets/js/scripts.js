jQuery(document).ready(function(){
	jQuery(".make-elementor-expando-box h4").click(function(){
		jQuery(this).toggleClass( "closed" );
		jQuery(this).next().toggleClass( "closed" );
	});
	// for rss carousel
	if(jQuery(".rss-carousel-read-more").length) {
		var slideBy = 'page';
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			slideBy = 1;
		}
		jQuery('.custom-rss-elementor.carousel').owlCarousel({
			loop: true,
			slideBy: slideBy,
			nav:true,
			responsiveClass:true,
			responsive:{
				0:{
        			items:1,
					stagePadding: 0,
				},
		        600:{
					items:3,
					stagePadding: 0,
		        },
			},
			navText : ["<i class='fas fa-arrow-alt-circle-left'></i>","<i class='fas fa-arrow-alt-circle-right'></i>"]
		})
	}
});
