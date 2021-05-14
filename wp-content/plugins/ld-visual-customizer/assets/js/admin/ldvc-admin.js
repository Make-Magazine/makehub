jQuery(document).ready(function($) {


    $('.ldvc-course-preview-btn').click(function(e) {

        e.preventDefault();

        var previewUrl  = encodeURI( $('#ldvc_course_preview').val() );
        var baseUrl     = $('#ldvc-preview-url').val();

        window.open( baseUrl + 'customize.php?url=' + previewUrl + '&autofocus[panel]=lds_visual_customizer' );

    });

});
