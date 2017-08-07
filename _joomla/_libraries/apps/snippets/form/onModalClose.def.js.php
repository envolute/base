<?php
// ON MODAL CLOSE
// Ações quando o modal é fechado
?>
// Limpa a validação quando o formulário é fechado
<?php echo $APPTAG?>_clearValidation(mainForm);

// Reseta o form
<?php echo $APPTAG?>_formReset();

// Reseta o relacionamento
relationId.val('<?php echo $_SESSION[$RTAG.'RelId']?>');

// Reseta o parent
if(parentFieldId != null) {
  parentFieldId.val(0).selectUpdate(); // select
  parentFieldGroup.prop('hidden', false);
}
<?php
// recarrega a listagem
if($cfg['listFull']) echo $APPTAG.'_listReload(fReload, false);';
?>
