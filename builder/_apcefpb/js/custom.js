// SCRIPTS CUSTOMIZADOS DO PROJETO

//JQUERY
jQuery(function() {

	// GET URL BASE FROM INPUT FIELD INTO TEMPLATE BASE
	var URLBase = jQuery('#baseurl').val();

	// EVENTOS RESPONSIVOS
	window.customResponsive = function () {

		// fecha o menu quando sai do modo 'mobile'
		setTimeout(function() {
			if(!jQuery('html').hasClass('media-to-sm') && jQuery('.sm-menu').length) {
				sm160.closeSidebar();
				sm237.closeSidebar();
			}
		}, 100);

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

			// SHOW/HIDE SCROLL-TO-TOP BUTTON
			scrollToTop();
			// ON SCROLL
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
