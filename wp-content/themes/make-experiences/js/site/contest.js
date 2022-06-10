jQuery(document).ready(function () {
	if(jQuery(".page-template-page-search").length) {
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
