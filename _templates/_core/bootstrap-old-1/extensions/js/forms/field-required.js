//JQUERY
jQuery(function() {

  // REQUIRED FIELD
  // Atribui as classes que formatam os campo obrigatórios 'required'
  window.setFieldRequired = function() {
    var field = jQuery('.field-required input, .field-required select, .field-required textarea');
    field.each(function() {
      if(jQuery(this).hasClass('field-id')) {
        jQuery(this).addClass('id-required');
      } else {
        jQuery(this).addClass('input-required');
      }
    });
  };

  // RESET REQUIRED FIELD
  // Reseta os campos obrigatórios em um 'input-group'
  window.resetFieldRequired = function() {
    var field = jQuery('.input-group .input-required, .input-group .id-required');
    field.each(function() {
      jQuery(this).change(function() {
        jQuery(this).closest('.input-group').parent().children('.error').addClass('valid');
      });
    });
    // input file 'button'
    field = jQuery('.btn-file').find('input:file');
    field.each(function() {
      jQuery(this).change(function() {
        var obj = jQuery(this).closest('.btn-file.has-error');
        obj.removeClass('has-error');
        obj.next('.error').addClass('valid');
      });
    });
    // Checkbox/Radio 'buttons'
    fields = jQuery('.btn-group').find('input');
    fields.each(function() {
      jQuery(this).change(function() {
        var obj = jQuery(this).closest('.btn-group.has-error');
        obj.removeClass('has-error');
        obj.parent().children('.error').addClass('valid');
      });
    });
  };

});
