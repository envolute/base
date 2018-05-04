//JQUERY
jQuery(function() {

	// SET HIDDEN
	// Seta a propriedade 'hidden' do elemento
	// 'elem' seta a propriedade 'hidden' de acordo com 'state'
	// 'state' 'hidden' (true/false)
	// 'toggleElem' elementos com a propriedade 'hidden' invertida
	// Ex: quando elem.prop('hidden', true) => toggleElem.prop('hidden', false);
	window.setHidden = function(elem, state, toggleElem) {
		var e = setElement(elem);
		var i = isSet(toggleElem) ? setElement(toggleElem) : false;
		var s = (isSet(state) && !isEmpty(state) && state && state != 0) ? true : false;
		var t = (s ? false : true);
		// define a propriedade dos elementos setados em 'elem'
		if(elementExist(e)) e.each(function() { jQuery(this).prop('hidden', s) });
		// Inverte a propriedade dos elementos setados em 'toggleElem'
		if(elementExist(i)) i.each(function() { jQuery(this).prop('hidden', t) });
	};

});
