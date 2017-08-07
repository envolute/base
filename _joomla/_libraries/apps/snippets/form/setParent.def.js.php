<?php
// SET PARENT DEFAULT
// Ações default para atribuir um ID de um item pai
?>
if(typeof id !== "null" && typeof id !== "undefined" && id != 0) {
  if(parentFieldId != null) {
    parentFieldId.val(id).selectUpdate(); // selects
    parentFieldId.trigger('change');
    // hide 'parentFieldId'
    if(parentFieldGroup && <?php echo $_SESSION[$RTAG.'HideParentField']?> && parentFieldId.find('option[value="'+id+'"]').length) {
      parentFieldGroup.prop('hidden', true);
    }
  }
  btnPrev.remove();
  btnNext.remove();
}
