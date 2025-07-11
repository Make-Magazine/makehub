function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

jQuery(document).ready(function () {
	jQuery('#LoginBtn').attr("href", function(i, href) {
		if(jQuery(this).attr("data-balloon") != "Log Out") {
			var redirect_url = window.location.protocol + "//" + window.location.host + "/dashboard/";
			if(window.location.pathname == "/join/") { // if they logged in from the join page, send them to the referrer if it exists
				if(document.referrer && document.referrer != "") { redirect_url = document.referrer; }
				return href.split("?")[0] + '?redirect_to=' + redirect_url + "%3Flogin%3Dtrue";
			} else if(window.location.pathname == "/") { // if on homepage, redirect to dashboard
				return href.split("?")[0] + '?redirect_to=' + redirect_url + "%3Flogin%3Dtrue";
			} else { // otherwise send them to where they logged in from
				return href.split("?")[0] + '?redirect_to=' + window.location.href + "%3Flogin%3Dtrue";
			}
		}
	});
	// remove mp avatar dropdown from mp registration pages
	if(jQuery(".single-memberpressproduct").length){
		jQuery(".profile-menu__arrow_down").remove();
		jQuery(".profile-menu__dropdown").remove();
		jQuery("#user-menu-button").on("click", function(event){
			event.preventDefault();
			window.location.href = window.location.protocol + "//" + window.location.host + "/dashboard/";
		});
	}
	if(window.location.pathname == '/register/premium-subscriber/') {
		if(GetURLParameter('upgrade')) {
			jQuery('.have-coupon-link').css('display', 'none');
			jQuery('.mepr-coupon-code').attr('value', GetURLParameter('upgrade'));
			jQuery("label[for='mpgft-signup-gift-checkbox1']").remove();
			jQuery('h1.entry-title').prepend('Upgrade ');
			jQuery('.mepr_price_cell').before('24.99 the first year then &nbsp;');
			jQuery('.mepr-signup-form').attr('action', jQuery('.mepr-signup-form').attr('action') + '-' + GetURLParameter('upgrade'));
		}
		if (window.location.href.indexOf('mepr_jump-65WSJ3T3GY') > -1) {
			jQuery('h1.entry-title').prepend('Upgrade ');
			jQuery('.mp_price_str').text(jQuery('.mp_price_str').text().replace('with coupon 65WSJ3T3GY',''));
			jQuery('.mp-table tbody tr:eq(1) td:eq(0)').text("Upgrade Discount");
		}
   }
	if(window.location.pathname == '/register/contest/') {
	   jQuery("label[for='mpgft-signup-gift-checkbox1']").remove();
	}
	// if the update credit card button occurs on any mp-membership page other than the mp-subscriptions page, the link has to be updated to point to mp-subscriptions
	if (getLastPath(window.location.href) === "membership") {
		jQuery(".mepr-account-update").each(function(){
			var updateLink = jQuery(this).attr("href");
			var insertPoint = updateLink.lastIndexOf("?");
			updateLink = updateLink.substring(0,insertPoint) + 'subscriptions/' + updateLink.substring(insertPoint);
			jQuery(this).attr("href", updateLink);
		});
		var oldUrl = jQuery(".mepr-account-row-action").attr("href");
        var newUrl = oldUrl.replace("mp-membership", "membership"); 
		jQuery(".mepr-account-row-action").attr("href", newUrl);
	}
	// refresh page if buddypress cover image is changed
	if(typeof bp.CoverImage !== 'undefined') {
		bp.CoverImage.Attachment.on( 'change:url', function( data ) {
			location.reload();
		} );
	}
});


function getLastPath(url) {
    var parts = url.split("/");
    return (url.lastIndexOf('/') !== url.length - 1 
       ? parts[parts.length - 1]
       : parts[parts.length - 2]);
}

function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

jQuery(".truncated").click(function() {
	jQuery(this).css("display", "inherit");
});

jQuery("#scrollToTop").click(function() {
	jQuery('html, body').animate({scrollTop:0}, 300);
});

// Default all accordions to closed
jQuery(document).ready(function($) {
	if( $(".elementor-widget-accordion")[0] ){
		var delay = 100; setTimeout(function() {
		$('.elementor-tab-title').removeClass('elementor-active');
		$('.elementor-tab-content').css('display', 'none'); }, delay);
	}
	if($(".event-info")[0]) {
		$(".event-info").find('a').each(function() {
			$(this).attr("target", "_blank");
		});
	}
});



jQuery(".expando-box h4").click(function(){
	jQuery(this).toggleClass( "open" );
	jQuery(this).next().toggleClass( "open" );
});

// universal treatment for info buttons to pull up their related "modal"
jQuery(document).on('gpnf_post_render', function(){
    infoButton();
});

jQuery(document).on('gform_page_loaded', function(event, form_id, current_page){
	infoButton();
});
jQuery(document).ready(function(event, form_id, current_page){
	infoButton();
});

function infoButton(){
	jQuery(".info-btn").on("click", function(){
		var infoElement = jQuery(this).attr("data-info");
		jQuery('.info-modal.'+infoElement).toggle();
	});
}
