//JQUERY
jQuery(function() {

	// TOGGLE DISPLAY FIELD
	// Result = true => Visível
	window.toggleDisplay = function (input, status) {
		if(!isSet(status) || status == false || status == 'false') status = false;
		else status = true;
		input.each(function() {
			var obj = (input.is('select') && input.next('.chosen-container').length) ? jQuery(this).next('.chosen-container') : jQuery(this);
			obj.prop('hidden', (status ? false : true));
		});
		return status;
	};

	// TOGGLE DISABLED FIELD
	// Result = true => habilitado
	window.toggleDisabled = function (input, status) {
		if(!isSet(status) || status == false || status == 'false') status = false;
		else status = true;
		input.prop('disabled', status);
		if(input.is('select') && input.next('.chosen-container').length) input.trigger("chosen:updated"); // select
		return (status ? false : true);
	};

});
