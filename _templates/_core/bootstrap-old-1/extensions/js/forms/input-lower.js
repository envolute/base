//JQUERY
jQuery(function() {

  // LOWER CASE
  // atribui/força 'lower case' ao campo durante a digitação
  window.inputLowercase = function(input) {
		var field 	= ".lower input, input.lower";
		input = setElement(input, field);
		// quando vier preenchido
		input.each(function(){
			var obj = jQuery(this);
			obj.val(obj.val().toLowerCase());
			// quando for digitado no campo
			obj.on("focus blur change", function(e) {
				//Transforma tudo em maiusculo
				jQuery(this).val(jQuery(this).val().toLowerCase());
			});
		});
	};

});
