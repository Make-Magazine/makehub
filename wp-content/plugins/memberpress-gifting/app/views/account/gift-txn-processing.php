<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<script>
  var mpgft_processing_txn = '<?php echo esc_html(__('Processing your transaction..','memberpress-gifting')); ?>';
  var mpgft_unable_processing_txn = '<?php echo esc_html(__('Unable to process your transaction.','memberpress-gifting')); ?>';
  var mpgft_redirecting_txn = '<?php echo esc_html(__('Redirecting..','memberpress-gifting')); ?>';

  if(jQuery.magnificPopup) {

    //var $cta_button = jQuery('<div class="mepr-btn mepr-btn-secondary">').text(cta_btn_title).on('click', function () {
      //jQuery.magnificPopup.close();
    //});

    var $mpgft_txn_popup_content = jQuery('<div class="mpgft-txn-popup">').append(
      jQuery('<img id="mpgft-txn-loader" class="mpgft-txn-popup-icon">').attr('src', '<?php echo MEPR_IMAGES_URL . '/square-loader.gif'; ?>'),
      jQuery('<p id="mpgft-txn-processing">').text(mpgft_processing_txn),
      jQuery('<p id="mpgft-unable-txn" style="display:none;">').text(mpgft_unable_processing_txn),
      jQuery('<p id="mpgft-redirecting-txn" style="display:none;">').text(mpgft_redirecting_txn)
    );

    jQuery.magnificPopup.open({
      mainClass: 'mpgft-txn-mfp',
      items: {
        src: $mpgft_txn_popup_content,
        type: 'inline'
      }
    });

    var mpgft_run_txn_count = 0;
    var mpgft_response_success = false;

    function mpgft_txn_ajax_request(){

      // We already know txn status, just redirect.
      if( mpgft_response_success && mpgft_run_txn_count == 3 ) {
        jQuery('#mpgft-txn-processing').hide();
        jQuery('#mpgft-txn-loader').show();
        jQuery('#mpgft-redirecting-txn').show();
        jQuery('#mpgft-unable-txn').hide();
        setTimeout(function(){
          window.location = '<?php echo esc_url_raw($redirect_to); ?>';
        }, 1000);
        return;
      }

      // Saving uncessary AJAX call.
      if( ! mpgft_response_success ) {
        var data = {
          action: 'mpgft_txn_status',
          txn_id: '<?php echo (int)$txn->id; ?>',
          security: '<?php echo wp_create_nonce('mpgft_txn_status'); ?>'
        }

        jQuery.post(MeprI18n.ajaxurl, data, function(response) {

          if( response.success ) {
            mpgft_response_success = true;
          }

          if( mpgft_run_txn_count == 3 ) {
            if( ! response.success ) {
              jQuery('#mpgft-unable-txn').show();
              jQuery('#mpgft-txn-processing').hide();
              jQuery('#mpgft-txn-loader').hide();
              return;
            }

            if( response.success ) {
              jQuery('#mpgft-txn-processing').hide();
              jQuery('#mpgft-txn-loader').show();
              jQuery('#mpgft-redirecting-txn').show();
              jQuery('#mpgft-unable-txn').hide();
              setTimeout(function(){
                window.location = '<?php echo esc_url_raw($redirect_to); ?>';
              }, 1000);
            }
          }
        });
      }


    }

    function mpgft_txn_status_checker() {
        mpgft_run_txn_count++;
        if(mpgft_run_txn_count >= 3) {
          clearInterval(mpgft_run_txn_tid);
        }

        mpgft_txn_ajax_request(mpgft_run_txn_count);
    }

    var mpgft_run_txn_tid = setInterval(mpgft_txn_status_checker, 5000);
  }
</script>