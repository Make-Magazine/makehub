jQuery(function ($) {
  $('.esaf-signup-form, .esaf-affiliate-application-form').each(function () {
    var $form = $(this),
      $fields = $form.find('input:not([type="checkbox"], [type="radio"]), select, textarea');

    $fields.on('blur', function () {
      esafValidateInput($(this));
    });

    $fields.on('focus', function () {
      $(this).removeClass('esaf-invalid').closest('.esaf-form-row').find('.esaf-validation-error').hide();
    });

    $form.find('.esaf-submit').on('click', function (e) {
      // We want to control if/when the form is submitted
      e.preventDefault();

      $fields.each(function(i, field) {
        esafValidateInput($(field));
      });

      if($form.find('.esaf-invalid').length === 0) {
        this.disabled = true;
        $form.find('.esaf-loading-gif').show();
        $form.trigger('esaf-signup-submit');
        $form.submit();
      }
    });
  });

  $('.esaf-signup-form').each(function () {
    var $form = $(this),
      $country = $form.find('select[name="wafp_user_country"]');

    if($country.length) {
      var $tax_id_us = $form.find('.esaf_tax_id_us').hide(),
        $tax_id_int = $form.find('.esaf_tax_id_int').hide();

      $country.on('change', function () {
        $tax_id_us[$country.val() === 'US' ? 'show' : 'hide']();
        $tax_id_int[$country.val() === 'US' ? 'hide' : 'show']();
      }).triggerHandler('change');
    }
  });
});
