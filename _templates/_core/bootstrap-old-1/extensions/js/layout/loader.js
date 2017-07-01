//JQUERY
jQuery(function() {

  // TOGGLE LOADER
	// 'Mostra/esconde' o Loader (ícone de carregamento)
  // São defindas duas opções de 'loader'
  // Default: apenas um ícone indicativo em uma posição da tela
  // Fullscreen: Layer sobre o conteúdo, ocupa toda a tela e impossibilida ações durante a execução
	// 'state = true' mostra o loader
	// 'state = false' esconde o loader
	// 'state = toggle/undefined', alterna o esta
	// 'full = true' loader Fullscreen
	// 'full = false' loader Default
  window.toggleLoader = function(state, full) {
		var el = jQuery('#tmpl-loader');
		if(elementExist(el)) {
			// Set 'fullscreen' definition
			if(isSet(full) && full) el.addClass('fullScreen');
			else el.removeClass('fullScreen');
			// Set 'loader' state
			if(isSet(state) && state != 'toggle') {
				if(state) el.addClass('active');
				else el.removeClass('active');
			} else {
				el.toggleClass('active');
			}
		}
	};

});

// Esconde o loader após o carregamento da página
jQuery(window).load(function() {
	if(jQuery('body').hasClass('preloader')) {
		setTimeout(function() {
			jQuery('body').removeClass('preloader'); // mostra o conteúdo
		}, 1000);
	}
});
