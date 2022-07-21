jQuery(document).ready(function () {
	if(jQuery(".page-template-page-search").length) {
		var url = window.location.href.split('?')[0];
		jQuery("form.searchandfilter").after("<div class='filter-reset'><a href='" + url + "' class='btn universal-btn' style='display:none;'>Reset Filters</a></div>");
		if(document.location.search.length) {
			jQuery(".filter-reset .btn").css("display", "inline-flex");
		} else {
			// if the form changes, show the reset button
			jQuery(".searchandfilter").change(function(){
				jQuery(".filter-reset .btn").css("display", "inline-flex");
			});
		}
	}
	if(jQuery("body[class*='page-judge-set']").length) {
		jQuery(".MuiTypography-body1 .universal-btn").attr("target", "_blank");
	}
	jQuery(".wpvc-vote").prepend('<a class="back-btn" data-toggle="tooltip" data-placement="right" href="/amazing-maker-awards/" title="Take me back to the project gallery or click the right arrow to see another project entry."><svg class="wpvc_gallery_btn" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></svg></a>');
});
