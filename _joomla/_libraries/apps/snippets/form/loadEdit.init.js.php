if(!reload) popup.modal({backdrop: 'static', keyboard: false});
<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader

// Default Fields
formId.val(item.id);
displayId.val(item.id);

// state
checkOption(state, item.state);
// se houver upload
<?php if($cfg['hasUpload']) :?>
	<?php echo $APPTAG?>_resetFiles(files);
	<?php echo $APPTAG?>_loadFiles(item.files);
<?php endif;?>
