//JQUERY
jQuery(function() {

  // REMOVE AFFIX
	// Metodo para desabilitar a funcionalidade 'affix' em um elemento
  // O método original não traz essa funcionalidade
  window.removeAffix = function(elemento) {
		var el = setElement(elemento);
		if(elementExist(el)) {
      jQuery(window).off('.affix');
			el.removeData('bs.affix').removeClass('affix affix-top affix-bottom');
		}
	};

});
