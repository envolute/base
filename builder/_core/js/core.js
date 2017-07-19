// initialization of the javascript files for template BASE

//JQUERY
jQuery(document).ready(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	window.URLBase = jQuery('#baseurl').val();

	// Essa função verifica se o parâmetro foi passado
	window.isSet = function (e) {
		return (typeof e === "null" || typeof e === "undefined") ? false : true;
	};

	// Essa função verifica se o parâmetro foi passado
	window.isEmpty = function (e) {
	  return (e == "") ? true : false;
	};

	// Essa função verifica a largura da janela
	window.getPageWidth = function () {
		var e = window, a = 'inner';
		if (!('innerWidth' in window )) {
			a = 'client';
			e = document.documentElement || document.body;
		}
		return e[ a+'Width' ];
	};
	// get current width and height
	window._WIDTH_ = getPageWidth();
	window._IS_WIDTH_CHANGE_ = false;
	window._HEIGHT_ = jQuery(window).height();
	window._IS_HEIGHT_CHANGE_ = false;

	// Essa função é identica a 'setElement' em core.js
	// isso é para não haver dependência do core.js
	window.setElement = function (e, def) {
		var obj = e;
		if(!isSet(e)) {
			obj = jQuery(def);
		} else if(typeof e === 'string') {
			obj = jQuery(e);
		}
		return obj;
	};

	// Essa função verifica se o parâmetro foi passado
	window.elementExist = function (e) {
		var obj = setElement(e);
		return (obj.length) ? true : false;
	};

	// FUNCIONALIDADES --------------------------------------------------------

	// EVENTOS RESPONSIVOS
	// Variações de largura do GRID responsivo
	var sm = 768, md = 992, lg = 1200;
	window.responsive = function (width, sm, md, lg) {
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
	};

	// EVENTOS RESPONSIVOS
	responsive(_WIDTH_,sm,md,lg);

	// seta a altura do elemento
	window.visibleHeight = function(elem, offset, offsetIds) {
		var e = setElement(elem, '.set-visible-height');
		var w = jQuery(window).height();
		if(elementExist(e)) {
			var a = Array();
			var obj, off, offIds;
			e.each(function() {
				obj = jQuery(this);
				off = (isSet(offset) && offset != 0) ? offset : ((isSet(obj.data('offset')) && obj.data('offset') != 0) ? obj.data('offset') : 0);
				offIds = (isSet(offsetIds) && !isEmpty(offsetIds)) ? offsetIds : ((isSet(obj.data('offsetIds')) && !isEmpty(obj.data('offsetIds'))) ? obj.data('offsetIds') : null);
				if(offIds) {
					a = offIds.split(',');
					for(i = 0; i < a.length; i++) {
						el = setElement(a[i].trim());
						off = off + (elementExist(el) ? el.outerHeight(true) : 0);
					}
				}
				// padding interno do elemento interfere na altura
				// dessa forma, é necessário remover da altura
				var pad = obj.outerHeight() - obj.height();
				obj.height(w - (off + pad));
			});
		}
	};

	// seta a largura do elemento pai
	window.setParentWidth = function(elem, offsetLeft, offsetRight) {
		var e = setElement(elem, '.set-parent-width');
		if(elementExist(e)) {
			e.each(function() {
				var obj = jQuery(this);
				var offLeft = (offsetLeft != null) ? offsetLeft : (obj.data('offsetLeft') != null ? obj.data('offsetLeft') : 0);
				var offRight = (offsetRight != null) ? offsetRight : (obj.data('offsetRight') != null ? obj.data('offsetRight') : 0);
				obj.width(obj.parent().width() - offLeft - offRight);
			});
		}
	};

	// SET CLICK
	// seta a ação de click em um elemento
	window.setClick = function (e, target) {
		input = setElement(e, '.set-click');
		input.each(function() {
			obj = setElement((!isSet(target) ? jQuery(this).data('target') : target));
			jQuery(this).click(function(e) {
				e.preventDefault();
				obj.trigger('click');
			});
		});
	};

	//SHOW/HIDE ALERT MESSAGE OF RETURN
	var field_setAlertBalloon = '.set-alert-balloon'
	window.setAlertBalloon = function(elem, seconds, offsetTop, offsetRight, show) {
		var e = setElement(elem, field_setAlertBalloon);
		if(elementExist(e)) {
			e.each(function() {
				var obj = jQuery(this);
				var sec = (seconds != null) ? seconds : (obj.data('seconds') != null ? obj.data('seconds') : 0);
				var offTop = (offsetTop != null) ? offsetTop : (obj.data('offsetTop') != null ? obj.data('offsetTop') : 15);
				var offRight = (offsetRight != null) ? offsetRight : (obj.data('offsetRight') != null ? obj.data('offsetRight') : 15);
				var showOn = (show != null) ? show : (obj.data('show') != null ? obj.data('show') : true);
				obj.css({'top' : offTop, 'right' : offRight});
				// show object
				if(showOn) setTimeout(function() { obj.fadeIn() }, 500);
				if(sec > 0) setTimeout(function() { obj.fadeOut() }, (sec * 1000));
				jQuery(document).on('keydown', function (e) {
				    if (e.keyCode === 27) obj.fadeOut();
				});
				obj.find('.close').click(function() { obj.fadeOut() });
			});
		}
	};
	window.showAlertBalloon = function(elem, seconds) {
		var e = setElement(elem, field_setAlertBalloon);
		if(elementExist(e)) {
			e.each(function() {
				var obj = jQuery(this);
				var sec = (seconds != null) ? seconds : (obj.data('seconds') != null ? obj.data('seconds') : 0);
				obj.fadeIn();
				if(sec > 0) setTimeout(function() { obj.fadeOut() }, (sec * 1000));
			});
		}
	};

	// SHOW/HIDE LOADER
	window.toggleLoader = function(fullScreen) {
		var obj = jQuery('#loader');
		if(typeof fullScreen === "null" || typeof fullScreen === "undefined") {
			obj.toggleClass('active');
		} else {
			if(fullScreen == false) obj.removeClass('fullScreen');
		 	else obj.addClass('fullScreen');
			// mudança de classes
			setTimeout(function() { obj.toggleClass('active') }, 300);
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
			e = (elementExist(object)) ? jQuery(obj) : jQuery('body');
		}
		jQuery('html,body').animate({scrollTop: e.offset().top - topSpace},'slow');
	};

	// #GOTO -> SMOOTH ANCHOR
	window.gotoElement = function(elem, offset) {
		// Basta utilizar o termo "goto-" após o hash "#". Ex: #component -> #goto-component
		var e = setElement(elem, 'a[href*="#goto-"], a.-goto-');
		if(elementExist(e)) {
			e.each(function() {
				jQuery(this).click(function(e) {
					var obj = jQuery(this);
					if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
						var offSet = (offset != null) ? offset : (obj.data('offset') != null ? obj.data('offset') : 0);
						var filter = this.hash.replace("#goto-", "#");
						var target = jQuery(filter);
						target = elementExist(target) ? target : jQuery('[name=' + filter.slice(1) +']');
						if (elementExist(target)) {
							scrollTo(target, offSet);
							return false;
						}
					}
					e.preventDefault();
				});
			});
		}
	};

	// IS ON SCREEN
	// Verifica se o elemento está na tela
	jQuery.fn.isOnScreen = function(x, y){

	    if(x == null || typeof x == 'undefined') x = 0.1;
	    if(y == null || typeof y == 'undefined') y = 0.1;

	    var win = jQuery(window);

	    var viewport = {
	        top : win.scrollTop(),
	        left : win.scrollLeft()
	    };
	    viewport.right = viewport.left + win.width();
	    viewport.bottom = viewport.top + win.height();

	    var height = this.outerHeight();
	    var width = this.outerWidth();

	    if(!width || !height){
	        return false;
	    }

	    var bounds = this.offset();
	    bounds.right = bounds.left + width;
	    bounds.bottom = bounds.top + height;

	    var visible = (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

	    if(!visible){
	        return false;
	    }

	    var deltas = {
	        top : Math.min( 1, ( bounds.bottom - viewport.top ) / height),
	        bottom : Math.min(1, ( viewport.bottom - bounds.top ) / height),
	        left : Math.min(1, ( bounds.right - viewport.left ) / width),
	        right : Math.min(1, ( viewport.right - bounds.left ) / width)
	    };

	    return (deltas.left * deltas.right) >= x && (deltas.top * deltas.bottom) >= y;

	};

	// ELEMENTS ON SCREEN
	// seta a classe 'viewed' quando elemento já foi visualizado e 'on-screen' quando está visível na tela
	window.elementView = function(view) {
			if(view.isOnScreen()) view.addClass('viewed on-screen');
			else view.removeClass('on-screen');
	};
	var el = jQuery('.element-view'); // when element yet was visibled
	if(elementExist(el)) {
		el.each(function() {
			elementView(jQuery(this));
		});
		jQuery(window).scroll(function() {
			el.each(function() {
				elementView(jQuery(this));
			});
		});
	}

	// TOOLTIP / POPOVER -> Bootstrap
	window.setTips = function() {

		// reset Tips
		jQuery('[data-toggle="tooltip"], *[rel=tooltip], .hasTooltip').tooltip('destroy'); // reset Tooltips {when ajax reload}
		jQuery('[data-toggle="popover"], .hasPopover').popover('destroy'); // reset Popovers {when ajax reload}
		// remove current 'tooltips' or 'popovers' created instances
		hideTips();

		// set Tips
		setTimeout(function() { // evita conflito com o 'destroy'
			// hasTooltip
			jQuery('[data-toggle="tooltip"], *[rel=tooltip], .hasTooltip').each(function() {
				var pos = (jQuery(this).data('position') == 'fixed') ? 'pos-fixed' : '';
				var trg = (jQuery(this).data('trigger')) ? jQuery(this).data('trigger') : 'hover';
				jQuery(this).tooltip({
					container: 'body',
					html: true,
					trigger: trg
				}).data('bs.tooltip').tip().addClass(pos);
			});
			// hasPopover
			jQuery('[data-toggle="popover"], .hasPopover').each(function() {
				var pos = (jQuery(this).data('position') == 'fixed') ? 'pos-fixed' : '';
				var trg = (jQuery(this).data('trigger')) ? jQuery(this).data('trigger') : (jQuery(this).hasClass('hasPopover') ? 'hover' : 'click');
				jQuery(this).popover({
					container: 'body',
					html: true,
					trigger: trg
				}).on('show.bs.popover', function () {
					// remove a área do conteúdo caso não haja informação
					if(!jQuery(this).data('content')) jQuery(this).data('bs.popover').tip().find('.popover-content').remove();
				}).data('bs.popover').tip().addClass(pos);
			});
		}, 1000);

	};
	// force tooltip close
	window.hideTips = function() { setTimeout(function() { jQuery('.tooltip.in, .popover').remove(); }, 1000) };

	// LINK ACTIONS DEFAULT
	window.linkActionsDef = function () {

		// LINK AVOID
		// desabilita ação quando link for apenas '#'
		jQuery('a[href$="#"]').click(function (e) {
			e.preventDefault();
		});

		// LINK PADRÃO PARA IMPRESSÃO
		jQuery('#act-print').click(function () {
			print();
		});

	};

	// END FUNCTION DECLARATIONS--------------------------------------------------------

	window.setJsDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS AUXILIARES
		// -------------------------------------------------------------------------------

			// ATRBUI A ALTURA DE ACORDO COM A ÁREA VÍSIVEL
			visibleHeight();

			// ATRIBUI A LARGURA DO OBJETO PAI
			setParentWidth();

			// SET CLICK
			// seta a ação de click em um elemento
			setClick();

			// #GOTO -> SMOOTH ANCHOR
			gotoElement();

			// SHOW/HIDE ALERT MESSAGE OF RETURN
			setAlertBalloon();

			// TOOLTIP / POPOVER -> Bootstrap
			setTips();

			// LINK ACTIONS DEFAULT
			linkActionsDef();

		// FUNCIONALIDADES ESPECÍFICAS
		// -------------------------------------------------------------------------------

			// BASE

				// MODULE TOGGLE
				// show/hide module content
				window.modToggleAction = function(obj, mod, iconOpened, iconClosed) {
					if(obj.hasClass('retract')) {
						mod.find('.module-body').addClass('hide');
						obj.removeClass(iconOpened).addClass(iconClosed);
					} else {
						mod.find('.module-body').removeClass('hide');
						obj.removeClass(iconClosed).addClass(iconOpened);
					}
				};
				// set 'module toggle' element
				var moduleToggle = jQuery('a.module-toggle');
				if(elementExist(moduleToggle)) {
					moduleToggle.each(function() {
						var obj = jQuery(this);
						var mod = obj.closest('.module');
						var iconOpened = (isSet(obj.data('iconOpened'))) ? obj.data('iconOpened') : 'base-icon-down-open';
						var iconClosed = (isSet(obj.data('iconClosed'))) ? obj.data('iconClosed') : 'base-icon-up-open';
						modToggleAction(obj, mod, iconOpened, iconClosed);
						obj.off().on('click', function(e) {
							e.preventDefault();
							obj.toggleClass('retract');
							modToggleAction(obj, mod, iconOpened, iconClosed);
						});
					});
				}

				// FITVIDS
				// chamada do plugin 'fitvids' para deixar os vídeos fluidos...
				// http://fitvidsjs.com/
    		jQuery("#wrapper").fitVids();

			// JOOMLA CONTENT

				// LOGIN
				// atribui o foco no onload
				if(elementExist(jQuery('#login'))) {
					jQuery('#login').on('shown.bs.modal', function () {
						jQuery(this).find('#mod_login_username').focus();
					});
				}

				// por default, esconde botao para editar conteudo
				if(elementExist(jQuery('#toggleBtnEdit'))) {
					if(elementExist(jQuery('.edit-icon'))) {
						// só mostra a opção de esconder quando existe o botão de edição
						jQuery('#toggleBtnEdit').show();
					} else {
						jQuery('#toggleBtnEdit').hide();
					}
					// mostra/esconde botao para editar conteudo
					jQuery('#toggleBtnEdit input[type=checkbox]').click(function(){
						if(elementExist(jQuery('.edit-icon'))) jQuery('.edit-icon').toggle();
					});
				}

				// FONTSIZER -> Redimensionamento da Fonte no conteúdo
				if(elementExist(jQuery('#fontsize'))) {
					jQuery('#fontsize').fontSize({alvo:'#content', setCookie:false, opResetar:false});
				}

				// ZOOM NA IMAGEM PRINCIPAL
				if(elementExist(jQuery('.scroll-image-zoom'))){
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

			// MODAL -> Bootstrap
			// corrige scroll quando há mais de uma modal aberta
			jQuery(document).on('hidden.bs.modal', '.modal', function () {
				jQuery('.modal:visible').length && jQuery(document.body).addClass('modal-open');
			});

			// MODAL -> Colorbox
			// esconde o header do modal quando vier vazio
			jQuery(document).bind('cbox_complete', function(){
			  if(jQuery('#colorbox').find('#cboxTitle').is(':empty')) jQuery('#colorbox').addClass('no-header');
			});
			jQuery(document).bind('cbox_closed', function(){
			  jQuery('#colorbox').removeClass('no-header');
			});

			// TABDROP -> TABS & PILLS
			// agrupa tabs/pills quando passa do limite de largura
			if(elementExist(jQuery('.nav-pills')) || elementExist(jQuery('.nav-tabs'))){
				try {
					jQuery('.nav-pills, .nav-tabs').each(function() {
						var el = jQuery(this);
						if(!el.hasClass('nav-stacked') && !el.hasClass('nav-justified') && !el.hasClass('menu') && !el.hasClass('no-drop')) {
							el.tabdrop();
						}
						// set tabdrop for nested tabs
						// the 'resize' event set tabdrop in hidden tabs
						el.on('show.bs.tab', function (e) {
							jQuery(window).trigger('resize');
						});
					});
				}catch(err){};
			}

			// ACCORDION -> COLLAPSE INDICATOR
			// adiciona um ícone para indicar o estado (aberto ou fechado) do item
			var cps = jQuery('.collapse-indicator');
			function toggleChevron(e) {
				jQuery(e.target).prev('.panel-heading').find(".indicator").toggleClass('base-icon-up-open');
			}
			if(elementExist(cps)) {
				cps.find('.panel-heading').each(function() {
					if(!elementExist(jQuery(this).find('.panel-title .indicator'))) { // verifica se já foi 'appended'
						if(jQuery(this).next('.collapse').hasClass('in')) {
							jQuery(this).find('.panel-title').append('<span class="indicator base-icon-down-open base-icon-up-open pull-right"></span>');
						} else {
							jQuery(this).find('.panel-title').append('<span class="indicator base-icon-down-open pull-right"></span>');
						}
					}
				});
				cps.on('hidden.bs.collapse', toggleChevron);
				cps.on('shown.bs.collapse', toggleChevron);
			}

			// CHOSEN DEFAULT -> tradução
			// essa função será mantida apenas em caratér informativo, pois
			// como é utilizada a biblioteca do próprio joomla, a tradução é feita no arquivo de tradução "pt-BR.ini" (site & admin)
			// Obs: A tradução não vem implementa por padrão no "pt-BR", dessa forma foi necessário colocar manualmente, a partir do "en-GB.ini"
			var chzSearch = 10;
			var chzNoResults = 'Sem resultados para';
			// atribui o chosen default para todos os selects visíveis
			jQuery('select').not('.no-chosen').chosen({
					disable_search_threshold: chzSearch,
					no_results_text: chzNoResults,
					placeholder_text_single: " ",
					placeholder_text_multiple: " "
			});
			window.setChosenWidth = function() {
				if(elementExist(jQuery('select'))) {
					// para resolver o problema da largura = 0 para selects 'hidden'
					// o chosen é atribuído a cada um 'select:hidden' separadamente
					// assim é possível setar a largura através do plugin 'jquery.actual.js'
					// pois ele consegue 'trazer' as dimensões dos elementos 'hidden'
					jQuery('select:hidden').not('.no-chosen').each(function() {
						jQuery(this).next('.chosen-container').width(jQuery(this).actual('outerWidth'));
					});
					// seta uma largura fixa para validar o 'overflow' do select
					var chosensLen = jQuery('.input-group').find('.chosen-container').length;
					chosensLen += 2; // z-index mínimo do chosen-container é 3 ("2" acima do mínimo default => 1)
					jQuery('.input-group').find('.chosen-container').each(function(index, el) {
						var obj = jQuery(this);
						var dpl = obj.find('.chosen-single');
						var grp = obj.closest('.input-group');
						obj.css('position','absolute');
						var w = grp.actual('outerWidth');
						obj.css('position','');
						var wGroup = 0;
						grp.find('.input-group-btn > *, .input-group-addon').each(function() {
							wGroup += jQuery(this).actual('outerWidth');
						});
						w = w - wGroup;
						obj.width(w);
						// atribui uma largura para implementar o 'overflow' do select
						dpl.width(w - dpl.css('padding-left').replace('px', ''));
						// corrige o z-index do select
						obj.css('z-index', chosensLen - index);
					});
				}
			};
			if(elementExist(jQuery('select'))) {
				setChosenWidth();
				// jquery.validation -> remove error class when chosen is change
				var chznError;
				jQuery('select:hidden').not('.no-chosen').change(function() {
					jQuery(this).removeClass('error');
					chznError = jQuery(this).next('.chosen-container').next('.error');
					if(chznError.length) chznError.remove();
				});
			}

			// JOOMLA - VERTICAL MENU
			// abre o(s) nível(is) do item ativo marcando como ativo também o(s) item(ns) pai
			if(elementExist(jQuery('.sm-menu'))) jQuery('.sm-menu dt.active').addClass('current').parents('dd').show().prev('dt').addClass('active opened');

			// NAVBAR -> menu de administração
			// corrige o problema da expansão do link quando o dropdown é acionado
			if(elementExist(jQuery('.menu.dropdown-menu'))) jQuery('.menu.dropdown-menu').removeClass('jmoddiv');

			// NAV MENU & LIST-MENU -> menu horizontal & vertical
			// Atribui a class parent se não houver
			var navChild = jQuery('.nav[class*="menu"] .nav-child');
			if(elementExist(navChild)) {
					navChild.each(function() {
						if(!jQuery(this).closest('li').hasClass('parent')) jQuery(this).closest('li').addClass('parent');
					});
			}
			// Indicador de sub-item
			if(elementExist(jQuery('.nav[class*="menu"]'))) {
				jQuery('.nav[class*="menu"] li.parent > a').each(function() {
					if(!elementExist(jQuery(this).find('.child-reveal'))) { // verifica se já foi 'appended'
						if(jQuery(this).attr('href') == '#') {
							jQuery(this).append('<span class="child-reveal"></span>');
						} else {
							jQuery(this).append('<a href="#" class="child-reveal"></a>');
						}
					}
				});
				// Abre o sub-menu no 'click'
				jQuery('.nav[class*="menu"] li.parent').each(function() {
					var obj = jQuery(this);
					var a = obj.children('a[href="#"]');
					var b = obj.children('a').find('a.child-reveal');
					a.add(b).off().on('click', function(e) {
						e.preventDefault();
						obj.toggleClass('opened');
						// remove também dos sub-itens
						if(!obj.hasClass('opened')) obj.find('li.opened').removeClass('opened');
					});
				});
				// seta o item pai como ativo quando o filho estiver
				jQuery('.nav[class*="menu"] li a:hover, .nav[class*="menu"] li.active').parents('li').addClass('active');
				// Toggle sub-menu
				jQuery('.nav.list-menu li.parent > a').click(function() {
					jQuery(this).parent('li.parent').toggleClass("active");
				});
			}

	};
	// END JS DEFINITIONS --------------------------------------------------------------

	// CHAMADA GERAL DAS PREDEFINIÇÕES DE FORMULÁRIO
	// ---------------------------------------------------------------------------------
	setJsDefinitions();

	// EVENTOS RESPONSIVOS
	responsive(_WIDTH_,sm,md,lg);
	// ON RESIZE
	jQuery(window).resize(function() {
		var nW = getPageWidth();
		var nH = jQuery(window).height(); // atribui a nova altura
		// Funções relacionadas à Largura
		// executa apenas se houver mudança na largura da página
		if(_WIDTH_ != nW) {
			responsive(nW, sm,md,lg);
			setParentWidth();
			setChosenWidth();
			// atribui a nova largura
			_WIDTH_ = nW;
			_IS_WIDTH_CHANGE_ = true;
		}
		// Funções relacionadas à Altura
		// executa apenas se houver mudança na altura da página
		if(_HEIGHT_ != nH) {
			visibleHeight();
			// atribui a nova altura
			_HEIGHT_ = nH;
			_IS_HEIGHT_CHANGE_ = true;
		}
	});
	// ---------------------------------------------------------------------------------

});
