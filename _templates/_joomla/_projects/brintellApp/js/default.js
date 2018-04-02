// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// EVENTOS RESPONSIVOS
	window.customResponsive = function () {

		// VERTICAL MENU
    // Fecha o menu quando sai do modo 'mobile'
		setTimeout(function() {
			if((_WIDTH_ < _LG_) && jQuery('.sm-menu').length) sm90.closeSidebar();
		}, 100);

	};

	// SHOW/HIDE SCROLL-TO-TOP BUTTON
	window.scrollToTop = function() {
		var obj = jQuery('#scroll-to-top');
		var pos = jQuery(window).scrollTop();
		if(pos > 200) obj.fadeIn();
		else obj.fadeOut();
	};

	// JOOMLA FRONTEND CUSTOM
	// customização/adaptação de elementos nativos do joomla
	window.joomlaFrontendCustom = function() {
		// define a class 'btn-default' para elementos '.btn' legado do bootstrap 2
	  jQuery('.btn:not([class*=" btn-"])').each(function() {
	    jQuery(this).addClass('btn-default');
	  });
	};

	// END FUNCTION DECLARATIONS--------------------------------------------------------

	window.setCustomDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS AUXILIARES
		// -------------------------------------------------------------------------------

			// CUSTOM RESPONSIVE
			customResponsive();

			// Buttons
			btnRippleEffect();

			// SCROLL TO TOP
			scrollToTop();
			jQuery(window).scroll(function(){ scrollToTop() });

			// JOOMLA FRONTEND CUSTOM
			joomlaFrontendCustom();

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
