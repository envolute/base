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
			if(obj.find('option[value="'+val +'"]').length) obj.val(val);
			// senão atribui o valor 'default'
			else obj.val(def);
		}
	}
	// Atualiza o campo e seta o evento 'change'
	if(obj.hasClass('no-chosen')) obj.trigger("change");
	else obj.trigger("chosen:updated").trigger("change");
}
