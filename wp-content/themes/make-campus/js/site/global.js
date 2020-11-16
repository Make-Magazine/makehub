function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

jQuery("#scrollToTop").click(function() {
	jQuery('html, body').animate({scrollTop:0}, 300);
});

// Default all accordions to closed
jQuery(document).ready(function($) { 
	if( $(".elementor-widget-accordion")[0] ){
		var delay = 100; setTimeout(function() { 
		$('.elementor-tab-title').removeClass('elementor-active');
		$('.elementor-tab-content').css('display', 'none'); }, delay); 
	}
}); 


// should this be universal?
// stick the secondary nav at the top of the hamburglar
jQuery(document).ready(function () {
	if (jQuery(window).width() < 767) {
		jQuery("#menu-secondary_universal_menu").clone().insertBefore(jQuery(".nav-flyout-columns"));
	}
	jQuery(window).on('resize', function(){
		if (jQuery("#nav-flyout #menu-secondary_universal_menu").length == 0 ) {
			if (jQuery(window).width() < 767) {
				jQuery("#menu-secondary_universal_menu").clone().insertBefore(jQuery(".nav-flyout-columns"));
			}
		}
		if (jQuery(window).width() > 767) {
			jQuery("#nav-flyout #menu-secondary_universal_menu").remove();
		}
	});
});