jQuery("document").ready(function(){
	// If it's a profile page
	if(jQuery(".bp-user").length) {
		jQuery('.yz-profile-navmenu .yz-navbar-item').each(function() {
			// add classes to identify the user navbar items
			var navItemClass = jQuery(this).text().trim().replace(/\s+/g, '-').toLowerCase().replace(/[0-9]\s*$/, '');
			jQuery(this).addClass(navItemClass);
			// remove events navbar item unless it's a featured makerspace
			if(!jQuery(".bp-user").hasClass('member-type-makerspace') && !jQuery(".bp-user").hasClass('member-level-makerspace') && jQuery(this).hasClass("events")){
				jQuery(this).remove();
			}
		});
	}
});