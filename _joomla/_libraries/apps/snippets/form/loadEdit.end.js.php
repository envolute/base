<?php // set content in html editor
if($cfg['htmlEditor']) echo 'setContentEditor();';
?>

// show relation's buttons
if(groupRelations.length) groupRelations.prop('hidden', false);

// set form's paginator
<?php echo $APPTAG?>_formPaginator(item.id, item.prev, item.next);
// recarrega os scripts de formulário para os campos
// necessário após um procedimento ajax que envolve os elementos
setFormDefinitions();

// Seta o focus no carregamento do formulário
if(elementExist(firstField)) setTimeout(function() { inputGetFocus(firstField) }, 500);
