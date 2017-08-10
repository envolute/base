<?php
// FORM RESET CLOSURE
// Ações finais do reset default
?>

<?php // set content in html editor
if($cfg['htmlEditor']) echo 'setContentEditor();';
?>

// state -> radio: default = 1
checkOption(state, 1);

// limpa as classes e mensagens de validação caso sejam setadas...
<?php echo $APPTAG?>_clearValidation(mainForm);

// Seta o focus no carregamento do formulário
if(elementExist(firstField)) setTimeout(function() { inputGetFocus(firstField) }, 10);

// remove a paginação do form
<?php echo $APPTAG?>_formPaginator(0);

// Esconde botão Salvar & Novo e deletar 'btn-FORM-save-new, btn-FORM-delete'
mainForm.find('#btn-<?php echo $APPTAG?>-delete').prop('hidden', true);
