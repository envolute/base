//JQUERY
jQuery(function() {

  // SET ELEMENT HEIGHT
  // Seta a altura do elemento de acordo com o 'offset' informado
  // Recomendado para elementos com posição fixa que devem ter sua altura sempre visível
  // Obs: Informando os Id's dos elementos através do parâmetro 'offsetElems', o 'offset' será calculado
	window.setElementHeight = function(elem, offset, offsetElems) {
		var e = setElement(elem, '.set-height');
		var h = jQuery(window).outerHeight();
		if(elementExist(e)) {
			var a = Array();
			var obj, off = 0, offElems;
			e.each(function() {
				obj = jQuery(this);
				// offset
				if(isSet(offset) && !isEmpty(offset) && offset != 0) off = offset;
				else off = (isSet(obj.data('offset')) && obj.data('offset') != 0) ? obj.data('offset') : 0;
				// offset id's
				// Se ambos 'offset' e 'offsetElems' forem informados, o ofsset será o somatório de ambos
				// Isso pode ser utilizado caso seja necessário adicionar um valor extra ao 'offsetElems'
				if(isSet(offsetElems) && !isEmpty(offsetElems)) offElems = offsetElems;
				else offElems = (isSet(obj.data('offsetElements')) && !isEmpty(obj.data('offsetElements'))) ? obj.data('offsetElements') : null;
				if(offElems) {
					a = offElems.split(',');
					for(i = 0; i < a.length; i++) {
						el = setElement(a[i].trim());
						var eH = 0;
						if(elementExist(el)) {
							el.each(function() {
								if(jQuery(this).is(':visible')) eH += jQuery(this).outerHeight(true);
							});
						}
						off = off + eH;
					}
				}
				// padding interno do elemento interfere na altura
				// dessa forma, é necessário remover da altura
				var pad = obj.outerHeight() - obj.height();
				obj.height(h - (off + pad));
			});
		}
	};

});
