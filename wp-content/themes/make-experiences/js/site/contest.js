jQuery(document).ready(function () {
	// set the max-height to what it actually needs
	if(jQuery(".wpvc-main-wrapper").length) {
		var maxHeight = (jQuery(".wpvc-profile").outerHeight() > 900) ? jQuery(".wpvc-profile").outerHeight() : 900;
		jQuery('.wpvc-main-wrapper').css("max-height", maxHeight);
		jQuery(".collapsed").on("click", function(){
			jQuery('.wpvc-main-wrapper').toggleClass(function() {
				if ( jQuery('.wpvc-main-wrapper').outerHeight > jQuery(".wpvc-profile").outerHeight() ) {
					return "tallboy";
				} else {
					return "short";
				}
			});
		});
    }
	if(jQuery(".page-template-page-search").length) {
		jQuery(".fluid-width-video-wrapper").css("padding-top", "");
		var url = window.location.href.split('?')[0];
		jQuery("article").before("<div class='filter-reset'><a href='" + url + "' class='btn universal-btn' style='display:none;'>Reset Filters</a></div>");
		if(document.location.search.length) {
			jQuery(".filter-reset .btn").css("display", "inline-flex");
		} else {
			// if the form changes, show the reset button
			jQuery(".searchandfilter").change(function(){
				jQuery(".filter-reset .btn").css("display", "inline-flex");
			});
		}
	}
});
