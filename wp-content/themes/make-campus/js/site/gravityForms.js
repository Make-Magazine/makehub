jQuery(document).ready(function ($) {
    jQuery(document).on("change", "li.preview_image div.ginput_container input[type=file]", function () {
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {                
                jQuery(this).parent().append('<img src="' + e.target.result + '" style="width:150px;"');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
})
