//JQUERY
jQuery(function() {

  // SET PARENT WIDTH
  // Seta a largura do elemento de acordo com a largura do elemento pai
  // Recomendado para elementos com posição fixa que devem ter sua largura definida
  // Obs: 'offset' pode ser utilizado para atribuir um valor adicional a largura
	window.setParentWidth = function(elem, parentID, offset) {
		var e = setElement(elem, '.w-parent');
		var w = e.parent().width();
		if(elementExist(e)) {
			e.each(function() {
				obj = jQuery(this);
				// parent
				if(isSet(parentID) && !isEmpty(parentID)) pai = parentID;
				else pai = (isSet(obj.data('parentID')) && obj.data('parentID') != 0) ? obj.data('parentID') : false;
        w = (pai) ? jQuery(pai).width() : obj.parent().width();
				// offset
				if(isSet(offset) && !isEmpty(offset) && offset != 0) off = offset;
				else off = (isSet(obj.data('offset')) && obj.data('offset') != 0) ? obj.data('offset') : 0;
        // padding interno do elemento interfere na largura
				// dessa forma, é necessário remover da largura
        var pad = obj.outerWidth() - obj.width();
        obj.width(w - (off + pad));
			});
		}
	};

});
