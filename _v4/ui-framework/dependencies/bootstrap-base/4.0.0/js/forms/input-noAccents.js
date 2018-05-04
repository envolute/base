//JQUERY
jQuery(function() {

  // NO ACCENTS
  // Sem acentuação
	window.inputNoAccents = function (input) {
    var field	= ".no-accents input, input.no-accents";
		input = setElement(input, field);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[áéíóúÁÉÍÓÚâêîôûÂÊÎÔÛãõÃÕàÀçÇ´`~^]/g, ""));
		});
	};

});
