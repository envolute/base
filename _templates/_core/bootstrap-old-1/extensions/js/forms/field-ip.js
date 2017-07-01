//JQUERY
jQuery(function() {

  // CEP
  var field_ip = ".field-ip input, input.field-ip";

  window.setIP = function (input, autotab) {
    input = setElement(input, field_ip);
    input.each(function() {
      var obj = jQuery(this);
      var width = isSet(obj.data('width')) ? obj.data('width') : false;
      if(width) obj.css('width', width);
      obj.css({'min-width':'10em', 'max-width':'100%'});
      obj.inputmask({ "alias": "ip" });
    });
  };

});
