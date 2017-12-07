// initialization of the javascript files for template BASE

//JQUERY
jQuery(document).ready(function() {

	// RESPONSIVE DEFINITIONS
	responsive(_WIDTH_);

	// CHAMADA GERAL DOS MÉTODOS AUXILIARES
	window.setCoreDefinitions = function () {

		// Helpers
		modalHelpers();
		setBaseModal();
		setTips();
		collapseAll();
		// Browser
		gotoElement();
		// Buttons
		btnToggleState();
		// Forms
		setChosenDefault();
		// Layout
		loadOnView();
		// Navs
		navMenu();
		// Typography
		linkActionsDef();
		// Utilities
		setElementHeight();
		imageRetina();
		setParentWidth();
		gotoElement();
		toggleIcon();

	};
	// CHAMADA GERAL DAS DEFINIÇÕES
	setCoreDefinitions();

	// ---------------------------------------------------------------------------------
	// ON RESIZE CONTROLLER
	// Controle das funcionalidades executadas no evento 'resize'
	jQuery(window).resize(function() {
		var nW = getPageWidth();
		var nH = jQuery(window).height(); // atribui a nova altura
		// Funções relacionadas à Largura
		// executa apenas se houver mudança na largura da página
		if(_WIDTH_ != nW) {
			responsive(nW);
			// setTimeout para que o 'responsive' rode primeiro
			setTimeout(function() {
				navStacked();
				setChosenWidth();
				setParentWidth();
			}, 100);
			// atribui a nova largura
			_WIDTH_ = nW;
			_IS_WIDTH_CHANGE_ = true;
		}
		// Funções relacionadas à Altura
		// executa apenas se houver mudança na altura da página
		if(_HEIGHT_ != nH) {
			setElementHeight();
			// atribui a nova altura
			_HEIGHT_ = nH;
			_IS_HEIGHT_CHANGE_ = true;
		}
	});
	// ---------------------------------------------------------------------------------

});
