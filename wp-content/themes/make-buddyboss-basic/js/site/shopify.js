jQuery(document).ready(function () {
	if (window.location.href.indexOf("/lessons/") > -1 || window.location.href.indexOf("/courses/") > -1) {
		jQuery(".shopify-close-btn").on("click", function(){
			jQuery(".shopify-buy-frame").toggleClass("minimize");
		});
	}
});
