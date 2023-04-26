function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

jQuery(document).ready(function () {
	// buddyboss Buddypanel
	jQuery("body").addClass("buddypanel-closed");
	jQuery("a.bb-toggle-panel").on("click", function(){
		if(jQuery("body").hasClass("buddypanel-closed")) {
			jQuery("body").removeClass("buddypanel-closed");
			jQuery("body").addClass("buddypanel-open");
		} else {
			jQuery("body").addClass("buddypanel-closed");
			jQuery("body").removeClass("buddypanel-open");
		}
	});
});

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
