//JQUERY
jQuery(function() {

  // UPPER CASE
  // atribui/força 'UPPER CASE' ao campo durante a digitação
	window.inputUppercase = function(input) {
    var field 	= ".upper input, input.upper";
		input = setElement(input, field);
		// quando vier preenchido
		input.each(function(){
			var obj = jQuery(this);
			obj.val(obj.val().toUpperCase());
			// quando for digitado no campo
			obj.on("focus blur change", function(e) {
				//Transforma tudo em maiusculo
				jQuery(this).val(jQuery(this).val().toUpperCase());
			});
		});
	};

});
