jQuery(".ticket-selector-submit-ajax").on("click", function(){
	if(jQuery(".ticket-selector-tbl-qty-slct").val() == 0) {
		jQuery("#tickets .error_message").text("Please add a ticket Quantity.");
	}
});
