jQuery(document).ready(function () {
	if(jQuery('[id^=product-component]').find('.shopify-close-btn').length == 0) {
		jQuery('[id^=product-component]').append('<i class="shopify-close-btn fas fa-angle-double-right"></i>');
	}	
	jQuery(".shopify-close-btn").on("click", function(){
		jQuery(".shopify-buy-frame").toggleClass("minimize");
	});
});
