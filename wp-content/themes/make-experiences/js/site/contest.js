jQuery(document).ready(function () {
	// set the max-height to what it actually needs
	if(jQuery(".wpvc-main-wrapper").length) {
		var maxHeight = (jQuery(".wpvc-profile").outerHeight() > 800) ? jQuery(".wpvc-profile").outerHeight() : 800;
		jQuery('.wpvc-main-wrapper').css("max-height", maxHeight);
    }
});
