jQuery(document).ready(function () {
	if (window.location.href.indexOf("/project/") > -1 || window.location.href.indexOf("/projects/") > -1) {
		jQuery(".shopify-close-btn").on("click", function(){
			jQuery(".shopify-buy-frame").toggleClass("minimize");
		});
	}
});
