//JQUERY
jQuery(function() {

  // VIEWED STATE
	// Seta a classe 'viewed' quando elemento já foi visualizado e 'on-screen' quando está visível na tela
	window.viewedState = function(view) {
			if(view.isOnScreen()) view.addClass('viewed on-screen');
			else view.removeClass('on-screen');
	};

  // LOAD ON VIEW
  window.loadOnView = function(elem) {
    // Elemento a ser visualizado
  	var el = setElement(elem, '.load-onView');
  	if(elementExist(el)) {
      // Seta o estado atual
  		el.each(function() { viewedState(jQuery(this)); });
      // Seta o estado durante o evento 'Scroll'
  		jQuery(window).scroll(function() {
  			el.each(function() { viewedState(jQuery(this)); });
  		});
  	}
  };

});
