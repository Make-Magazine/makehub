jQuery(document).ready(function($) {
    var scheduled = false;
    var wasRunning = false;
    var wasStopped = false;

    $('#activecampaign-run-historical-sync').click(function (e) {
        if ( ! $(e.target).hasClass('disabled')) {
            $('#activecampaign-run-historical-sync').addClass('disabled');
            $('#activecampaign-run-historical-sync-status').html('Historical sync will start shortly...').show();
            runAjax({
                'action': 'activecampaign_for_woocommerce_schedule_historical_sync',
                'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
            });
            scheduled = true;
            wasStopped = false;
            wasRunning = false;
            startUpdateCheck();
        }
    });

    $('#activecampaign-cancel-historical-sync').click(function (e) {
        console.log('cancel historical sync button press');
        runAjax({
            'action': 'activecampaign_for_woocommerce_cancel_historical_sync',
            'type': 1,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        wasStopped = true;
        $('#sync-start-section').show();
        disableStopButtons();
    });

    $('#activecampaign-pause-historical-sync').click(function (e) {
        console.log('pause historical sync button press');
        runAjax({
            'action': 'activecampaign_for_woocommerce_pause_historical_sync',
            'type': 2,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        wasStopped = true;
        $('#sync-start-section').show();
        disableStopButtons();

    });
    $('#activecampaign-reset-historical-sync').click(function (e) {
        console.log('reset historical sync');
        runAjax({
            'action': 'activecampaign_for_woocommerce_reset_historical_sync',
            'type': 2,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        enableStopButtons();
        hideRunSection();
    });

    updateStatus();
    // Check sync status
    var statInt = setInterval(updateStatus, 6000);

    function startUpdateCheck() {
        console.log('Function: startUpdateCheck');
        statInt = setInterval(updateStatus, 6000);
    }

    function cancelUpdateCheck() {
        console.log('Function: cancelUpdateCheck');
        clearInterval(statInt);
    }

    function runAjax(data) {
        console.log('run the ajax', data);
        return new Promise((resolve, reject) => {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data
            }).done(response => {
                console.log('runAjax done response', response);
                $('#activecampaign-run-historical-sync-status').html(response.data);
                resolve(response.data);
            }).fail(response => {
                console.log('runAjax fail response', response);
                $('#activecampaign-run-historical-sync-status').html(response.responseJSON.data);
                reject(response.responseJSON.data)
            });
        });
    }

    function showRunSection() {
        console.log('Function: showRunSection');
        $('#sync-run-section').show();
    }

    function hideRunSection() {
        console.log('Function: hideRunSection');
        $('#sync-run-section').hide();
    }

    function disableStopButtons(){
        console.log('Function: disableStopButtons');
        $('#activecampaign-cancel-historical-sync').addClass('disabled');
        $('#activecampaign-pause-historical-sync').addClass('disabled');
    }

    function enableStopButtons(){
        console.log('Function: enableStopButtons');
        $('#activecampaign-cancel-historical-sync').removeClass('disabled');
        $('#activecampaign-pause-historical-sync').removeClass('disabled');
    }

    function enableStartSection() {
        console.log('Function: enableStartSection');
        $('#activecampaign-run-historical-sync').removeClass('disabled');
        $('#sync-start-section').show();

    }

    function disableStartSection() {
        console.log('Function: disableStartSection');
        $('#sync-start-section').hide();
        $('#activecampaign-run-historical-sync').addClass('disabled');
    }

    function updateStatus() {
        var data = {
            'action': 'activecampaign_for_woocommerce_check_historical_sync_status'
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data
        }).done(response => {
            console.log('sync status response', response.data);
            if (response.data <= 0) {
                if (!scheduled) {
                    console.log('not scheduled, updateStatus ajax response', response);
                    hideRunSection();
                    enableStartSection();
                    cancelUpdateCheck();
                }
                if (wasRunning && wasStopped) {
                    $('#activecampaign-run-historical-sync-status').html('Historical sync was stopped.' ).show();
                }else if(wasRunning) {
                    $('#activecampaign-run-historical-sync-status').html('Historical sync finished.').show();
                }
            } else if(response.data === 1){
                scheduled = true;
                console.log('is scheduled');
            } else if(response.data.is_paused && !scheduled){
                console.log('is paused');
                scheduled = false;

                cancelUpdateCheck();
                enableStartSection();
                showRunSection();
                disableStopButtons();

                $('#activecampaign-run-historical-sync-running-status').html('Paused on record:' + response.data.current_record + '/' + response.data.total_orders).show();
                $('#activecampaign-run-historical-sync-status-progress').css('width', response.data.percentage + '%');
            } else {
                console.log('running');
                scheduled = false;
                wasRunning = true;

                if (!wasStopped) {
                    showRunSection();
                    disableStartSection();
                    enableStopButtons();
                    $('#activecampaign-run-historical-sync-running-status').html('Processing record:' + response.data.current_record + '/' + response.data.total_orders).show();
                    $('#activecampaign-run-historical-sync-status-progress').css('width', response.data.percentage + '%');
                }
            }
        }).fail(response => {
            $('#activecampaign-run-historical-sync-status').html(response.responseJSON.data);
            cancelUpdateCheck();
        });
    }
});
