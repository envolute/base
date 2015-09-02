// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	var URLBase = jQuery('#baseurl').val();

	// EVENTOS RESPONSIVOS

		window.customResponsive = function () {

			// fecha o menu quando sai do modo 'mobile'
			if(!jQuery('html').hasClass('media-only-xs') && jQuery('.sm-menu').length) sm160.closeSidebar();

		};
		// CHAMADA DA FUNÇÃO
		customResponsive();
		jQuery(window).resize(function() { customResponsive(); }); // ON RESIZE

	// CHOSEN DEFAULT -> tradução
	// essa função será mantida apenas em caratér informativo, pois
	// como é utilizada a biblioteca do próprio joomla, a tradução é feita no arquivo de tradução "pt-BR.ini" (site & admin)
	// Obs: A tradução não vem implementa por padrão no "pt-BR", dessa forma foi necessário colocar manualmente, a partir do "en-GB.ini"

		jQuery('select').chosen({
			disable_search_threshold: 10,
			no_results_text: "Sem resultados para",
			placeholder_text_single: " ",
			placeholder_text_multiple: " "
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

});
