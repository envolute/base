// FIELD CONTEXTS
// Set bootstrap validation options with icons to text fields

jQuery(function() {

	window.setFieldSuccess = function(input) {
		input = validaField(input);
		var parent = input.parents('.form-group');
		if(parent.length) {
			parent.removeClass('has-warning has-error').addClass('has-feedback has-success');
			parent.find('label').addClass('control-label');
			parent.find('.form-control-feedback').remove();
			input.after('<span class="base-icon-ok form-control-feedback" aria-hidden="true"></span>');
		}
	};
	window.setFieldWarning = function(input) {
		input = validaField(input);
		var parent = input.parents('.form-group');
		if(parent.length) {
			parent.removeClass('has-success has-error').addClass('has-feedback has-warning');
			parent.find('label').addClass('control-label');
			parent.find('.form-control-feedback').remove();
			input.after('<span class="base-icon-attention form-control-feedback" aria-hidden="true"></span>');
		}
	};
	window.setFieldError = function(input) {
		input = validaField(input);
		var parent = input.parents('.form-group');
		if(parent.length) {
			parent.removeClass('has-success has-warning').addClass('has-feedback has-error');
			parent.find('label').addClass('control-label');
			parent.find('.form-control-feedback').remove();
			input.after('<span class="base-icon-cancel form-control-feedback" aria-hidden="true"></span>');
		}
	};
  window.setFieldContext = function() {
		jQuery('.form-group.has-feedback').each(function() {
      var obj = jQuery(this);
			var field = jQuery(this).find('input');
      if(obj.hasClass('has-success'))
        setFieldSuccess(field);
      else if(obj.hasClass('has-warning'))
        setFieldWarning(field);
      else if(obj.hasClass('has-error'))
        setFieldError(field);
    });
  };
  window.unsetFieldContext = function(input) {
    input = validaField(input);
    var parent = input.parent('.form-group');
    if(parent.length) {
      parent.removeClass('has-success has-warning has-error');
			parent.find('label').addClass('control-label');
      parent.find('.form-control-feedback').remove();
    }
  };

  // init states
  setFieldContext();

});
