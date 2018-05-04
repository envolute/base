//JQUERY
jQuery(function() {

	// GET FOCUS
	// Recebe o foco no campo selecionado
	window.inputGetFocus = function (input) {
    var field 	= ".get-focus input, input.get-focus";
		input = setElement(input, field);
		if(elementExist(input)) {
			setTimeout(function() {
				if(input.is('select') && input.next('.chosen-container').length) {
					input.next('.chosen-container').find('input').filter(':first').focus();
				} else {
					input.focus();
				}
		  }, 500);
		  // 500: é um tempo médio de execução de outras funcionalidades...
		  // Isso faz com que a maioria dos elementos já estejam disponíveis/visíveis
		  // após funções de alternância como 'toggle', 'modal' ou 'collapse'
		}
	};

});
