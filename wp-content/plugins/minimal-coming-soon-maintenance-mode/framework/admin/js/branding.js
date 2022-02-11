/*
 * CSMM
 * Backend GUI rebranding
 * (c) Web factory Ltd, 2016 - 2020
 */

jQuery(document).ready(function($){
  if (typeof csmm_rebranding  == 'undefined') {
    return;
  }

  if($('[data-slug="minimal-coming-soon-maintenance-mode"]').length > 0){
      $('[data-slug="minimal-coming-soon-maintenance-mode"]').children('.plugin-title').children('strong').html('<strong>' + csmm_rebranding.name + '</strong>');
  }

});
