jQuery(document).ready(function () {
	jQuery(".shopify-buy-frame svg").on("click", function(){
		console.log("hit it");
		jQuery(".shopify-buy-frame").toggleClass("minimize");
	});
});
