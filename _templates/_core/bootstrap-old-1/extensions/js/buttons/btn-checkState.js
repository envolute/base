//JQUERY
jQuery(function() {

  // BTN CHECK STATE
	// Define o estado atual dos radios ou checkboxes quando estiverem formatados como botão
	window.btnCheckState = function() {
		var obj;
		jQuery('.btn-group label.btn').find('input:radio, input:checkbox').each(function() {
			obj = jQuery(this);
			if(obj.prop('checked')) obj.closest('label').addClass('active');
			else obj.closest('label').removeClass('active');
		});
	};

});
