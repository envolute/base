// SELECT UPDATE
// Atualiza o campo do tipo select
jQuery.fn.selectUpdate = function(val, reset) {
	var obj = jQuery(this);
	if(isSet(val)) {
		var def = isSet(reset) ? reset : 0;
		if(jQuery.isArray(val)) {
			obj.val(val);
		} else {
			// verifica se existe a opção com o valor
			var option = obj.find('option[value="'+val +'"]');
			if(option.length) {
				if(obj.prop('multiple')) option.prop('selected', true);
				else obj.val(val);
			} else {
				// senão atribui o valor 'default'
				obj.val(def);
			}
		}
	}
	// Atualiza o campo e seta o evento 'change'
	if(obj.hasClass('no-chosen')) obj.trigger("change");
	else obj.trigger("chosen:updated").trigger("change");
}
