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
	
});

function numbersAndDashes() {
    var e = event || window.event;  // get event object
    var key = e.keyCode || e.which; // get key cross-browser
	var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode != 46 && charCode != 45 && charCode > 31  && (charCode < 48 || charCode > 57)) {
        //Prevent default action, which is inserting character
        if (e.preventDefault) e.preventDefault(); //normal browsers
        e.returnValue = false; //IE
    }
}

