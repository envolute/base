//JQUERY
jQuery(function() {

	// CPF
	window.setCPF = function (input, autotab) {
		var field = ".field-cpf input, input.field-cpf";
		input = setElement(input, field);
		var error = 'CPF inv&aacute;lido';
		input.each(function() {
		var obj = jQuery(this);
		var width = isSet(obj.data('width')) ? obj.data('width') : false;
		if(width) obj.css('width', width);
		// autotab param
		var tab = isSet(autotab) ? autotab : true;
		tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

		obj.inputmask("999.999.999-99", {
			oncomplete: function(){
				// reseta mensagem de erro
				obj.next('.cpf-error').remove();
				if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
					// mostra mensagem de erro
					obj.addClass('error').after('<span class="cpf-error error">'+error+'</span>');
				} else {
					if(tab) obj.autoTab();
				}
			},
			onKeyDown: function(event, buffer, caretPos, opt){
				if(obj.val().replace(/[^0-9]/g, '').length < 11) {
					// remove a mensagem de erro enquanto digita
					obj.removeClass('error');
					obj.next('.cpf-error').remove();
				}
			}
			}).on('blur', function(event) {
				// reseta mensagem de erro
				obj.next('.cpf-error').remove();
				if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
					// mostra mensagem de erro
					obj.addClass('error').after('<span class="cpf-error error">'+error+'</span>');
					// limpa o campo para evitar o envio do valor errado
					// if(obj.val().replace(/\D/g, "").length == 11) obj.val('');
				} else {
					obj.removeClass('error');
				}
			});
		});
	};

	// CNPJ
	window.setCNPJ = function (input, autotab) {
		var field = ".field-cnpj input, input.field-cnpj";
		input = setElement(input, field);
		var error = 'CNPJ inv&aacute;lido!<br />Informe o CNPJ com apenas 14 dígitos.<br />Ignore, se houver, o dígito 0 (zero) inicial';
		input.each(function() {
			var obj = jQuery(this);
			var width = isSet(obj.data('width')) ? obj.data('width') : false;
			if(width) obj.css('width', width);
			// autotab param
			var tab = isSet(autotab) ? autotab : true;
			tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

			obj.inputmask("99.999.999/9999-99", {
				oncomplete: function(){
					// reseta mensagem de erro
					obj.next('.cpf-error').remove();
					if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
						// mostra mensagem de erro
						obj.addClass('error').after('<span class="cnpj-error error">'+error+'</span>');
					} else {
						if(tab) obj.autoTab();
					}
				},
				onKeyDown: function(event, buffer, caretPos, opt) {
					if(obj.val().replace(/[^0-9]/g, '').length < 14) {
						// remove a mensagem de erro enquanto digita
						obj.removeClass('error');
						obj.next('.cpf-error').hide();
					}
				}
			}).on('blur', function(event) {
				// reseta mensagem de erro
				obj.next('.cpf-error').remove();
				if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
					// mostra mensagem de erro
					obj.addClass('error').after('<span class="cnpj-error error">'+error+'</span>');
					// limpa o campo para evitar o envio do valor errado
					// if(obj.val().replace(/\D/g, "").length == 14) obj.val('');
				} else {
					obj.removeClass('error');
				}
			});
		});
	};

	// VALIDAÇÃO CPF/CNPJ
	// Valida o CPF
	window.isCpf = function (cpf) {
		var soma;
		var resto;
		var i;
		if ( (cpf.length != 11) ||
		(cpf == "00000000000") || (cpf == "11111111111") ||
		(cpf == "22222222222") || (cpf == "33333333333") ||
		(cpf == "44444444444") || (cpf == "55555555555") ||
		(cpf == "66666666666") || (cpf == "77777777777") ||
		(cpf == "88888888888") || (cpf == "99999999999") ) {
			return false;
		}
		soma = 0;
		for (i = 1; i <= 9; i++) {
			soma += Math.floor(cpf.charAt(i-1)) * (11 - i);
		}
		resto = 11 - (soma - (Math.floor(soma / 11) * 11));
		if ( (resto == 10) || (resto == 11) ) resto = 0;
		if ( resto != Math.floor(cpf.charAt(9)) ) return false;
		soma = 0;
		for (i = 1; i<=10; i++) {
			soma += cpf.charAt(i-1) * (12 - i);
		}
		resto = 11 - (soma - (Math.floor(soma / 11) * 11));
		if ( (resto == 10) || (resto == 11) ) resto = 0;
		if (resto != Math.floor(cpf.charAt(10)) ) return false;

		return true;
	}
	// Valida o CNPJ
	window.isCnpj = function (cnpj){
		if(cnpj == '') return false;
		if (cnpj.length != 14) return false;
		// Elimina CNPJs invalidos conhecidos
		if (cnpj == "00000000000000" ||
		cnpj == "11111111111111" ||
		cnpj == "22222222222222" ||
		cnpj == "33333333333333" ||
		cnpj == "44444444444444" ||
		cnpj == "55555555555555" ||
		cnpj == "66666666666666" ||
		cnpj == "77777777777777" ||
		cnpj == "88888888888888" ||
		cnpj == "99999999999999")
		return false;

		// Valida DVs
		tamanho = cnpj.length - 2
		numeros = cnpj.substring(0,tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		soma += numeros.charAt(tamanho - i) * pos--;
		if (pos < 2)
		    pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
		return false;

		tamanho = tamanho + 1;
		numeros = cnpj.substring(0,tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
		soma += numeros.charAt(tamanho - i) * pos--;
		if (pos < 2)
		    pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1))
		  return false;

		return true;
	}
	// Define se é um CPF ou CNPJ
	window.isCpfCnpj = function (valor) {
		var retorno = false;
		var numero  = valor;
		numero = String(numero).replace(/\D/g, "");
		if (numero.length > 11) {
			//numero = numero.replace(/^0+/, ""); // retira o zero inicial, se houver
			if (isCnpj(numero)) retorno = true;
		} else {
			if (isCpf(numero)) retorno = true;
		}
		return retorno;
	}

});
