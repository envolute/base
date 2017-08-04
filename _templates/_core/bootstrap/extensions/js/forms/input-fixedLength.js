//JQUERY
jQuery(function() {

	// FIXED LENGTH
	// Completa o valor com caracteres adicionais
	// Caractere default é: char = '0'
	// posição default é: placement = 'before' [after]
	window.inputFixedLength = function (length, input, char, placement) {

		var field	= ".length-fixed input, input.length-fixed";
		input = setElement(input, field);

		input.each(function() {
			var obj = jQuery(this);

			// length
			var l = isSet(length) ? length : 0;
			l = isSet(obj.data('length')) ? obj.data('length') : l;
			// char
			var c = isSet(char) ? char : 0;
			c = isSet(obj.data('char')) ? obj.data('char') : c;
			// placement -> '0' = before; '1' = after
			var p = isSet(placement) ? placement : 'before';
			p = isSet(obj.data('placement')) ? obj.data('placement') : p;

			// field length
			var len = jQuery(this).val().length;
			if(len > 0 && len < l) {
				var e = l - len;
				var x = '';
				for(i=0; i<e; i++) {
					x = x + c.toString();
				}
				var str = (p == 'before') ? x + obj.val() : obj.val() + x;
				obj.val(str);
			}

			// Chama a funcionalidade no evento 'onblur' do campo
			obj.blur(function() {
				inputFixedLength(length, jQuery(this), char, placement);
			});

		});
	};

});
