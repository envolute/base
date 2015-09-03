// initialization of the javascript files for template BASE

//JQUERY
jQuery(document).ready(function() { 

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	var URLBase = jQuery('#baseurl').val();

	window.setElement = function (e, def) {
		var obj = e;
		if(typeof e === "null" || typeof e === "undefined") {
			obj = jQuery(def);
		} else if(typeof e === 'string') {
			obj = jQuery(e);
		}
		return obj;
	};

	// seta a largura do elemento pai
	window.setParentWidth = function(elem, offsetLeft, offsetRight) {
		var e = setElement(elem, '.set-parent-width');
		if(e.length) {
			e.each(function() {
				var obj = jQuery(this);
				var offLeft = (offsetLeft != null) ? offsetLeft : (obj.data('offsetLeft') != null ? obj.data('offsetLeft') : 0);
				var offRight = (offsetRight != null) ? offsetRight : (obj.data('offsetRight') != null ? obj.data('offsetRight') : 0);
				obj.width(obj.parent().width() - offLeft - offRight);
			});
		}
	};

	// seta a altura do elemento
	window.visibleHeight = function(elem, offsetTop, offsetBottom) {
		var e = setElement(elem, '.set-visible-height');
		if(e.length) {
			e.each(function() {
				var obj = jQuery(this);
				var offTop = (offsetTop != null) ? offsetTop : (obj.data('offsetTop') != null ? obj.data('offsetTop') : 0);
				var offBottom = (offsetBottom != null) ? offsetBottom : (obj.data('offsetBottom') != null ? obj.data('offsetBottom') : 0);
				obj.height(jQuery(window).height() - offTop - offBottom);
			});
		}
	};

	// SMOOTH SCROLL
	window.scrollTo = function(obj, offSet){
		var e;
		var topSpace = (offSet != null) ? offSet : 0;
		if(obj == null) {
			e = jQuery('body');
		} else {
			var object = (jQuery.type(obj) === "string") ? jQuery(obj) : obj;
			e = (object.length) ? jQuery(obj) : jQuery('body');
		}
		jQuery('html,body').animate({scrollTop: e.offset().top - topSpace},'slow');
	};

	// #GOTO -> SMOOTH ANCHOR
	window.gotoElement = function(elem, offset) {
		// Basta utilizar o termo "goto-" após o hash "#". Ex: #component -> #goto-component
		var e = setElement(elem, 'a[href*="#goto-"], a.-goto-');
		if(e.length) {
			e.each(function() {
				jQuery(this).click(function(e) {
					var obj = jQuery(this);
					if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
						var offSet = (offset != null) ? offset : (obj.data('offset') != null ? obj.data('offset') : 0);
						var filter = this.hash.replace("#goto-", "#");
						var target = jQuery(filter);
						target = target.length ? target : jQuery('[name=' + filter.slice(1) +']');
						if (target.length) {
							scrollTo(target, offSet);
							return false;
						}
					}
					e.preventDefault();
				});
			});
		}
	};

	// EVENTOS RESPONSIVOS

		// Variações de largura do GRID responsivo
		var sm = 768, md = 992, lg = 1200;

		window.responsive = function (sm,md,lg) {
			var e = window, a = 'inner';
			if (!('innerWidth' in window )) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			var width = e[ a+'Width' ];

			// "col" define o formato atual
			jQuery('html').addClass('nav-default').removeClass('media-only-xs media-only-sm media-only-md media-only-lg nav-mobile');
			if (width >= lg) 		jQuery('html').addClass('media-only-lg');
			if (width < lg && width >= md) 	jQuery('html').addClass('media-only-md');
			if (width < md && width >= sm) 	jQuery('html').addClass('media-only-sm');
			if (width < sm)			jQuery('html').addClass('media-only-xs');

			// "media" define o formato mínimo
			jQuery('html').addClass('media-sm media-md media-lg');
			if (width < lg) jQuery('html').removeClass('media-lg');
			if (width < md) jQuery('html').removeClass('media-md');
			if (width < sm) jQuery('html').removeClass('media-sm');

			// "media" define o formato máximo
			jQuery('html').removeClass('media-to-sm media-to-md');
			if (width < lg) jQuery('html').addClass('media-to-md');
			if (width < md) jQuery('html').addClass('media-to-sm').removeClass('media-to-md');

			// hlist responsivo
			if (jQuery('.hlist').not('.no-responsive').length && width < sm) jQuery('.hlist').removeClass('hlist').addClass('list responsive-hlist');
			if (jQuery('.responsive-hlist').length && width >= sm) jQuery('.responsive-hlist').removeClass('list responsive-hlist').addClass('hlist');

			return width;
		};
		// CHAMADA DA FUNÇÃO
		// seta a largura da janela para o resto do código
		var width = responsive(sm,md,lg);
		jQuery(window).resize(function() { width = responsive(sm,md,lg); }); // ON RESIZE

	// LOGIN -> atribui o foco no onload
	jQuery('#login').on('shown.bs.modal', function () {
		jQuery(this).find('#mod_login_username').focus();
	})

	// CONTEÚDO

		// por default, esconde botao para editar conteudo
		if(jQuery('.edit-icon').length) {
			// só mostra a opção de esconder quando existe o botão de edição
			jQuery('#toggleBtnEdit').show();
		} else {
			jQuery('#toggleBtnEdit').hide();
		}
		// mostra/esconde botao para editar conteudo
		jQuery('#toggleBtnEdit input[type=checkbox]').click(function(){
			if(jQuery('.edit-icon').length) jQuery('.edit-icon').toggle();
		});

		// FONTSIZER -> Redimensionamento da Fonte no conteúdo
		if(jQuery('#fontsize').length) {
			jQuery('#fontsize').fontSize({alvo:'#content', setCookie:false, opResetar:false});
		}

		// LINK PADRÃO PARA IMPRESSÃO
		jQuery('#act-print').click(function () {
			print();
		});

		// ZOOM NA IMAGEM PRINCIPAL
		if(jQuery('.scroll-image-zoom').length){
			var obj = '.scroll-image-zoom';
			jQuery.getScript(URLBase+'/templates/base/core/js/content/wheelzoom.js', function(){
				jQuery(obj).each(function() {
					var imgZoom = jQuery(this);
					imgZoom.css('cursor','ne-resize');
					wheelzoom(imgZoom);
					imgZoom.bind('touchstart mousedown', function() { imgZoom.css('cursor','all-scroll'); });
					imgZoom.on('touchend mouseup', function() { imgZoom.css('cursor','ne-resize'); });
					imgZoom.on('dblclick', function() {
						document.querySelector(obj).dispatchEvent(new CustomEvent('wheelzoom.reset'));
					});
				});
			});
		}

	// ANIMATION LOAD

		jQuery('.load-on-view:in-viewport').addClass('viewed');
		jQuery(window).scroll(function() {
			jQuery('.load-on-view:in-viewport').addClass('viewed');
		});

	// TOOLTIP / POPOVER -> Bootstrap

		// hasTooltip
		jQuery('.hasTooltip, [data-toggle="tooltip"]').tooltip({ container: 'body', html: true });
		jQuery('.setTooltip').tooltip({ container: 'body', html: true, trigger: 'focus' });

		// hasPopover
		jQuery('.hasPopover, [data-toggle="popover"]').popover({ container: 'body', html: true, trigger: 'hover focus' });
		jQuery('.setPopover').popover({ container: 'body', html: true, trigger: 'focus' });

	// NAVIGATION

		// desabilita ação quando link for apenas '#'
		jQuery('a[href$="#"]').click(function (e) {
			e.preventDefault();
		});

		// TABDROP -> TABS & PILLS
		// agrupa tabs/pills quando passa do limite de largura
		try {
			jQuery('.nav-pills:not(.nav-stacked):not(.menu), .nav-tabs').each(function() {
				jQuery(this).tabdrop();
			});
		}catch(err){};

		// ACCORDION -> COLLAPSE INDICATOR
		// adiciona um ícone para indicar o estado (aberto ou fechado) do item
		var cps = jQuery('.collapse-indicator');
		function toggleChevron(e) {
			jQuery(e.target).prev('.panel-heading').find(".indicator").toggleClass('base-icon-up-open');
		}
		if(cps.length) {
			cps.find('.panel-heading').each(function() {
				if(jQuery(this).next('.collapse').hasClass('in')) {
					jQuery(this).find('.panel-title').append('<span class="indicator base-icon-down-open base-icon-up-open pull-right"></span>');
				} else {
					jQuery(this).find('.panel-title').append('<span class="indicator base-icon-down-open pull-right"></span>');
				}
			});
			cps.on('hidden.bs.collapse', toggleChevron);
			cps.on('shown.bs.collapse', toggleChevron);
		}

		// VERTICAL MENU -> NAVIGATION

		// abre o(s) nível(is) do item ativo marcando como ativo também o(s) item(ns) pai
		if(jQuery('.sm-menu').length) jQuery('.sm-menu dt.active').addClass('current').parents('dd').show().prev('dt').addClass('active opened');

		// NAVBAR -> menu de administração

			// corrige o problema da expansão do link quando o dropdown é acionado
			jQuery('.menu.dropdown-menu').removeClass('jmoddiv');

		// NAVSIDE

			// seta a altura da barra fixa de navegação lateral
			var navside		= jQuery('#navside');
			if(navside.length) {
				var nbHeight	= (jQuery("#navbar").length) ? jQuery("#navbar").outerHeight(true) : 0;
				var ntHeight	= (jQuery("#navtop").length) ? jQuery("#navtop").outerHeight(true) : 0;
				var navOffset	= nbHeight + ntHeight;
				visibleHeight(navside, navOffset);
				jQuery(window).resize(function() { visibleHeight(navside, navOffset); }); // ON RESIZE
			}

		// NAV MENU & LIST-MENU -> menu horizontal & vertical

			// Atribui a class parent se não houver
			var navChild = jQuery('.nav[class*="menu"] .nav-child');
			if(navChild.length) {
					navChild.each(function() {
						if(!jQuery(this).parent('li').hasClass('parent')) jQuery(this).parent('li').addClass('parent');
					});
			}

			// Indicador de sub-item
			jQuery('.nav[class*="menu"] li.parent > a').append('<span class="child-reveal"></span>');

			// mantém o hover no item pai quando o filho receber o foco
			jQuery('.nav[class*="menu"] li.active').parents('li').addClass('active');

			// Atribui o 'hover' ao item pai quando o filho receber o foco
			jQuery('.nav.menu a').hover(function() {
				jQuery(this).parents('ul.nav-child').prev('a').not(this).toggleClass("hovered");
			});

			// Toggle sub-menu
			jQuery('.nav.list-menu li.parent > a').click(function() {
				jQuery(this).parent('li.parent').toggleClass("active");
			});

			// CHAMADA GERAL DOS MÉTODOS AUXILIARES
			// -------------------------------------------------------------------------------

			// ATRIBUI A LARGURA DO OBJETO PAI
			setParentWidth();

			// ATRBUI A ALTURA DE ACORDO COM A ÁREA VÍSIVEL
			visibleHeight();

			// #GOTO -> SMOOTH ANCHOR
			gotoElement();

			// -------------------------------------------------------------------------------

});
