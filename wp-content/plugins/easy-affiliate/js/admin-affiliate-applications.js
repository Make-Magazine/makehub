// Easy Affiliate Affiliate
jQuery(document).ready(function($) {

  $('table.wp-list-table tr.type-esaf-application').on('mouseenter',function(e) {
    $(this).find('.esaf-cpt-row-actions').show();
  });

  $('table.wp-list-table tr.type-esaf-application').on('mouseleave',function(e) {
    $(this).find('.esaf-cpt-row-actions').hide();
  });

  $('#publishing-action input#publish').val(EsafAffiliateApplications.submit_button_text);

  var esaf_update_row_action_visibility = function(curr_status, app_id) {
    var $row_actions = $('#esaf-cpt-row-actions-' + app_id);

    $row_actions.data('status', curr_status);

    $row_actions.find('span.esaf-aff-app-approve').hide();
    $row_actions.find('span.esaf-aff-app-ignore').hide();
    $row_actions.find('span.esaf-aff-app-resend-approved-email').hide();

    if(curr_status === 'pending') {
      $row_actions.find('span.esaf-aff-app-approve').show();
      $row_actions.find('span.esaf-aff-app-ignore').show();
    }
    else if(curr_status === 'ignored') {
      $row_actions.find('span.esaf-aff-app-approve').show();
    }
    else if(curr_status === 'approved' && $row_actions.data('affiliate') === 0) {
      $row_actions.find('span.esaf-aff-app-resend-approved-email').show();
    }
  };

  var esaf_update_all_row_action_visibility = function() {

    $('.esaf-cpt-row-actions').each( function() {
      var curr_status = $(this).data('status');
      var app_id = $(this).data('id');

      esaf_update_row_action_visibility(curr_status, app_id);
    });

  };

  // Update row action visibility on all the rows in the list
  esaf_update_all_row_action_visibility();

  var esaf_update_affiliate_application_status = function(app_id, new_status) {
    var data = {
      'action': 'esaf-update-affiliate-application-status',
      'security': EsafAffiliateApplications.security,
      'id': app_id,
      'status': new_status
    };

    $.post(ajaxurl, data, function(res) {
      if(res==-1) {
        return alert('Unauthorized');
      }

      if(typeof res.error !== 'undefined') {
        return alert(res.error);
      }

      esaf_update_row_action_visibility(new_status, app_id);
      esaf_update_status(new_status, app_id);

      alert(res.message);
    }, 'json');
  };

  var esaf_update_status = function(new_status, app_id) {
    $('#esaf-app-status-'+app_id).removeClass('esaf-red esaf-green esaf-blue');

    if(new_status=='pending') {
      $('#esaf-app-status-'+app_id).text(EsafAffiliateApplications.pending_text);
      $('#esaf-app-status-'+app_id).addClass('esaf-red');
    }
    else if(new_status=='approved') {
      $('#esaf-app-status-'+app_id).text(EsafAffiliateApplications.approved_text);
      $('#esaf-app-status-'+app_id).addClass('esaf-green');
    }
    else if(new_status=='ignored') {
      $('#esaf-app-status-'+app_id).text(EsafAffiliateApplications.ignored_text);
      $('#esaf-app-status-'+app_id).addClass('esaf-blue');
    }
  };

  $('span.esaf-aff-app-approve a').on('click', function(e) {
    e.preventDefault();
    var app_id = $(this).data('id');
    esaf_update_affiliate_application_status(app_id, 'approved');
  });

  $('span.esaf-aff-app-ignore a').on('click', function(e) {
    e.preventDefault();
    var app_id = $(this).data('id');
    esaf_update_affiliate_application_status(app_id, 'ignored');
  });

  $('span.esaf-aff-app-resend-approved-email a').on('click', function(e) {
    e.preventDefault();

    if(!confirm(EsafAffiliateApplications.confirm_resend_approved_email)) {
      return;
    }

    $.ajax({
      url: EsafAffiliateApplications.ajax_url,
      method: 'POST',
      data: {
        action: 'esaf_resend_affiliate_application_approved_email',
        id: $(this).data('id'),
        _ajax_nonce: EsafAffiliateApplications.security
      }
    }).done(function (response) {
      if(response && typeof response.success == 'boolean') {
        alert(response.data);
      }
      else {
        alert('sorry  todo');
      }
    }).fail(function () {
      alert('sorry  todo');
    });
  });

});
