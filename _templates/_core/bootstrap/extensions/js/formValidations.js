// CUSTOM VALIDATION
jQuery(function() {

	// CUSTOM ERROR REPLACEMENTS
	window.baseErrorReplace = function(error, element) {
			// 'Group & Chosen' fix validation message
			var group		= element.closest('.input-group, .btn-group, .btn-file, .form-check, .form-inline');
			var chzn		= element.next('.chosen-container'); // chosen
			if(group.length) error.insertAfter(group);
			else if(chzn.length) error.insertAfter(chzn);
			else error.insertAfter(element);
	};

	// CUSTOM ERROR REPLACEMENTS
	window.baseErrorCustomFormat = function(element) {
			var group		= element.closest('.input-group, .btn-group, .btn-file, .form-inline');
			if(group.length) group.addClass('has-error');
	};

	// CUSTOM ERROR CLEAR
	window.baseErrorCustomClear = function(element) {
			var group		= element.closest('.input-group.has-error, .btn-group.has-error, .btn-file.has-error, .form-inline.has-error');
			if(group.length) group.removeClass('has-error');
	};

	window.setFormValidations = function () {

		// SETA CLASSE 'NONAME' PARA CAMPO SEM O ATRIBUTO 'NAME'
		jQuery('input').each(function() {
			var name = jQuery(this).attr('name');
			if(!isSet(name) || isEmpty(name)) jQuery(this).addClass('noname');
		});

		// SET DEFAULT VALIDATION CONFIGURATIONS
		if(jQuery('form').length && jQuery.validator) {

			jQuery.validator.setDefaults({
				// debug: true,
				errorElement: "span",
				success: "valid",
				errorPlacement: function(error, element) {
					// formata as mensagens de erro de acordo com o tipo do campo
					// Ex: bootstrap -> (btn-group, ckeckbox), chosen -> (select)
					baseErrorReplace(error, element);
				},
		    	highlight: function(element, errorClass, validClass) {
					jQuery(element).addClass(errorClass);
		      		baseErrorCustomFormat(jQuery(element));
		    	},
		    	unhighlight: function(element, errorClass, validClass) {
					jQuery(element).removeClass(errorClass);
			      	baseErrorCustomClear(jQuery(element));
			    },
				// IGNORE -> Campo ignorados pela validação
				// .no-validate:
				// -> Classe opcional para desabilitar a validação em um campo
				// .noname:
				// -> Campos inicialmente sem nome declarado
				// :hidden:not(input:file):not(select):
				// -> Não ignora os campo do tipo 'file' e 'select' ocultos.
				// -> O campo 'file' por causa da implementação do 'btn-fileAction',
				// -> e o 'select' por causa do chosen.
				// :hidden :hidden:
				// -> Desabilita a validação de elementos contidos em um elemento oculto.
				// -> Ex: Tabs, Collapse, Divs (não visíveis)...
				// .chosen-container input:
				// -> Elementos internos do chosen.
				ignore: ".no-validate, .noname, :hidden:not(input:file):not(.has-chosen), :hidden :hidden, .chosen-container input"
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
				min: jQuery.validator.format("Informe um valor maior igual &agrave; {0}."),
				price: "Campo Obrigat&oacute;rio"
			});
		}

	};
	// END FORM DEFINITIONS ------------------------------------------------------------

	// CHAMADA GERAL DAS PREDEFINIÇÕES DE VALIDAÇÃO DE FORMULÁRIO
	// ---------------------------------------------------------------------------------
	setFormValidations();
	// ---------------------------------------------------------------------------------

});

// DEFAULT VALIDATIONS FOR ALL FORMS
jQuery(window).load(function() {

	window.setDefaultValidationFields = function() {

		// custom validation for not-equal
		jQuery.validator.addMethod("notEqual", function(value, element, param) {
			return this.optional(element) || value != param;
		}, 'Campos com valores iguais');
		// custom validation for required NOT ACCEPT 'zero'
		jQuery.validator.addMethod("requiredId", function(value, element) {
			return this.optional(element) || value != 0;
		}, 'Campo Obrigat&oacute;rio');
		// custom validation for phone number
		jQuery.validator.addMethod("phoneNumber", function(value, element) {
			var isMask = value.indexOf('(') === -1 ? false : true;
			var number = value.replace(/[^0-9]/g,'').length >= (isMask ? 10 : 8);
			return this.optional(element) || number;
		}, 'N&uacute;mero inv&aacute;lido');
		// custom validation for CPF
		jQuery.validator.addMethod("cpf", function(value, element) {
			var isValid = (value.replace(/\D/g, "").length == 11 && isCpfCnpj(value)) ? true : false;
			return this.optional(element) || isValid;
		}, 'CPF inv&aacute;lido');
		// custom validation for CNPJ
		jQuery.validator.addMethod("cnpj", function(value, element) {
			var isValid = (value.replace(/\D/g, "").length == 14 && isCpfCnpj(value)) ? true : false;
			return this.optional(element) || isValid;
		}, 'CNPJ inv&aacute;lido');
		// custom validation for regex expression
		jQuery.validator.addMethod("regex", function(value, element, regexp) {
			// Obs: 'regexp' deve ser declarada sem aspas;
			var re = new RegExp(regexp);
			return this.optional(element) || re.test(value);
		}, 'O valor informado n&atilde;o &eacute; v&aacute;lido');

		if(jQuery('form').length && jQuery.validator) {
			// REQUIRED
			if(jQuery('input.input-required').length || jQuery('select.input-required').length || jQuery('textarea.input-required').length) {
				jQuery('input:not(:file).input-required, select.input-required, textarea.input-required').each(function() {
					jQuery(this).rules('add', { required: true });
				});
			}
			// REQUIRED ID -> NOT ACCEPT 'ZERO' VALUE
			if(jQuery('select.id-required').length) {
				jQuery('select.id-required').each(function() {
					jQuery(this).rules('add', { requiredId: true });
				});
			}
			// E-MAIL
			if(jQuery('input.field-email').length) {
				jQuery('input.field-email').each(function() {
					jQuery(this).rules('add', { email: true });
				});
			}
			// USERNAME
			if(jQuery('input.field-username').length) {
				jQuery('input.field-username').each(function() {
					jQuery(this).rules('add', {
						regex: /^[A-Z0-9._@-]*$/i,
						messages: {
							regex: 'Nome de Usu&aacute;rio inv&aacute;lido!'
						}
					});
				});
			}
			// CPF
			if(jQuery('input.field-cpf').length) {
				jQuery('input.field-cpf').each(function() {
					jQuery(this).rules('add', { cpf: true });
				});
			}
			// CNPJ
			if(jQuery('input.field-cnpj').length) {
				jQuery('input.field-cnpj').each(function() {
					jQuery(this).rules('add', { cnpj: true });
				});
			}
			// URL
			if(jQuery('input.field-url').length) {
				jQuery('input.field-url').each(function() {
					jQuery(this).rules('add', { url: true });
				});
			}
			// PHONE NUMBER
			if(jQuery('input.field-phone').length) {
				jQuery('input.field-phone').each(function() {
					jQuery(this).rules('add', { phoneNumber: true });
				});
			}
		}
	};
	// CHAMADA DAS REGRAS DE VALIDACAO DEFAULT
	setTimeout(function() {
		setDefaultValidationFields();
	}, 200);

});
