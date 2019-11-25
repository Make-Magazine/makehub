jQuery(".yz-column-content .widget-title").click(function(){
	jQuery(".yz-column-content .bps-form").slideToggle( "slow" );
});

// hide or show long list of checkboxes
jQuery(".bps-checkbox .bps-label").click(function(){
	if(jQuery(this).hasClass('active')) {
		jQuery(this).removeClass('active');
		jQuery(this).nextAll().css( "display", "none" );
	} else {
		jQuery(this).addClass('active');
		jQuery(this).nextAll().css( "display", "inline" );
	}
});

// set the default for bps select boxes to 'All'
jQuery(document).ready(function(){
	if(jQuery(".bps-selectbox").length) {
		jQuery(".bps-selectbox select option").first().html("All");
		jQuery(".bps-selectbox .list .option").first().html("All");
		jQuery(".bps-selectbox .list .option").first().attr('data-value', 'All')
		if(!jQuery(".bps-selectbox .nice-select .current").text().trim().length) {
			jQuery(".bps-selectbox .nice-select .current").text("All");
		}
	}
   // if current user is on their profile page, have the avatar link to the change avatar page
	if(window.location.pathname.replace(/\/$/, "").includes("/members/" + ajax_object.wp_user_nicename )){
		jQuery(".yz-profile-img").attr("href", "/members/" + ajax_object.wp_user_nicename + "/profile/change-avatar");
	}
});