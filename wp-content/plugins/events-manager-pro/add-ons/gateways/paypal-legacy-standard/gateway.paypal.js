//add paypal redirection
jQuery(document).on('em_booking_gateway_add_paypal', function(event, response){
	// called by EM if return JSON contains gateway key, notifications messages are shown by now.
	if(response.result && typeof response.paypal_url != 'undefined' ){
		var ppForm = jQuery('<form action="'+response.paypal_url+'" method="post" id="em-paypal-redirect-form"></form>');
		jQuery.each( response.paypal_vars, function(index,value){
			ppForm.append('<input type="hidden" name="'+index+'" value="'+value+'" />');
		});
		ppForm.append('<input id="em-paypal-submit" type="submit" style="display:none" />');
		ppForm.appendTo('body').trigger('submit');
	}
});