<?php
// BTN STATUS
// habilita/desabilita botões se houver, ou não, checkboxes marcados na Listagem
?>
window.<?php echo $APPTAG?>_setBtnStatus = function() {
	var disable = true;
	var btn = jQuery('.<?php echo $APPTAG?>-btn-action');
	jQuery('.checkAll-child').each(function() {
		if(jQuery(this).is(':checked')) disable = false;
	});
	btn.prop('disabled', disable);
};
