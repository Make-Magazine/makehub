// GDPR compliance never looked so good
window.addEventListener('load', function() {

	var storage = new CrossStorageClient('http://makehub.local/wp-content/universal-assets/v1/page-elements/universal-gdpr-helper.html', {
	  timeout: 90000000,
	  frameId: 'storageFrame'
	});
	
	
	storage.onConnect().then(function(result) {
		return storage.get('cookie-law');
	}).then(function(res){
		if( res == null ) {
			jQuery("#cookie-footer").css("display", "block");
		} else if(res == 'no') {
			setCookie('cookielawinfo-checkbox-non-necessary', 'yes', 365);
		} 
	});
							 
	jQuery("#cookie-configure").click(function(){
		jQuery("#cookie-dialog").dialog({
			draggable: false,
			resizable: false,
			title: null,
		});
	});
	
	jQuery("#cookie-accept").click(function(){
		jQuery("#cookie-dialog").dialog({
			draggable: false,
			resizable: false,
			title: null,
		});
	});
	
	jQuery('div#cookie-dialog').on('dialogclose', function(event) {
		jQuery("#cookie-footer").hide();
		if(jQuery("#nonNeccessaryCookies").is(':checked') && localStorage.getItem('cookie-law') ) {
			return storage.set('cookie-law', 'no');
		} else {
			return storage.set('cookie-law', 'yes');
		}
	});

	
});