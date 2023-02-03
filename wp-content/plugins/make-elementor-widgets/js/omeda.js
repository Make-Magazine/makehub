jQuery(document).ready(function ($) {
    save_omeda_postalID($);
});

function save_omeda_postalID($) {
    var updateOmedaIdBtn = $('#make-update-Omeda-ID')

    updateOmedaIdBtn.on('click', function (e) {
        e.preventDefault();

        // retrieve ID entered
        var postal_id = $("#omeda_postal_id").val();
        if(postal_id==''){
            alert('you must enter an account number');
            return;
        }

        // set data object
        var data = {
            action: 'saveOmedaID',
            nonce: make_ajax_object.ajaxnonce,
            postal_id: postal_id
        };

        // call Ajax
        $.ajax({
            url: make_ajax_object.ajaxurl,  // Ajax handler
            type: "post",
            data: data,
            beforeSend: function (xhr) {
                // show loading text
                updateOmedaIdBtn.find('.update').hide();
                updateOmedaIdBtn.find('.loading').show();
            },
            success: function (res) {
                console.log('success');

                // hide loading text
                updateOmedaIdBtn.find('.update').show();
                updateOmedaIdBtn.find('.loading').hide();

                $('#omeda-ajax-return-msg').text(res);

            },
            error: function (e) {
                console.log(e);
            },

        });

    });

}
