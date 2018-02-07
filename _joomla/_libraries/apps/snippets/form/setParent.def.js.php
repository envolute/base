<?php
// SET PARENT DEFAULT
// Ações default para atribuir um ID de um item pai
?>

if(isSet(id) && id > 0) {
	if(parentFieldId != null) {
		if(parentFieldId.is('select')) parentFieldId.selectUpdate(id, 0); // selects
		else parentFieldId.val(id);
		// hide 'parentFieldId'
		if(parentFieldGroup && <?php echo $_SESSION[$RTAG.'HideParentField']?> && parentFieldId.find('option[value="'+id+'"]').length) {
			parentFieldGroup.prop('hidden', true);
		}
	}
	btnPrev.remove();
	btnNext.remove();
}
