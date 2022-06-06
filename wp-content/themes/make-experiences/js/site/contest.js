jQuery(document).ready(function () {
	if(jQuery(".wpvc-profile").length) {
		var sidebarHeight = 0;
		jQuery(".wpvc-profile").children().each(function(){
			console.log(jQuery(this).attr("class") + " " + jQuery(this).height());
			sidebarHeight += jQuery(this).outerHeight(true);
		});
		jQuery(".wpvc-main-wrapper").css("height", sidebarHeight + 25 );
    }
});
