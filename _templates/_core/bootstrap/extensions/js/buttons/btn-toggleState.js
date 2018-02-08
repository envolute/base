//JQUERY
jQuery(function() {

	// BUTTON TOGGLE STATE
	// Alterna, no clique do botão, a classe 'active'
	// Essa é uma opção para ser utilizada através da classe ou da chamada da função
	// O Bootstrap também possui essa funcionalidade através da propriedade 'data-toggle'
	// Ex: <button data-toggle="button">
	// buttonOff: Desativa um botão secundário...
	// Desativa o 'target' (outro botão), removendo a classe 'active'
	// Um exemplo é quando um botão 'collapse-all' (target) está ativo e
	// outro botão é clicado. Assim, ativa o botão clicado e desativa o 'collapse-all' (botão alvo)

	window.btnToggleState = function(button, buttonOff) {
		var btn = setElement(button, '.btn.toggle-state');
		btn.each(function() {
			var obj = jQuery(this);
			obj.off('click').on('click',function(e) {
				obj.not(':disabled').not('.disabled').toggleClass('active');
				// Desativa o botão 'buttonOff'
				var target = isSet(buttonOff) ? buttonOff : false;
				target = isSet(obj.data('buttonOff')) ? setElement(obj.data('buttonOff')) : target;
				if(target && target.hasClass('active')) {
					target.removeClass('active');
					// Caso o 'target' tenha a classe 'toggle-icon'
					toggleIcon(target);
				}
			});
		});
	};

});
