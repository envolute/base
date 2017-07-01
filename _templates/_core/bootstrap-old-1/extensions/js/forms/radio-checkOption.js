//JQUERY
jQuery(function() {

  // CHECK OPTION
  // Seleciona a opção (radio) de acordo com o valor informado
  window.checkOption = function(field, value) {
    var input = setElement(field);
    if(isSet(value)) {
      // clear current value
      input.each(function() { jQuery(this).prop('checked', false); });
      // seleciona o item com o valor informado
      if(input.filter('[value="'+value+'"]').length)
      input.filter('[value="'+value+'"]').prop('checked', true).trigger('change');
      // Se for um botão, seta o estado 'ativo'
      btnCheckState();
    } else {
      console.log('checkOption: "value" param is not set');
    }
  };

});
