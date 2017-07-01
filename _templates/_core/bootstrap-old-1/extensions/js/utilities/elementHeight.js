//JQUERY
jQuery(function() {

  // SET ELEMENT HEIGHT
  // Seta a altura do elemento de acordo com o 'offset' informado
  // Recomendado para elementos com posição fixa que devem ter sua altura sempre visível
  // Obs: Informando os Id's dos elementos através do parâmetro 'offsetIds', o 'offset' será calculado
	window.setElementHeight = function(elem, offset, offsetIds) {
		var e = setElement(elem, '.set-height');
		var h = jQuery(window).height();
		if(elementExist(e)) {
			var a = Array();
			var obj, off = 0, offIds;
			e.each(function() {
				obj = jQuery(this);
				// offset
				if(isSet(offset) && !isEmpty(offset) && offset != 0) off = offset;
				else off = (isSet(obj.data('offset')) && obj.data('offset') != 0) ? obj.data('offset') : 0;
				// offset id's
        // Se ambos 'offset' e 'offsetIds' forem informados, o ofsset será o somatório de ambos
        // Isso pode ser utilizado caso seja necessário adicionar um valor extra ao 'offsetIds'
				if(isSet(offsetIds) && !isEmpty(offsetIds)) offIds = offsetIds;
				else offIds = (isSet(obj.data('offsetIds')) && !isEmpty(obj.data('offsetIds'))) ? obj.data('offsetIds') : null;
				if(offIds) {
					a = offIds.split(',');
					for(i = 0; i < a.length; i++) {
						el = setElement(a[i].trim());
						off = off + (elementExist(el) ? el.outerHeight(true) : 0);
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
