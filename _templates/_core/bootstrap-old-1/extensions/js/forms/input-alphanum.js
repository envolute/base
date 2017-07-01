//JQUERY
jQuery(function() {

  // ALPHANUMERIC
	// Apenas Número e Caracteres, sem caracteres especiais
  var field_alphanum = ".alphanum input, input.alphanum";

	window.inputAlphanumeric = function (input) {
		input = setElement(input, field_alphanum);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚâêîôûÂÊÎÔÛãõÃÕàÀçÇ ]/g, ""));
		});
	};

});
