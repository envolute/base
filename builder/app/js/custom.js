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

	// CHOSEN DEFAULT -> tradução
	// essa função será mantida apenas em caratér informativo, pois
	// como é utilizada a biblioteca do próprio joomla, a tradução é feita no arquivo de tradução "pt-BR.ini" (site & admin)
	// Obs: A tradução não vem implementa por padrão no "pt-BR", dessa forma foi necessário colocar manualmente, a partir do "en-GB.ini"

		var chzSearch = 10;
		var chzNoResults = 'Sem resultados para';
		// atribui o chosen default para todos os selects visíveis
		jQuery('select:visible').not('no-chosen').chosen({
				disable_search_threshold: chzSearch,
				no_results_text: chzNoResults,
				placeholder_text_single: " ",
				placeholder_text_multiple: " "
		});
		// para resolver o problema da largura = 0 para selects 'hidden'
		// o chosen é atribuído a cada um 'select:hidden' separadamente
		// assim é possível setar a largura através do plugin 'jquery.actual.js'
		// pois ele consegue 'trazer' as dimensões dos elementos 'hidden'
		jQuery('select:hidden').not('no-chosen').each(function() {
			jQuery(this).chosen({
				disable_search_threshold: chzSearch,
				no_results_text: chzNoResults,
				placeholder_text_single: " ",
				placeholder_text_multiple: " ",
				width: jQuery(this).actual('outerWidth') + 'px'
			});
		});

	// SHOW/HIDE SCROLL-TO-TOP BUTTON

		window.scrollToTop = function() {
			var obj = jQuery('#scroll-to-top');
			var pos = jQuery(window).scrollTop();
			if(pos > 200) obj.fadeIn();
			else obj.fadeOut();
		};
		scrollToTop();
		jQuery(window).scroll(function(){ scrollToTop() });

	// MMENU -> Mobile Menu

		var $mmenu = jQuery("#navigation").clone();
		$mmenu.removeClass().attr( "id", "mm-navigation" );
		$mmenu.find('.nav.menu').removeClass();
		$mmenu.mmenu({
			navbars: false,
			extensions: ["theme-black", "border-full", "pageshadow"]
		});
		jQuery(window).resize(function() { $mmenu.data("mmenu").close(); });

});
