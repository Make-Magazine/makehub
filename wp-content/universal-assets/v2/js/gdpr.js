// GDPR compliance never looked so good
jQuery(document).ready( function() {

	var storage = new CrossStorageClient('https://make.co/wp-content/universal-assets/v2/page-elements/universal-gdpr-helper.html', {
	  timeout: 5000,
	  frameId: 'storageFrame'
	});

	var gdpr_state = null;

	jQuery("#cookie-dialog").dialog({
		modal: true,
		title: null,
		resizable: false,
		draggable: false,
		autoOpen: false
	});

	storage.onConnect().then(function(result) {
		return storage.get('cookies-allowed');
	}).then(function(res){
		gdpr_state = res;
		if( res == null ) {
			jQuery("#cookie-footer").show();
		} else if(res == 'yes') {
			setCookie('cookielawinfo-checkbox-non-necessary', 'yes', 365);
		} else {
			jQuery("#cookie-settings-btn").show();
			setCookie('cookielawinfo-checkbox-non-necessary', 'no', 365);
			jQuery("#nonNeccessaryCookies").removeAttr('checked');
		}
	});

	jQuery("#cookie-settings-btn").click(function(){
		jQuery("#cookie-settings-btn").hide();
		jQuery("#cookie-dialog").dialog("open");
	});

	jQuery("#cookie-configure").click(function(){
		jQuery("#cookie-footer").hide();
		jQuery("#cookie-dialog").dialog("open");
	});

	jQuery("#cookie-accept").click(function(){
		jQuery("#cookie-footer").hide();
		if( !localStorage.getItem('cookie-law') ) {
			setCookie('cookielawinfo-checkbox-non-necessary', 'yes', 365);
			return storage.set('cookies-allowed', 'yes');
		}
	});

	jQuery('div#cookie-dialog').on('dialogclose', function(event) {
		jQuery("#cookie-settings-btn").show();
		if(jQuery("#nonNeccessaryCookies").is(':checked') ) {
			setCookie('cookielawinfo-checkbox-non-necessary', 'yes', 365);
			jQuery("#cookie-settings-btn").addClass("accepted");
			return storage.set('cookies-allowed', 'yes');
		} else {
			setCookie('cookielawinfo-checkbox-non-necessary', 'no', 365);
			return storage.set('cookies-allowed', 'no');
		}
	});


});
