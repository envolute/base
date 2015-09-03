// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// EVENTOS RESPONSIVOS

		window.customResponsive = function () {

			// fecha o menu quando sai do modo 'mobile'
			if(!jQuery('html').hasClass('media-only-xs') && jQuery('.sm-menu').length) sm160.closeSidebar();

		};
		// CHAMADA DA FUNÇÃO
		customResponsive();
		jQuery(window).resize(function() { customResponsive(); }); // ON RESIZE

});
