//JQUERY
jQuery(function() {

  // BTN CHECK STATE
	// Define o estado atual dos radios ou checkboxes quando estiverem formatados como botão
	window.btnCheckState = function(field) {
		var input = jQuery('.btn-group label.btn').find('input:radio, input:checkbox');
		input = setElement(field, input);
		var obj;
		input.each(function() {
			obj = jQuery(this);
			if(obj.prop('checked')) obj.closest('label').addClass('active');
			else obj.closest('label').removeClass('active');
		});
	};

});
