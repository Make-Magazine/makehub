jQuery(document).ready(function ($) {
    jQuery(document).on("change", "li.preview_image div.ginput_container input[type=file]", function () {
        readURL(this);
		console.log("how often does this happen");
    });

    function readURL(input) {
        var inputID = jQuery(input).attr('id');
        if (input.files && input.files[0]) {
			console.log(input);
			console.log(input.files);
            var reader = new FileReader();
            reader.onload = function (e) {         
                jQuery("#preview_"+inputID).remove();
                jQuery(input).after('<div class="image-wrapper"><div id="preview_'+inputID+'"><img src="" style="width:250px;"></div></div>');
                jQuery('#preview_'+inputID+'>img' ).attr( 'src', e.target.result );
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
})
