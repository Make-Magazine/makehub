jQuery(window).load(function(){
	if (window.location.href.indexOf("/projects-search/") > -1) {
		jQuery("article").before("<div class='filter-reset'><a href='/projects-search/' class='btn universal-btn'>Reset Filters</a></div>");
	}
});
