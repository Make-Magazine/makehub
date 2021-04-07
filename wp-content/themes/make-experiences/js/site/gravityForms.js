jQuery(document).ready(function ($) {
    jQuery(document).on("change", "li.preview_image div.ginput_container input[type=file]", function () {
        readURL(this);
    });

    function readURL(input) {
        var inputID = jQuery(input).attr('id');
        if (input.files && input.files[0]) {			
            var reader = new FileReader();
            reader.onload = function (e) {         
                jQuery("#preview_"+inputID).remove();
                jQuery(input).after('<div id="preview_'+inputID+'"><div class="preview_img-wrapper"></div></div>');
                jQuery('#preview_'+inputID+' .preview_img-wrapper').css( 'background-image', 'url(' + e.target.result + ')' );
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
	/*jQuery('.datepicker').delegate("click", function () { 
		$(this).datepicker({showOn:'focus'}).focus(); 
	});*/
});


$(document).on("mouseenter touchstart",".time",function(e){
	jQuery(".ui-timepicker-wrapper").remove();
	jQuery(this).timepicker({ 
		stopScrollPropagation: false,
		disableTextInput: true,
		disableTouchKeyboard: true,
		orientation: "bl",
		step: 15
	} );
});

/*$(document).on("click",".datepicker",function(e){
	console.log("this is happening");
	jQuery(this).datepicker();
});*/



/*jQuery(document).on('gform_post_render', function(event, formId, currentPage) {
	alert("is this on?");
	jQuery('.time').each(function(i, obj) {
		jQuery(".ui-timepicker-wrapper").remove();
		 jQuery(this).timepicker({
			stopScrollPropagation: false,
			disableTextInput: true,
			disableTouchKeyboard: true,
			orientation: "bl",
			step: 15
		} );
	});
});*/
