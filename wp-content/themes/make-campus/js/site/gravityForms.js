jQuery(document).ready(function ($) {
    jQuery(document).on("change", "li.preview_image div.ginput_container input[type=file]", function () {
        readURL(this);
    });

    function readURL(input) {
        var inputID = jQuery(input).attr('id');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {            
                jQuery(input).after('<div id="preview_'+inputID+'"><img src="" style="width:250px;"></div>');
                jQuery('#preview_'+inputID+'>img' ).attr( 'src', e.target.result );
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
})
