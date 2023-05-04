jQuery(document).ready(function () {
	if(jQuery(".page-template-page-search").length) {
		var url = window.location.href.split('?')[0];
		jQuery("form.searchandfilter").after("<div class='filter-reset'><a href='" + url + "' class='btn universal-btn no-click'>Reset Filters</a></div>");
		if(document.location.search.length) {
			jQuery(".filter-reset .btn").removeClass('no-click');
		} else {
			// if the form changes, show the reset button
			jQuery(".searchandfilter").change(function(){
				jQuery(".filter-reset .btn").removeClass('no-click');
			});
		}
	}
	jQuery(".wpvc-vote").prepend('<a class="back-btn" data-toggle="tooltip" data-placement="right" href="/amazing-maker-awards/" title="Take me back to the project gallery or click the right arrow to see another project entry."><svg class="wpvc_gallery_btn" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></svg></a>');
	// set the max-height to what it actually needs
	if(jQuery(".wpvc-main-wrapper").length) {
		var maxHeight = (jQuery(".wpvc-profile").outerHeight() > 900) ? jQuery(".wpvc-profile").outerHeight() : 900;
		jQuery('.wpvc-main-wrapper').css("max-height", maxHeight);
		jQuery(".collapsed").on("click", function(){
			alert("test");
			jQuery('.wpvc-main-wrapper').css("max-height", 1500);
		});
	}
});
