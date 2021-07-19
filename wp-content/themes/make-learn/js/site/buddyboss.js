jQuery(window).load(function(){
	if (window.location.href.indexOf("/projects-search/") > -1) {
		jQuery("article").before("<a href='/adventures' class='btn universal-btn'>Reset Filters</a>");
	}
});
