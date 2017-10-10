//JQUERY
jQuery(function() {

	// FIELD PRICE
	// Formata campos de preço
	var field_price		= ".field-price input, input.field-price";

	window.setPrice = function (input, limit, centsLimit, centsSeparator, prefix) {

		// o formato ideal para a moeda brasileira (R$) seria "9.999,00".
		// Mas esse formato não funciona para campos do tipo decimal(10,2)
		// que é o default para campos relativo a valores financeiros.
		// Dessa forma, o formato configurado foi "9999.00"
		// IMPORTANTE: Para que funcione corretamente, o campo da tabela deve ser "decimal(10,2)".

		input = setElement(input, field_price);

		if(elementExist(input)) {
			input.each(function() {
				obj = jQuery(this);
				var width = isSet(obj.data('width')) ? obj.data('width') : false;
				// 'centsLimit' ou 'data-cents-limit'
				// Limite de decimais '.000...'
				// Default = 2;
				var c = isSet(centsLimit) ? centsLimit : 2;
				c = isSet(obj.data('centsLimit')) ? obj.data('centsLimit') : c;
				// 'centsSeparator' ou 'data-cents-separator'
				// Separador de Decimais ',00'
				// Default = ',';
				var f = isSet(centsSeparator) ? centsSeparator : ',';
				f = isSet(obj.data('centsSeparator')) ? obj.data('centsSeparator') : f;
				// 'limit' ou 'data-limit'
				// Limite de caracteres numéricos '1.000.000.000,00'
				// Obs: Não conta com os separadores '.' ou ','
				// Default = 12;
				var l = isSet(limit) ? limit : (10 + c);
				l = isSet(obj.data('limit')) ? obj.data('limit') : l;
				// 'prefix' ou 'data-prefix'
				// Prefixo do valor 'R$'
				// Default = '';
				var p = (isSet(prefix) && !isEmpty(prefix)) ? prefix : '';
				p = (isSet(obj.data('prefix')) && !isEmpty(isSet(obj.data('prefix')))) ? obj.data('prefix') : p;

				if(width) obj.css('width', width);
				obj.css('max-width','100%');

				// define o formato (default é 1.000,00)
				sep1 = (f == ',') ? '.' : f;

				// Placeholder
				var str = '0';
				if(c > 0) obj.attr('placeholder', str + f + str.repeat(c));

				obj.priceFormat({
					prefix: p,
					centsLimit: c,
					thousandsSeparator: sep1,
					centsSeparator: f,
					limit: l
				});

				// evita que o usuário arraste um valor para o campo e quebre a máscara
				inputNoDrop(obj);
			});
		}
	};

	// Formata o 'field-price' para o formato de banco (decimal, float)
	window.priceDecimal = function (input) {
		price = setElement(input, field_price);
		price.each(function() {
			var obj, value, number, decimal;
			obj = jQuery(this);
			val = obj.val();
			if(isSet(input) || (isSet(obj.data('convert')) && obj.data('convert'))) {
				// replace comma ',' for dot '.'
				val = val.replace(/\,/g,'.');
				number = val.substring(0, val.lastIndexOf("."));
				decimal = val.substring(val.lastIndexOf("."));
				// remove separator '.'
				number = number.replace(/\./g,'');
				// set price format
				obj.val(number + decimal);
			}
		});
	};

});
