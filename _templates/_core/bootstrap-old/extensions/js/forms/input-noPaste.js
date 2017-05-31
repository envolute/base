//JQUERY
jQuery(function() {

  // NO PASTE
  // Desabilita a funcionalidade de colar um valor para o campo
	window.inputNoPaste = function (input) {
    var field	= ".no-paste input, input.no-paste";
		input = setElement(input, field);
		input.on('paste', function (e) { e.preventDefault(); });
	};

});
