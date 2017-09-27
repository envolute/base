// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// EVENTOS RESPONSIVOS
	window.customResponsive = function () {

		

	};

	// SHOW/HIDE SCROLL-TO-TOP BUTTON
	window.scrollToTop = function() {
		var obj = jQuery('#scroll-to-top');
		var pos = jQuery(window).scrollTop();
		if(pos > 200) obj.fadeIn();
		else obj.fadeOut();
	};

	// END FUNCTION DECLARATIONS--------------------------------------------------------

	window.setCustomDefinitions = function () {

		// CHAMADA GERAL DOS MÉTODOS AUXILIARES
		// -------------------------------------------------------------------------------

			// CUSTOM RESPONSIVE
			customResponsive();

			// SCROLL TO TOP
			scrollToTop();
			jQuery(window).scroll(function(){ scrollToTop() });

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
