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
    }
});
