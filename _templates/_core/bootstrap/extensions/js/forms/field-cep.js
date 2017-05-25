//JQUERY
jQuery(function() {

  // CEP
  var field_cep 		= ".field-cep input, input.field-cep";
  
  window.setCEP = function (input, autotab) {
    input = setElement(input, field_cep);
    input.each(function() {
      var obj = jQuery(this);
      var width = isSet(obj.data('width')) ? obj.data('width') : false;
      // autotab param
      var tab = isSet(autotab) ? autotab : true;
      tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

      if(width) obj.css('width', width);
      obj.css({'min-width':'7.2em', 'max-width':'100%'});
      obj.inputmask("99999-999", { oncomplete: function(){ if(tab) obj.autoTab(); } });
    });
  };

});
