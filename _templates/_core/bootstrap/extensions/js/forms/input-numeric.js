//JQUERY
jQuery(function() {

  // INTEGER - APENAS NÚMEROS INTEIROS
	window.inputInteger = function (input) {
    var field	= ".integer input, input.integer";
		input = setElement(input, field);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[^\d]/g, ""));
			if(e.which > 65) e.preventDefault();
		});
	};

	// FLOAT - APENAS NÚMEROS COM PONTO FLUTUANTE
	window.inputFloat = function (input) {
    var field = ".float input, input.float";
		input = setElement(input, field);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g,''));
			if(e.which > 65) e.preventDefault();
		});
	};

	// APENAS NÚMEROS
	window.inputNumeric = function (input, setType) {
    var field	= ".numeric input, input.numeric, .number input, input.number";
		input = setElement(input, field);
		// chama as funcoes numericas
		inputInteger();
		inputFloat();
    input.each(function() {
      var obj = jQuery(this);
      // setType
      var type = isSet(setType) ? setType : false;
      type = isSet(obj.data('type')) ? obj.data('type') : type;
  		switch (type) {
  			case 'float':
  				inputFloat(obj);
  				break;
  			default:
  				inputInteger(obj);
  		}
    });
	};

});
