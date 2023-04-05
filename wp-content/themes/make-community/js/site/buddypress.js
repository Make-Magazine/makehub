jQuery(document).ready(function () {
	if(jQuery(".widget_bp_groups_widget").length) {
		jQuery(".widget_bp_groups_widget h2").append("<i class='menu-button'></i>");
		jQuery(".widget_bp_groups_widget h2 .menu-button").on("click", function(){
			console.log("here we go");
			jQuery(this).closest('h2').next(".item-options").toggle();
		});
	}
});
