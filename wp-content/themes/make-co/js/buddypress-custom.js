jQuery("document").ready(function(){
	// If it's a profile page
	if(jQuery(".bp-user").length) {
		jQuery('.yz-profile-navmenu .yz-navbar-item').each(function() {
			// add classes to identify the user navbar items
			var navItemClass = jQuery(this).text().trim().replace(/\s+/g, '-').toLowerCase().replace(/[0-9]\s*$/, '');
			jQuery(this).addClass(navItemClass);
			// remove events navbar item unless it's a featured makerspace
			if(!jQuery(".bp-user").is('.member-type-maker_space.member-level-makerspace') && !jQuery(".bp-user").is('.member-type-producer') && jQuery(this).hasClass("events")){
				jQuery(this).remove();
			}
		});
	}
	// get the autocomplete working for the directory search form
	if(jQuery("#search-members-form")) {
		jQuery("#search-members-form").attr('role', 'search');
		jQuery("#search-members-form #members_search").attr("name", "s");
	}
	
	// remove the email editting field from the ump account page while we wait to hear from their support
	if(jQuery("#ihc_account_page_wrapp")) {
		jQuery("#ihc_account_page_wrapp .iump-form-line-register.iump-form-text").remove();
	}
});