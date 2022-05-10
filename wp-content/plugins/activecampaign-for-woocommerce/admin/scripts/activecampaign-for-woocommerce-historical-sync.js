jQuery(document).ready(function($) {
    var scheduled = false;
    var stickCount = 0;
    var lastRec = 0;

    $('#activecampaign-run-historical-sync').click(function (e) {
        if ( ! $(e.target).hasClass('disabled')) {
            $('#activecampaign-run-historical-sync').addClass('disabled');
            $('#activecampaign-historical-sync-run-shortly').show();

            var startRec = 0;
            var batchLimit = 100;

            if($('#activecampaign-historical-sync-starting-record').val() > 0) {
                startRec = $('#activecampaign-historical-sync-starting-record').val();
            }

            if($('#activecampaign-historical-sync-limit').find(":selected").text() > 0) {
                batchLimit = $('#activecampaign-historical-sync-limit').find(":selected").text()
            }

            var action = 'activecampaign_for_woocommerce_schedule_bulk_historical_sync';

            runAjax({
                'action': action,
                'startRec': startRec,
                'batchLimit': batchLimit,
                'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
            });
            scheduled = true;
            startUpdateCheck();
        }
    });

    $('#activecampaign-cancel-historical-sync').click(function (e) {
        runAjax({
            'action': 'activecampaign_for_woocommerce_cancel_historical_sync',
            'type': 1,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        $('#activecampaign-historical-sync-stop-requested').show();
        disableStopButtons();
    });

    $('#activecampaign-pause-historical-sync').click(function (e) {
        runAjax({
            'action': 'activecampaign_for_woocommerce_pause_historical_sync',
            'type': 2,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        $('#activecampaign-historical-sync-stop-requested').show();
        disableStopButtons();
    });
    $('#activecampaign-reset-historical-sync').click(function (e) {
        runAjax({
            'action': 'activecampaign_for_woocommerce_reset_historical_sync',
            'type': 2,
            'activecampaign_for_woocommerce_settings_nonce_field': $('#activecampaign_for_woocommerce_settings_nonce_field').val()
        });
        setTimeout(function(){
            enableStartSection();
            hideRunSection();
            getLastSyncData();
        }, 500);
    });

    updateStatus();
    // Check sync status
    var statInt = setInterval(updateStatus, 2000);

    function startUpdateCheck() {
        statInt = setInterval(updateStatus, 2000);
    }

    function cancelUpdateCheck() {
        clearInterval(statInt);
    }

    function runAjax(data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data
            }).done(response => {
                resolve(response.data);
            }).fail(response => {
                reject(response.responseJSON.data)
            });
        });
    }

    function showRunSection() {
        $('#sync-run-section').show();
        $('#activecampaign-sync-running-header').show();
        $('#activecampaign-last-sync-header').hide();
        $('#activecampaign-last-sync-status').hide();
    }

    function hideRunSection() {
        $('#sync-run-section').hide();
        $('#activecampaign-sync-running-header').hide();
        $('#activecampaign-last-sync-header').show();
        $('#activecampaign-last-sync-status').show();
    }

    function disableStopButtons(){
        $('#activecampaign-cancel-historical-sync').addClass('disabled');
        $('#activecampaign-pause-historical-sync').addClass('disabled');
    }

    function enableStopButtons(){
        $('#activecampaign-cancel-historical-sync').removeClass('disabled');
        $('#activecampaign-pause-historical-sync').removeClass('disabled');
    }

    function enableStartSection() {
        $('#activecampaign-run-historical-sync').removeClass('disabled');
        $('#activecampaign-run-historical-sync-status').hide();
    }

    function disableStartSection() {
        $('#activecampaign-run-historical-sync').addClass('disabled');
    }

    function getLastSyncData() {
        var data = {
            'action': 'activecampaign_for_woocommerce_fetch_last_historical_sync'
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data
        }).done(response => {
            if (response && response.data.html){
                $('#activecampaign-last-sync-status').html(response.data.html);
                $('#activecampaign-historical-sync-stop-requested').hide();
            }
        }).fail(response => {
            $('#activecampaign-run-historical-sync-status').html(response.responseJSON.data);
            cancelUpdateCheck();
        });
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
            if(response.data === 1) {
                $('#activecampaign-historical-sync-run-shortly').show();
                // scheduled = true;
            } else {
                $('#activecampaign-historical-sync-run-shortly').hide();
                cancelUpdateCheck();
                var last_sync = response.data.last_sync;
                var run_status = response.data.status;

                if (!run_status && !last_sync){
                    enableStartSection();
                    getLastSyncData();
                    hideRunSection();
                }else if (!run_status && last_sync) {
                    getLastSyncData();
                    enableStartSection();
                    hideRunSection();
                } else if(run_status.is_paused && !scheduled){
                    scheduled = false;
                    cancelUpdateCheck();
                    enableStartSection();
                    showRunSection();
                    disableStopButtons();
                    $('#activecampaign-run-historical-sync-running-status').html('Paused on latest record:' + run_status.last_processed_id ).show();
                    $('#activecampaign-historical-sync-starting-record').val(run_status.last_processed_id - 1);
                    $('#activecampaign-run-historical-sync-current-record-num').html(run_status.last_processed_id);
                }else if (run_status){
                    startUpdateCheck();
                    showRunSection();
                    disableStartSection();
                    enableStopButtons();
                    $('#activecampaign-last-sync-status').html('');
                    $('#activecampaign-run-historical-sync-current-record-num').html(run_status.last_processed_id);
                    if(run_status.last_processed_id !== lastRec){
                        lastRec = run_status.last_processed_id;
                        stickCount = 0;
                    }else{
                        stickCount ++;
                    }

                    if(stickCount < 20) {
                        $('#activecampaign-run-historical-sync-running-status').html('Running...');
                    }

                    if(stickCount >= 20 && stickCount < 40) {
                        $('#activecampaign-run-historical-sync-running-status').html('The historical sync might be stuck or has exited improperly... please check your logs.');
                    }

                    if(stickCount >= 40) {
                        $('#activecampaign-run-historical-sync-running-status').html('The historical sync process is no longer updating. It may be required to reset the sync status.');
                        cancelUpdateCheck();
                    }
                }
            }
        }).fail(response => {
            $('#activecampaign-run-historical-sync-status').html(response.responseJSON.data);
            cancelUpdateCheck();
        });
    }
});
