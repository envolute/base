//JQUERY
jQuery(function() {

	// FIELD PHONE
	// Formatação para número de telefones
	var field_setPhone 	= ".field-phone input, input.field-phone";

	window.setPhone = function (input, prefix, toggleMask) {
		input = setElement(input, field_setPhone);
		var ph = ' '; // placeholder
		//var width_noMask = '15em';
		var minWidth = '9.5em';
		// verifica se existe um campo do tipo 'phone'
		if(elementExist(input)){
			//se existir, verifico o valor em cada um
			input.each(function() {
				var obj = jQuery(this);
				// MASK DEFINITION
					// prefix param
					var pfx = isSet(prefix) ? prefix : true;
					pfx = isSet(obj.data('prefix')) ? obj.data('prefix') : pfx;
					var pre = !pfx ? '' : '(99) ';
					// mask format
					var ed = pre+'9999-9999[9]'; // eight digits
					var nd = pre+'9999[9]-9999'; // nine digits
					var lg = ed.replace('[','').replace(']','').length;
					// Resolve mask nine digits
					var options = {
						greedy: false,
						placeholder: ph,
						onKeyValidation: function (key, result) {
							if(result.pos == (lg-1)) obj.inputmask(nd, options);
						},
						onKeyDown: function(event, buffer, caretPos, opt){
							if(buffer[lg-5] == '-' && buffer[lg-1] == ph) obj.inputmask(ed, options);
						}
					}
					var msg1 = '<del>'+pre+'9999-9999</del>';
					var msg2 = pre+'9999-9999';
				// TOGGLE BUTTONS
				var btnMask = '<span class="input-group-btn"><a href="javascript:;" class="toggle-mask btn btn-info strong" title="'+msg1+'">#</a></span></div>';
				var btnUnmask = '<span class="input-group-btn"><a href="javascript:;" class="toggle-mask btn btn-danger strong" title="'+msg2+'">#</a></span></div>'
				var width = isSet(obj.data('width')) ? obj.data('width') : 'auto';
				if(width != 'auto') obj.css('width', width);
				// Togglemask param
				var tm = isSet(toggleMask) ? toggleMask : false;
				tm = isSet(obj.data('toggleMask')) ? obj.data('toggleMask') : tm;
				// if togglemask option is true
				if(tm == true) {
					// clear object -> evita botões aninhados
					var h = obj.closest('.input-group');
					if(elementExist(h)) h.replaceWith(obj);
					// create button for toggle mask
					obj.wrap('<div class="input-group" style="width:'+width+'; min-width:12em; max-width:100%;"></div>');
					obj.css({'width':'100%'});
					var mask = setPhoneMask(obj.val(), ed, nd);
					//se o campo não estiver preenchido
					if(mask) {
						// carrega a máscara
						obj.inputmask(mask, options);
						obj.removeClass('no-masked');
						if(!obj.hasClass('form-control')) obj.addClass('form-control');
						obj.after(btnMask);
					} else {
						// senão, fica 'sem máscara'
						obj.inputmask('remove');
						obj.addClass('no-masked');
						obj.after(btnUnmask);
					}
					jQuery('.toggle-mask').tooltip({container: 'body', html: true});

					obj.next('span').find('.btn').off('click').on('click', function(){
						if(jQuery(this).hasClass('btn-info')) {
							obj.addClass('no-masked').inputmask('remove').focus();
							jQuery(this).removeClass('btn-info').addClass('btn-danger').attr('data-original-title', msg2).tooltip();
						} else {
							var nMask = (obj.val().replace(/[^0-9]/g, '').length > 10) ? nd : ed; // pega o valor atualizado do campo
							obj.removeClass('no-masked').inputmask(nMask, options).focus();
							jQuery(this).removeClass('btn-danger').addClass('btn-info').attr('data-original-title', msg1).tooltip();
						}
					});
				} else {
					obj.inputmask(mask, options);
				}
				// seta a mascara no evento 'paste' e 'change'
				obj.on('paste change', function(event) {
					var el = jQuery(this);
					el.removeClass('error');
					// setPhone(jQuery(this), toggleMask);
					el.phoneMaskUpdate(el.val());
					// el.focus();
				});
			});
		}
	};

	window.setPhoneMask = function (val, ed, nd) {
        if(!isEmpty(val)) {
			var mask = 0;
			// valida pelo tamanho da string
			if(val.replace(/[^0-9a-zA-Z]/g, '').length == 11) mask = nd;
			else if(val.replace(/[^0-9a-zA-Z]/g, '').length <= 10) mask = ed;
			var hasDDD = (val.indexOf("(") == 0) ? 1 : 0;
			var hasDiv = (val.indexOf("-") == 9 || val.indexOf("-") == 10) ? 1 : 0;
			return (hasDDD && hasDiv && mask) ? mask : 0;
		}
		return ed;
	};

	// PHONE MASK UPDATE
	// Seta a mascara em formulários AJAX
	jQuery.fn.phoneMaskUpdate = function(val) {
		var obj = jQuery(this);
		var btn = obj.next('span').find('.btn');
		// MASK DEFINITION
			// prefix param
			var pre = isSet(obj.data('prefix')) ? obj.data('prefix') : '(99) ';
			// mask format
			var ed = pre+'9999-9999[9]'; // eight digits
			var nd = pre+'9999[9]-9999'; // nine digits
		// SET VALUE AND FORMAT
		if(isSet(val)) {
			// SET MASK
			if(setPhoneMask(val, ed, nd)) {
				// se a máscara estiver desabilitada, seta o click no botão
				// senão, mantem o estado atual
				if(elementExist(btn) && btn.hasClass('btn-danger')) btn.trigger('click');
			// UNSET MASK
			} else {
				// se a máscara estiver habilitada, seta o click no botão
				// senão, mantem o estado atual
				if(elementExist(btn) && !btn.hasClass('btn-danger')) btn.trigger('click');
			}
			// set field value
			obj.val(val);
		} else {
			obj.val('');
		}
	}

});
