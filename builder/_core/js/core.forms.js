//JQUERY
jQuery(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	var URLBase = jQuery('#baseurl').val();

	// GET INFO ABOUT BROWSER
	var nua = navigator.userAgent;

	// Essa função verifica se o parâmetro foi passado
	window.isSet = function (e) {
		return (typeof e === "null" || typeof e === "undefined") ? false : true;
	};

	// Essa função verifica se o parâmetro é uma string vazia
	window.isEmpty = function (e) {
	  return (e == "") ? true : false;
	};

	// Essa função é identica a 'setElement' em core.js
	// isso é para não haver dependência do core.js
	window.setElement = function (e, def) {
		var obj = e;
		if(!isSet(e)) {
			obj = jQuery(def);
		} else if(typeof e === 'string') {
			e = e.replace('##', '#'); // evita erro no ID do elemento
			obj = jQuery(e);
		}
		return obj;
	};

	// Essa função verifica se o parâmetro foi passado
	window.elementExist = function (e) {
		var obj = setElement(e);
		return (obj.length) ? true : false;
	};

	// FIELDS -> classes dos campos customizados
	var fieldsetEmbed	= ".fieldset-embed, .fieldset-toggle";
	var btnToggleStatus	= ".toggle-status .btn, .btn.toggle-status";
	var field_noDrop	= ".no-drop input, input.no-drop";
	var field_noPaste	= ".no-paste input, input.no-paste";
	var field_setFocus 	= ".set-focus input, input.set-focus";
	var btn_fileAction	= ".set-file-action .btn, .btn.set-file-action"
	var field_upper 	= ".upper input, input.upper";
	var field_lower 	= ".lower input, input.lower";
	var field_cpf 		= ".field-cpf input, input.field-cpf";
	var field_cnpj 		= ".field-cnpj input, input.field-cnpj";
	var field_selectAutoTab	= ".auto-tab select, select.auto-tab";
	var field_checkAutoTab 	= ".auto-tab input:checkbox, input:checkbox.auto-tab, .auto-tab input:radio, input:radio.auto-tab";
	var field_setBtnAction = ".set-btn-action input, input.set-btn-action";
	var field_setPhone 	= ".field-phone input, input.field-phone,.field-mobile input, input.field-mobile";
	var field_cep 		= ".field-cep input, input.field-cep";
	var field_fixPaste	= ".field-cpf input, input.field-cpf, .field-cnpj input, input.field-cnpj, .field-cep input, input.field-cep";
	var field_date 		= ".field-date input, input.field-date";
	var field_time 		= ".field-time input, input.field-time";
	var field_date_time = ".field-date-time input, input.field-date-time";
	var field_price		= ".field-price input, input.field-price";
	var field_noNumber	= ".field-nonumber input, input.field-nonumber";
	var field_noSpecialCharacter = ".no-special-character input, input.no-special-character";
	var field_noBlankSpace	= ".no-blank-space input, input.no-blank-space";
	var field_noAccents	= ".no-accents input, input.no-accents";
	var field_integer	= ".field-integer input, input.field-integer";
	var field_float		= ".field-float input, input.field-float";
	var field_number	= ".field-number input, input.field-number";
	var field_editor	= ".field-editor textarea, textarea.field-editor, .field-html textarea, textarea.field-html";
	var length_fixed	= ".length-fixed input, input.length-fixed";
	var field_selectOrder	= ".select-order";
	var field_selectTo	= ".select-order select.select-to";
	// AUTO COMPLETE
	var field_state		= '.field-state input, input.field-state';
	var field_country	= '.field-country input, input.field-country';
	var field_uf		= ".field-uf select, select.field-uf";
	var field_city		= ".field-city input, input.field-city";
	var field_cidade	= ".field-cidade input, input.field-cidade";
	var field_searchCep	= ".field-cep.field-search input, input.field-cep.field-search";
	var field_address	= ".field-address input, input.field-address";
	var field_address_number = ".field-address-number input, input.field-address-number";
	var field_district	= ".field-district input, input.field-district";

	// TOGGLE FIELDSET FILTER
	window.toggleFieldsetEmbed = function(button, target, offset, fieldFocus) {
		var btn = jQuery(button);
		var obj = setElement(target, fieldsetEmbed);
		var field = setElement(fieldFocus);
		var tag = obj.attr('id');
		var grp = obj.data('group');
		var tEl = setElement(obj.data('toggleElement'));
		var hide = (obj.hasClass('closed')) ? true : false;
		var elem;

		// se o fieldset 'target' fizer parte de um grupo
		if(isSet(grp) && hide) {
			// identifica os outros elementos do grupos e fecha todos
			jQuery(fieldsetEmbed).filter(function() {
				elem = jQuery(this);
				if(elem.data('group') == grp && !elem.hasClass('closed')) {
					elem.slideToggle().toggleClass('closed');
					// remove a classe 'active' dos botões relacionados aos outros elementos
					jQuery('[data-target="'+elem.attr('id')+'"]').removeClass('active');
				}
			});
		}
		// se o elemento não estiver na área visível 'viewport'
		if(!isSet(offset)) offset = 0;
		if(!obj.isOnScreen()) {
			// rola a página até o elemento
			scrollTo(tag, offset);
			// se ele estiver aberto, mantém e não executa mais nenhuma ação
			// assim o botão também funciona como um âncora para o elemento
			// fazendo com que ele só seja fechado se estiver visível
			if(!hide) return;
		}
		// atribui a propriedade 'data-target' ao botão
		// o valor é o ID do fieldset 'target'
		// assim definimos uma relação entre o botão e o alvo
		btn.attr('data-target', tag);

		// verifica se existe um elemento para alternar
		if(tEl && (tEl.css('display') != obj.css('display'))) tEl.slideToggle();

		// mostra/esconde o fieldset
		obj.slideToggle(function() {
			// se o fieldset for aberto, atribui a classe 'active' ao botão
			if(jQuery(this).css('display') != 'none') btn.addClass('active');
			// senão, remove a classe 'active'
			else btn.removeClass('active');
		}).toggleClass('closed');
		// rola a página até o elemento caso ele não esteja na área visível 'viewport'
		if(!obj.isOnScreen() && hide) scrollTo(tag, offset);
		// Seta o focus no campo indicado, caso o fieldset esteja sendo aberto
		if(isSet(fieldFocus) && hide) field.focus();
	};

	// TOGGLE BTN STATUS
	window.toggleBtnStatus = function(button) {
		var btn = setElement(button, btnToggleStatus);
		btn.each(function() {
			jQuery(this).off('click').on('click',function() {
				jQuery(this).not(':disabled').not('.disabled').toggleClass('active');
			});
		});
	};

	// NO DROP -> desabilita a funcionalidade de arrastar um valor para o campo
	window.noDrop = function (input) {
		input = setElement(input, field_noDrop);
		input.on('drop', function (e) {
			e.preventDefault();
		});
	};

	// NO PASTE -> desabilita a funcionalidade de colar um valor para o campo
	window.noPaste = function (input) {
		input = setElement(input, field_noPaste);
		input.on('paste', function (e) {
	    		e.preventDefault();
    		});
	};

	// SET FOCUS -> Seta o foco no campo selecionado
	window.setFocus = function (input) {
		input = setElement(input, field_setFocus);
		input.focus();
	};

	// UPPER CASE
	window.setUppercase = function(input) {
		input = setElement(input, field_upper);
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

	// LOWER CASE
	window.setLowercase = function(input) {
		input = setElement(input, field_lower);
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

	// SET ACTIVE STATE
	// define os radios ou checkboxes ativos quando estiverem formatados como botão
	window.setActiveState = function() {
		var obj;
		jQuery('.btn-group label.btn').find('input:radio, input:checkbox').each(function() {
			obj = jQuery(this);
			if(obj.prop('checked')) obj.closest('label').addClass('active');
			else obj.closest('label').removeClass('active');
		});
	};

	// Set State Value -> define o valor do campo 'state'
	window.selectRadio = function(field, value) {
		var input = setElement(field);
		input.each(function() {
			jQuery(this).prop('checked', false); // clear default value
		});
		// atribui o valor padrão
		if(input.filter('[value="'+value+'"]').length)
		input.filter('[value="'+value+'"]').prop('checked', true).trigger('change');
		// if 'button', set active
		setActiveState();
	};


	// AUTO TAB + INPUTMASK -> seta o focus no proximo campo após finalizar a mascara
	// ex: jQuery("#data").inputmask("dd/mm/yyyy",{oncomplete: function(){obj.autoTab();}});

		// SET AUTO-TAB
		jQuery.fn.autoTab = function() {
			return this.each(function() {
				var fields = jQuery(this).parents("form:eq(0),body").find("button,input,textarea,select");
				var index = fields.index( this );
				if ( index > -1 && ( index + 1 ) < fields.length ) {
					var obj = fields.eq( index + 1 );
					obj.focus();
					// verifica se é um 'select' chosen
					if(obj.next('.chosen-container').length) {
						// seta o foco no elemento 'chosen' após o evento autoTab
						obj.next('.chosen-container').find('a.chosen-single').focus();
					}
				}
				return false;
			});
		};

		//SELECT AUTO-TAB
		window.selectAutoTab = function (input, target, group) {
			input = setElement(input, field_selectAutoTab);
			input.each(function() {

				var obj = jQuery(this);
				var objFocus = setElement((isSet(target) ? target : '#'+obj.data('target').replace('#','')));
				var objGroup = setElement((isSet(group) ? group : obj.data('targetGroup')));

				obj.change(function() {

					var option = jQuery(this).find('option:selected');
					var disable = option.data('targetDisabled');
					var display = option.data('targetDisplay');
					var value = option.data('targetValue');
					var newStatus;

					// IMPORTANT: this must go before autoTab
					// 'objFocus' corrige o focus do 'select.chosen'
					if(elementExist(objFocus)) {
						// set target disable status
						if(disable != null) {
							newStatus = toggleDisabled(objFocus, disable);
							if(isSet(objGroup)) toggleDisabled(objGroup, (newStatus ? false : true));
						}
						// set target status display
						if(display != null) {
							newStatus = toggleDisplay(objFocus, display);
							if(isSet(objGroup)) toggleDisplay(objGroup, (newStatus ? false : true));
						}
						// tab to target
						element = (!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select')) ? objFocus.find('input').filter(':visible:first') : objFocus;
						if(element.is('select') && element.next('.chosen-container').length) {
							el = element.next('.chosen-container').find('a.chosen-single');
							isChosen = true;
						} else {
							el = element;
							isChosen = false;
						}
						setTimeout(function() { el.focus() }, 10);
						// set value
						if(value != null && value != "undefined") element.val(value);
						if(isChosen) element.trigger("chosen:updated"); // select
					} else {
						// auto tab
						obj.autoTab();
					}
				});
			});
		};

		//CHECK AUTO-TAB
		window.checkAutoTab = function (input, target, group) {
			input = setElement(input, field_checkAutoTab);
			input.each(function() {

				var obj = jQuery(this);
				var objFocus = setElement((isSet(target) ? target : '#'+obj.data('target').replace('#','')));
				var objGroup = setElement((isSet(group) ? group : obj.data('targetGroup')));

				if(elementExist(objFocus)) {
					var disable = obj.data('targetDisabled');
					var display = obj.data('targetDisplay');
					var value = obj.data('targetValue');
					var newStatus;

					obj.change(function() {
						if(jQuery(this).is(':checked')) {
							// IMPORTANT: this must go before autoTab
							if(elementExist(objFocus)) {
								// set target disable status
								if(disable != null) {
									newStatus = toggleDisabled(objFocus, disable);
									if(isSet(objGroup)) toggleDisabled(objGroup, (newStatus ? false : true));
								}
								// set target status display
								if(display != null) {
									newStatus = toggleDisplay(objFocus, display);
									if(isSet(objGroup)) toggleDisplay(objGroup, (newStatus ? false : true));
								}
								// tab to target
								element = (!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select')) ? objFocus.find('input').filter(':visible:first') : objFocus;
								if(element.is('select') && element.next('.chosen-container').length) {
									el = element.next('.chosen-container').find('a.chosen-single');
									isChosen = true;
								} else {
									el = element;
									isChosen = false;
								}
								setTimeout(function() { el.focus() }, 10);
								// set value
								if(value != null && value != "undefined") element.val(value);
								if(isChosen) element.trigger("chosen:updated"); // select
							} else {
								// auto tab
								obj.autoTab();
							}
						} else {
							if(elementExist(objFocus)) {
								// set target status disable
								if(disable != null) {
									toggleDisabled(objFocus, (disable ? false : true));
									if(isSet(objGroup)) toggleDisabled(objGroup, disable);
								}
								// set target status display
								if(display != null) {
									toggleDisplay(objFocus, (display ? false : true));
									if(isSet(objGroup)) toggleDisplay(objGroup, display);
								}
								// tab to target
								element = (!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select')) ? objFocus.find('input').filter(':visible:first') : objFocus;
								if(element.is('select') && element.next('.chosen-container').length) {
									el = element.next('.chosen-container').find('a.chosen-single');
									isChosen = true;
								} else {
									el = element;
									isChosen = false;
								}
								if(elementExist(el)) setTimeout(function() { el.focus() }, 10);
								// set value
								if(value != null && value != "undefined") element.val(value);
								if(isChosen) element.trigger("chosen:updated"); // select
							} else {
								// auto tab
								obj.autoTab();
							}
						}
					});
				}
			});
		};

		// TOGGLE DISPLAY FIELD
		window.toggleDisplay = function (input, status) {
			if(!isSet(status) || status == false || status == 'false') status = false;
			else status = true;
			input.each(function() {
				var obj = (input.is('select') && input.next('.chosen-container').length) ? jQuery(this).next('.chosen-container') : jQuery(this);
				if(status) {
					if(obj.hasClass('hide') || obj.hasClass('toggleHide'))
					obj.removeClass('hide').addClass('toggleHide');
					else obj.show();
				} else {
					if(obj.hasClass('hide') || obj.hasClass('toggleHide'))
					obj.removeClass('toggleHide').addClass('hide');
					else obj.hide();
				}
			});
			return status;
		};

		// TOGGLE DISABLED FIELD
		window.toggleDisabled = function (input, status) {
			if(!isSet(status) || status == false || status == 'false') status = false;
			else status = true;
			input.prop('disabled', status);
			if(input.is('select') && input.next('.chosen-container').length) input.trigger("chosen:updated"); // select
			return status;
		};

		// SET FILE ACTION -> Seta a ação em um campo do tipo file
		window.setFileAction = function (btn, target, imgTarget, width, height) {
			btn = setElement(btn, btn_fileAction);
			var e = isSet(target) ? target : jQuery(btn.data('target'));
			btn.each(function() {
				jQuery(this).off('click').on('click',function() {
					if(elementExist(e)) {
						e.click();
					} else {
						jQuery(this).closest('.btn-group').next('input:file').click();
					}
				})
			});
		};

		// SET FILE ACTIVE -> retorno, caso o arquivo seja selecionado
		window.setFileActive = function () {
			jQuery('.btn-group').next('input:file.element-invisible').each(function() {
				jQuery(this).change(function() {
					var obj = jQuery(this);
					var group = obj.prev('.btn-group');
					if(obj.val() != '') {
						// mostra o indicador
						group.find('.set-file-action').removeClass('btn-default').addClass('btn-success');
						// remove error message if field is 'required'
						obj.next('.error').addClass('valid').empty();
					}
				});
			});
		};

		// SET BUTTON ACTION
		// seta a ação de click(default) ou focus no botão (btn) através da tecla 'enter' quando o foco estiver no campo (input)
		window.setBtnAction = function (input, target, action) {
			input = setElement(input, field_setBtnAction);
			input.each(function() {

				if(!isSet(target))
					this.target = jQuery('#'+jQuery(this).data('target'));
				else this.target = target;

				if(!isSet(action))
					this.action = jQuery(this).data('action');
				else this.action = action;

				if(elementExist(this.target)) {
					var obj = jQuery(this);
					input.keyup(function (e) {
						if (e.keyCode==13) {
							if(this.action == 'focus') this.target.focus();
							else {
								obj.blur(); // avoid click again
								this.target.focus().click();
							}
						}
					});
				}

			});
		};


	// MASKEDINPUT
	// mascaras pré-definidas. jQuery Input Mask plugin -> http://robinherbots.github.io/jquery.inputmask

		// set defaults
		Inputmask.extendDefaults({
	    showMaskOnHover: false
		});

		//CPF
		window.setCPF = function (input, autotab) {
			input = setElement(input, field_cpf);
			var error = 'CPF INVÁLIDO';
			input.each(function() {
				var obj = jQuery(this);
				var width = isSet(obj.data('width')) ? obj.data('width') : false;
				if(width) obj.css('width', width);
				obj.css({'min-width':'9.5em', 'max-width':'100%'});
		    // autotab param
				var tab = isSet(autotab) ? autotab : true;
				tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

				obj.inputmask("999.999.999-99", {
					oncomplete: function(){
						if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
							jQuery('.cpf-error').remove();
							obj.addClass('error').after('<span class="cpf-error error">'+error+'</span>');
						} else {
							if(tab) obj.autoTab();
						}
					},
					onKeyDown: function(event, buffer, caretPos, opt){
						if(obj.val().replace(/[^0-9]/g, '').length < 11) {
							obj.removeClass('error');
							obj.next('.error').hide();
						}
					}
				}).on('blur', function(event) {
					if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
						jQuery('.cpf-error').remove();
						obj.addClass('error').after('<span class="cpf-error error">'+error+'</span>');
						obj.val('');
					}
					obj.removeClass('error');
					obj.next('.error').hide();
				});
			});
		};

		//CNPJ
		window.setCNPJ = function (input, autotab) {
			input = setElement(input, field_cnpj);
			var error = 'CNPJ INVÁLIDO!<br />Informe o CNPJ com apenas 14 dígitos.<br />Ignore, se houver, o dígito 0 (zero) inicial';
			input.each(function() {
				var obj = jQuery(this);
				var width = isSet(obj.data('width')) ? obj.data('width') : false;
				if(width) obj.css('width', width);
				obj.css({'min-width':'12em', 'max-width':'100%'});
		    // autotab param
				var tab = isSet(autotab) ? autotab : true;
				tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

				obj.inputmask("99.999.999/9999-99", {
					oncomplete: function(){
						if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
							jQuery('.cnpj-error').remove();
							obj.addClass('error').after('<span class="cnpj-error error">'+error+'</span>');
						} else {
							if(tab) obj.autoTab();
						}
					},
					onKeyDown: function(event, buffer, caretPos, opt) {
						if(obj.val().replace(/[^0-9]/g, '').length < 14) {
							obj.removeClass('error');
							obj.next('.error').hide();
						}
					}
				}).on('blur', function(event) {
					if(obj.val().replace(/\D/g, "").length > 0 && !isCpfCnpj(obj.val())) {
						jQuery('.cnpj-error').remove();
						obj.addClass('error').after('<span class="cnpj-error error">'+error+'</span>');
						obj.val('');
					}
					obj.removeClass('error');
					obj.next('.error').hide();
				});
			});
		};

		//TELEFONES
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
					// prefix param
					var pfx = isSet(prefix) ? prefix : true;
					pfx = isSet(obj.data('prefix')) ? obj.data('prefix') : pfx;
					var pre = !pfx ? '' : '(99) ';
					// mask format
					var ed = pre+'9999-9999[9]'; // eight digits
				  var nd = pre+'9999[9]-9999'; // nine digits
				  var lg = ed.replace('[','').replace(']','').length;
				  var msg1 = '<del>'+pre+'9999-9999</del>';
				  var msg2 = pre+'9999-9999';
					var btnMask = '<span class="input-group-btn"><a class="toggle-mask btn btn-info strong" title="'+msg1+'">#</a></span></div>';
				  var btnUnmask = '<span class="input-group-btn"><a class="toggle-mask btn btn-danger strong" title="'+msg2+'">#</a></span></div>'
				  // resolve mask nine digits
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
		      var mask = (obj.val().replace(/[^0-9]/g, '').length > 10) ? nd : ed;
		      // -------------------------
		      var nomask = 0;
		      var width = isSet(obj.data('width')) ? obj.data('width') : 'auto';
		      if(width != 'auto') obj.css('width', width);
					// togglemask param
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
		        //se o campo não estiver preenchido
		        if(isEmpty(obj.val()) || obj.val().indexOf("(") >= 0) {
		          //carrega a máscara
		          obj.inputmask(mask, options);
		        } else {
		          //senão, fica 'sem máscara'
		          obj.inputmask('remove');
		          nomask = 1;
		        }
		        // se a mascara for carregada
		        if(!nomask){
		          obj.removeClass('no-masked');
		          if(!obj.hasClass('form-control')) obj.addClass('form-control');
		          obj.after(btnMask);
		        } else {
		          obj.addClass('no-masked');
		          obj.after(btnUnmask);
		        }
		        jQuery('.toggle-mask').tooltip({container: 'body', html: true});

		        obj.next('span').find('.btn').off('click').on('click', function(){
		          if(jQuery(this).hasClass('btn-info')) {
		            obj.addClass('no-masked').inputmask('remove').focus();
		            jQuery(this).removeClass('btn-info').addClass('btn-danger').attr('data-original-title', msg2).tooltip('fixTitle');
		          } else {
	              var nMask = (obj.val().replace(/[^0-9]/g, '').length > 10) ? nd : ed; // pega o valor atualizado do campo
	              obj.removeClass('no-masked').inputmask(nMask, options).focus();
	              jQuery(this).removeClass('btn-danger').addClass('btn-info').attr('data-original-title', msg1).tooltip('fixTitle');
		          }
		        });
		      } else {
		        obj.inputmask(mask, options);
		      }
					// seta a mascara no evento 'paste'
					obj.on('paste', function(event) {
						jQuery(this).removeClass('error');
						setPhone(jQuery(this), toggleMask);
						jQuery(this).focus();
					});
		    });
		  }
		};

		//CEP
		window.setCEP = function (input, autotab) {
			input = setElement(input, field_cep);
			input.each(function() {
				var obj = jQuery(this);
				var width = isSet(obj.data('width')) ? obj.data('width') : false;
		    // autotab param
				var tab = isSet(autotab) ? autotab : true;
				tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

				if(width) obj.css('width', width);
				obj.css({'min-width':'7.2em', 'max-width':'100%'});
				obj.inputmask("99999-999", { oncomplete: function(){ if(tab) obj.autoTab(); } });
			});
		};

		// SET HTML EDITOR
		window.setEditor = function (input, isFull, isDisable) {
			// $.trumbowyg.svgPath = false:
			input = setElement(input, field_editor);
			input.each(function() {
				var obj = jQuery(this);
				// set full
				var full = isSet(isFull) ? isFull : false;
				full = isSet(obj.data('editorFull')) ? obj.data('editorFull') : full;
				// set disabled
				var disable = isSet(isDisable) ? isDisable : false;
				disable = isSet(obj.data('editorDisabled')) ? obj.data('editorDisabled') : disable;

				var basicOptions = ['btnGrp-semantic',['formatting'],'btnGrp-justify','btnGrp-lists',['horizontalRule'],['link'],['removeformat'],['fullscreen']];
				var fullOptions = [['viewHTML'],'btnGrp-semantic','superscript',['formatting'],['foreColor'],'btnGrp-justify','btnGrp-lists',['horizontalRule'],['link'],'image',['noembed'],['removeformat'],['fullscreen']]
				if(full) {
					var options = fullOptions;
					var imageGroup = { dropdown: ['insertImage', 'base64'], ico: 'insertImage' };
				} else {
					var options = basicOptions;
					var imageGroup = null;
				}

				obj.trumbowyg({
					lang: 'pt',
					removeformatPasted: true,
					disabled: disable,
					btnsDef: { image: imageGroup },
					btns: options
				});
			});
		};
		window.setContentEditor = function (input, value) {
			input = setElement(input, field_editor);
			input.each(function() {
				var obj = jQuery(this);
				var val = isEmpty(obj.val()) ? '' : obj.val();
				val = isSet(value) ? value : val;
				obj.trumbowyg('html', val); // html editor
			});
		};
		window.getContentEditor = function (input) {
			input = setElement(input, field_editor);
			input.each(function() {
				jQuery(this).val(jQuery(this).trumbowyg('html'));
			});
		};

		// DATA
		window.setDate = function (input, setTime, seconds, autotab) {
		  input = setElement(input, field_date);
		  input.each(function() {
		    var obj = jQuery(this);
				// evita erros com data zerada '00/00/0000' ou em branco
				// Obs: quando a vem em branco, obj.val() = '0_/__/____'
				if(obj.val().indexOf('0_/') >= 0 || isEmpty(obj.val())) obj.val('');
				// formata o valor
		    else obj.val(dateFormat(obj.val()));
		    // setTime param
		    var time = isSet(setTime) ? setTime : false;
		    time = isSet(obj.data('time')) ? obj.data('time') : time;
		    // seconds param
		    var sec = isSet(seconds) ? seconds : true;
		    sec = isSet(obj.data('seconds')) ? obj.data('seconds') : sec;
		    // autotab param
		    var tab = isSet(autotab) ? autotab : true;
		    tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;

		    var mask = 'd/m/y';
		    var hold = '__/__/____';
		    var minW = '8.2em';
		    if(time == true) {
		      if(sec == true){
		        mask = mask+' h:s:s';
		        hold = hold+' __:__:__';
		        minW = '13em';

		      } else {
		        mask = mask+' h:s';
		        hold = hold+' __:__';
		        minW = '11.5em';
		      }
		    }
		    var mindate = obj.data('mindate');
		    var maxdate = obj.data('maxdate');
		    var yrange  = obj.data('yearRange');
		    var width = (!isSet(obj.data('width')) ? obj.css('width', minW) : obj.css('width', obj.data('width')));
		    obj.css({'min-width': minW, 'max-width':'100%'});

		    // mask date
		    obj.inputmask(mask, {
		      placeholder: hold,
		      showMaskOnHover: true,
		      oncomplete: function(){
		        obj.datepicker("hide");
						obj.change();
		        if(tab) obj.autoTab();
		      },
		      onKeyDown: function(){
		        obj.datepicker("hide");
		      }
		    });
		    // open datepicker on click
		    obj.off('click').on('click', function() { obj.focus(); })
		    // define datapicker format
		    var _dateFormat = "dd/mm/yy";
		    var _dayNames = ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado","Domingo"];
		    var _dayNamesMin = ["D","S","T","Q","Q","S","S","D"];
		    var _dayNamesShort = ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb","Dom"];
		    var _monthNames = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
		    var _monthNamesShort = ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"];
		    var _nextText = "Próximo";
		    var _prevText = "Anterior";
		    var _currDate;
		    obj.datepicker({
		      dateFormat: _dateFormat,
		      dayNames: _dayNames,
		      dayNamesMin: _dayNamesMin,
		      dayNamesShort: _dayNamesShort,
		      monthNames: _monthNames,
		      monthNamesShort: _monthNamesShort,
		      nextText: _nextText,
		      prevText: _prevText,
		      changeMonth: true,
		      changeYear: true,
		      beforeShow: function(dateText, inst){
		        if(time) {
		          _currDate = obj.val().split(' ');
		        }
		      },
		      onSelect: function(dateText, inst){
		        if(time) {
		          dateText = dateText + " " + _currDate[1];
		          obj.val(dateText);
		          if(dateText.replace(/[^0-9]/g, '').length == 14) {
		            if(tab) setTimeout(function(){ obj.autoTab(); }, 100);
		          } else {
		            obj.focus();
		          }
		        } else {
		          if(tab) setTimeout(function(){ obj.autoTab(); }, 100);
		        }
		      }
		    });
		    // tipos de calendário
		    if(mindate != null) obj.datepicker("option", "minDate", mindate );
		    if(maxdate != null) obj.datepicker("option", "maxDate", maxdate);
		    if(yrange  != null) obj.datepicker("option", "yearRange", yrange );

		  });
		};
		// converte do formato de banco (0000-00-00) para o formato padrão do 'field-date' (00-00-0000)
		window.dateFormat = function (val) {
		  if(val.indexOf('-') == 4) {
		    var dh = val.split(' ');
		    var dt = dh[0].split('-');
				var time = isSet(dh[1]) ? dh[1] : '00:00:00';
		    return dt[2]+'/'+dt[1]+'/'+dt[0]+' '+time;
		  } else {
		    return val;
		  }
		};
		// formata o 'field-date' para o formato de banco (0000-00-00)
		window.dateConvert = function () {
		  jQuery('.field-date').each(function() {
				var obj, dh, dt, d, m, y, t, setTime, seconds;
		    obj = jQuery(this);
		    if(isSet(obj.data('convert')) && obj.data('convert') && !isEmpty(obj.val())) {
		      dh = obj.val().split(' ');
		      dt = dh[0].split('/');
		      d = (isSet(dt[0]) && dt[0].length == 2) ? dt[0] : '';
		      m = (isSet(dt[1]) && dt[1].length == 2) ? dt[1] : '';
		      y = (isSet(dt[2]) && dt[2].length == 4) ? dt[2] : '';
		      if(!isEmpty(d) && !isEmpty(m) && !isEmpty(y)) {
		        t = (isSet(dh[1]) && dh[1].length > 0) ? ' '+dh[1] : '';
		        // remove mask to enable converted value
		        if(obj.inputmask) obj.inputmask('remove');
		        // set converted value
		        obj.val(y+'-'+m+'-'+d+t);
		        // reset mask after 3 seconds
		        setTime = (isSet(obj.data('time')) && obj.data('time')) ? true : false;
		        seconds = (isSet(obj.data('seconds')) && !obj.data('seconds')) ? false : true;
		        setTimeout(function() {
		          setDate(obj, setTime, seconds);
		        } , 1000 );
		      }
		    }
		  });
		};

		//HORA
		window.setTime = function (input, seconds, autotab) {
			input = setElement(input, field_time);
			input.each(function() {
				var obj = jQuery(this);
				var width = isSet(obj.data('width')) ? obj.data('width') : false;
		    // seconds param
				var sec = isSet(seconds) ? seconds : false;
				sec = isSet(obj.data('seconds')) ? obj.data('seconds') : sec;
		    // autotab param
				var tab = isSet(autotab) ? autotab : true;
				tab = isSet(obj.data('autotab')) ? obj.data('autotab') : tab;
				if(sec) {
					obj.inputmask("h:s:s",{ oncomplete: function(){ if(tab) obj.autoTab(); } });
					w = '6em';
				} else {
					obj.inputmask("h:s",{ oncomplete: function(){ if(tab) obj.autoTab(); } });
					w = '4.5em';
				}
				if(width) obj.css('width', width);
				obj.css({'min-width':w, 'max-width':'100%'});
			});
		};


	// FORMATA PREÇO
	window.setPrice = function (input, cents, usFormat) {

	  // o formato ideal para a moeda brasileira (R$) seria "9.999,00".
	  // Mas esse formato não funciona para campos do tipo decimal(10,2)
	  // que é o default para campos relativo a valores financeiros.
	  // Dessa forma, o formato configurado foi "9999.00"
	  // IMPORTANTE: Para que funcione corretamente, o campo da tabela deve ser "decimal(10,2)".

	  input = setElement(input, field_price);

	  if(elementExist(input)){
	    input.each(function() {
	      obj = jQuery(this);
	      var width = isSet(obj.data('width')) ? obj.data('width') : false;

	      // cents param
	      var c = isSet(cents) ? cents : true;
	      c = isSet(obj.data('cents')) ? obj.data('cents') : c;
	      // usFormat param
	      var f = isSet(usFormat) ? usFormat : false;
	      f = isSet(obj.data('usFormat')) ? obj.data('usFormat') : f;

	      if(width) obj.css('width', width);
	      obj.css('max-width','100%');

	      // define se vai usar centavos.
	      var decimal = (c == true) ? 2 : 0;
	      var limite = (c == true) ? 12 : 10;

	      // define o formato (default é 1.000,00)
	      if(f == true) {
	        sep1 = ',';
	        sep2 = '.';
	        if(c) obj.attr('placeholder','0.00');
	      } else {
	        sep1 = '.';
	        sep2 = ',';
	        if(c) obj.attr('placeholder','0,00');
	      }

	      obj.priceFormat({
	        prefix: '',
	        centsLimit: decimal,
	        thousandsSeparator: sep1,
	        centsSeparator: sep2,
	        limit: limite
	      });

	      // evita que o usuário arraste um valor para o campo e quebre a máscara
	      noDrop(obj);
	    });
	  }
	};
	// formata o 'field-price' para o formato de banco (decimal, float)
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

	// APENAS CARACTERES 'letras' COM OU EM ACENTO
	window.setNoNumber = function (input) {
		input = setElement(input, field_noNumber);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[0-9]/g, ""));
		});
	};

	// APENAS NÚMEROS E CARACTERES
	window.setNoSpecialCharacter = function (input) {
		input = setElement(input, field_noSpecialCharacter);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚâêîôûÂÊÎÔÛãõÃÕàÀçÇ ]/g, ""));
		});
	};

	// SEM ESPAÇO EM BRANCO
	window.setNoBlankSpace = function (input) {
		input = setElement(input, field_noBlankSpace);
		input.on("keypress keyup blur",function (e) {
			if(e.which == 32) e.preventDefault();
		});
	};

	// SEM ACENTUAÇÃO
	window.setNoAccents = function (input) {
		input = setElement(input, field_noAccents);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[áéíóúÁÉÍÓÚâêîôûÂÊÎÔÛãõÃÕàÀçÇ´`~^]/g, ""));
		});
	};

	// INTEGER - APENAS NÚMEROS INTEIROS
	window.setInteger = function (input) {
		input = setElement(input, field_integer);
		input.on("keypress keyup blur",function (e) {
			jQuery(this).val(jQuery(this).val().replace(/[^\d]/g, ""));
			if(e.which > 65) e.preventDefault();
		});
	};

	// FLOAT - APENAS NÚMEROS COM PONTO FLUTUANTE
	window.setFloat = function (input) {
		input = setElement(input, field_float);
		input.on("keypress keyup blur",function (event) {
			jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g,''));
			if(e.which > 65) e.preventDefault();
		});
	};

	// APENAS NÚMEROS
	window.setNumeric = function (input, type) {

		// chama as funcoes numericas
		setInteger();
		setFloat();

		input = setElement(input, field_number);
		switch (type) {
			case 'float':
				setFloat(input);
				break;
			default:
				setInteger(input);
		}

	};

	// COMPLETA O VALOR COM CARACTERES ADICIONAIS
	// Caractere default é: char = '0'
	// posição default é: placement = 'before' [after]
	window.lengthFixed = function (length, input, char, placement) {

		input = setElement(input, length_fixed);

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
		});
	};

	// MONTA UM SELECT A PARTIR DE OUTRO
	// 'element' é referente ao container
	window.selectOrder = function (element) {
		el = setElement(element, field_selectOrder);
	  el.each(function() {
		  var obj = jQuery(this);
			var selFrom = obj.find('.select-from');
			var selTo = obj.find('.select-to');
			h = isSet(obj.data('height')) ? obj.data('height') : 100;
			selFrom.add(selTo).css('min-height', h);
			obj.find('.btn-add').click(function(){
				selFrom.find('option:selected').each( function() {
					selTo.append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
          jQuery(this).remove();
        });
	    });
	    obj.find('.btn-remove').off('click').on('click', function(){
        selTo.find('option:selected').each( function() {
          selFrom.append("<option value='"+jQuery(this).val()+"'>"+jQuery(this).text()+"</option>");
          jQuery(this).remove();
        });
	    });
	    obj.find('.btn-up').off('click').on('click', function() {
        selTo.find('option:selected').each( function() {
          var newPos = selTo.find('option').index(this) - 1;
          if (newPos > -1) {
            selTo.find('option').eq(newPos).before("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
            jQuery(this).remove();
          }
        });
	    });
	    obj.find('.btn-down').off('click').on('click', function() {
        var countOptions = obj.find('.select-to option').size();
				var countSelected = obj.find('.select-to option:selected').size();
        selTo.find('option:selected').each( function() {
          var newPos = selTo.find('option').index(this) + countSelected;
          if (newPos < countOptions) {
            selTo.find('option').eq(newPos).after("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
            jQuery(this).remove();
          }
        });
	    });
	  });
	};
	// seleciona todas as opções de um select multiple
	window.selectAllOptions = function (input) {
		if(isSet(input)) {
			input = setElement(input);
			if(elementExist(input)) {
			  input.each(function() {
				  jQuery(this).find('option').prop('selected', true);
				});
			}
		}
	};

	// VALIDAÇÃO CPF/CNPJ
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

	window.isCnpj = function (cnpj){

		if(cnpj == '') return false;

		if (cnpj.length != 14)
		return false;

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

	// RETORNA O ESTADO NO LUGAR DA SIGLA
	window.ufToState = function (uf) {
		switch (uf) {
			case 'AC' :
				return 'ACRE';
			case 'AL' :
				return 'ALAGOAS';
			case 'AP' :
				return 'AMAPÁ';
			case 'AM' :
				return 'AMAZONAS';
			case 'BA' :
				return 'BAHIA';
			case 'CE' :
				return 'CEARÁ';
			case 'DF' :
				return 'DISTRITO FEDERAL';
			case 'ES' :
				return 'ESPÍRITO SANTO';
			case 'GO' :
				return 'GOIÁS';
			case 'MA' :
				return 'MARANHÃO';
			case 'MT' :
				return 'MATO GROSSO';
			case 'MS' :
				return 'MATO GROSSO DO SUL';
			case 'MG' :
				return 'MINAS GERAIS';
			case 'PA' :
				return 'PARÁ';
			case 'PB' :
				return 'PARAÍBA';
			case 'PR' :
				return 'PARANÁ';
			case 'PE' :
				return 'PERNAMBUCO';
			case 'PI' :
				return 'PIAUÍ';
			case 'RJ' :
				return 'RIO DE JANEIRO';
			case 'RN' :
				return 'RIO GRANDE DO NORTE';
			case 'RS' :
				return 'RIO GRANDE DO SUL';
			case 'RO' :
				return 'RONDÔNIA';
			case 'RR' :
				return 'RORAIMA';
			case 'SC' :
				return 'SANTA CATARINA';
			case 'SP' :
				return 'SÃO PAULO';
			case 'SE' :
				return 'SERGIPE';
			case 'TO' :
				return 'TOCANTINS';
			default :
				return uf;
		}

	}

	// END FUNCTION DECLARATIONS--------------------------------------------------------

	window.setFormDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS AUXILIARES
		// -------------------------------------------------------------------------------

			// TOGGLE BTN STATUS
			toggleBtnStatus();

			// NO DROP
			noDrop();

			// NO PASTE
			noPaste();

			// SET FOCUS
			setFocus();

			// UPPER CASE
			setUppercase();

			// LOWER CASE
			setLowercase();

			// SET ACTIVE STATE
			setActiveState();

			//CPF
			setCPF();

			//CNPJ
			setCNPJ();

			//SELECT AUTO-TAB
			selectAutoTab();

			//CHECK AUTO-TAB
			checkAutoTab();

			// SET BUTTON ACTION
			setBtnAction();

			// SET FILE ACTION
			setFileAction();

			// SET FILE ACTIVE
			setFileActive();

			//TELEFONES
			setPhone();

			//DATA
			setDate();

			//HORA
			setTime();

			//CEP
			setCEP();

			// SET HTML EDITOR
			setEditor();

			//FORMATA PREÇO
			setPrice();

			//APENAS LETRAS
			setNoNumber();

			//DESABILITA CARACTERES ESPECIAIS
			setNoSpecialCharacter();

			//DESABILITA CARACTERES ESPECIAIS
			setNoBlankSpace();

			// SEM ACENTUAÇÃO
			setNoAccents();

			//APENAS NÚMEROS
			setNumeric();

			// MONTA UM SELECT A PARTIR DE OUTRO
			selectOrder();

		// FUNCIONALIDADES ESPECÍFICAS
		// -------------------------------------------------------------------------------

			// CORREÇÕES DO BOOTSTRAP PARA O ANDROID
			var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 && nua.indexOf('Chrome') === -1);
			if (isAndroid) jQuery('select.form-control').removeClass('form-control').css('width', '100%');

			// SETA OS CAMPOS OBRIGATÓRIOS
			jQuery('.field-required input, .field-required select, .field-required textarea').each(function() {
				if(jQuery(this).hasClass('field-id')) {
					jQuery(this).addClass('id-required');
				} else {
					jQuery(this).addClass('input-required');
				}
			});
			// RESETA CAMPOS OBRIGATÓRIOS EM UM 'INPUT-GROUP'
			jQuery('.input-group .input-required, .input-group .id-required').each(function() {
				jQuery(this).change(function() {
					jQuery(this).closest('.input-group').parent().children('.error').remove();
				});
			});

			// CAMPOS COM MASCARA QUE APRESENTARAM PROBLEMAS QUANDO TENTA 'COLAR' UM VALOR
			// alguns campos com mascara não estavam colando o valor corretamente, apenas quando a mascara toda é selecionada
			// sendo assim, a função abaixo seleciona todo o valor do campo quando o evento 'paste'(copiar) é chamado...
			jQuery(field_fixPaste).bind({
				paste : function(){
					jQuery(this).select();
				}
			});

			// DESABILITA O CLICK EM LABEL "disabled"
			jQuery('label.btn').each(function() {
				jQuery(this).click(function() {
					if(jQuery(this).hasClass('disabled')) jQuery(this).preventDefault();
				});
			});

		// AUTOCOMPLETE

		// CONSULTA CEP -> republicavirtual.com.br
		if(elementExist(jQuery(field_searchCep))){
			jQuery.getScript("https://envolute.com/cdn/sources/consulta_cep.js", function(){
				jQuery(field_searchCep).blur(function(){
					if(jQuery(this).val().replace(/\_/g, "").length == 9){
						var campo_country = jQuery(field_country);
						var campo_uf = jQuery(field_uf);
						var campo_state = jQuery(field_state);
						var campo_cidade = jQuery(field_cidade);
						var campo_city = jQuery(field_city);
						var campo_bairro = jQuery(field_district);
						var campo_logradouro = jQuery(field_address);
						var campo_numero = jQuery(field_address_number);

						campo_numero.focus();
						getEndereco(jQuery(this),campo_uf,campo_cidade,campo_bairro,campo_logradouro,campo_city,campo_state,campo_country);
						//seta o evento change para avisar que os campos foram alterados
						setTimeout(function(){
							jQuery(field_address,field_address_number,field_district,field_cidade,field_city,field_uf,field_state,field_country).trigger("change");
							if(elementExist(campo_uf.next('.chzn-container, .chosen-container'))) {
								campo_uf.trigger("liszt:updated"); // versão antiga -> que funciona - OLD
								campo_uf.trigger("chosen:updated"); // nova versão -> em caso de atualização
							}
						},1000);
					}
				});
			});
			jQuery(field_searchCep).on('paste', function(e){
				setTimeout(function () {
					if(e.target.value.replace(/\_/g, "").length == 9) {
						jQuery(field_searchCep).val(e.target.value).trigger("blur");
					}
				}, 100);
			});

		}

	};
	// END FORM DEFINITIONS ------------------------------------------------------------

});

// CHAMADA GERAL DAS PREDEFINIÇÕES DE FORMULÁRIO
// -----------------------------------------------------------------------------------
jQuery(window).load(function() {
	setFormDefinitions();
});
