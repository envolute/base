//JQUERY
jQuery(function() {

	// CHECK OPTION
	// Seleciona a opção (radio) de acordo com o valor informado
	window.checkOption = function(field, value) {
		var input = setElement(field);
		// clear current value
		input.each(function() { jQuery(this).prop('checked', false); });
		if(isSet(value)) {
			// seleciona o item com o valor informado
			if(input.filter('[value="'+value+'"]').length)
			input.filter('[value="'+value+'"]').prop('checked', true).trigger('change');
			else if(input.is(':checkbox')) input.trigger('change');
			// Se for um botão, seta o estado 'ativo'
			btnCheckState(input);
		} else {
			console.log('checkOption: "value" param is not set');
		}
	};

});
