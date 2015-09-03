// CUSTOM VALIDATION
jQuery(function() {
		
	// SET DEFAULT VALIDATION CONFIGURATIONS
		
	if(jQuery.validator) {
		jQuery.validator.setDefaults({
			debug: true,
			errorElement: "span",
			success: "valid",
			ignore: ":hidden:not(select)",
			ignore: ".chosen-choices input, .chosen-search input",
			ignore: ".chzn-choices input, .chzn-search input" // Joomla Chosen
		});
		jQuery.extend(jQuery.validator.messages, {
			required: "Campo Obrigat&oacute;rio",
			remote: "Por favor, verifique esse campo",
			email: "Informe uma e-mail v&aacute;lido",
			url: "Informe uma URL v&aacute;lida",
			date: "Informe uma data v&aacute;lida",
			dateISO: "Informe uma data v&aacute;lida (ISO).",
			number: "Informe um n&uacute;mero v&aacute;lido",
			digits: "Informe apenas digitos",
			creditcard: "Informe um n&uacute;mero de cart&atilde;o de cr&eacute;dito v&aacute;lido",
			equalTo: "Campos com valores diferentes",
			accept: "Extens&atilde;o Inv&aacute;lida!",
			maxlength: jQuery.validator.format("Informe no m&aacute;ximo {0} caracteres."),
			minlength: jQuery.validator.format("Informe ao menos {0} caracteres."),
			rangelength: jQuery.validator.format("Informe um valor de {0} &agrave; {1} caracteres."),
			range: jQuery.validator.format("Informe um valor entre {0} e {1}."),
			max: jQuery.validator.format("Informe um valor menor igual &agrave; {0}."),
			min: jQuery.validator.format("Informe um valor maior igual &agrave; {0}.")
		});
	}
	
	// DEFAULT SCRIPTS FOR ALL FORMS
	window.baseValidation = function(form) {
		// setTimeout ensures that the function will run only after the validation occurs
		setTimeout(function() {
			
			// btn-group->radio fix validation message
			var rr = form.find('.btn-group label input:radio');
			if(rr.length && rr.hasClass('required error')) rr.parents('.btn-group').addClass('has-error');
			
			// checkbox fix validation message
			var rr = form.find('label input:checkbox + .error');
			if(rr.length) rr.addClass('pull-right left-expand');
			
			// chosen fix validation message
			var rr = form.find('.error + .chosen-container, .error + .chzn-container');
			if(rr.length) {
				rr.each(function() {
					jQuery(this).prev('.error').insertAfter(jQuery(this));
				});
			}
			
		}, 100);
	};
});

// DEFAULT VALIDATIONS FOR ALL FORMS
jQuery(window).load(function() {
	// REQUIRED
	if(jQuery('input.required').length) {
		jQuery('input.required, select.required, textarea.required').each(function() {
			jQuery(this).rules('add', { required: true });
		});
	}
	// E-MAIL
	if(jQuery('input.field-email').length) {
		jQuery('input.field-email').each(function() {
			jQuery(this).rules('add', { email: true });
		});
	}
	// URL
	if(jQuery('input.field-url').length) {
		jQuery('input.field-url').each(function() {
			jQuery(this).rules('add', { url: true });
		});
	}
});