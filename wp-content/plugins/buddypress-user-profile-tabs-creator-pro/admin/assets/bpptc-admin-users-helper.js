jQuery( function ( $ ) {

    var $users_list = $('#bpptc-selected-users-list');
    var $user_selector_field = $("#bpptc-user-selector");

    $user_selector_field.autocomplete({
        // define callback to format results, fetch data
        source: function(req, add){
            var ids= get_included_user_ids();
                ids = ids.join(',');
            // pass request to server
            $.post( ajaxurl,
                {
                    action: 'bpptc_get_users_list',
                    'q': req.term,
                    'included': ids,
                    cookie:encodeURIComponent(document.cookie)
                } , function(data) {

                add(data);
            },'json');
        },
        //define select handler
        select: function(e, ui) {

            var $li = "<li>" +
                "<input type='hidden' value='" + ui.item.id + "' name='_bpptc_tab_users[]'/>" +
                "<a class='bpptc-remove-user' href='#'>X</a>" +
                "<a href='"+ui.item.url + "'>" + ui.item.label + "</a>" +
                "</li>";
            $users_list.append($li );

            this.value="";
            return false;// do not update input box
        },
        // when a new menu is shown
        open: function(e, ui) {

        },
        // define select handler
        change: function(e, ui) {
        }
    });// end of autosuggest.


    //remove
    $users_list.on('click', '.bpptc-remove-user', function () {
        $(this).parent().remove();
        return false;
    });

    function get_included_user_ids() {
        var ids = [];

        $users_list.find('li input').each( function (index, element ) {
           ids.push( $(element).val());
        });

        return ids;
    }
});