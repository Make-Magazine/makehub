/** Some basic methods to validate form elements */
var eaValidateEmail = function (email) {
  //In case the email is not entered yet and is not required
  if (!email || 0 === email.length) {
    return true;
  }

  var filter = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,25}$/i;

  return filter.test(email);
};

var eaValidateNotBlank = function (val) {
  return !!(val && val.length > 0);
};

var eaToggleFieldValidation = function ($field, valid) {
  $field.toggleClass('esaf-invalid', !valid);
  $field.toggleClass('esaf-valid', valid);
  $field.closest('.esaf-form-row').find('.esaf-validation-error').toggle(!valid);

  var $form = $field.closest('.esaf-form');

  if (0 < $form.find('.esaf-invalid').length) {
    $form.find('.esaf-form-has-errors').show();
  } else {
    $form.find('.esaf-form-has-errors').hide();
  }
};

var esafValidateInput = function ($field) {
  var $form = $field.closest('.esaf-form'),
    value = $field.val();

  if($field.attr('required') !== undefined) {
    var notBlank = true;

    if($field.is('input:not([type="checkbox"], [type="radio"]), select, textarea')) {
      notBlank = eaValidateNotBlank(value);
    }

    eaToggleFieldValidation($field, notBlank);
  }

  // Validate actual email only if it's not empty otherwise let the required/un-required logic hold
  if($field.attr('type') === 'email' && value && value.length > 0) {
    eaToggleFieldValidation($field, eaValidateEmail(value));
  }

  // Validate the URL by using the browser validation functions
  if($field.attr('type') === 'url' && value && value.length > 0) {
    eaToggleFieldValidation($field, $field.is(':valid'));
  }

  if($field.hasClass('esaf-password') && value && value.length > 0) {
    var $confirm_password = $form.find('.esaf-password-confirm');

    if($confirm_password.length) {
      var confirm_password_value = $confirm_password.val();

      if(confirm_password_value && confirm_password_value.length > 0) {
        eaToggleFieldValidation($confirm_password, value === confirm_password_value);
      }
    }
  }

  if($field.hasClass('esaf-password-confirm') && value && value.length > 0) {
    eaToggleFieldValidation($field, value === $form.find('.esaf-password').val());
  }

  $field.trigger('esaf-validate-input');
};
