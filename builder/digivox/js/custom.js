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
		// window.navChildHeight = function() {
		// 	var wH = jQuery(window).height(); // window height
		// 	var hH = jQuery('#header').outerHeight(); // #header height
		// 	var fH = jQuery('#footer').outerHeight(); // #footer height
		// 	var e = jQuery('.nav').find('.nav-child');
		// 	e.each(function() {
		// 		var obj = jQuery(this);
		// 		var H = wH - (hH + fH);
		// 		var rH = getRealHeight(obj);
		// 		obj.css('height', (rH > H ? H : 'auto'));
		// 	});
		// };
		// window.getRealHeight = function(e) {
		// 	e.css({position:'absolute', visibility: 'hidden', display: 'block'}); // torna o objeto visível para o código
		// 	var h = e[0].scrollHeight; // pega altura real do objeto
		// 	e.css({position: '', visibility: '', display: ''}); // retorna ao padrão do objeto
		// 	return h;
		// };
		// // run function
		// navChildHeight();
		// jQuery(window).on('resize', function() { navChildHeight(); }); // on resize

	// MMENU -> Mobile Menu

		if(jQuery("#navigation").length) {
			var $mmenu = jQuery("#navigation").clone();
			$mmenu.removeClass().attr( "id", "mm-navigation" );
			$mmenu.mmenu({
				"slidingSubmenus": false
			});
			jQuery('#close-menu').click(function() {
				$mmenu.data("mmenu").close();
			});
			jQuery('#toggle-menu').click(function() {
				jQuery('#mm-navigation, #screen').toggleClass('closed');
				setChosenWidth();
			});
			if(!jQuery('html').hasClass('media-md')) jQuery('#mm-navigation, #screen').addClass('closed');
			jQuery(window).resize(function() {
				$mmenu.data("mmenu").close();
				if(!jQuery('html').hasClass('media-md')) jQuery('#mm-navigation, #screen').addClass('closed');
				else jQuery('#mm-navigation, #screen').removeClass('closed');
			});
		}

		// Seleciona o menu ativo
		window.menuItemActive = function(item) {
			if(isSet(item)) {
				// define menu item active
				var i = jQuery('#mm-navigation '+item);
				if(i.length) {
					i.addClass('active current');
					i.parents('li.parent').addClass('mm-opened active');
				}
			}
		}

	// AFFIX ELEMENTS

		jQuery('#header').affix({ offset: { top: 1 } });
		jQuery('#toolbar-btns').affix({ offset: { top: 15 } });

	// BTN-TOOLBAR RIGHT POSITION CALCULATE

		// window.toolbarPosition = function() {
		// 	var toolbarPos = (jQuery(window).width() - jQuery('#main-content').width()) / 2;
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

	// BOTÃO 'TOGGLE' DO FILTRO DA TELA DE RAMAIS
	jQuery('#filtro-reveal').click(function() {
		jQuery('#filtro-agenda').toggleClass('active');
	});
	jQuery('#filtro-agenda').removeClass('hide');

});


// ON LOAD
jQuery(window).load(function() {
	// corrige o tamanho dos selects quando o menu é carregado
	setChosenWidth();
});
