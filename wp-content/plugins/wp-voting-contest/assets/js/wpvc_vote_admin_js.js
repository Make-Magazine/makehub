jQuery(document).ready(function(){
    //#vote_onlyloggedinuser
    jQuery(document).on('click','.ow_add_manualvotes',function() {
        var votes_id = jQuery(this).data('id');
        var termid = jQuery(this).data('term');
        var ow_manual_votes = jQuery('#ow_manual_votes'+votes_id).val();

        if(ow_manual_votes !== "" && ow_manual_votes > 0 && Math.floor(ow_manual_votes) == ow_manual_votes && jQuery.isNumeric(ow_manual_votes)){
            jQuery('.wpvc_admin_error').html('');
            jQuery.ajax({
                url : ajaxurl,
                type: 'POST',
                data: 'action=wpvc_add_manual_votes&no_votes='+ow_manual_votes+'&pid='+votes_id+'&termid='+termid,
                cache : false,
                success: function(result){
                    if(result !== ""){
                        alert('Manual Votes added successfully !');
                        location.reload();
                    }
                }
            });
        }
        else{
            jQuery('.wpvc_admin_error').html('Add Valid Integer');
        }
    });

});