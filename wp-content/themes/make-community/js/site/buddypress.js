jQuery(document).ready(function () {
	if(jQuery(".widget_bp_groups_widget").length) {
		jQuery(".widget_bp_groups_widget h2").append("<i class='menu-button'></i>");
		jQuery(".widget_bp_groups_widget h2 .menu-button").on("click", function(){
			jQuery(this).closest('h2').next(".item-options").toggle();
		});
	}
	// remove group events subnav
	if(jQuery("#events-groups-li").length) {
		jQuery("#events-groups-li").remove();
	}
	// toggle for the category list widget
	if(jQuery(".wp-block-categories-list").length) {
		jQuery('.wp-block-categories-list .cat-item').each(function() {
			if (jQuery(this).find('.children').length !== 0) {
				jQuery(this).prepend("<i class='fa fa-plus see-children'></i>");
			}
		});
	}
	jQuery(document).on('click', ".see-children" , function() {
		jQuery(this).parent().toggleClass("show-children");
    });
	// activity form
	if(jQuery("#mpp-activity-upload-buttons").length) {
		jQuery("#whats-new-form").append(jQuery("#mpp-activity-upload-buttons"));
		jQuery("#whats-new-form").append(jQuery("#mpp-notice-message"));
		jQuery("#whats-new-form").append(jQuery("#mpp-activity-media-upload-container"));
	}
});
