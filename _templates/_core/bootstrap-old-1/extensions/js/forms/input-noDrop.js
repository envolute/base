//JQUERY
jQuery(function() {

  // NO DROP
  // Desabilita a funcionalidade de arrastar um valor para o campo
	window.inputNoDrop = function (input) {
    var field	= ".no-drop input, input.no-drop";
		input = setElement(input, field);
		input.on('drop', function (e) { e.preventDefault(); });
	};

});
