//JQUERY
jQuery(function() {

  // NO NUMBER
  // Apenas caracteres 'letras' com ou sem acento
	window.inputNoNumber = function (input) {
    var field	= ".no-number input, input.no-number";
		input = setElement(input, field);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[0-9]/g, ""));
		});
	};

});
