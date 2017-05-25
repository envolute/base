// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	var URLBase = jQuery('#baseurl').val();

// EVENTOS RESPONSIVOS

	window.customResponsive = function () {

		// --

	};
	// CHAMADA DA FUNÇÃO
	customResponsive();
	jQuery(window).resize(function() { customResponsive(); }); // ON RESIZE

	// SHOW/HIDE SCROLL-TO-TOP BUTTON

		window.scrollToTop = function() {
			var obj = jQuery('#scroll-to-top');
			var pos = jQuery(window).scrollTop();
			if(pos > 200) obj.fadeIn();
			else obj.fadeOut();
		};
		scrollToTop();
		jQuery(window).scroll(function(){ scrollToTop() });

	// NAV MENU -> Menu Principal

		// Cria barra de rolagem caso o submenu ultrapasse o limite inferior da janela.
		// É necessário por causa da posição fixa do menu,
		// que esconde os itens abaixo do limite inferior da janela
		window.navChildHeight = function() {
			var wH = jQuery(window).height(); // window height
			var hH = jQuery('#header').outerHeight(); // #header height
			var fH = jQuery('#footer').outerHeight(); // #footer height
			var e = jQuery('.nav').find('.nav-child');
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

	// BTN-TOOLBAR RIGHT POSITION CALCULATE

		// window.toolbarPosition = function() {
		// 	var toolbarPos = (jQuery(window).width() - jQuery('#main-content').width()) / 2;
		// 	// ajuste
		// 	toolbarPos = toolbarPos - 7;
		// 	jQuery('#toolbar-btns').css('right', toolbarPos);
		// };
		// toolbarPosition();
		// // call on resize
		// jQuery(window).resize(function() { toolbarPosition(); });

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
					e.find('.badge').removeClass('hide');
				}
				e.popover('destroy');
			} else {
				e.addClass('calling');
				e.find('.badge').addClass('hide');
				alertInterval = setInterval(function() {
					e.find('span').effect('shake', {times:4, distance:4}, 1000);
				}, 2000);
				e.popover({
					container: 'body',
					viewport: '#alert-calling',
					html: true,
					trigger: 'focus',
					placement: 'left',
					title: '<strong class="base-icon-clock"> LEMBRETE</strong><a href="javascript:;" onclick="alertCalling(\''+el+'\', null, null, false)" class="base-icon-cancel pull-right"> Fechar</a>',
					content: '<strong class="text-xs text-live">'+date+'</strong><p>'+msg+'</p><a href="javascript:;" onclick="alertCalling(\''+el+'\', null, null, true)" class="base-icon-ok"> Marcar como lido</a>'
				}).data('bs.popover').tip().addClass('pos-fixed');
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

});
