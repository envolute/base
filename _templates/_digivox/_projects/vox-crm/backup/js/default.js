// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// EVENTOS RESPONSIVOS
	window.customResponsive = function () {

	};

	// NAV MENU -> Menu Principal

		// Cria barra de rolagem caso o submenu ultrapasse o limite inferior da janela.
		// É necessário por causa da posição fixa do menu,
		// que esconde os itens abaixo do limite inferior da janela
		window.navChildHeight = function() {
			var wH = jQuery(window).height(); // window height
			var hH = jQuery('#header').outerHeight(); // #header height
			var fH = jQuery('#footer').outerHeight(); // #footer height
			var e = jQuery('.nav.menu').find('.nav-child');
			e.each(function() {
				var obj = jQuery(this);
				var H = wH - (hH + fH);
				var rH = getRealHeight(obj);
				obj.css('height', (rH > H ? H : 'auto'));
			});
		};
		window.getRealHeight = function(e) {
			e.css({position:'absolute', visibility: 'hidden', display: 'block'}); // torna o objeto visível para o código
			var h = e[0].scrollHeight; // pega altura real do objeto
			e.css({position: '', visibility: '', display: ''}); // retorna ao padrão do objeto
			return h;
		};
		// run function
		navChildHeight();
		jQuery(window).on('resize', function() { navChildHeight(); }); // on resize

	// MMENU -> Mobile Menu
	if(jQuery("#navigation").length) {
		var $mmenu = jQuery("#navigation").clone();
		$mmenu.removeClass().attr( "id", "mm-navigation" );
		$mmenu.find('.nav.menu').removeClass();
		$mmenu.mmenu({
			navbars: false,
			extensions: ["theme-black", "border-full", "pageshadow"]
		});
		jQuery(window).resize(function() { $mmenu.data("mmenu").close(); });
	}

	// AFFIX ELEMENTS
	jQuery('#header, #toolbar-btns').affix({ offset: { top: 2 } });

	// CORREÇÃO PARA MODAL DENTRO DO 'toolbar-btns'

		var newLocation = jQuery('#hidden');
		jQuery('#toolbar-btns').find('.modal.fade').each(function() {
			jQuery(this).appendTo(newLocation);
		});

	// STATS REVEAL
	jQuery('#stats-reveal').click(function() {
		jQuery(this).toggleClass('active base-icon-down-open base-icon-up-open');
		jQuery('#stats-content').toggle();
		jQuery(this).blur();
	});

	// ALERT CALLING
	window.alertCalled = 0;
	window.alertCalledInt = null;
	window.alertCalling = function(el, date, msg, read) {
		var e = jQuery(el);
		if(e.length) {
			if(alertCalled) {
				alertCalled = 0;
				e.popover('hide');
				if(read) {
					e.removeClass('calling');
					clearInterval(alertInterval); // stop the interval
					e.find('.badge').prop('hidden', false);
				}
				e.popover('dispose');
			} else {
				e.addClass('calling');
				e.find('.badge').prop('hidden', true);
				alertInterval = setInterval(function() {
					e.find('span').effect('shake', {times:4, distance:4}, 1000);
				}, 2000);
				e.popover({
					container: 'body',
					viewport: '#alert-calling',
					html: true,
					trigger: 'focus',
					placement: 'left',
					title: '<strong class="base-icon-clock"> LEMBRETE</strong><a href="javascript:;" onclick="alertCalling(\''+el+'\', null, null, false)" class="base-icon-cancel float-right"> Fechar</a>',
					content: '<strong class="text-xs text-live">'+date+'</strong><p>'+msg+'</p><a href="javascript:;" onclick="alertCalling(\''+el+'\', null, null, true)" class="base-icon-ok"> Marcar como lido</a>'
				});
				e.popover('show');
				alertCalled = 1;
			}
		}
	};
	var dt = '09/06 - 12:00';
	var ms = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ex ex, lobortis nec ipsum convallis, hendrerit tristique nisi. Mauris eu nisi blandit, egestas lacus at, fermentum ante.';
	setTimeout(function() {
		alertCalling('#alert-calling', dt, ms);
	}, 2000);
	setInterval(function() {
		alertCalling('#alert-calling', dt, ms);
	}, 360000);

	// CHECK/UNCHECK TODO
	window.setTodoAction = function() {
		setTimeout(function() {
			jQuery('ul.todo').find('a').each(function() {
				jQuery(this).click(function() {
					jQuery(this).toggleClass('base-icon-check-empty base-icon-check text-success strong');
				});
			});
		}, 1000);
	};

	// END FUNCTION DECLARATIONS--------------------------------------------------------

	window.setCustomDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS AUXILIARES
		// -------------------------------------------------------------------------------
			// CUSTOM RESPONSIVE
			customResponsive();

	};
	// END CUSTOM DEFINITIONS --------------------------------------------------------------

	// CHAMADA GERAL DAS CUSTOMIZAÇÕES JAVASCRIPT
	setCustomDefinitions();

	// ON RESIZE
	jQuery(window).resize(function() {
		// se houve alteração na largura da página
		if(_IS_WIDTH_CHANGE_) {
			customResponsive();
		}
		// se houve alteração na altura da página
		if(_IS_HEIGHT_CHANGE_) {

		}
	});
	// -------------------------------------------------------------------------------

});
