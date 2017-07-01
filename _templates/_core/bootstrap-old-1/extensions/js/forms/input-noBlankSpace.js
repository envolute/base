//JQUERY
jQuery(function() {

  // NO BLANK SPACE
  // Sem espaço em branco
	window.inputNoBlankSpace = function (input) {
    var field	= ".no-blankSpace input, input.no-blankSpace";
		input = setElement(input, field);
		input.on("keypress keyup blur",function (e) {
			if(e.which == 32) e.preventDefault();
		});
	};

});
