jQuery(document).ready(function($) {
  // TAGS
  if($('#mepractivecampaign_add_tags').is(":checked")) {
      $('div#mepractivecampaign_tags_area').show();
  } else {
      $('div#mepractivecampaign_tags_area').hide();
  }
  $('#mepractivecampaign_add_tags').click(function() {
      $('div#mepractivecampaign_tags_area').slideToggle();
  });
}); //End main document.ready func

