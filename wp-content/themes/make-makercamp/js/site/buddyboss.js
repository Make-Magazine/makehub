jQuery(window).load(function(){
	if (window.location.href.indexOf("/projects-search/") > -1) {
		jQuery("article").before("<a href='/adventures' class='btn universal-btn'>Reset Filters</a>");
	}
	if (window.location.href.indexOf("/makercamp-register/") > -1) {
		jQuery("#LoginBtn").attr("href", "/wp-login.php?redirect_to=" + window.location.protocol + "//" + window.location.hostname + "/?logged-in=true");
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
