//JQUERY
jQuery(function() {

	// CHECK ALL
	// marca/desmarca todos os checkboxes em um determinado elemento
	// check => seletor identificador do checkbox selecionador
	// child => Checkbox que serão selecionados
	// container => Elemento que contém os 'children'.
	// => Caso o container não seja declarado, irá buscar o elemento 'form' > 'parent' do check
	window.checkAll = function (check, child, container) {

		// Checkbox
		var inputCheck = setElement(check, '.input-checkAll');

		inputCheck.each(function() {
			var obj = jQuery(this);
			var objContainer = isSet(container) ? container : '';
			objContainer = isSet(obj.data('container')) ? obj.data('container') : objContainer;
			var children = isSet(child) ? child : '.checkAll-child';
			children = isSet(obj.data('child')) ? obj.data('child') : children;

			objContainer = !isEmpty(objContainer) ? setElement(objContainer) : obj.closest('form');
			children = objContainer.find(children);
			
			if(inputCheck.length && objContainer.length && children.length) {

				obj.click(function() {
					var checked = jQuery(this).is(':checked') ? true : false;
					children.each(function(i) {
						jQuery(this).prop("checked", checked);
					});
				});
				// desmarca checkAll caso um checkbox da lista seja alterado individualmente
				children.click(function() {
					obj.prop("checked", false);
				});
			}
		});

	};

});
