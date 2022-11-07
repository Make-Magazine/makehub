(function($) {
    $(document).ready(function() {
        /**
         * On Entry Detail Page / click on Upgrade subscriptio button to upgrade the sub from older version to new version
         */
        $("#upgrade_mepr_sub").click(function() {
            $(".gp_entry_sub_wrapper .spinner").css('visibility', 'visible');
            var entry_id = $(this).data('entry');
            var gateway = $(this).data('gateway'); // payment gateway
            var type = $(this).data('type'); // recurring or non-recurring
            $.ajax({
                url: gpajax.ajaxurl,
                method: 'POST',
                data: {
                    'action': 'gp_upgrade_subscription',
                    entry: entry_id,
                    'wp_nonce_encrypto': gpajax.wp_nonce_encrypto,
                    gateway: gateway,
                    type: type
                },
                beforeSend: function(xhr) {
                    $(".gp_entry_sub_wrapper .spinner").css('visibility', 'visible');
                }
            }).done(function(data, textStatus, jqXHR) {
                $(".gp_entry_sub_wrapper .spinner").css('visibility', 'hidden');
                $(".gp_entry_sub_wrapper").hide();
                alert('updated successfully.');
            }).fail(function(data, textStatus, jqXHR) {
                alert(data.responseText);
                $(".gp_entry_sub_wrapper .spinner").css('visibility', 'hidden');
            });

        });
    });
})(jQuery);