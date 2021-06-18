jQuery(document).ready(function(){
	// scroll to the image uploader if user goes to that tab of the group admin
	if (window.location.href.indexOf("/admin/group-avatar/") > -1 || window.location.href.indexOf("/admin/group-cover-image/") > -1)   {
		jQuery([document.documentElement, document.body]).animate({
			scrollTop: jQuery("#group-settings-form").offset().top - 100
		}, 2000);
	}
	if (window.location.href.indexOf("/members/type/makerspaces/") > -1) {
		jQuery(".members-nav").append("<a href='https://makerspaces.make.co' class='btn universal-btn' style='float:right;margin-bottom:10px;margin-top:-10px;'>See Map</a>");
	}
	if (window.location.href.indexOf("/groups/") > -1) {
		setTimeout("jQuery('.bp-groups-tab a').attr('target', '_self');", 100);
	}
	if (window.location.href.indexOf("/projects-search/") > -1) {
		jQuery("article").before("<a href='/adventures' class='btn universal-btn'>Back to Adventures</a>");
	}
});

// 
jQuery("select#member-type-order-by").on('change', function(){
	if(jQuery('option:selected', this).text().replace(/\s+/g, '') == "Makerspace") {
		jQuery("h1.entry-title").append("<span>: Makerspaces</span>");
		jQuery(".members-nav").append("<a href='https://makerspaces.make.co' class='btn universal-btn' style='float:right;margin-bottom:10px;margin-top:-10px;'>See Map</a>");
	} else {
		jQuery(".members-nav .universal-btn").remove();
		jQuery("h1.entry-title span").remove();
	}
});