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
            var syncContacts = 0;

            if($('#activecampaign-historical-sync-starting-record').val() > 0) {
                startRec = $('#activecampaign-historical-sync-starting-record').val();
            }

            if($('#activecampaign-historical-sync-limit').find(":selected").text()) {
                batchLimit = $('#activecampaign-historical-sync-limit').find(":selected").text();
            }

            if($('#activecampaign-historical-sync-contacts:checked').val() == 1) {
                syncContacts = 1;
            }

            var action = 'activecampaign_for_woocommerce_schedule_bulk_historical_sync';

            runAjax({
                'action': action,
                'startRec': startRec,
                'batchLimit': batchLimit,
                'syncContacts': syncContacts,
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

    function startUpdateCheck( degrade = 0 ) {
        var intv = 2000;

        if(degrade > 0){
            intv = degrade + intv;
        }

        if(statInt) {
            cancelUpdateCheck();
        }

        statInt = setInterval(updateStatus, intv);
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
                startUpdateCheck(4000);
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
            $('#activecampaign-run-historical-sync-status').html('There was an error retrieving data from ' + ajaxurl);
            startUpdateCheck(4000);
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
            var run_status = false;
            var status_name = false;

            if(response.data.status){
                run_status = response.data.status;
                if (response.data.status.status_name) {
                    status_name = response.data.status.status_name;
                }
            }

            if( status_name === 'waiting') {
                $('#activecampaign-historical-sync-run-shortly').show();
                $('#activecampaign-historical-sync-run-contacts').hide();
            }else if( 'contacts' === status_name){
                $('#activecampaign-historical-sync-run-shortly').hide();
                $('#activecampaign-historical-sync-run-contacts').show();
                if(run_status.contact_count >= 0) {
                    $('#activecampaign-historical-sync-contacts-count').html(run_status.contact_count + '/' + run_status.contact_total);
                }
            } else if ( status_name === 'orders') {
                $('#activecampaign-historical-sync-run-shortly').hide();
                $('#activecampaign-historical-sync-run-contacts').hide();
                var last_sync = response.data.last_sync;
                run_status = response.data.status;

                startUpdateCheck();
                showRunSection();
                disableStartSection();
                enableStopButtons();
                if (typeof run_status.contact_count !== 'undefined' && run_status.contact_count > 0) {
                    $('#activecampaign-run-historical-sync-contact-block').show();
                    $('#activecampaign-run-historical-sync-contact-record-num').html(run_status.contact_count);
                } else {
                    $('#activecampaign-run-historical-sync-contact-block').hide();
                }

                $('#activecampaign-last-sync-status').html('');
                $('#activecampaign-run-historical-sync-current-record').show();
                $('#activecampaign-run-historical-sync-prep-record').hide();
                $('#activecampaign-run-historical-sync-current-record-num').html(run_status.last_processed_id);
                $('#activecampaign-run-historical-sync-current-sync-count').html(run_status.sync_count);
                $('#activecampaign-run-historical-sync-current-record-status').html(JSON.stringify(response.data));
                $('#activecampaign-run-historical-sync-total-record-num').html(run_status.total_orders);
                $('#activecampaign-run-historical-sync-last-update').html(run_status.last_update);

                if (run_status.last_processed_id !== lastRec) {
                    lastRec = run_status.last_processed_id;
                    stickCount = 0;
                } else {
                    stickCount++;
                }

                if (stickCount < 40) {
                    $('#activecampaign-run-historical-sync-running-status').html('Running...');
                }

                if (stickCount >= 40 && stickCount < 60) {
                    $('#activecampaign-run-historical-sync-running-status').html('The historical sync might be stuck or has exited improperly... please check your logs.');
                }

                if (stickCount >= 60) {
                    var timeStuck = Math.round((stickCount * 2) / 60);
                    $('#activecampaign-run-historical-sync-running-status').html('The historical sync process has not updated in ' + timeStuck + ' minutes. It may be required to reset the sync status.');
                }

            } else if ( status_name === 'finished') {
                getLastSyncData();
                enableStartSection();
                hideRunSection();
                startUpdateCheck(8000);
            }else{
                getLastSyncData();
                enableStartSection();
                hideRunSection();
                startUpdateCheck(8000);
            }
        }).fail(response => {
            startUpdateCheck(4000);
            $('#activecampaign-run-historical-sync-status').html('Response failed from ' + ajaxurl);

        });
    }
});
