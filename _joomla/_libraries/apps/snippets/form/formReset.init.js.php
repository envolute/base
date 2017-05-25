<?php
// FORM RESET INITIALIZATION
// Ações iniciais do reset default
?>
// Default Fields
formId.val('');
displayId.val('');

// se houver upload
<?php if($cfg['hasUpload']) echo $APPTAG.'_resetFiles(files)'; ?>

// hidden relation's buttons
if(groupRelations.length) groupRelations.prop('hidden', true);
