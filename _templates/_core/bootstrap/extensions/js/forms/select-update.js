// SELECT UPDATE
// Atualiza o campo do tipo select
jQuery.fn.selectUpdate = function() {
	var obj = jQuery(this);
	if(obj.hasClass('no-chosen')) obj.trigger("change");
	else obj.trigger("chosen:updated").trigger("change");
}
