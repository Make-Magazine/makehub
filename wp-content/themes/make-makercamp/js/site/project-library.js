jQuery(window).load(function(){
	if (window.location.href.indexOf("/project-library/") > -1) {
		jQuery("article").before("<div class='filter-reset'><a href='/project-library/' class='btn universal-btn' style='display:none;'>Reset Filters</a></div>");
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
