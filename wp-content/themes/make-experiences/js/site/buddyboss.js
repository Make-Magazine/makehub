jQuery(document).ready(function(){
	// scroll to the image uploader if user goes to that tab of the group admin
	if (window.location.href.indexOf("/admin/group-avatar/") > -1 || window.location.href.indexOf("/admin/group-cover-image/") > -1)   {
		jQuery([document.documentElement, document.body]).animate({
			scrollTop: jQuery("#group-settings-form").offset().top - 100
		}, 2000);
	}
});
