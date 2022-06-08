
jQuery(document).ready(function () {
	// set the max-height to what it actually needs
	if(jQuery(".wpvc-main-wrapper").length) {
		jQuery('.wpvc-main-wrapper').css("max-height", jQuery(".wpvc-profile").outerHeight());
    }
});
